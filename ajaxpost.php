<?php
    include "classes/database.php";
    include "classes/action.php";
    if(isset($_POST['add_new_comm'])){        
        $sendComment = new Action();
        $succes = $sendComment->sendComment();
        header("Location: index.php");
    }
    if(isset($_POST['edit']) && isset($_POST['parent_id'])){
        $editComment = new Action();
        $succes = $editComment->editComment($_POST['parent_id']);        
        header("Location: index.php");
    }
    if(($_POST['action'] == "delete") && isset($_POST['id'])){
         $sendComment = new Action();
         $succes = $sendComment->deleteComment($_POST['id']);         
    }    
?>