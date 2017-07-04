<?php
/**
 * User: hzq
 */
require 'Api/api.php';

if (isGet()) {
    $api = new api();
    $database = $api->get_db();
    $mobile = isset($_GET['mobile']) ? $_GET['mobile'] : null;

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
    /*var_dump($has_mobile);
    exit;*/
    if (!$has_mobile) {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '用户不存在，请注册！',
        ));
        exit;
    }

    $sql = "SELECT `user_award`.`award_id`,`user_award`.`code` FROM `user` LEFT JOIN `user_award` ON `user`.`id` = `user_award`.`user_id` WHERE `user`.`mobile` = " . $mobile." AND `user_award`.`code` != ''";
    $result = $database->query($sql)->fetchAll();
    if ($result) {
        echo json_encode(array(
            'status' => 'success',
            'message' => '查询成功',
            'awardList' => $result
        ));
        exit;
    } else {
        echo json_encode(array(
            'status' => 'fail',
            'message' => '查询失败',
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