<?php
require 'init.php';
if(isPost()){
	$award_id = isset($_POST['award_id'])?$_POST['award_id']:'';
	$redeem_codes = $db->select('redeem_code','*',['award_id'=>$award_id]);
	if($redeem_codes){
		echo json_encode(array(
			'status'=>'success',
			'message'=>'获取成功',
			'data'=>$redeem_codes
			));
		exit;
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'获取失败',
			));
		exit;
	}

}else{
	if(isAjax()){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'请求方式出错',
			));
		exit;
	}else{
		Header("Location: activity.php");
	}
}