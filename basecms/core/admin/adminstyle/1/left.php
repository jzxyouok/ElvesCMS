<?php
define('ElvesCMSAdmin','1');
require("../../../class/connect.php");
require("../../../class/db_sql.php");
require("../../../class/functions.php");
$link=db_connect();
$Elves=new mysqlquery();
$editor=2;
//验证用户
$lur=is_login();
$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];
$r=ReturnLeftLevel($loginlevel);
$movecolor=" onMouseOver=\"this.style.backgroundColor='#EFEFEF'\" onMouseOut=\"this.style.backgroundColor='#FFFFFF'\"";
$gid=(int)$_GET['gid'];
//参数设置
$display="";
if($display=="")
{
$addimg="images/noadd.gif";
}
else
{
$addimg="images/add.gif";
}
$elve=RepPostVar(RepPathStr($_GET['elve']));
$menus=',system,classdata,template,usercp,tool,extend,other,fastmenu,';
$showmenu='menu/'.$elve.'.php';
if($elve&&strstr($menus,','.$elve.',')&&file_exists($showmenu))
{
	@include($showmenu);
}
db_close();
$Elves=null;
?>