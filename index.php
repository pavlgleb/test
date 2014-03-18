<?php
    include "classes/database.php";
    include "classes/action.php";
     $obj = new Action();
     $comments = $obj->showComment()
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Comment-vacansy</title>
        <link href="css/style.css" rel="stylesheet" type="text/css"  />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" charset="utf-8"></script>       
        <script src="js/myscript.js"></script>
    </head>
    <body>
     <div class="main">
         <div class="header">
            <span class="count_comment">
                 <?php
                    if($obj->count_comment){
                        echo "Comments: ".$obj->count_comment;
                    }else{
                        echo "No comments";
                    }
                 ?>
            </span>
            <span class="add_comment">
                <?php
                    if($obj->count_comment == 0){
                        echo "[New comment]";
                    }
                ?>
            </span>
         </div>
         <div class="editor" style="display:none;">
            <form action="/ajaxpost.php" method="post" name="form_comment">
                <input type='hidden' name='parent_id' value='0'>
                <div class="row">                    
                    <input type="text" name="name" value=""/>
                    <label>Name</label>
                </div>
                <div class="row">                   
                    <textarea name="comment" rows="5" cols="20"></textarea>                    
                </div>
                <div class="row">
                    <input type="submit" name="add_new_comm" value="Add">
                </div>
            </form>
         </div>         
         <?php            
            echo $comments;
         ?>
     </div>
    </body>
</html>
