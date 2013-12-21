<?php
//新用户投稿验证
function qCheckNewMemberAddInfo($registertime){
	global $public_r;
	if(empty($public_r['newaddinfotime']))
	{
		return '';
	}
	$registertime=eReturnMemberIntRegtime($registertime);
	if(time()-$registertime<=$public_r['newaddinfotime']*60)
	{
		printerror('NewMemberAddInfoError','',1);
	}
}

//验证同一IP发信息数
function eCheckIpAddInfoNum($ip,$tbname,$mid,$checked=1){
	global $Elves,$dbtbpre,$public_r,$emod_r;
	if(!$public_r['ipaddinfonum']||!$public_r['ipaddinfotime'])
	{
		return '';
	}
	//是否有IP字段
	$qenterf=$emod_r[$mid]['qenter'];
	if(!strstr($qenterf,',infoip,'))
	{
		return '';
	}
	$infotb=ReturnInfoMainTbname($tbname,$checked);
	//时间
	$cktime=time()-$public_r['ipaddinfotime']*3600;
	$num=$Elves->gettotal("select count(*) as total from ".$infotb." where newstime>$cktime and infoip='$ip'");
	if($num+1>$public_r['ipaddinfonum'])
	{
		printerror('IpMaxAddInfo','history.go(-1)',1);
	}
}

//屏蔽字符
function qCheckInfoCloseWord($mid,$add,$closewordsf,$closewords){
	if(empty($closewordsf)||$closewordsf=='|'||empty($closewords)||$closewords=='|')
	{
		return '';
	}
	$fr=explode('|',$closewordsf);
	$count=count($fr);
	$r=explode('|',$closewords);
	$countr=count($r);
	for($i=0;$i<$count;$i++)
	{
		if(empty($fr[$i]))
		{
			continue;
		}
		for($j=0;$j<$countr;$j++)
		{
			if($r[$j])
			{
				if(stristr($r[$j],'##'))//多字
				{
					$morer=explode('##',$r[$j]);
					if(stristr($add[$fr[$i]],$morer[0])&&stristr($add[$fr[$i]],$morer[1]))
					{
						printerror("HaveCloseWords","history.go(-1)",1);
					}
				}
				else
				{
					if(stristr($add[$fr[$i]],$r[$j]))
					{
						printerror("HaveCloseWords","history.go(-1)",1);
					}
				}
			}
		}
	}
}

//提交字段值的处理
function DoqValue($mid,$f,$val){
	global $public_r,$emod_r;
	$val=RepPhpAspJspcodeText($val);
	if(strstr($emod_r[$mid]['editorf'],','.$f.','))//编辑器
	{
		$val=ClearNewsBadCode($val);
	}
	else
	{
		$val=doehtmlstr($val);//替换html
		if(!strstr($emod_r[$mid]['tobrf'],','.$f.',')&&strstr($emod_r[$mid]['dohtmlf'],','.$f.','))//加回车
		{
			$val=doebrstr($val);
		}
	}
	return $val;
}

//过滤
function ClearNewsBadCode($text){
	$text=preg_replace(array('!<script!i','!</script>!i','!<link!i','!<iframe!i','!</iframe>!i','!<meta!i','!<body!i','!<style!i','!</style>!i'),array('&lt;script','&lt;/script&gt;','&lt;link','&lt;iframe','&lt;/iframe&gt;','&lt;meta','&lt;body','&lt;style','&lt;/style&gt;'),$text);
	return $text;
}

//返回字段值的处理
function DoReqValue($mid,$f,$val){
	global $public_r,$emod_r;
	if($emod_r[$mid]['savetxtf']&&$emod_r[$mid]['savetxtf']==$f)//存文本
	{
		$val=stripSlashes(GetTxtFieldText($val));
	}
	if(strstr($emod_r[$mid]['editorf'],','.$f.','))//编辑器
	{
		return $val;
	}
	$val=dorehtmlstr($val);//替换html
	if(!strstr($emod_r[$mid]['tobrf'],','.$f.',')&&strstr($emod_r[$mid]['dohtmlf'],','.$f.','))//加回车
	{
		$val=dorebrstr($val);
	}
	return $val;
}

//替换html代码
function doehtmlstr($str){
	$str=ehtmlspecialchars($str,ENT_QUOTES);
	return $str;
}

//还原html代码
function dorehtmlstr($str){
	return $str;
}

//替换回车
function doebrstr($str){
	$str=str_replace("\n","<br />",$str);
	return $str;
}

//还原回车
function dorebrstr($str){
	$str=str_replace("<br />","\n",$str);
	$str=str_replace("<br>","\n",$str);
	return $str;
}

//投稿生成内容页面
function qAddGetHtml($classid,$id){
	$titleurl=DoGetHtml($classid,$id);
	return $titleurl;
}

//投稿生成页面
function qAddListHtml($classid,$mid,$qaddlist,$listdt){
	global $class_r;
	if($qaddlist==0)//不生成
	{
		return "";
	}
	elseif($qaddlist==1)//生成当前栏目
	{
		if(!$listdt)
		{
			$sonclass="|".$classid."|";
			QReClassHtml($sonclass);
		}
	}
	elseif($qaddlist==2)//生成首页
	{
		QReIndex();
	}
	elseif($qaddlist==3)//生成父栏目
	{
		$featherclass=$class_r[$classid]['featherclass'];
		if($featherclass&&$featherclass!="|")
		{
			QReClassHtml($featherclass);
		}
	}
	elseif($qaddlist==4)//生成当前栏目与父栏目
	{
		$featherclass=$class_r[$classid]['featherclass'];
		if(empty($featherclass))
		{
			$featherclass="|";
		}
		if(!$listdt)
		{
			$featherclass.=$classid."|";
		}
		QReClassHtml($featherclass);
	}
	elseif($qaddlist==5)//生成父栏目与首页
	{
		QReIndex();
		$featherclass=$class_r[$classid]['featherclass'];
		if($featherclass&&$featherclass!="|")
		{
			QReClassHtml($featherclass);
		}
	}
	elseif($qaddlist==6)//生成当前栏目、父栏目与首页
	{
		QReIndex();
		$featherclass=$class_r[$classid]['featherclass'];
		if(empty($featherclass))
		{
			$featherclass="|";
		}
		if(!$listdt)
		{
			$featherclass.=$classid."|";
		}
		QReClassHtml($featherclass);
	}
}

