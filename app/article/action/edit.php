<?php
defined('IN_TS') or die('Access Denied.'); 

//用户是否登录
$userid = aac('user')->isLogin();

switch($ts){

	
	case "":
		
		$articleid = intval($_GET['articleid']);
		
		$cateid = intval($_GET['cateid']);
		
		$strArticle = $new['article']->find('article',array(
			'articleid'=>$articleid,
		));
		
		if($strArticle['userid'] == $userid || $TS_USER['user']['isadmin']==1){
			
			
			//找出TAG
			$arrTags = aac('tag')->getObjTagByObjid('article', 'articleid', $articleid);
			foreach($arrTags as $key=>$item){
				$arrTag[] = $item['tagname'];
			}
			$strArticle['tag'] = array_to_str($arrTag);
			
			
			$title = '修改文章';
			include template('edit');
		
		}else{
		
			tsNotice('非法操作！');
		
		}
		

		break;
		
	case "do":
		
		$articleid = intval($_POST['articleid']);
		$cateid = intval($_POST['cateid']);
		$title	= trim($_POST['title']);
		$content = trim($_POST['content']);
		
		if($TS_USER['user']['isadmin']==0){
			//过滤内容开始
			aac('system')->antiWord($title);
			aac('system')->antiWord($content);
			//过滤内容结束
		}
		
		if($title=='' || $content=='') qiMsg("标题和内容都不能为空！");

		$new['article']->update('article',array(
		
			'articleid'=>$articleid,
		
		),array(
		
			'cateid'=>$cateid,
			'title'=>$title,
			'content'=>$content,
		
		));
		
		
		//处理标签
		$tag = trim($_POST['tag']);
		if($tag){
			aac('tag')->delIndextag('article','articleid',$articleid);
			aac('tag') -> addTag('article', 'articleid', $articleid, $tag); 
		}
		
		
		
		// 上传帖子图片开始
		$arrUpload = tsUpload($_FILES['picfile'], $articleid, 'article', array('jpg', 'gif', 'png','jpeg'));
		if ($arrUpload) {
			$new['article'] -> update('article', array(
				'articleid' => $articleid,
			), array(
				'path' => $arrUpload['path'],
				'photo' => $arrUpload['url'],
			));
			
			tsDimg($arrUpload['url'],'article','180','140',$arrUpload['path']);
			
		} 
		// 上传帖子图片结束
		
		header("Location: ".tsUrl('article','show',array('id'=>$articleid)));
		
		break;
}