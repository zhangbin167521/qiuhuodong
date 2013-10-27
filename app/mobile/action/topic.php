<?php
defined('IN_TS') or die('Access Denied.');

switch($ts){
	
	case "":
	
		//最新帖子	
		$arrNewTopics = aac('group')->findAll('group_topic',"`isaudit`='0'",'uptime desc',null,35);
		foreach($arrNewTopics as $key=>$item){
			$arrTopic[] = $item;
			$arrTopic[$key]['title']=htmlspecialchars($item['title']);
			$arrTopic[$key]['user'] = aac('user')->getOneUser($item['userid']);
		}

		$title = '帖子'.' - '.$TS_CF['info']['powered'];
		include template('topic');
	
		break;
		
		
	case "show":
	
		$topicid = intval($_GET['topicid']);
		$strTopic = $new['mobile']->find('group_topic',array(
			'topicid'=>$topicid,
		));
		
		$strTopic['content'] = str_replace('nowrap','',$strTopic['content']);
		
		$strTopic['user'] = aac('user')->getOneUser($strTopic['userid']);
		
		
		//评论
		$arrComment = $new['mobile']->findAll('group_topic_comment',array(
			'topicid'=>$strTopic['topicid'],
		),'addtime desc',null,10);
		foreach($arrComment as $key=>$item){
			$arrComment[$key]['user'] = aac('user')->getOneUser($item['userid']);
		}

		
		$arrHeadButton = array(
			'left'=>array(
				'title'=>'返回',
				'url'=>tsUrl('mobile','group',array('ts'=>'show','groupid'=>$strTopic['groupid'])),
			),
			'right'=>array(
				'title'=>'评论',
				'url'=>'#',
			),
		);
		$title = $strTopic['title'].' - '.$TS_CF['info']['powered'];
		include template('topic_show');
	
		break;
	
}