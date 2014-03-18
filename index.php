<?
$conf['db'] = array(
  "server"=>"localhost",
  "user"=>"root",
  "pass"=>"",
  "base"=>"superbase"
);

mysql_connect($conf['db']['server'], $conf['db']['user'], $conf['db']['pass'])or die("Could not connect: ".mysql_error());
mysql_select_db($conf['db']['base'])or die("Could not select: ".mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");

if($_GET['op'] === 'clear-all'){
  mysql_query("DELETE FROM les_comments");
  header("Location: index.php");
}

$time = time();
  
if(isset($_POST['uname'])){
  $uname = mysql_real_escape_string($_POST['uname']);
  setcookie("unamecom", $uname, $time + 1209600);   // время существования куки две недели
}elseif(isset($_COOKIE["unamecom"])){
  $uname = mysql_real_escape_string($_COOKIE["unamecom"]);
}else{
  $uname = "Аноним";
}

if(!empty($_POST['uname']) && !empty($_POST['message']) && $_POST['op'] == 'add-comment') {


  $comment = mysql_real_escape_string(strip_tags($_POST['message'], "<p><b><i><font><img>")); // удалим левые теги
  $ip = $_SERVER['REMOTE_ADDR'];
  $client = $_SERVER['HTTP_USER_AGENT'];
  $content_id = intval($_POST['content']);
  $parent_id = intval($_POST['parent']);

  mysql_query("INSERT INTO les_comments (`id`, `name`, `ip`, `client`, `comment`, `content_id`, `parent_id`, `time`) VALUES (NULL, '$uname', '$ip', '$client', '$comment', '$content_id', '$parent_id', '$time')");
}

/*
*/

$content_id = 0;  // это ключевой идентификатор от конкретной статьи(комменты то разные в каждой статье, помним?)

// выводим комменты
$msg = array();
$result = mysql_query("SELECT * FROM les_comments WHERE content_id='$id'");
while($row = mysql_fetch_assoc($result)){
  $msg[] = $row;
}
$count = count($msg);

$parent = 0;
$form = "<div class='editor'>
<form id='comment-form' autocomplete='off' method='post'>
<input type='hidden' name='op' value='add-comment'>
<input type='hidden' name='content' value='{$id}'>
<input type='hidden' name='parent' value='{$parent}'>
<table border='0'><tr><td><input id='uname' name='uname' type='text' value='{$uname}' maxlength='20' size='25' /></td><td>Ваше имя*</td></tr></table>
<textarea name='message' rows='5' cols='65'></textarea><br><input id='submit' name='signup' type='submit' value='Добавить' /></div>
</form>";

$i = 0;
if($count){
  $comments = "<div class='comments-all'><span style='float:left'>Всего комментариев: {$count}</span><span class='add-comment'>Написать комментарий</span></div>".$form;
  
  $msg = crazysort($msg);
  while($i<$count){
    $margin = $msg[$i]['level'] * 20;
    $date = date("d.m.Y в H:i",$msg[$i]['time']);
    $comments .= "<div id='msg{$msg[$i]['id']}' style='margin-left: {$margin}px'><div class='comment-title'><span style='float:left'><b>{$msg[$i]['name']}</b> <small>({$date})</small></span><span class='comment-ans' id={$msg[$i]['id']}>ответить</span></div><div class='comment-message'>{$msg[$i]['comment']}</div></div>";
    $i++;
  }  
}else{
  $comments = "<div class='comments-all'><span style='float:left'>Эту новость ещё не комментировали</span><span class='add-comment'>Написать комментарий</span></div>".$form;
}

// функция сортирует массив по деревьям
function crazysort(&$comments, $parentComment = 0, $level = 0, $count = null){
  if (is_array($comments) && count($comments)){
    $return = array();
    if (is_null($count)){
      $c = count($comments);
    }else{
      $c = $count;
    }
    for($i=0;$i<$c;$i++){
      if (!isset($comments[$i])) continue;
      $comment = $comments[$i];
      $parentId = $comment['parent_id'];
      if ($parentId == $parentComment){
        $comment['level'] = $level;
        $commentId = $comment['id'];
        $return[] = $comment;
        unset($comments[$i]);
        while ($nextReturn = crazysort($comments, $commentId, $level+1, $c)){
          $return = array_merge($return, $nextReturn);
        }
      }
    }
    return $return;
  }
  return false;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css"/> 
<title>Древовидные комментарии для Вашего сайта</title> 
<meta name="keywords" content="http://amatar.by комменты"/> 
<meta name="description" content="урок с сайта радиолюбителей http://amatar.by"/> 
<link rel='stylesheet' type='text/css' href='css/style.css'/>
<script type='text/javascript' src='js/jquery-1.5.2.min.js'></script>
</head>
<body>
<div style='margin:0 auto; width:780px'>
<?
echo $comments;
echo "<center><a href='?op=clear-all'>очистить все комменты</a></center>";
?>
</div>
<script>
$(function () {
  $('.add-comment').click(function(){
    var editor = $('.editor');
    if (editor.is(":hidden")){
      editor.slideDown();
    }else{
      editor.slideUp();
    }
    return false;
  });
  
  $('.comment-ans').click(function(){
    var $editor = $('.editor');
    $editor.hide();
    var mid = $(this).attr("id");
    var clone = $editor.clone();
    $editor.remove();
    setTimeout(function(){
      $(clone).css("margin", "5px 0 5px 20px");
      $(clone).insertAfter("div#msg"+mid).slideDown();
      $("input[name=parent]").val(mid);
    }, 200);
  });
    
});
</script>
</body>
</html>