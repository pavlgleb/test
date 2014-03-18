<?php
    class Action extends DataBase{

        private $name;
        private $comment;
        private $error;
        private $parent_id;
        public $count_comment;                       
        
        public function  __construct() {
            $this->name = $this->clearForm($_POST['name']);
            $this->comment = $this->clearForm($_POST['comment']);
            $this->parent_id = ($this->clearForm($_POST['parent_id']) !== 0) ? $this->clearForm($_POST['parent_id']) : 0;
        }

        public function sendComment(){
            if($this->validate() === false){
                die("Заполните поля формы");
                return $this->error;
            }
            else{                                                              
                $succes = $this->addComment();
                if($succes){
                    return true;
                }
                else{
                    return false;
                }
            }
        }

        private function addComment(){
            $check = false;
            if($this->parent_id == 0){                
                $sql = mysql_query("SELECT id left_key, right_key, level FROM comment_list");
                if(mysql_num_rows($sql) != 0)
                    $check = true;
                if(mysql_num_rows(mysql_query("SELECT id FROM comment_list")) == 0)
                    $check = true;
            }else{               
                $sql = mysql_query("SELECT id left_key, right_key, level FROM comment_list WHERE id='".$this->parent_id."'");
                if(mysql_num_rows($sql) != 0)
                    $check = true;
            }
            if($check){                
                $row = mysql_fetch_array($sql);                
                $left_key= $row['left_key'];
                $right_key = $row['right_key'];
                $level = $row['level'];
                $time = time();                                
                mysql_query("UPDATE comment_list SET left_key = left_key + 2, right_key = right_key + 2 WHERE left_key > '".$right_key."'");
                mysql_query("UPDATE comment_list SET right_key = right_key+2 WHERE right_key >='".$right_key."' AND left_key < '".$right_key."'");
                $succes = mysql_query("INSERT INTO `comment_list` (`parent_id`, `name`, `comment`, `time`, `left_key`, `right_key`, `level`) VALUES
                ('".$this->parent_id."', '".$this->name."', '".$this->comment."', '".$time."', '".$right_key."', '".$right_key."'+1, '".$level."'+1)");
                return $succes;
            }
        }

        public function showComment(){           
            $message=array();
            $comments="";
            $query = "SELECT * FROM `comment_list` ORDER BY left_key";
            $succes = mysql_query($query);
            while($row = mysql_fetch_assoc($succes)){
                $message[] = $row;
            }
            $this->count_comment = count($message);
            if($this->count_comment){                           
                $level = 0;
                foreach ($message as $k=>$v){
                    if($level>=$v['level'] and $level!=0)$comments.='</li>';
                    if($level>$v['level'])$comments.=str_repeat('</ul></li>',($level-$v['level']));
                    if($level<$v['level'])$comments.='<ul>';
                    $comments.='<li id="ul'.$v['id'].'">
                                            <div class="all-comment" id="all-comment'.$v['id'].'">
                                                <div class="title-comment">
                                                    <span class="auth" id="auth'.$v['id'].'">'.$v['name'].'</span>
                                                    <span class="delete" id="d'.$v['id'].'">[Delete]</span>
                                                    <span class="edit" id="e'.$v['id'].'">[Edit]</span>
                                                    <span class="add_comment" id="'.$v['id'].'">[Answer]</span>                                                    
                                                </div>
                                                <div class="body-comment" id="com'.$v['id'].'">'.$v['comment'].'</div>
                                            </div>';
                    $level=$v['level'];
                }
                $comments.='</li>';
                if($level>=1)$r.=str_repeat('</ul></li>',$level-1);
                $comments.='</ul>';
            }
            return $comments;
        }

        // edit
        public function editComment($id_comment){            
            //echo json_encode(array('msg' => $this->name));exit;            
            $sql = mysql_query("SELECT * FROM comment_list WHERE id='".$id_comment."'");
            $check = false;

            if(mysql_num_rows($sql) !== 0)
                $check = true;
            
            if($check){                
                $keys = mysql_fetch_array($sql);
                $left_key = $keys['left_key'];
                $right_key = $keys['right_key'];
                $level = $keys['level'];
                $id = $keys['id'];                
                
                mysql_query("UPDATE comment_list SET name = '".$this->name."', comment ='".$this->comment."' WHERE left_key='".$left_key."' AND right_key ='".$right_key."'");
            }            
        }
        // delete
        public function deleteComment($id_node){
            $id_node = (int)str_replace("d", "", $id_node);
            //echo json_encode(array('msg' => $id_node));exit;            
            $sql = mysql_query("SELECT id, left_key, right_key, level FROM comment_list WHERE id='".$id_node."'");
            if(mysql_num_rows($sql) == 0){
                die(json_encode(array(
                    "result" => "error",
                    "message" => "Request error"
                )));
            }
            $keys = mysql_fetch_array($sql);
            $left_key = $keys['left_key'];
            $right_key = $keys['right_key'];
            $level = $keys['level'];
            $id = $keys['id'];
            
            mysql_query("DELETE FROM comment_list WHERE left_key >= $left_key AND right_key <= $right_key");
            mysql_query("UPDATE comment_list SET right_key = right_key - ($right_key - $left_key + 1) WHERE right_key > '".$right_key."' AND left_key < '".$left_key."'");
            mysql_query("UPDATE comment_list SET left_key = left_key - ($right_key - $left_key + 1), right_key = right_key - ($right_key - $left_key + 1) WHERE left_key > $right_key");            
            
            die(json_encode(array(
                "result" => "success",
                "message" => "Deleted"
            )));
        }

        private function clearForm($text){
            $clear_text = mysql_real_escape_string(trim(htmlspecialchars(addslashes(stripslashes($text)))));
            return $clear_text;
        }

        private function validate(){
            if(empty($this->name) || empty($this->comment)){
                $this->error = "Заполните все поля";
                return false;
            }
            else{
                return true;
            }
        }        
    }
?>