//投稿生成栏目
function QReClassHtml($sonclass){
	global $Elves,$dbtbpre,$class_r;
	$r=explode("|",$sonclass);
	$count=count($r);
	for($i=1;$i<$count-1;$i++)
	{
		//终极栏目
		if($class_r[$r[$i]]['islast'])
		{
			if(!$class_r[$r[$i]]['listdt'])
			{
				ListHtml($r[$i],'',0,$userlistr);
			}
		}
		elseif($class_r[$r[$i]]['islist']==1)//列表式父栏目
		{
			if(!$class_r[$r[$i]]['listdt'])
			{
				ListHtml($r[$i],'',3);
			}
		}
		elseif($class_r[$r[$i]]['islist']==3)//栏目绑定信息式
		{
			ReClassBdInfo($r[$i]);
		}
		else//父栏目
		{
			$cr=$Elves->fetch1("select classtempid from {$dbtbpre}melveclass where classid='$r[$i]'");
			$classtemp=$class_r[$r[$i]]['islist']==2?GetClassText($r[$i]):GetClassTemp($cr['classtempid']);
			NewsBq($r[$i],$classtemp,0,0);
		}
	}
}

//投稿生成首页
function QReIndex(){
	$indextemp=GetIndextemp();
	NewsBq($classid,$indextemp,1,0);
}

//验证权限
function CheckQdoinfo($classid,$id,$userid,$tbname,$adminqinfo,$elve=0){
	global $Elves,$dbtbpre,$emod_r,$class_r;
	//索引表
	$index_r=$Elves->fetch1("select id,classid,checked from {$dbtbpre}elve_".$tbname."_index where id='$id' limit 1");
	if(!$index_r['id']||$index_r['classid']!=$classid)
	{
		printerror("HaveNotLevelQInfo","history.go(-1)",1);
	}
	//返回表
	$infotb=ReturnInfoMainTbname($tbname,$index_r['checked']);
	$r=$Elves->fetch1("select * from ".$infotb." where id='$id' and classid='$classid' and ismember=1 and userid='$userid' limit 1");
	if(!$r['id'])
	{
		printerror("HaveNotLevelQInfo","history.go(-1)",1);
	}
	$r['checked']=$index_r['checked'];
	if($adminqinfo==1)//管理未审核信息
	{
		if($index_r['checked'])
		{
			printerror("ClassSetNotAdminQCInfo","history.go(-1)",1);
		}
	}
	elseif($adminqinfo==2)//只可编辑未审核信息
	{
		if($index_r['checked']||$elve!=1)
		{
			printerror("ClassSetNotEditQCInfo","history.go(-1)",1);
		}
	}
	elseif($adminqinfo==3)//只可删除未审核信息
	{
		if($index_r['checked']||$elve!=2)
		{
			printerror("ClassSetNotDelQCInfo","history.go(-1)",1);
		}
	}
	elseif($adminqinfo==4)//管理所有信息
	{}
	elseif($adminqinfo==5)//只可编辑所有信息
	{
		if($elve!=1)
		{
			printerror("ClassSetNotEditQInfo","history.go(-1)",1);
		}
	}
	elseif($adminqinfo==6)//只可删除所有信息
	{
		if($elve!=2)
		{
			printerror("ClassSetNotDelQInfo","history.go(-1)",1);
		}
	}
	else//不能管理投稿
	{
		printerror("ClassSetNotAdminQInfo","history.go(-1)",1);
	}
	//返回表信息
	$infotbr=ReturnInfoTbname($tbname,$index_r['checked'],$r['stb']);
	//副表
	$mid=$class_r[$classid]['modid'];
	$finfor=$Elves->fetch1("select ".ReturnSqlFtextF($mid)." from ".$infotbr['datatbname']." where id='$r[id]' limit 1");
	$r=array_merge($r,$finfor);
	return $r;
}

//组合下载/影视
function DoqReturnDownPath($path,$elve=0){
	global $fun_r;
	$downqz="";
	$fen=0;
	$fuser=0;
	$f_exp="::::::";
	$r_exp="\r\n";
	$returnstr="";
	$path=str_replace($f_exp,"",$path);
	$path=str_replace($r_exp,"",$path);
	if($elve==0)
	{
		$name=$fun_r['DownPath']."1";
	}
	else
	{
		$name="1";
	}
	if($path)
	{
		$returnstr=$name.$f_exp.$path.$f_exp.$fuser.$f_exp.$fen.$f_exp.$downqz.$r_exp;
	}
	//去掉最后的字符
	$returnstr=substr($returnstr,0,strlen($returnstr)-2);
	return $returnstr;
}

//返回下载/影视地址
function DoReqDownPath($downpath){
	if(empty($downpath))
	{
		return "";
	}
	$f_exp="::::::";
	$r_exp="\r\n";
	$r=explode($r_exp,$downpath);
	$r1=explode($f_exp,$r[0]);
	return $r1[1];
}

//特殊字段处理
function DoqSpecialValue($mid,$f,$value,$add,$infor,$elve=0){
	global $public_r,$loginin,$emod_r;
	if($f=="morepic")//图片集
	{
		$add['msavepic']=0;
		$value=ReturnMorepicpath($add['msmallpic'],$add['mbigpic'],$add['mpicname'],$add['mdelpicid'],$add['mpicid'],$add,$add['mpicurl_qz'],$elve,0,($elve==1?$infor['fstb']:$public_r['filedeftb']));
		$value=doehtmlstr($value);
	}
	elseif($f=="downpath")//下载地址
	{
		$value=DoqReturnDownPath($value,0);
		$value=doehtmlstr($value);
	}
	elseif($f=="onlinepath")//在线地址
	{
		$value=DoqReturnDownPath($value,1);
		$value=doehtmlstr($value);
	}
	elseif($f=="newstext")//内容
	{
		//远程保存
		//$value=addslashes(CopyImg(stripSlashes($value),$add[copyimg],$add[copyflash],$add[classid],$add[qz_url],$loginin,$add['id'],$add['filepass'],$add['mark'],($elve==1?$infor['fstb']:$public_r['filedeftb'])));
	}
	//存文本
	if($emod_r[$mid]['savetxtf']&&$f==$emod_r[$mid]['savetxtf'])
	{
		if($elve==1)
		{
			//建立目录
			$newstexttxt_r=explode("/",$infor[$f]);
			$thetxtfile=$newstexttxt_r[2];
			$truevalue=MkDirTxtFile($newstexttxt_r[0]."/".$newstexttxt_r[1],$thetxtfile);
		}
		else
		{
			//建立目录
			$thetxtfile=GetFileMd5();
			$truevalue=MkDirTxtFile(date("Y/md"),$thetxtfile);
		}
		//写放文件
		EditTxtFieldText($truevalue,$value);
		$value=$truevalue;
	}
	return $value;
}

