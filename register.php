<?php
/**
 * User: hzq
 */
require 'Api/api.php';

if (isPost()) {

    $api = new api();
    $database = $api->get_db();

    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;
    $openid = isset($_POST['openid']) ? $_POST['openid'] : null;

    if (!$mobile) {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '手机号输入不能为空',
        ));
        exit;
    }

    if (!preg_match('/^1[34578]\d{9}$/', $mobile)) {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '手机号输入格式不正确',
        ));
        exit;
    }

    $has_mobile = $database->has('user', array('mobile' => $mobile));
    if ($has_mobile) {
        $user_info = $database->get('user', '*', array('mobile' => $mobile));
        $person_day_times = $database->get('activity','person_day_times',['id[>]'=>0]);

        $today_timestamp = strtotime(date('Y-m-d'));
        if($today_timestamp<$user_info['time']){
            $today_count = $person_day_times - $user_info['today_chance'];
            if ($today_count < 0) $today_count = 0;
        }else{
            $today_count = $person_day_times;
        }
        echo json_encode(array(
            'status' => 'fail',
            'message' => '您的手机号已经注册过啦！',
            'today_count'=>$today_count,
        ));
        exit;
    }

    $result = $database->insert('user', array(
        'mobile' => $mobile,
        'openid' => $openid,
        'count' => 0,
        'used_chance' => 0,
        'time' => time(),
        'today_chance' => 0
    ));
    
    if ($result) {
        echo json_encode(array(
            'status' => 'success',
            'message' => '注册成功',
        ));
        exit;
    } else {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '注册失败！网络错误',
        ));
        exit;
    }
} else {
    echo json_encode(array(
        'status' => 'fail',
        'message' => '请求方式错误',
    ));
    exit;
}
