<?php
if(!defined('InElvesCMS'))
{
  exit();
}

//--------------- EVLES界面参数 ---------------

//会员界面附件地址前缀
$memberskinurl=$public_r['newsurl'].'skin/member/images/';

//LOGO图片地址
$logoimgurl=$memberskinurl.'logo.jpg';

//加减号图片地址
$addimgurl=$memberskinurl.'add.gif';
$noaddimgurl=$memberskinurl.'noadd.gif';

//上下横线背景色
$bgcolor_line='#666';

//其它色调可修改CSS部分

//--------------- 界面参数 ---------------


//识别并显示当前菜单
function elveShowThisMemberMenu(){
  global $memberskinurl,$noaddimgurl;
  $selffile=eReturnSelfPage(0);
  if(stristr($selffile,'/member/msg'))
  {
    $menuname='menumsg';
  }
  elseif(stristr($selffile,'core/DoInfo'))
  {
    $menuname='menuinfo';
  }
  elseif(stristr($selffile,'/member/mspace'))
  {
    $menuname='menuspace';
  }
  elseif(stristr($selffile,'core/ShopSys'))
  {
    $menuname='menushop';
  }
  elseif(stristr($selffile,'core/payapi')||stristr($selffile,'/member/buygroup')||stristr($selffile,'/member/card')||stristr($selffile,'/member/buybak')||stristr($selffile,'/member/downbak'))
  {
    $menuname='menupay';
  }
  else
  {
    $menuname='menumember';
  }
  echo'<script>turnit(do'.$menuname.',"'.$menuname.'img");</script>';
  ?>
  <script>
  do<?=$menuname?>.style.display="";
  document.images.<?=$menuname?>img.src="<?=$noaddimgurl?>";
  </script>
  <?php
}

//网页标题
$thispagetitle=$public_diyr['pagetitle']?$public_diyr['pagetitle']:'会员中心';
//会员信息
$tmgetuserid=(int)getcvar('mluserid');  //用户ID
$tmgetusername=RepPostVar(getcvar('mlusername')); //用户名
$tmgetgroupid=(int)getcvar('mlgroupid');  //用户组ID
$tmgetgroupname='游客';
//会员组名称
if($tmgetgroupid)
{
  $tmgetgroupname=$level_r[$tmgetgroupid]['groupname'];
  if(!$tmgetgroupname)
  {
    include_once(elve_PATH.'core/data/dbcache/MemberLevel.php');
    $tmgetgroupname=$level_r[$tmgetgroupid]['groupname'];
  }
}

