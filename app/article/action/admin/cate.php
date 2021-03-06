<?php
defined('IN_TS') or die('Access Denied.');
/* 
 * 小组分类管理
 */

switch($ts){
	//分类列表 
	case "list":
		
		$arrCate = $new['article']->findAll('article_cate');

		include template("admin/cate_list");
		
		break;
	
	//分类添加
	case "add":
	

		include template("admin/cate_add");
		
		break;
		
	case "add_do":
		
		$new['article']->create('article_cate',array(
		
			'catename'=>$_POST['catename'],
		
		));
		
		
		header("Location: ".SITE_URL."index.php?app=article&ac=admin&mg=cate&ts=list");
		
		break;
	
	//分类删除
	case "del":
		
		$cateid = intval($_GET['cateid']);
		$articleNum = $new['article']->findCount('article',array(
			'cateid'=>$cateid,
		));
		
		if($articleNum > 0){
			qiMsg("此分类有文章存在，不允许删除！");
		}
		
		$new['article']->delete('article_cate',array(
			'cateid'=>$cateid,
		));
		
		
		
		
		qiMsg("分类删除成功！");
		
		break;
	
	//分类修改
	case "edit":
	
		$cateid = intval($_GET['cateid']);
		
		$strCate = $new['article']->findAll('article_cate',array(
			'cateid'=>$cateid,
		));

		include template("admin/cate_edit");
		
		break;
	
	//分类修改执行 
	case "edit_do":
		$cateid = $_POST['cateid'];
		$catename = $_POST['catename'];
		
		$new['article']->update('article_cate',array(
			'cateid'=>$cateid,
		),array(
			'catename'=>$catename,
		));
		
		header("Location: ".SITE_URL."index.php?app=article&ac=admin&mg=cate&ts=list");
		
		break;
}