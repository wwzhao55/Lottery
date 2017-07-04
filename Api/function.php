<?php
/**
 * 是否是AJAx提交的
 * @return bool
 */
function isAjax(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        return true;
    }else{
        return false;
    }
}

/**
 * 是否是GET提交的
 */
function isGet(){
    return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}

/**
 * 是否是POST提交
 * @return int
 */
function isPost() {
    return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
}

function create_template($activity_info,$awards){
	require '../Api/smarty/libs/Smarty.class.php';
	$smarty = new Smarty;

	$smarty->assign("activity", $activity_info);
	$smarty->assign("awards", $awards);
	$smarty->compile_dir = dirname(__FILE__).'/../uploads/temp';

	$h5_tpls = ['config.js','config.php','index.html'];
	foreach ($h5_tpls as $tpl) {
		$content_h5_tpl = $smarty->fetch("../resource/h5Tpl/".$tpl);  
		$fp = fopen("../h5/".$tpl, "w");  
		fwrite($fp, $content_h5_tpl);  
		fclose($fp);
	}

	$weixin_tpls = ['award.html','config.js','config.php','index.html'];
	foreach ($weixin_tpls as $tpl) {
		$content_weixin_tpl = $smarty->fetch("../resource/weixinTpl/".$tpl);  
		$fp = fopen("../weixin/".$tpl, "w");  
		fwrite($fp, $content_weixin_tpl);  
		fclose($fp);
	}

	$content_common_tpl = $smarty->fetch("../resource/commonTpl/index.html");
	$fp = fopen("../index.html", "w");  
	fwrite($fp, $content_common_tpl);  
	fclose($fp); 
}