//模型
$tgetmid=(int)$_GET['mid'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=$thispagetitle?></title>
<link href="<?=$public_r['newsurl']?>skin/member/style.css" rel="stylesheet">

<SCRIPT lanuage="JScript">
function DisplayImg(ss,imgname,mgxe)
{
  if(imgname=="menumemberimg")
  {
    img=todisplay(domenumember,mgxe);
    document.images.menumemberimg.src=img;
  }
  else if(imgname=="menumsgimg")
  {
    img=todisplay(domenumsg,mgxe);
    document.images.menumsgimg.src=img;
  }
  else if(imgname=="menuinfoimg")
  {
    img=todisplay(domenuinfo,mgxe);
    document.images.menuinfoimg.src=img;
  }
  else if(imgname=="menuspaceimg")
  {
    img=todisplay(domenuspace,mgxe);
    document.images.menuspaceimg.src=img;
  }
  else if(imgname=="menupayimg")
  {
    img=todisplay(domenupay,mgxe);
    document.images.menupayimg.src=img;
  }
  else if(imgname=="menushopimg")
  {
    img=todisplay(domenushop,mgxe);
    document.images.menushopimg.src=img;
  }
  else
  {
  }
  DisplayAllMenu(imgname);
}
function todisplay(ss,mgxe)
{
  if(ss.style.display=="") 
  {
      ss.style.display="none";
    theimg="<?=$addimgurl?>";
  }
  else
  {
      ss.style.display="";
    theimg="<?=$noaddimgurl?>";
  }
  return theimg;
}
function turnit(ss,img)
{
  DisplayImg(ss,img,0);
}
function DisplayAllMenu(imgname)
{
  if(imgname!='menumemberimg'&&domenumember.style.display=="")
  {
    domenumember.style.display="none";
    document.images.menumemberimg.src="<?=$addimgurl?>";
  }
  if(imgname!='menumsgimg'&&domenumsg.style.display=="")
  {
    domenumsg.style.display="none";
    document.images.menumsgimg.src="<?=$addimgurl?>";
  }
  if(imgname!='menuinfoimg'&&domenuinfo.style.display=="")
  {
    domenuinfo.style.display="none";
    document.images.menuinfoimg.src="<?=$addimgurl?>";
  }
  if(imgname!='menuspaceimg'&&domenuspace.style.display=="")
  {
    domenuspace.style.display="none";
    document.images.menuspaceimg.src="<?=$addimgurl?>";
  }
  if(imgname!='menupayimg'&&domenupay.style.display=="")
  {
    domenupay.style.display="none";
    document.images.menupayimg.src="<?=$addimgurl?>";
  }
  if(imgname!='menushopimg'&&domenushop.style.display=="")
  {
    domenushop.style.display="none";
    document.images.menushopimg.src="<?=$addimgurl?>";
  }
}
</SCRIPT>
</head>

<body leftmargin="0" topmargin="0">
<div id="top">
<table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="42%"><h1>会员中心</h1></td>
    <td width="58%">
  <?php
  if($tmgetuserid)  //已登录
  {
  ?>
  <table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2">您好，<strong><?=$tmgetusername?></strong> &lt;<?=$tmgetgroupname?>&gt; </td>
      </tr>
      <tr>
        <td width="65%">[ <!--<a href="<?=$public_r['newsurl']?>core/space/?userid=<?=$tmgetuserid?>">我的空间</a> | --><a href="<?=$public_r['newsurl']?>core/member/msg/">站内消息</a> | <a href="<?=$public_r['newsurl']?>core/member/fava/">收藏夹</a> | <a href="<?=$public_r['newsurl']?>core/member/doaction.php?melve=exit" onclick="return confirm('确认要退出?');">退出</a> ]</td>
        <td width="35%"><div align="right"><a href="<?=$public_r['newsurl']?>"><u>网站首页</u></a> | <a href="<?=$public_r['newsurl']?>core/member/cp/"><u>会员中心</u></a> | <a href="<?=$public_r['newsurl']?>core/member/list/"><u>成员列表</u></a></div></td>
      </tr>
    </table>
  <?php
  }
  else  //游客
  {
  ?>
  <table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2">您好，<strong>游客</strong> &lt;游客&gt;</td>
      </tr>
      <tr>
        <td width="65%">[ <a href="<?=$public_r['newsurl']?>core/member/login/">马上登录</a> | <a href="<?=$public_r['newsurl']?>core/member/register/">注册帐号</a> ]</td>
        <td width="35%"><div align="right"><a href="<?=$public_r['newsurl']?>"><u>网站首页</u></a> | <a href="<?=$public_r['newsurl']?>core/member/cp/"><u>会员中心</u></a> | <a href="<?=$public_r['newsurl']?>core/member/list/"><u>会员列表</u></a></div></td>
      </tr>
    </table>
  <?php
  }
  ?>
    </td>
  </tr>
</table></div>

<table width="960" border="0" class="nav" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td> <?php
  if($tmgetuserid)  //已登录
  {
  ?>当前位置：<?=$url?>
  <?php }?>&nbsp;</td>
  </tr>
</table>

<table width="960" border="0" align="center" cellpadding="0" cellspacing="0" >
  <tr>
    <?php
  if($tmgetuserid)  //已登录
  {
  ?>
    <td width="20%" valign="top" class="left" bgcolor="#FFFFFF">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tableborder">
      <tr class="header" id="domenumemberid" onMouseUp="turnit(domenumember,'menumemberimg');" style="CURSOR: hand" title="展开/收缩">
        <td height="25">&nbsp;&nbsp;<img src="<?=$addimgurl?>" name="menumemberimg" width="20" height="9" border="0">帐号</td>
      </tr>
      <tbody id="domenumember" style="display:none">
        <tr>
          <td bgcolor="#FFFFFF"><table width="90%" border="0" align="right" cellpadding="0" cellspacing="0">
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/EditInfo/">修改资料</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/EditInfo/EditSafeInfo.php">修改安全信息</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/my/">帐号状态</a></td>
              </tr>
             <!-- <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/fava/">收藏夹</a></td>
              </tr>-->
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/friend/">好友列表</a></td>
              </tr>
              <!--<tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/memberconnect/ListBind.php">绑定外部登录</a></td>
              </tr>-->
          </table></td>
        </tr>
      </tbody>
      <tr class="header" id="domenumsgid" onMouseUp="turnit(domenumsg,'menumsgimg');" style="CURSOR: hand" title="展开/收缩">
        <td height="25">&nbsp;&nbsp;<img src="<?=$addimgurl?>" name="menumsgimg" width="20" height="9" border="0">站内消息</td>
      </tr>
      <tbody id="domenumsg" style="display:none">
        <tr>
          <td bgcolor="#FFFFFF"><table width="90%" border="0" align="right" cellpadding="0" cellspacing="0">
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/msg/AddMsg/?melve=AddMsg">发送消息</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/msg/">消息列表</a></td>
              </tr>
          </table></td>
        </tr>
      </tbody>
      <tr class="header" id="domenuinfoid" onMouseUp="turnit(domenuinfo,'menuinfoimg');" style="CURSOR: hand" title="展开/收缩">
        <td height="25">&nbsp;&nbsp;<img src="<?=$addimgurl?>" name="menuinfoimg" width="20" height="9" border="0">功能管理</td>
      </tr>
      <tbody id="domenuinfo" style="display:none">
        <tr>
          <td bgcolor="#FFFFFF">
          <table width="90%" border="0" align="right" cellpadding="0" cellspacing="0">
      <?php
      //输出可管理的模型
      $tmodsql=$Elves->query("select mid,qmname from {$dbtbpre}melvemod where usemod=0 and showmod=0 and qenter<>'' order by myorder,mid");
      while($tmodr=$Elves->fetch($tmodsql))
      {
        $fontb="";
        $fontb1="";
        if($tmodr['mid']==$tgetmid)
        {
          $fontb="<b>";
          $fontb1="</b>";
        }
      ?>
              <tr>
                <td width="74%" height="23"><a href="<?=$public_r['newsurl']?>core/DoInfo/ListInfo.php?mid=<?=$tmodr['mid']?>"><?=$fontb?>管理<?=$tmodr[qmname]?><?=$fontb1?></a></td>
                <td width="26%"><div align="right"><a href="<?=$public_r['newsurl']?>core/DoInfo/ChangeClass.php?mid=<?=$tmodr['mid']?>"><?=$fontb?>发布<?=$fontb1?></a></div></td>
              </tr>
      <?php
      }
      ?>
          </table>
          </td>
        </tr>
      </tbody>
      <!--<tr class="header" id="domenuspaceid" onMouseUp="turnit(domenuspace,'menuspaceimg');" style="CURSOR: hand" title="展开/收缩">
        <td height="25">&nbsp;&nbsp;<img src="<?=$addimgurl?>" name="menuspaceimg" width="20" height="9" border="0">会员空间</td>
      </tr>
      <tbody id="domenuspace" style="display:none">
        <tr>
          <td bgcolor="#FFFFFF"><table width="90%" border="0" align="right" cellpadding="0" cellspacing="0">
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/space/?userid=<?=$tmgetuserid?>">预览空间</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/mspace/SetSpace.php">设置空间</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/mspace/ChangeStyle.php">选择模板</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/mspace/gbook.php">管理留言</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/mspace/feedback.php">管理反馈</a></td>
              </tr>
          </table></td>
        </tr>
      </tbody>
      <tr class="header" id="domenupayid" onMouseUp="turnit(domenupay,'menupayimg');" style="CURSOR: hand" title="展开/收缩">
        <td height="25">&nbsp;&nbsp;<img src="<?=$addimgurl?>" name="menupayimg" width="20" height="9" border="0">财务</td>
      </tr>
      <tbody id="domenupay" style="display:none">
        <tr>
          <td bgcolor="#FFFFFF"><table width="90%" border="0" align="right" cellpadding="0" cellspacing="0">
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/payapi/">在线支付</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/buygroup/">在线充值</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/card/">点卡充值</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/buybak/">点卡充值记录</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/member/downbak/">下载消费记录</a></td>
              </tr>
          </table></td>
        </tr>
      </tbody>
      <tr class="header" id="domenushopid" onMouseUp="turnit(domenushop,'menushopimg');" style="CURSOR: hand" title="展开/收缩">
        <td height="25">&nbsp;&nbsp;<img src="<?=$addimgurl?>" name="menushopimg" width="20" height="9" border="0">商城</td>
      </tr>
      <tbody id="domenushop" style="display:none">
        <tr>
          <td bgcolor="#FFFFFF"><table width="90%" border="0" align="right" cellpadding="0" cellspacing="0">
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/ShopSys/ListDd/">我的订单</a></td>
              </tr>
              <tr>
                <td height="23"><a href="#elve" onclick="window.open('<?=$public_r['newsurl']?>core/ShopSys/buycar/','','width=680,height=500,scrollbars=yes,resizable=yes');">我的购物车</a></td>
              </tr>
              <tr>
                <td height="23"><a href="<?=$public_r['newsurl']?>core/ShopSys/address/ListAddress.php">管理配送地址</a></td>
              </tr>
          </table></td>
        </tr>
      </tbody>-->
      <tr class="header">
        <td height="25">&nbsp;&nbsp;<img src="<?=$noaddimgurl?>" width="20" height="9" border="0"><a href="<?=$public_r['newsurl']?>core/member/doaction.php?melve=exit" onclick="return confirm('确认要退出?');">退出</a></td>
      </tr>
    </table>

  </td>  <?php
  }
  ?>
    <td width="80%" valign="top" class=" right">