//检测点数是否足够
function MCheckEnoughFen($userfen,$userdate,$fen){
	if(!($userdate-time()>0))
	{
		if($userfen+$fen<0)
		{
			printerror("HaveNotFenAQinfo","history.go(-1)",1);
		}
	}
}

//返回字段
function ReturnQAddinfoF($mid,$add,$infor,$classid,$filepass,$userid,$username,$elve=0){
	global $Elves,$dbtbpre,$public_r,$emod_r,$elve_config;
	$pr=$Elves->fetch1("select qaddtran,qaddtransize,qaddtranimgtype,qaddtranfile,qaddtranfilesize,qaddtranfiletype,closewords,closewordsf from {$dbtbpre}melvepublic limit 1");
	$isadd=$elve==0?1:0;
	qCheckInfoCloseWord($mid,$add,$pr['closewordsf'],$pr['closewords']);//屏蔽字符验证
	//检测必填字段
	$pagef=$emod_r[$mid]['pagef'];
	$mustr=explode(",",$emod_r[$mid]['mustqenterf']);
	$mustcount=count($mustr)-1;
	for($i=1;$i<$mustcount;$i++)
	{
		$mf=$mustr[$i];
		if(strstr($emod_r[$mid]['filef'],','.$mf.',')||strstr($emod_r[$mid]['imgf'],','.$mf.',')||strstr($emod_r[$mid]['flashf'],','.$mf.',')||$mf=='downpath'||$mf=='onlinepath')//附件
		{
			$mfilef=$mf."file";
			//上传文件
			if($_FILES[$mfilef]['name'])
			{
				if(strstr($emod_r[$mid]['imgf'],','.$mf.','))//图片
				{
					if(!$pr['qaddtran'])
					{
						printerror("CloseQTranPic","",1);
					}
				}
				else//附件
				{
					if(!$pr['qaddtranfile'])
					{
						printerror("CloseQTranFile","",1);
					}
				}
			}
			elseif(!trim($add[$mf])&&!$infor[$mf])
			{
				printerror("EmptyQMustF","",1);
			}
		}
		else
		{
			$chmustval=ReturnCheckboxAddF($add[$mf],$mid,$mf);//复选框
			$chmustval=ReturnMoreValueAddF($add,$chmustval,$mid,$mf,$elve);//多值
			if(!trim($chmustval))
			{
				printerror("EmptyQMustF","",1);
			}
		}
	}
	//字段处理
	$dh="";
	$tranf="";
	$fr=explode(',',$emod_r[$mid]['qenter']);
	$count=count($fr)-1;
	for($i=1;$i<$count;$i++)
	{
		$f=$fr[$i];
		if($f=='special.field'||($elve==0&&!strstr($emod_r[$mid]['canaddf'],','.$f.','))||($elve==1&&!strstr($emod_r[$mid]['caneditf'],','.$f.',')))
		{continue;}
		//附件
		$add[$f]=str_replace('[!#@-','elve',$add[$f]);
		if(strstr($emod_r[$mid]['filef'],','.$f.',')||strstr($emod_r[$mid]['imgf'],','.$f.',')||strstr($emod_r[$mid]['flashf'],','.$f.',')||$f=='downpath'||$f=='onlinepath')
		{
			//上传附件
			$filetf=$f."file";
			if($_FILES[$filetf]['name'])
			{
				$filetype=GetFiletype($_FILES[$filetf]['name']);//取得文件类型
				if(CheckSaveTranFiletype($filetype))
				{
					printerror("NotQTranFiletype","",1);
				}
				if(strstr($emod_r[$mid]['imgf'],','.$f.','))//图片
				{
					if(!$pr['qaddtran'])
					{
						printerror("CloseQTranPic","",1);
					}
					if(!strstr($pr['qaddtranimgtype'],"|".$filetype."|"))
					{
						printerror("NotQTranFiletype","",1);
					}
					if($_FILES[$filetf]['size']>$pr['qaddtransize']*1024)
					{
						printerror("TooBigQTranFile","",1);
					}
					if(!strstr($elve_config['sets']['tranpicturetype'],','.$filetype.','))
					{
						printerror("NotQTranFiletype","",1);
					}
				}
				else//附件
				{
					if(!$pr['qaddtranfile'])
					{
						printerror("CloseQTranFile","",1);
					}
					if(!strstr($pr['qaddtranfiletype'],"|".$filetype."|"))
					{
						printerror("NotQTranFiletype","",1);
					}
					if($_FILES[$filetf]['size']>$pr['qaddtranfilesize']*1024)
					{
						printerror("TooBigQTranFile","",1);
					}
					if(strstr($emod_r[$mid]['flashf'],','.$f.','))//flash
					{
						if(!strstr($elve_config['sets']['tranflashtype'],",".$filetype.","))
						{printerror("NotQTranFiletype","",1);}
					}
					if($f=="onlinepath")//视频
					{
						if(strstr($wmv_type,",".$filetype.","))
						{}
					}
				}
				$tranf.=$dh.$f;
				$dh=",";
				$fval="[!#@-".$f."-@!]";
			}
			else
			{
				$fval=$add[$f];
				if($elve==1&&$infor[$f]&&!trim($fval))
				{
					$fval=$infor[$f];
					//特殊字段
					if($f=="downpath"||$f=="onlinepath")
					{
						$fval=DoReqDownPath($fval);
					}
				}
			}
		}
		elseif($f=='newstime')//时间
		{
			if($add[$f])
			{
				$fval=to_time($add[$f]);
			}
			else
			{
				$fval=time();
			}
		}
		elseif($f=='newstext')//内容
		{
			if($elve==0)
			{
				$fval=DoReplaceKeyAndWord($add[$f],1,$classid);//替换关键字和字符
			}
			else
			{
				$fval=$add[$f];
			}
		}
		elseif($f=='infoip')	//ip
		{
			$fval=egetip();
		}
		elseif($f=='infozm')	//字母
		{
			$fval=$add[$f]?$add[$f]:GetInfoZm($add[title]);
		}
		else
		{
			$add[$f]=ReturnCheckboxAddF($add[$f],$mid,$f);//复选框
			$add[$f]=ReturnMoreValueAddF($add,$add[$f],$mid,$f,$elve);//多值
			$fval=$add[$f];
		}
		$fval=DoFFun($mid,$f,$fval,$isadd,1);//执行函数
		if($pagef!=$f)
		{
			$fval=RepTempvarPostStr($fval);
		}
		ChIsOnlyAddF($mid,$infor[id],$f,$fval,1);//唯一值
		$fval=DoqValue($mid,$f,$fval);
		$fval=DoqSpecialValue($mid,$f,$fval,$add,$infor,$elve);
		$fval=RepPostStr2($fval);
		if($elve==1)
		{
			SameDataAddF($info[id],$classid,$mid,$f,$fval);
		}
		$fval=addslashes($fval);
		if($elve==0)//添加
		{
			if(strstr($emod_r[$mid]['tbdataf'],','.$f.','))//副表
			{
				$ret_r[2].=",".$f;
				$ret_r[3].=",'".$fval."'";
			}
			else
			{
				$ret_r[0].=",".$f;
				$ret_r[1].=",'".$fval."'";
			}
		}
		else//编辑
		{
			if($f=='infoip')	//ip
			{
				continue;
			}
			if(strstr($emod_r[$mid]['tbdataf'],','.$f.','))//副表
			{
				$ret_r[3].=",".$f."='".$fval."'";
			}
			else
			{
				$ret_r[0].=",".$f."='".$fval."'";
			}
		}
	}
	//上传附件
	if($tranf)
	{
		if($elve==0)
		{
			$infoid=0;
		}
		else
		{
			$infoid=$infor['id'];
			$filepass=0;
		}
		$tranr=explode(",",$tranf);
		$count=count($tranr);
		for($i=0;$i<$count;$i++)
		{
			$tf=$tranr[$i];
			$tffile=$tf."file";
			$tfr=DoTranFile($_FILES[$tffile]['tmp_name'],$_FILES[$tffile]['name'],$_FILES[$tffile]['type'],$_FILES[$tffile]['size'],$classid);
			if($tfr['tran'])
			{
				//文件类型
				$mvf=$tf."mtfile";
				if(strstr($emod_r[$mid]['imgf'],','.$tf.','))//图片
				{
					$type=1;
				}
				elseif(strstr($emod_r[$mid]['flashf'],','.$tf.','))//flash
				{
					$type=2;
				}
				elseif($add[$mvf]==1)//多媒体
				{
					$type=3;
				}
				else//附件
				{
					$type=0;
				}
				//写入数据库
				$filetime=time();
				$filesize=(int)$_FILES[$tffile]['size'];
				$classid=(int)$classid;
				eInsertFileTable($tfr[filename],$filesize,$tfr[filepath],'[Member]'.$username,$classid,'['.$tf.']'.addslashes(RepPostStr($add[title])),$type,$infoid,$filepass,$public_r[fpath],0,0,($elve==1?$infor['fstb']:$public_r['filedeftb']));
				//删除旧文件
				if($elve==1&&$infor[$tf])
				{
					DelYQTranFile($classid,$infor['id'],$infor[$tf],$tf,$infor['fstb']);
				}
				$repfval=$tfr['url'];
			}
			else
			{
				$repfval=$infor[$tf];
				//特殊字段
				if($tf=="downpath"||$tf=="onlinepath")
				{
					$repfval=DoReqDownPath($repfval);
				}
			}
			if($elve==0)//添加
			{
				$ret_r[1]=str_replace("[!#@-".$tf."-@!]",$repfval,$ret_r[1]);
				$ret_r[3]=str_replace("[!#@-".$tf."-@!]",$repfval,$ret_r[3]);
			}
			else//编辑
			{
				$ret_r[0]=str_replace("[!#@-".$tf."-@!]",$repfval,$ret_r[0]);
				$ret_r[3]=str_replace("[!#@-".$tf."-@!]",$repfval,$ret_r[3]);
			}
		}
	}
	$ret_r[4]=$emod_r[$mid]['deftb'];
	return $ret_r;
}

