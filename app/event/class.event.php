<?php
defined('IN_TS') or die('Access Denied.');
class event extends tsApp{

	//构造函数
	public function __construct($db){
		parent::__construct($db);
	}
	
	//通过topicid获取活动基本信息
	function getEventByEventid($eventid){
		$strEvent = $this->db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
		if(!$strEvent){header("Location: ".SITE_URL."index.php?app=event");}
		
		//用户 
		$strEvent['user'] = $this->db->once_fetch_assoc("select * from ".dbprefix."user_info where userid='".$strEvent['userid']."'");  
		
		$strEvent['time_start'] = date('Y',strtotime($strEvent['time_start'])).'-'.date('m',strtotime($strEvent['time_start'])).'-'.date('d',strtotime($strEvent['time_start'])).' '.date('H:i',strtotime($strEvent['time_start']));
		
		$strEvent['time_end'] = date('Y',strtotime($strEvent['time_end'])).'-'.date('m',strtotime($strEvent['time_end'])).'-'.date('d',strtotime($strEvent['time_end'])).' '.date('H:i',strtotime($strEvent['time_end']));
		
		//地点
		$strEvent['area'] = aac('location')->getAreaForApp($strEvent['areaid']);
		
		//活动类型
		$strEvent['type'] = $this->db->once_fetch_assoc("select * from ".dbprefix."event_type where typeid='".$strEvent['typeid']."'");
		
		return $strEvent;
	}
	
	//
	function getSimpleEvent($eventid){
		$strEvent = $this->db->once_fetch_assoc("select * from ".dbprefix."event where eventid='$eventid'");
		
		//地点
		$strEvent['area'] = aac('location')->getAreaForApp($strEvent['areaid']);
		
		//用户 
		$strEvent['user'] = $this->db->once_fetch_assoc("select * from ".dbprefix."user_info where userid='".$strEvent['userid']."'");
		
		return $strEvent;
	}

	
	/*
	 *获取内容评论列表
	 */
	
	function getEventComment($page = 1, $prePageNum,$eventid){
		$start_limit = !empty($page) ? ($page - 1) * $prePageNum : 0;
		$limit = $prePageNum ? "LIMIT $start_limit, $prePageNum" : '';
		$arrGroupContentComment	= $this->db->fetch_all_assoc("select * from ".dbprefix."event_comment where eventid='$eventid' order by addtime desc $limit");
		
		if(is_array($arrGroupContentComment)){
			foreach($arrGroupContentComment as $key=>$item){
				//$arrGroupContentComment[$key]['user'] = $this->getUserData($item['userid']);
				$arrGroupContentComment[$key]['user'] = aac('user')->getOneUser($item['userid']);
				$arrGroupContentComment[$key]['content'] = h($item['content']);
			}
		}
		
		return $arrGroupContentComment;
	}
	
	/*
	 *获取内容评论列表数
	 */
	
	function getEventCommentNum($virtue, $setvirtue){
		$where = 'where '.$virtue.'='.$setvirtue.'';
		$res = "SELECT * FROM ".dbprefix."event_comment $where";
		$groupContentCommentNum = $this->db->once_num_rows($res);
		return $groupContentCommentNum;
	}
	
	//获取活动小组
	function getGroup($groupid){
		$strGroup = $this->db->once_fetch_assoc("select * from ".dbprefix."app_group where groupid='$groupid'");
		if($strGroup['groupicon'] == ''){
			$strGroup['groupicon'] = 'uploadfile/group/default/default.gif';
		}
		return $strGroup;
	}
}