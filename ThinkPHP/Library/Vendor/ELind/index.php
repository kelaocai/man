<?php 
include("print.class.php");
$print = new Yprint();
$content = "测试测试";
$apiKey = "xxxxxx";
$msign = 'xxxxxx';
//打印
$print->action_print(407,'4004508952',$content,$apiKey,$msign);
//添加打印机
//$print->action_addprint(407,'4004508952','wajuka','k2s测试','18111111111',$apikEY,$msign);
//删除打印机
//$print->action_removeprinter(407,'4004508952',$apiKey,$msign);
 ?>