//删除原附件
function DelYQTranFile($classid,$id,$file,$tf,$fstb='1'){
	global $Elves,$dbtbpre;
	//特殊字段
	if($tf=="downpath"||$tf=="onlinepath")
	{
		$file=DoReqDownPath($file);
	}
	if(empty($file))
	{
		return "";
	}
	$r=explode("/",$file);
	$count=count($r);
	$filename=$r[$count-1];
	$fr=$Elves->fetch1("select filename,path,fileid,fpath,classid from {$dbtbpre}melvefile_{$fstb} where classid='$classid' and id='$id' and filename='$filename' limit 1");
	if($fr['fileid'])
	{
		$sql=$Elves->query("delete from {$dbtbpre}melvefile_{$fstb} where fileid='$fr[fileid]'");
		DoDelFile($fr);
	}
}

//信息投稿
function DodoInfo($add,$elve=0){
	global $Elves,$public_r,$emod_r,$level_r,$class_r,$dbtbpre,$fun_r;
	//验证来源
	if($elve==0||$elve==1)
	{
		CheckCanPostUrl();
	}
	//开启投稿
	if($public_r['addnews_ok'])
	{
		printerror("CloseQAdd","",1);
	}
	//验证本时间允许操作
	eCheckTimeCloseDo('info');
	$classid=(int)$add['classid'];
	$mid=(int)$class_r[$classid]['modid'];
	if(!$mid||!$classid)
	{
		printerror("EmptyQinfoCid","",1);
	}
	$tbname=$emod_r[$mid]['tbname'];
	$qenter=$emod_r[$mid]['qenter'];
	if(!$tbname||!$qenter||$qenter==',')
	{
		printerror("ErrorUrl","history.go(-1)",1);
	}
	$muserid=(int)getcvar('mluserid');
	$musername=RepPostVar(getcvar('mlusername'));
	$mrnd=RepPostVar(getcvar('mlrnd'));
	//取得栏目信息
	$isadd=0;
	if($elve==0)
	{
		$isadd=1;
	}
	$setuserday='';
	$cr=DoQCheckAddLevel($classid,$muserid,$musername,$mrnd,$elve,$isadd);
	$setuserday=$cr['checkaddnumquery'];
	$filepass=(int)$add['filepass'];
	$id=(int)$add['id'];
	//组合标题属性
	$titlecolor=RepPostStr(RepPhpAspJspcodeText($add[titlecolor]));
	$titlefont=TitleFont($add[titlefont],$titlecolor);
	$titlecolor="";
	$titlefont="";
	$ttid=(int)$add['ttid'];
	$keyboard=addslashes(RepPostStr(trim(DoReplaceQjDh($add[keyboard]))));
	//返回关键字组合
	if($keyboard&&strstr($qenter,',special.field,'))
	{
		$keyboard=str_replace('[!--f--!]','elve',$keyboard);
		$keyid=GetKeyid($keyboard,$classid,$id,$class_r[$classid][link_num]);
	}
	//验证码
	$keyvname='checkinfokey';
	//-----------------增加
	if($elve==0)
	{
		//时间
		$lasttime=getcvar('lastaddinfotime');
		if($lasttime)
		{
			if(time()-$lasttime<$public_r['readdinfotime'])
			{
				printerror("QAddInfoOutTime","",1);
			}
		}
		//验证码
		if($cr['qaddshowkey'])
		{
			elveCheckShowKey($keyvname,$add['key'],1);
		}
		//IP发布数限制
		$check_ip=egetip();
		$check_checked=$cr['wfid']?0:$cr['checkqadd'];
		eCheckIpAddInfoNum($check_ip,$tbname,$mid,$check_checked);
		//返回字段
		$ret_r=ReturnQAddinfoF($mid,$add,$infor,$classid,$filepass,$muserid,$musername,0);
		$checked=$cr['checkqadd'];
		$havehtml=0;
		$newspath=date($cr['newspath']);
		$truetime=time();
		$newstime=$truetime;
		$newstempid=$cr['newstempid'];
		$haveaddfen=0;
		//强制签发
		$isqf=0;
		if($cr['wfid'])
		{
			$checked=0;
			$isqf=1;
		}
		//增扣点
		if($checked&&$muserid)
		{
			AddInfoFen($cr['addinfofen'],$muserid);
			$haveaddfen=1;
		}
		if(empty($muserid))
		{
			$musername=$fun_r['guest'];
		}
		//会员投稿数更新
		if($setuserday)
		{
			$Elves->query($setuserday);
		}
		//发布时间
		if(!strstr($qenter,',newstime,'))
		{
			$ret_r[0]=",newstime".$ret_r[0];
			$ret_r[1]=",'$newstime'".$ret_r[1];
		}
		else
		{
			if($add['newstime'])
			{
				$newstime=to_time($add['newstime']);
				$newstime=intval($newstime);
			}
		}
		//附加链接参数
		$addelvecheck=empty($checked)?'&elvecheck=1':'';
		//索引表
		$indexsql=$Elves->query("insert into {$dbtbpre}elve_".$tbname."_index(classid,checked,newstime,truetime,lastdotime,havehtml) values('$classid','$checked','$newstime','$truetime','$truetime','$havehtml');");
		$id=$Elves->lastid();
		//返回表信息
		$infotbr=ReturnInfoTbname($tbname,$checked,$ret_r[4]);
		//主表
		$sql=$Elves->query("insert into ".$infotbr['tbname']."(id,classid,ttid,onclick,plnum,totaldown,newspath,filename,userid,username,firsttitle,isgood,istop,isqf,ismember,isurl,truetime,lastdotime,havehtml,groupid,userfen,titlefont,titleurl,stb,fstb,restb,keyboard".$ret_r[0].") values('$id','$classid','$ttid',0,0,0,'$newspath','','".$muserid."','".addslashes($musername)."',0,0,0,'$isqf',1,0,'$truetime','$truetime','$havehtml',0,0,'$titlefont','','$ret_r[4]','$public_r[filedeftb]','$public_r[pldeftb]','$keyboard'".$ret_r[1].");");
		//副表
		$fsql=$Elves->query("insert into ".$infotbr['datatbname']."(id,classid,keyid,dokey,newstempid,closepl,haveaddfen,infotags".$ret_r[2].") values('$id','$classid','$keyid',1,'$newstempid',0,'$haveaddfen',''".$ret_r[3].");");
		//扣点记录
		if($haveaddfen)
		{
			if($cr['addinfofen']<0)
			{
				BakDown($classid,$id,0,$muserid,$musername,RepPostStr($add[title]),abs($cr['addinfofen']),3);
			}
		}
		//签发
		if($isqf==1)
		{
			InfoInsertToWorkflow($id,$classid,$cr['wfid'],$muserid,addslashes($musername));
		}
		//文件命名
		$filename=ReturnInfoFilename($classid,$id,'');
		//信息地址
		$infourl=GotoGetTitleUrl($classid,$id,$newspath,$filename,0,0,'');
		$usql=$Elves->query("update ".$infotbr['tbname']." set filename='$filename',titleurl='$infourl' where id='$id'");
		//修改ispic
		UpdateTheIspic($classid,$id,$checked);
		//修改附件
		if($filepass)
		{
			UpdateTheFile($id,$filepass,$classid,$public_r['filedeftb']);
		}
		//更新栏目信息数
		AddClassInfos($classid,'+1','+1',$checked);
		//更新新信息数
		DoUpdateAddDataNum('info',$class_r[$classid]['tid'],1);
		//清除验证码
		elveEmptyShowKey($keyvname);
		esetcookie("qeditinfo","",0);
		//生成页面
		if($checked&&!$cr['showdt'])
		{
			$titleurl=qAddGetHtml($classid,$id);
		}
		//生成列表
		if($checked)
		{
			qAddListHtml($classid,$mid,$cr['qaddlist'],$cr['listdt']);
			//生成上一篇
			if($cr['repreinfo'])
			{
				$prer=$Elves->fetch1("select * from {$dbtbpre}elve_".$tbname." where id<$id and classid='$classid' order by id desc limit 1");
				GetHtml($prer['classid'],$prer['id'],$prer,1);
			}
		}
		if($sql)
		{
			$reurl=DoingReturnUrl("AddInfo.php?classid=$classid&mid=$mid".$addelvecheck,$add['elvefrom']);
			if($add['gotoinfourl']&&$checked)//返回内容页
			{
				if($cr['showdt']==1)
				{
					$reurl=$public_r[newsurl]."core/action/ShowInfo/?classid=$classid&id=$id";
				}
				elseif($cr['showdt']==2)
				{
					$rewriter=eReturnRewriteInfoUrl($classid,$id,1);
					$reurl=$rewriter['pageurl'];
				}
				else
				{
					$reurl=$titleurl;
				}
			}
			esetcookie("lastaddinfotime",time(),time()+3600*24);//设置最后发表时间
			printerror("AddQinfoSuccess",$reurl,1);
		}
		else
		{printerror("DbError","history.go(-1)",1);}
	}
	//---------------修改
	elseif($elve==1)
	{
		if(!$id)
		{
			printerror("ErrorUrl","history.go(-1)",1);
		}
		//检测权限
		$infor=CheckQdoinfo($classid,$id,$muserid,$tbname,$cr['adminqinfo'],1);
		//检测时间
		if($public_r['qeditinfotime'])
		{
			if(time()-$infor['truetime']>$public_r['qeditinfotime']*60)
			{
				printerror("QEditInfoOutTime","history.go(-1)",1);
			}
		}
		$iaddfield='';
		$addfield='';
		$faddfield='';
		//返回字段
		$ret_r=ReturnQAddinfoF($mid,$add,$infor,$classid,$filepass,$muserid,$musername,1);
		if($keyboard)
		{
			$addfield=",keyboard='$keyboard'";
			$faddfield=",keyid='$keyid'";
		}
		//时间
		if(strstr($qenter,',newstime,'))
		{
			if($add['newstime'])
			{
				$newstime=to_time($add['newstime']);
				$newstime=intval($newstime);
				$iaddfield.=",newstime='$newstime'";
			}
		}
		//修改是否需要审核
		$ychecked=$infor['checked'];
		if($cr['qeditchecked'])
		{
			$infor['checked']=0;
			$iaddfield.=",checked=0";
			$relist=1;
			//删除原页面
			DelNewsFile($infor[filename],$infor[newspath],$infor[classid],$infor[newstext],$infor[groupid]);
		}
		//会员投稿数更新
		if($setuserday)
		{
			//$Elves->query($setuserday);
		}
		$lastdotime=time();
		//附加链接参数
		$addelvecheck=empty($infor['checked'])?'&elvecheck=1':'';
		//索引表
		$indexsql=$Elves->query("update {$dbtbpre}elve_".$tbname."_index set lastdotime=$lastdotime,havehtml=0".$iaddfield." where id='$id'");
		//返回表信息
		$infotbr=ReturnInfoTbname($tbname,$ychecked,$infor['stb']);
		//主表
		$sql=$Elves->query("update ".$infotbr['tbname']." set lastdotime=$lastdotime,havehtml=0,ttid='$ttid'".$addfield.$ret_r[0]." where id=$id and classid=$classid and userid='$muserid' and ismember=1");
		//副表
		$fsql=$Elves->query("update ".$infotbr['datatbname']." set classid='$classid'".$faddfield.$ret_r[3]." where id='$id'");
		//修改ispic
		UpdateTheIspic($classid,$id,$ychecked);
		//更新附件
		UpdateTheFileEdit($classid,$id,$infor['fstb']);
		//未审核信息互转
		if($ychecked!=$infor['checked'])
		{
			MoveCheckInfoData($tbname,$ychecked,$infor['stb'],"id='$id'");
			//更新栏目信息数
			if($infor['checked'])
			{
				AddClassInfos($classid,'','+1');
			}
			else
			{
				AddClassInfos($classid,'','-1');
			}
		}
		esetcookie("qeditinfo","",0);
		//生成页面
		if($infor['checked']&&!$cr['showdt'])
		{
			$titleurl=qAddGetHtml($classid,$id);
		}
		//生成列表
		if($infor['checked']||$relist==1)
		{
			qAddListHtml($classid,$mid,$cr['qaddlist'],$cr['listdt']);
		}
		//生成上一篇
		if($cr['repreinfo']&&$infor['checked'])
		{
			$prer=$Elves->fetch1("select * from {$dbtbpre}elve_".$tbname." where id<$id and classid='$classid' order by id desc limit 1");
			GetHtml($prer['classid'],$prer['id'],$prer,1);
		}
		if($sql)
		{
			$reurl=DoingReturnUrl("ListInfo.php?mid=$mid".$addelvecheck,$add['elvefrom']);
			if($add['editgotoinfourl']&&$infor['checked'])//返回内容页
			{
				if($cr['showdt']==1)
				{
					$reurl=$public_r[newsurl]."core/action/ShowInfo/?classid=$classid&id=$id";
				}
				elseif($cr['showdt']==2)
				{
					$rewriter=eReturnRewriteInfoUrl($classid,$id,1);
					$reurl=$rewriter['pageurl'];
				}
				else
				{
					$reurl=$titleurl;
				}
			}
			printerror("EditQinfoSuccess",$reurl,1);
		}
		else
		{printerror("DbError","history.go(-1)",1);}
	}
	//---------------删除
	elseif($elve==2)
	{
		if(!$id)
		{
			printerror("ErrorUrl","history.go(-1)",1);
		}
		//检测权限
		$r=CheckQdoinfo($classid,$id,$muserid,$tbname,$cr['adminqinfo'],2);
		//附加链接参数
		$addelvecheck=empty($r['checked'])?'&elvecheck=1':'';
		//返回表信息
		$infotbr=ReturnInfoTbname($tbname,$r['checked'],$r['stb']);
		$stf=$emod_r[$mid]['savetxtf'];
		$pf=$emod_r[$mid]['pagef'];
		//分页字段
		if($pf)
		{
			if(strstr($emod_r[$mid]['tbdataf'],','.$pf.','))
			{
				$finfor=$Elves->fetch1("select ".$pf." from ".$infotbr['datatbname']." where id='$id' limit 1");
				$r[$pf]=$finfor[$pf];
			}
		}
		//存文本
		if($stf)
		{
			$newstextfile=$r[$stf];
			$r[$stf]=GetTxtFieldText($r[$stf]);
			//删除文件
			DelTxtFieldText($newstextfile);
		}
		//删除信息文件
		DelNewsFile($r[filename],$r[newspath],$classid,$r[$pf],$r[groupid]);
		$indexsql=$Elves->query("delete from {$dbtbpre}elve_".$tbname."_index where id='$id'");
		$sql=$Elves->query("delete from ".$infotbr['tbname']." where id=$id and classid=$classid and userid='$muserid' and ismember=1");
		$fsql=$Elves->query("delete from ".$infotbr['datatbname']." where id=$id");
		esetcookie("qdelinfo","",0);
		//更新栏目信息数
		AddClassInfos($classid,'-1','-1',$r['checked']);
		//删除其它表记录和附件
		DelSingleInfoOtherData($classid,$id,$r,0,0);
		//生成列表
		if($r['checked'])
		{
			qAddListHtml($classid,$mid,$cr['qaddlist'],$cr['listdt']);
			//生成上一篇
			if($cr['repreinfo'])
			{
				$prer=$Elves->fetch1("select * from {$dbtbpre}elve_".$tbname." where id<$id and classid='$classid' order by id desc limit 1");
				GetHtml($prer['classid'],$prer['id'],$prer,1);
				//下一篇
				$nextr=$Elves->fetch1("select * from {$dbtbpre}elve_".$tbname." where id>$id and classid='$classid' order by id limit 1");
				if($nextr['id'])
				{
					GetHtml($nextr['classid'],$nextr['id'],$nextr,1);
				}
			}
		}
		if($sql)
		{
			$reurl=DoingReturnUrl("ListInfo.php?mid=$mid",$add['elvefrom']);
			printerror("DelQinfoSuccess",$reurl,1);
		}
		else
		{printerror("DbError","history.go(-1)",1);}
	}
	else
	{
		printerror("ErrorUrl","",1);
	}
}

