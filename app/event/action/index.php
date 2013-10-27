<?php 
defined('IN_TS') or die('Access Denied.');
//调出活动类型 
$arrEventType = fileRead('data/event_types.php');
//若缓存文件不存在.由访问者生成缓存文件 BY Rice
if(!$arrEventType){
	$arrTypess = $db->fetch_all_assoc("select * from ".dbprefix."event_type");//获取所有活动分类
			foreach($arrTypess as $key=>$item){
				$event = $db->once_fetch_assoc("select count(eventid) from ".dbprefix."event where typeid='".$item['typeid']."' and isaudit='1'");//计算同分类下活动数未通过审核将不计算在内
				$arrTypes['list'][] = array(
					'typeid'	=> $item['typeid'],
					'typename'	=> $item['typename'],
					'count_event'	=> $event['count(eventid)'],
				);
			}
			
			$eventNum = $db->once_fetch_assoc("select count(eventid) from ".dbprefix."event where isaudit='1'");//获取所有活动数,未通过审核将不计算在内
			$arrTypes['count'] = $eventNum['count(eventid)'];
			fileWrite('event_types.php','data',$arrTypes);
			$arrEventType =fileRead('data/event_types.php');//重新读取缓存文件
}
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$lstart = $page*12-12;
$url = tsUrl('event','index',array('page'=>''));

$arrEvents = $db->fetch_all_assoc("select eventid from ".dbprefix."event where 1 and isaudit='1' order by addtime desc limit $lstart,12");
foreach($arrEvents as $item){
	$arrEvent[] = $new['event']->getEventByEventid($item['eventid']);
}

$topicNum = $new['event']->findCount('event',"`isaudit`='1'");
$pageUrl = pagination($topicNum, 12, $page, $url);


$title = '活动';
include template("index");