<?php
defined('IN_TS') or die('Access Denied.');
	switch($ts){
	
		//发布活动
		case "add":
			$userid = intval($TS_USER['user']['userid']);
			$title = h($_POST['title']);
			$typeid = $_POST['typeid'];
			$time_start = $_POST['time_start'];
			$time_end = $_POST['time_end'];
			$address = $_POST['address'];
			$content = trim($_POST['content']);
			
			$oneid = intval($_POST['oneid']);
			$twoid = intval($_POST['twoid']);
			$threeid = intval($_POST['threeid']);
			
			if($oneid != 0 && $twoid==0 && $threeid==0){
				$areaid = $oneid;
			}elseif($oneid!=0 && $twoid !=0 && $threeid==0){
				$areaid = $twoid;
			}elseif($oneid!=0 && $twoid !=0 && $threeid!=0){
				$areaid = $threeid;
			}else{
				$areaid = 0;
			}
			
			if($userid == '0' || $title=='' || $time_end <=$time_start || $address=='' || $content==''){
				qiMsg("活动信息不符合要求！");
			}
			
			$eventData = array(
				'userid'	=> $userid,
				'title'	=> $title,
				'typeid' => $typeid,
				'time_start'	=> $time_start,
				'time_end'	=> $time_end,
				'areaid'	=> $areaid,
				'address'	=> $address,
				'content' => $content,
				'addtime'	=> time(),
			);
			
			$eventid = $db->insertArr($eventData,dbprefix.'event');
			
			//上传
			$arrUpload = tsUpload($_FILES['photo'],$eventid,'event',array('jpg','gif','png'));
			
			if($arrUpload){

				$new['event']->update('event',array(
					'eventid'=>$eventid,
				),array(
					'path'=>$arrUpload['path'],
					'poster'=>$arrUpload['url'],
				));

			}

			//发送系统消息(通知网站创始人审核活动)
				$strEvent = $db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
					$msg_userid = '0';
					$msg_touserid = '1';
					$msg_content = ''.$strEvent['userid'].'发布了活动：《'.$strEvent['title'].'》活动ID为['.$strEvent['eventid'].']请审核<br />'
												.SITE_URL.'index.php?app=event&ac=show&eventid='.$eventid;
					aac('message')->sendmsg($msg_userid,$msg_touserid,$msg_content);
				
			
			header("Location: ".tsUrl('event','show',array('eventid'=>$eventid)));
			
			break;
	
		//参加或者感兴趣活动
		case "dowish":
			$userid = $TS_USER['user']['userid'];
			$eventid = $_POST['eventid'];
			
			//0加入1感兴趣
			$status = intval($_POST['status']);
			
			$userNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and userid='$userid'");
			
			if($userid == ''){
				echo '0';return false;
			}elseif($userNum > '0'){
				echo '1';
			}else{
				$db->query("insert into ".dbprefix."event_users (`eventid`,`userid`,`status`,`addtime`) values ('$eventid','$userid','$status','".time()."')");
				//统计一下参加的
				$userDoNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='0'");
				//统计感兴趣的
				$userWishNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='1'");
				
				$db->query("update ".dbprefix."event set `count_userdo`='$userDoNum',`count_userwish`='$userWishNum' where eventid='$eventid'");
				
				//event
				$strEvent = $db->once_fetch_assoc("select title,time_start,path,poster,address,count_userdo from ".dbprefix."event where `eventid`='$eventid'");
				
				
				echo '2';
			}
			
			break;
			
		//取消参加活动
		case "cancel":
			
			$eventid = $_POST['eventid'];
			$userid	= $_POST['userid'];
			
			$db->query("delete from ".dbprefix."event_users where eventid='$eventid' and userid='$userid'");
			//统计一下参加的
			$userDoNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='0'");
			//统计感兴趣的
			$userWishNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='1'");
			
			$db->query("update ".dbprefix."event set `count_userdo`='$userDoNum',`count_userwish`='$userWishNum' where eventid='$eventid'");
			
			echo '1';
			
			break;
			
		//参加活动
		case "do":
			$eventid = $_POST['eventid'];
			$userid	= $_POST['userid'];
			$db->query("update ".dbprefix."event_users set `status`='0' where eventid='$eventid' and userid='$userid'");
			
			//统计一下参加的
			$userDoNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='0'");
			//统计感兴趣的
			$userWishNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='1'");
			
			$db->query("update ".dbprefix."event set `count_userdo`='$userDoNum',`count_userwish`='$userWishNum' where eventid='$eventid'");
			
			echo '1';
			
			break;
			
		//编辑执行
		case "edit":
			//由于增加了审核机制.重新编辑活动将需要重新审核!
			//发布
			$eventid = $_POST['eventid'];
			$title = $_POST['title'];
			$typeid = $_POST['typeid'];
			$time_start = $_POST['time_start'];
			$time_end = $_POST['time_end'];
			$address = trim($_POST['address']);
			$content = trim($_POST['content']);
			
			//更新数据
			$new['event']->update('event',array(
				'eventid'=>$eventid,
			),array(
				'typeid' => $typeid,
				'title'	=> $title,
				'time_start'	=> $time_start,
				'time_end'	=> $time_end,
				'address'	=> $address,
				'content'	=> $content,
				'isaudit'	=> 0,
			));
			
			//上传
			$arrUpload = tsUpload($_FILES['photo'],$eventid,'event',array('jpg','gif','png'));
			
			if($arrUpload){

				$new['event']->update('event',array(
					'eventid'=>$eventid,
				),array(
					'path'=>$arrUpload['path'],
					'poster'=>$arrUpload['url'],
				));

			}

			//发送系统消息(通知网站创始人重新审核活动)
				$strEvent = $db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
				if($strEvent['userid'] != $TS_USER['user']['userid']){
					$msg_userid = '0';
					$msg_touserid = '1';
					$msg_content = ''.$strEvent['userid'].'重新编辑了活动：《'.$strEvent['title'].'》活动ID为['.$strEvent['eventid'].']请重新审核改活动<br />'
												.SITE_URL.'index.php?app=system';
					aac('message')->sendmsg($msg_userid,$msg_touserid,$msg_content);
				
				}
			
			header("Location: ".SITE_URL."index.php?app=event&ac=show&eventid=".$eventid);
			
			break;
			
		//添加评论 
		case "comment":
			$eventid	= intval($_POST['eventid']);
			$content	= trim($_POST['content']);
			
			if($TS_USER['user'] == ''){
				qiMsg('请登陆后再发表内容^_^','点击登陆','index.php/user/login');
			}elseif(empty($content)){
				qiMsg('没有任何内容是不允许你通过滴^_^');
			}else{
				$arrData	= array(
					'eventid'			=> $eventid,
					'userid'			=> $TS_USER['user']['userid'],
					'content'	=> $content,
					'addtime'		=> time()
				);
				
				$commentid = $db->insertArr($arrData,dbprefix.'event_comment');
				
				
				//发送系统消息(通知活动组织者有人回复他的活动啦)
				$strEvent = $db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
				if($strEvent['userid'] != $TS_USER['user']['userid']){

					$msg_userid = '0';
					$msg_touserid = $strEvent['userid'];
					$msg_content = '你发布的活动：《'.$strEvent['title'].'》有一条新评论，快去看看吧 <br />'
												.SITE_URL.'index.php?app=event&ac=show&eventid='.$eventid;
					aac('message')->sendmsg($msg_userid,$msg_touserid,$msg_content);
				
				}
				
				header("Location: ".SITE_URL."index.php/event/show/eventid-".$eventid);
				
			}	
			break;
		case "delcomment":
			if($TS_USER['user']['isadmin'] != '1'){
			qiMsg("非法操作,请返回!");
			}else{
			$commentid = intval($_GET['commentid']);
			if(!$commentid || $commentid == '0'){qiMsg("无法获取活动ID,请返回!");}
			$strEventcomment = $db->once_fetch_assoc("select * from ".dbprefix."event_comment where commentid='$commentid'");
			if(!$strEventcomment){
				qiMsg("无法获取数据,请返回!");
			}else{
				$db->query("DELETE FROM ".dbprefix."event_comment WHERE `commentid`='$commentid'");
				header("Location: ".SITE_URL."index.php/event/show/eventid-".$strEventcomment['eventid']);
			}
		    }
			break;
        //活动审核 
		case "isaudit":
			if($TS_USER['user']['isadmin'] != '1'){
			qiMsg("非法操作,请返回!");
			}else{
			$eventid = intval($_GET['eventid']);
			if(!$eventid || $eventid == '0'){qiMsg("无法获取活动ID,请返回!");}
			
			$db->query("update ".dbprefix."event set `isaudit`='1' where `eventid`='$eventid'");

			//发送系统消息(通知活动组织者他的活动已经通过)
				$strEvent = $db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
					$msg_userid = '0';
					$msg_touserid = $strEvent['userid'];
					$msg_content = '你发布的活动：《'.$strEvent['title'].'》已经通过审核!快去看看吧 <br />'
												.SITE_URL.'index.php?app=event&ac=show&eventid='.$eventid;
					aac('message')->sendmsg($msg_userid,$msg_touserid,$msg_content);

            //更新统计活动分类缓存
			$arrTypess = $db->fetch_all_assoc("select * from ".dbprefix."event_type");
			foreach($arrTypess as $key=>$item){
				$event = $db->once_fetch_assoc("select count(eventid) from ".dbprefix."event where typeid='".$item['typeid']."'");
				$arrTypes['list'][] = array(
					'typeid'	=> $item['typeid'],
					'typename'	=> $item['typename'],
					'count_event'	=> $event['count(eventid)'],
				);
			}
			
			$eventNum = $db->once_fetch_assoc("select count(eventid) from ".dbprefix."event where isaudit='1'");
			$arrTypes['count'] = $eventNum['count(eventid)'];
			
			//生成缓存文件
			fileWrite('event_types.php','data',$arrTypes);

			//活动发布人参与活动
			$db->query("insert into ".dbprefix."event_users (`eventid`,`userid`,`status`,`addtime`) values ('$eventid','$strEvent[userid]','0','$strEvent[addtime]')");
			
			//统计一下参加的
			$userDoNum = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='$eventid' and status='0'");
			
			$db->query("update ".dbprefix."event set `count_userdo`='$userDoNum' where eventid='$eventid'");
			//参加结束

			qiMsg("操作成功！");
			}
			
			break;

			//删除活动 
		case "del":
			if($TS_USER['user']['isadmin'] != '1'){
			qiMsg("非法操作,请返回!");
			}else{
			$eventid = intval($_GET['eventid']);
			if(!$eventid || $eventid == '0'){qiMsg("无法获取活动ID,请返回!");}
            $strEvent = $db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
			if(!$strEvent){qiMsg("获取活动数据,请返回!");}
			//开始删除数据以及海报
			unlink("uploadfile/event/".$strEvent['path']."/".$eventid.".jpg");//删除原始封面
			$eventimgname=md10($strEvent['poster']);//获取封面略缩图名称
            unlink("cache/event/".$strEvent['path']."/100/".$eventimgname."_100_100.jpg");//删除封面略缩图
			$db->query("DELETE FROM ".dbprefix."event WHERE `eventid`='$eventid'");
			$db->query("DELETE FROM ".dbprefix."event_comment WHERE `eventid`='$eventid'");
			$db->query("DELETE FROM ".dbprefix."event_users WHERE `eventid`='$eventid'");
            //数据删除完毕

			//更新统计活动分类缓存
			$arrTypess = $db->fetch_all_assoc("select * from ".dbprefix."event_type");
			foreach($arrTypess as $key=>$item){
				$event = $db->once_fetch_assoc("select count(eventid) from ".dbprefix."event where typeid='".$item['typeid']."'");
				$arrTypes['list'][] = array(
					'typeid'	=> $item['typeid'],
					'typename'	=> $item['typename'],
					'count_event'	=> $event['count(eventid)'],
				);
			}
			
			$eventNum = $db->once_fetch_assoc("select count(eventid) from ".dbprefix."event  where isaudit='1'");
			$arrTypes['count'] = $eventNum['count(eventid)'];
			
			//生成缓存文件
			fileWrite('event_types.php','data',$arrTypes);

			qiMsg("操作成功！");
			}
			
			break;
			
	}