//投稿权限检测
function DoQCheckAddLevel($classid,$userid,$username,$rnd,$elve=0,$isadd=0){
	global $Elves,$dbtbpre,$level_r,$public_r;
	$r=$Elves->fetch1("select * from {$dbtbpre}melveclass where classid='$classid'");
	if(!$r['classid']||$r[wburl])
	{
		printerror("EmptyQinfoCid","",1);
	}
	if(!$r['islast'])
	{
		printerror("MustLast","",1);
	}
	if($r['openadd'])
	{
		printerror("NotOpenCQInfo","",1);
	}
	//是否登陆
	if($elve==1||$elve==2||($r['qaddgroupid']&&$r['qaddgroupid']<>','))
	{
		$user=islogin($userid,$username,$rnd);
		//验证新会员投稿
		if($isadd==1&&$public_r['newaddinfotime'])
		{
			qCheckNewMemberAddInfo($user[registertime]);
		}
	}
	//会员组
	if($r['qaddgroupid']&&$r['qaddgroupid']<>',')
	{
		if(!strstr($r['qaddgroupid'],','.$user[groupid].','))
		{
			printerror("HaveNotLevelAQinfo","history.go(-1)",1);
		}
	}
	if($isadd==1)
	{
		//检测是否足够点数
		if($r['addinfofen']<0&&$user['userid'])
		{
			MCheckEnoughFen($user['userfen'],$user['userdate'],$r['addinfofen']);
		}
		//检测投稿数
		if($r['qaddgroupid']&&$r['qaddgroupid']<>','&&$level_r[$user[groupid]]['dayaddinfo'])
		{
			$r['checkaddnumquery']=DoQCheckAddNum($user['userid'],$user['groupid']);
		}
	}
	//审核
	if(($elve==0||$elve==1)&&$userid)
	{
		if(!$user[groupid])
		{
			$user=islogin($userid,$username,$rnd);
		}
		if($level_r[$user[groupid]]['infochecked'])
		{
			$r['checkqadd']=1;
			$r['qeditchecked']=0;
		}
	}
	return $r;
}

