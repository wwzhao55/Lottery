<?php
require 'init.php';
if(isPost()){
	$old_password = isset($_POST['old_password'])?$_POST['old_password']:null;
	$new_password = isset($_POST['new_password'])?$_POST['new_password']:null;
	$re_new_password = isset($_POST['re_new_password'])?$_POST['re_new_password']:null;
	if($old_password==null || $new_password==null || $re_new_password==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'参数缺失',
			));
		exit;
	}
	$admin_password = $db->get('admin','password',['id[>]'=>0]);
	if($admin_password == md5($old_password)){
		if($new_password==$re_new_password){
			$result = $db->update('admin',['password'=>md5($new_password)],['id[>]'=>0]);
			if($result){		
				echo json_encode(array(
					'status'=>'success',
					'message'=>'修改密码成功',
					));
				exit;
			}else{
				echo json_encode(array(
					'status'=>'fail',
					'message'=>'修改密码失败',
					));
				exit;
			}
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'两次密码输入不一致',
				));
			exit;
		}
		
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'原始密码错误',
			));
		exit;
	}

}else{
	echo json_encode(array(
		'status'=>'fail',
		'message'=>'请求方式错误',
		));
	exit;
}
