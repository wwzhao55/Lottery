<?php
require 'Api/smarty/libs/Smarty.class.php';
$smarty = new Smarty;

$smarty->assign("title", "hello, zichi");
$smarty->assign("name", "hello world");

$contect = $smarty->fetch("resource/h5/indexTpl.html");  
$fp = fopen("resource/h5/index.html", "w");  
fwrite($fp, $contect);  
fclose($fp);
?>