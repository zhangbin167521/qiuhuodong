<?php
defined('IN_TS') or die('Access Denied.');
$eventid = intval($_GET['eventid']);

//���Ϣ
$strEvent = $new['event']->getEventByEventid($eventid);
if($strEvent['isaudit'] =='0' && $TS_USER['user']['isadmin']!='1'){qiMsg("�ûδͨ�����.�ݲ������!");}

$strEvent['user'] = simpleUser($strEvent['userid']);

$strEvent['content'] = preg_replace("/\[@(.*)\:(.*)]/U","<a href='".tsUrl('user','space',array('id'=>'$2'))." ' rel=\"face\" uid=\"$2\"'>@$1</a>",$strEvent['content']);

//wishdo
if($TS_USER['user']['userid'] != ''){
	$isEventUser = $db->once_num_rows("select * from ".dbprefix."event_users where eventid='".$strEvent['eventid']."' and userid='".$TS_USER['user']['userid']."'");
	$strEventUser = $db->once_fetch_assoc("select * from ".dbprefix."event_users where eventid='".$strEvent['eventid']."' and userid='".$TS_USER['user']['userid']."'");
}


//��֯��
$arrOrganizers= $db->fetch_all_assoc("select userid from ".dbprefix."event_users where eventid='".$strEvent['eventid']."' and isorganizer='1'");
if(is_array($arrOrganizers)){
	foreach($arrOrganizers as $item){
		$arrOrganizer[] = simpleUser($item['userid']);
	}
}

//�μ������ĳ�Ա
$arrDoUsers = $db->fetch_all_assoc("select userid from ".dbprefix."event_users where eventid='".$strEvent['eventid']."' and status='0' order by addtime");
if(is_array($arrDoUsers)){
	foreach($arrDoUsers as $item){
		$arrDoUser[] = aac('user')->getOneUser($item['userid']);
	}
}

//����������Ȥ����
$arrWishUsers = $db->fetch_all_assoc("select userid from ".dbprefix."event_users where eventid='".$strEvent['eventid']."' and status='1' order by addtime");
if(is_array($arrWishUsers)){
	foreach($arrWishUsers as $item){
		$arrWishUser[] = aac('user')->getOneUser($item['userid']);
	}
}

//��ЩС�����ڷ�������
$arrGroups = $db->fetch_all_assoc("select groupid from ".dbprefix."event_group_index where eventid='$eventid'");
if(is_array($arrGroups)){
	foreach($arrGroups as $item){
		$arrGroup[] = aac('group')->getOneGroup($item['groupid']);
	}
}


//����

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$url = "index.php?app=event&ac=show&eventid=".$strEvent['eventid']."&page=";

$arrContentComment = $new['event']->getEventComment($page,15,$eventid);

if(is_array($arrContentComment)){
	foreach($arrContentComment as $key=>$item){
		$arrEventComment[] = $item;
		
		$arrEventComment[$key]['content'] =  h($item['content']);
	

	}
}

$eventCommentNum = $new['event']->getEventCommentNum('eventid',$eventid);

$pageUrl = pagination($eventCommentNum, 15, $page, $url);

$title = $strEvent['title'];
include template("show");

function simpleUser($userid){
	global $db;
	$userData = $db->once_fetch_assoc("select userid,username from ".dbprefix."user_info where userid='$userid'");
	return $userData;
}
function getPhotoById($photoid){
	global $db;
	$strPhoto = $db->once_fetch_assoc("select * from ".dbprefix."app_photo where photoid='$photoid'");
	
	return $strPhoto['photourl'];
}