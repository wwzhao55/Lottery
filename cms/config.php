<?php
require '../Api/function.php';
if(isPost()){
	$dbhost = isset($_POST['dbhost'])?$_POST['dbhost']:null;
	$dbport = isset($_POST['dbport'])?$_POST['dbport']:null;
	$dbname = isset($_POST['dbname'])?$_POST['dbname']:null;
	$username = isset($_POST['username'])?$_POST['username']:null;
	$password = isset($_POST['password'])?$_POST['password']:null;

	if($dbhost==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'db_host不能为空',
			));
		exit;
	}
	if($dbport==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'db_port不能为空',
			));
		exit;
	}
	if($dbname==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'dbname不能为空',
			));
		exit;
	}
	if($username==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'username不能为空',
			));
		exit;
	}
	if($password==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'password不能为空',
			));
		exit;
	}	

	$db_config_file = fopen('../Api/config.php', 'w');
	$text = "<?php\ndefine('DB_HOST', '".$dbhost."');\ndefine('DB_PORT', '".$dbport."');\ndefine('DB_NAME','".$dbname."');\ndefine('DB_USER','".$username."');\ndefine('DB_PASSWORD','".$password."');";
	$result = fwrite($db_config_file, $text);
	fclose($db_config_file);

	if($result){
		echo json_encode(array(
			'status'=>'success',
			'message'=>'配置文件生成成功',
			));
		exit;
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'配置文件生成失败',
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