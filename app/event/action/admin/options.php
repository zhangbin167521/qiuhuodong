<?php 

$titel = '配置';

if(!is_file('app/event/install/event_install.rice')){
	$install = '1';
}

include template("admin/options");