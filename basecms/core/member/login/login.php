<?php
require('../../class/connect.php');
require("../../class/db_sql.php");
require('../../member/class/user.php');
$link=db_connect();
$Elves=new mysqlquery();
$editor=1;
eCheckCloseMods('member');//关闭模块
if($elve_config['member']['loginurl'])
{
	echo"<script>window.close();</script>";
	//Header("Location:".$elve_config['member']['loginurl']);
	exit();
}
//导入模板
require(elve_PATH.'core/template/member/loginopen.php');
db_close();
$Elves=null;
?>