//检查投稿数
function DoQCheckAddNum($userid,$groupid){
	global $Elves,$dbtbpre,$level_r,$public_r;
	$ur=$Elves->fetch1("select userid,todayinfodate,todayaddinfo from {$dbtbpre}melvememberpub where userid='$userid' limit 1");
	$thetoday=date("Y-m-d");
	if($ur['userid'])
	{
		if($thetoday!=$ur['todayinfodate'])
		{
			$query="update {$dbtbpre}melvememberpub set todayinfodate='$thetoday',todayaddinfo=1 where userid='$userid'";
		}
		else
		{
			if($ur['todayaddinfo']>=$level_r[$groupid]['dayaddinfo'])
			{
				printerror("CrossDayInfo",$public_r['newsurl'],1);
			}
			$query="update {$dbtbpre}melvememberpub set todayaddinfo=todayaddinfo+1 where userid='$userid'";
		}
	}
	else
	{
		$query="replace into {$dbtbpre}melvememberpub(userid,todayinfodate,todayaddinfo) values('$userid','$thetoday',1);";
	}
	return $query;
}

//上传附件
function DoQTranFile($add,$file,$file_name,$file_type,$file_size,$userid,$username,$rnd,$elve=0){
	global $Elves,$dbtbpre,$class_r,$public_r,$elve_config;
	if($public_r['addnews_ok'])//关闭投稿
	{
		$elve!=1?printerror("NotOpenCQInfo","",9):elve_QEditorPrintError(1,'','','NotOpenCQInfo','','');
	}
	$filepass=(int)$add['filepass'];
	$classid=(int)$add['classid'];
	$infoid=(int)$add['infoid'];
	if(!$file_name||!$filepass||!$classid||!$class_r[$classid][tbname])
	{
		$elve!=1?printerror("EmptyQTranFile","",9):elve_QEditorPrintError(1,'','','EmptyQTranFile','','');
	}
	//信息
	if($infoid)
	{
		$index_r=$Elves->fetch1("select classid,checked from {$dbtbpre}elve_".$class_r[$classid][tbname]."_index where id='$infoid'");
		if(!$index_r['classid']||$classid!=$index_r['classid'])
		{
			$elve!=1?printerror("EmptyQTranFile","",9):elve_QEditorPrintError(1,'','','EmptyQTranFile','','');
		}
		$infotb=ReturnInfoMainTbname($class_r[$classid][tbname],$index_r['checked']);
		$infor=$Elves->fetch1("select classid,fstb from ".$infotb." where id='$infoid'");
		if(!$infor['fstb']||$classid!=$infor['classid'])
		{
			$elve!=1?printerror("EmptyQTranFile","",9):elve_QEditorPrintError(1,'','','EmptyQTranFile','','');
		}
		$fstb=$infor['fstb'];
	}
	else
	{
		$fstb=$public_r['filedeftb'];
	}
	//验证权限
	$userid=(int)$userid;
	$username=RepPostVar($username);
	$rnd=RepPostVar($rnd);
	DoQCheckAddLevel($classid,$userid,$username,$rnd,0,0);
	$filetype=GetFiletype($file_name);//取得文件类型
	if(CheckSaveTranFiletype($filetype))
	{
		$elve!=1?printerror("NotQTranFiletype","",9):elve_QEditorPrintError(1,'','','NotQTranFiletype','','');
	}
	$type=(int)$add['type'];
	$pr=$Elves->fetch1("select qaddtran,qaddtransize,qaddtranimgtype,qaddtranfile,qaddtranfilesize,qaddtranfiletype from {$dbtbpre}melvepublic limit 1");
	if($type==1)//图片
	{
		if(!$pr['qaddtran'])
		{
			$elve!=1?printerror("CloseQTranPic","",9):elve_QEditorPrintError(1,'','','CloseQTranPic','','');
		}
		if(!strstr($pr['qaddtranimgtype'],"|".$filetype."|"))
		{
			$elve!=1?printerror("NotQTranFiletype","",9):elve_QEditorPrintError(1,'','','NotQTranFiletype','','');
		}
		if($file_size>$pr['qaddtransize']*1024)
		{
			$elve!=1?printerror("TooBigQTranFile","",9):elve_QEditorPrintError(1,'','','TooBigQTranFile','','');
		}
		if(!strstr($elve_config['sets']['tranpicturetype'],','.$filetype.','))
		{
			$elve!=1?printerror("NotQTranFiletype","",9):elve_QEditorPrintError(1,'','','NotQTranFiletype','','');
		}
	}
	elseif($type==2)//flash
	{
		if(!$pr['qaddtranfile'])
		{
			$elve!=1?printerror("CloseQTranFile","",9):elve_QEditorPrintError(1,'','','CloseQTranFile','','');
		}
		if(!strstr($pr['qaddtranfiletype'],"|".$filetype."|"))
		{
			$elve!=1?printerror("NotQTranFiletype","",9):elve_QEditorPrintError(1,'','','NotQTranFiletype','','');
		}
		if($file_size>$pr['qaddtranfilesize']*1024)
		{
			$elve!=1?printerror("TooBigQTranFile","",9):elve_QEditorPrintError(1,'','','TooBigQTranFile','','');
		}
		if(!strstr($elve_config['sets']['tranflashtype'],','.$filetype.','))
		{
			$elve!=1?printerror("NotQTranFiletype","",9):elve_QEditorPrintError(1,'','','NotQTranFiletype','','');
		}
	}
	else//附件
	{
		if(!$pr['qaddtranfile'])
		{
			$elve!=1?printerror("CloseQTranFile","",9):elve_QEditorPrintError(1,'','','CloseQTranFile','','');
		}
		if(!strstr($pr['qaddtranfiletype'],"|".$filetype."|"))
		{
			$elve!=1?printerror("NotQTranFiletype","",9):elve_QEditorPrintError(1,'','','NotQTranFiletype','','');
		}
		if($file_size>$pr['qaddtranfilesize']*1024)
		{
			$elve!=1?printerror("TooBigQTranFile","",9):elve_QEditorPrintError(1,'','','TooBigQTranFile','','');
		}
	}
	$r=DoTranFile($file,$file_name,$file_type,$file_size,$classid);
	if(empty($r[tran]))
	{
		$elve!=1?printerror("TranFail","",9):elve_QEditorPrintError(1,'','','TranFail','','');
	}
	//写入数据库
	$filetime=time();
	$r[filesize]=(int)$r[filesize];
	$classid=(int)$classid;
	eInsertFileTable($r[filename],$r[filesize],$r[filepath],'[Member]'.$username,$classid,$r[filename],$type,$filepass,$filepass,$public_r[fpath],0,0,$fstb);
	//编辑器
	if($elve==1)
	{
		elve_QEditorPrintError(0,$r[url],$r[filename],'',$r[filename],$r[filesize]);
	}
	else
	{
		echo"<script>opener.document.add.".$add['field'].".value='".$r['url']."';window.close();</script>";
	}
	db_close();
	$Elves=null;
	exit();
}

//----------- 编辑器 --------------

//提示信息
function elve_QEditorPrintError($errorNumber,$fileUrl,$fileName,$customMsg,$fileno,$filesize){
	if(empty($errorNumber))
	{
		$errorNumber=0;
		$filesize=ChTheFilesize($filesize);
	}
	else
	{
		@include LoadLang("pub/q_message.php");
		$customMsg=$qmessage_r[$customMsg];
	}
	$errorNumber=(int)$errorNumber;
	echo"<script type=\"text/javascript\">window.parent.OnUploadCompleted($errorNumber,'".addslashes($fileUrl)."','".addslashes($fileName)."','".addslashes($customMsg)."','".addslashes($fileno)."','$filesize');</script>";
	db_close();
	exit();
}
?>