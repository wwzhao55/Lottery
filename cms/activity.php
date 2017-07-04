<?php
require 'init.php';
//获取活动信息
if(isGet()){
	if(isAjax()){
		$activity_info = $db->get('activity','*',['status[<]'=>3]);
		if($activity_info){
			echo json_encode(array(
				'status'=>'success',
				'message'=>'数据获取成功',
				'data'=>$activity_info,
				));
			exit;
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'数据获取失败',
				));
			exit;
		}
	}else{
		$main_html = file_get_contents('../resource/cms/activity.html');
		require '../resource/cms/layer.html';
		exit;
	}	
	
}else if(isPost()){
	$name = isset($_POST['name'])?$_POST['name']:null;
	$rule = isset($_POST['rule'])?$_POST['rule']:null;
	$start = isset($_POST['start'])?$_POST['start']:null;
	$end = isset($_POST['end'])?$_POST['end']:null;
	$addon = isset($_POST['addon'])?$_POST['addon']:null;
	$theme_color = isset($_POST['theme_color'])?$_POST['theme_color']:null;
	$title_color = isset($_POST['title_color'])?$_POST['title_color']:null;
	$status = isset($_POST['status'])?$_POST['status']:null;

	$domain = isset($_POST['domain'])?$_POST['domain']:null;
	$api_url = isset($_POST['api_url'])?$_POST['api_url']:null;
	$fake_notice = isset($_POST['fake_notice'])?$_POST['fake_notice']:null;
	$today_no_count_notice = isset($_POST['today_no_count_notice'])?$_POST['today_no_count_notice']:null;
	$api_channel = isset($_POST['api_channel'])?$_POST['api_channel']:null;
	$appid = isset($_POST['appid'])?$_POST['appid']:null;
	$sharetitle = isset($_POST['sharetitle'])?$_POST['sharetitle']:null;
    $sharedesc = isset($_POST['sharedesc'])?$_POST['sharedesc']:null;
	

	$update_info = [];

	if($upload->hasFile('bgimg')){
		$upload->set('path','../uploads');
		if($upload->upload('bgimg')){
			$bgimg_url = 'uploads/'.$upload->getFileName();
			$update_info['bgimg'] = $bgimg_url;
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'图片上传失败'
				));
			exit;
		}
	}

    if($upload->hasFile('shareimg')){
        $upload->set('path','../uploads');
        if($upload->upload('shareimg')){
            $shareimg_url = 'uploads/'.$upload->getFileName();
            $update_info['shareimg'] = $shareimg_url;
        }else{
            echo json_encode(array(
                'status'=>'fail',
                'message'=>'图片上传失败'
            ));
            exit;
        }
    }

    $awards = $db->select('award','*',['id[>]'=>0]);
    $chance = 0;//奖品总概率
	foreach ($awards as $award) {
		$chance += $award['chance'];
	}
	if($chance==100){
		if($status!=null){
			$update_info['status'] = $status;
		}
	}else{
		$update_info['status'] = 2;//故障
	}
	

	if($name!=null){
		$update_info['name'] = $name;
	}
	if($rule!=null){
		$update_info['rule'] = $rule;
	}
	if($start!=null){
		$update_info['start'] = $start;
	}
	if($end!=null){
		$update_info['end'] = $end;
	}
	if($addon!=null){
		$update_info['addon'] = $addon;
	}
	if($theme_color!=null){
		$update_info['theme_color'] = $theme_color;
	}
	if($title_color!=null){
		$update_info['title_color'] = $title_color;
	}

	if($domain!=null){
		$update_info['domain'] = $domain;
	}
	if($api_url!=null){
		$update_info['api_url'] = $api_url;
	}
	if($fake_notice!=null){
		$update_info['fake_notice'] = $fake_notice;
	}
	if($today_no_count_notice!=null){
		$update_info['today_no_count_notice'] = $today_no_count_notice;
	}
	if($api_channel!=null){
		$update_info['api_channel'] = $api_channel;
	}
	if($appid!=null){
		$update_info['appid'] = $appid;
	}
	if($sharetitle!=null) {
	    $update_info['sharetitle'] = $sharetitle;
    }
    if($sharedesc!=null) {
        $update_info['sharedesc'] = $sharedesc;
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

