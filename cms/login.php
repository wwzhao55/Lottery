<?php
require '../Api/api.php';
$api = new api();

$db = $api->get_db();
if($db == null){
	require '../resource/config.html';
	exit;
}

session_start();

if(isPost()){
	if(isset($_SESSION['uid'])){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'已经登录',
			));
		exit; 
	}
	$admin_count = $db->count('admin',['id[>]'=>0]);
	if($admin_count==0){
		$new_admin = $db->insert('admin',['account'=>'admin','password'=>md5('123456')]);
		if(!$new_admin){
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'数据初始化失败',
				));
			exit; 
		}
	}
	$account = isset($_POST['account'])?$_POST['account']:null;
	$password = isset($_POST['password'])?$_POST['password']:null;
	if($account==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'账号不能为空',
			));
		exit; 
	}
	if($password==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'密码不能为空',
			));
		exit; 
	}

	$admin = $db->get('admin','*',['AND'=>['account'=>$account,'password'=>md5($password)]]);
	if($admin){
		$_SESSION['uid'] = $admin['id'];
		$_SESSION['account'] = $admin['account'];
		echo json_encode(array(
			'status'=>'success',
			'message'=>'登录成功',
			));
		exit; 
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'账号密码错误',
			));
		exit; 
	}
}else{
	if(isset($_SESSION['uid'])){	
		Header("Location: activity.php"); 
	}else{
		require '../resource/cms/login.html';
		exit;
	}	
}
