<?php
require 'init.php';
//获取奖品信息
if(isGet()){
	if(isAjax()){
		$setter = $db->get('activity',['person_day_times','person_total_times','person_max_times'],['id[>]'=>0]);		
		if($setter){
			echo json_encode(array(
				'status'=>'success',
				'message'=>'获取成功',
				'data'=>$setter,
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
		$main_html = file_get_contents('../resource/cms/setter.html');
		require '../resource/cms/layer.html';
		exit;
	}
	
}else if(isPost()){
	$person_day_times = isset($_POST['person_day_times'])?$_POST['person_day_times']:null;
	$person_total_times = isset($_POST['person_total_times'])?$_POST['person_total_times']:null;
	$person_max_times = isset($_POST['person_max_times'])?$_POST['person_max_times']:null;
	
	$update_info = [];

	if($person_day_times!=null){
		$update_info['person_day_times'] = $person_day_times;
	}
	if($person_total_times!=null){
		$update_info['person_total_times'] = $person_total_times;
	}
	if($person_max_times!=null){
		$update_info['person_max_times'] = $person_max_times;
	}
	
	$result = $db->update('activity',$update_info,['id[>]'=>0]);
	if($result){
		$activity_info = $db->get('activity','*',['status[<]'=>3]);
		$awards = $db->select('award','*',['id[>]'=>0]);
		create_template($activity_info,$awards);
		echo json_encode(array(
			'status'=>'success',
			'message'=>'数据更新成功',
			));
		exit;
	}else{
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'数据更新失败',
			));
		exit;
	}			

}