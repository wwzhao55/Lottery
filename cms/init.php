<?php
ini_set('date.timezone','Asia/Shanghai');
session_start();
require '../Api/api.php';
$api = new api();

$db = $api->get_db();

$upload = $api->get_upload();

if($db == null){
	require '../resource/config.html';
	exit;
}

if(!isset($_SESSION['uid'])){	
	if(isAjax()){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'您还未登陆',
			));
		exit; 
	}else{
		Header("Location: login.php");
	}
}

#------活动初始化
//进行中的活动数
$active_activity_count_init = $db->count('activity',['id[>]'=>0]);
if($active_activity_count_init==0){
	$db->action(function($db){
		$new_award = [
			'name'=>'奖品名称',
			'img'=>'images/default.png',
			'desc'=>'奖品详情内容',
			'detail'=>'奖品详情名称',
			'total'=>100,
			'get_rule'=>'获取奖品规则',
			'get_url'=>'http://www.baidu.com',
			'give_out'=>0,
			'chance'=>10,
			'value'=>1000,
		];
		$db->query('truncate table award');
		for($i=1;$i<=8;$i++){
			$db->insert('award',$new_award);
		}
		$award_count = $db->count('award',['id[>]'=>0]);
		if($award_count != 8){
			return false;
		}
		$new_activity = [
			'name'=>'新建的活动',
			'status'=>0,
			'rule'=>'规则展示',
			'bgimg'=>'images/default.png',
			'start'=>time()+24*60*60,
			'end'=>time()+48*60*60,
			'title_color'=>'#ffffff',
			'theme_color'=>'#ffffff',
			'person_day_times'=>3,
			'person_total_times'=>10,
			'person_max_times'=>5,
			'domain'=>'your domain',
			'api_url'=>'your api_url',
			'fake_notice'=>'您没有中奖',
			'today_no_count_notice'=>'您今日的次数已用完',
			'api_channel'=>'WX',
			'appid'=>'your appid',
		];
		$new_activity_id = $db->insert('activity',$new_activity);
		if(!$new_activity_id){
			return false;
		}
	});
}
$result_init = $db->count('activity',['status[<]'=>3]);
$award_count = $db->count('award',['id[>]'=>0]);
if($result_init==0 || $award_count!=8){
	echo json_encode(array(
		'status'=>'fail',
		'message'=>'数据初始化失败',
		));
	exit;
}

