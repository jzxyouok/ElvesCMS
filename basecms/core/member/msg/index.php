<?php
require("../../class/connect.php");
require("../../class/q_functions.php");
require("../../class/db_sql.php");
require("../class/user.php");
require "../".LoadLang("pub/fun.php");
$link=db_connect();
$Elves=new mysqlquery();
$editor=1;
eCheckCloseMods('member');//关闭模块
$user=islogin();
$start=0;
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$add='';
$search='';
$line=20;//每行显示
$page_line=16;
$offset=$page*$line;
$totalquery="select count(*) as total from {$dbtbpre}melveqmsg where to_username='$user[username]'";
$query="select mid,title,haveread,from_userid,from_username,isadmin,msgtime,issys from {$dbtbpre}melveqmsg where to_username='$user[username]'";
$num=$Elves->gettotal($totalquery);
$query.=" order by mid desc limit $offset,$line";
$sql=$Elves->query($query);
$returnpage=page1($num,$line,$page_line,$start,$page,$search);
//导入模板
require(elve_PATH.'core/template/member/msg.php');
db_close();
$Elves=null;
?>