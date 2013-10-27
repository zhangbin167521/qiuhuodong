<?php 
defined('IN_TS') or die('Access Denied.');
//活动类型

$typeid = $_GET['typeid'];
//调出活动类型
$arrEventType = fileRead('data/event_types.php');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$lstart = $page*12-12;
$url = tsUrl('event','type',array('typeid'=>$typeid,'page'=>''));


if($typeid == 0){
	$arrEvents = $db->fetch_all_assoc("select eventid from ".dbprefix."event order by addtime desc limit $lstart,12");
	if(is_array($arrEvents)){
		foreach($arrEvents as $item){
			$arrEvent[] = $new['event']->getEventByEventid($item['eventid']);
		}
	}
	$topicNum = $new['event']->findCount('event',"`isaudit`='1'");
    $pageUrl = pagination($topicNum, 12, $page, $url);
	$title = '所有类型';
}else{
	$strType = $db->once_fetch_assoc("select * from ".dbprefix."event_type where typeid='$typeid'");
	//调出类型下面的活动 
	$arrEvents = $db->fetch_all_assoc("select eventid from ".dbprefix."event where typeid='$typeid' and isaudit='1' order by addtime desc limit $lstart,12");
	if(is_array($arrEvents)){
		foreach($arrEvents as $item){
			$arrEvent[] = $new['event']->getEventByEventid($item['eventid']);
		}
	}
	$topicNum = $new['event']->findCount('event',"`isaudit`='1' and `typeid`='$typeid'");
    $pageUrl = pagination($topicNum, 12, $page, $url);
	$title = $strType['typename'];
}

include template("type");