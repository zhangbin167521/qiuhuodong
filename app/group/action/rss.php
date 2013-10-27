<?php
defined('IN_TS') or die('Access Denied.');
//RSS输出
$groupid = intval($_GET['groupid']);
$strGroup = $new['group']->getOneGroup($groupid);
$arrTopics = $db->fetch_all_assoc("select * from ".dbprefix."group_topic where groupid='$groupid' order by addtime desc limit 30");

foreach($arrTopics as $key=>$item){
	$arrTopic[] = $item;
	$arrTopic[$key]['content'] = '';
	if($item['photo']){
		$arrTopic[$key]['content'] .= '<img src="'.tsXimg($item['photo'],'topic',500,'',$item['path']).'" /><br />';
	}
	$arrTopic[$key]['content'] .=nl2br(htmlspecialchars($item['content']));
}

$pubdate = time();
header( 'Content-Type:text/xml');
include template("rss");