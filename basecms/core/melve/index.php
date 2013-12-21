<?php
require("../class/connect.php");
require("../class/db_sql.php");
require("../data/dbcache/class.php");
require("../member/class/user.php");
require("../data/dbcache/MemberLevel.php");
require LoadLang("pub/fun.php");
$link=db_connect();
$Elves=new mysqlquery();
$melve=$_POST['melve'];
if(empty($melve))
{
	$melve=$_GET['melve'];
}
//导入文件
if($melve=='AddVote'||$melve=='AddInfoVote'||$melve=='AddInfoPfen')
{
	include('votefun.php');
}
elseif($melve=='AddGbook')
{
	include('gbookfun.php');
}
else
{
	include('../class/q_functions.php');
}
if($melve=='AddVote')//增加投票
{
	if($_GET['voteid'])
	{
		$voteid=$_GET['voteid'];
		$vote=$_GET['vote'];
	}
	else
	{
		$voteid=$_POST['voteid'];
		$vote=$_POST['vote'];
	}
	AddVote($voteid,$vote);
}
elseif($melve=='AddInfoVote')//增加信息投票
{
	if($_GET['id'])
	{
		$id=$_GET['id'];
		$classid=$_GET['classid'];
		$vote=$_GET['vote'];
	}
	else
	{
		$id=$_POST['id'];
		$classid=$_POST['classid'];
		$vote=$_POST['vote'];
	}
	AddInfoVote($classid,$id,$vote);
}
elseif($melve=='AddInfoPfen')//信息评分
{
	if($_GET['id'])
	{
		$add=$_GET;
	}
	else
	{
		$add=$_POST;
	}
	AddInfoPfen($add);
}
elseif($melve=="AddGbook")//增加留言
{
	AddGbook($_POST);
}
elseif($melve=="AddFeedback")//增加反馈
{
	$doetran=1;
	AddFeedback($_POST);
}
elseif($melve=="AddError")//增加错误报告
{
	AddError($_POST);
}
else
{printerror("ErrorUrl","history.go(-1)",1);}
db_close();
$Elves=null;
?>