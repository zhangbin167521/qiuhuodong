<?php
defined('IN_TS') or die('Access Denied.');


switch($ts){

	case "":
	
		$arrGroup = $new['mobile']->findAll('group',array(
			'isaudit'=>'0',
		),'isrecommend desc,addtime asc',null,10);

		$title = '小组'.' - '.$TS_CF['info']['powered'];
		include template('group');
	
		break;
		
	case "show":
	
		$groupid = intval($_GET['groupid']);
		$strGroup = aac('group')->getOneGroup($groupid);
		
		//最新帖子	
		$arrNewTopics = aac('group')->findAll('group_topic',"`groupid`='$groupid' and `isaudit`='0'",'uptime desc',null,35);
		foreach($arrNewTopics as $key=>$item){
			$arrTopic[] = $item;
			$arrTopic[$key]['title']=htmlspecialchars($item['title']);
			$arrTopic[$key]['user'] = aac('user')->getOneUser($item['userid']);
		}
		
		
		$arrHeadButton = array(
			'left'=>array(
				'title'=>'返回',
				'url'=>tsUrl('mobile','group'),
			),
			'right'=>array(
				'title'=>'发帖',
				'url'=>'#',
			),
		);
		
		$title = $strGroup['groupname'].' - '.$TS_CF['info']['powered'];
		include template('group_show');
		break;

}