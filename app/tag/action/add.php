<?php 
defined('IN_TS') or die('Access Denied.');
switch($ts){
	case "":
		
		$objname = $_GET['objname'];
		$idname = $_GET['idname'];
		$objid = intval($_GET['objid']);
		
		include template("add_ajax");
		break;
		
	case "do":
	
		$objname = $_POST['objname'];
		$idname = $_POST['idname'];
		$objid = intval($_POST['objid']);
		$tags = $_POST['tags'];
		
		$new['tag']->addTag($objname,$idname,$objid,$tags);
		
		tsNotice('标签添加成功！');
	
		break;
}