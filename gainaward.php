<?php
/**
 * User: hzq
 */
require 'Api/api.php';

/**
 * @param $rand_num
 * @param $num0
 * @param $award
 * @param $database
 * @param $user
 * @param $area_selected
 * @param $num1
 * @param $num2
 * @param $num3
 * @param $num4
 * @param $num5
 * @param $num6
 * @return mixed
 */
//$_SESSION['cnt'] = 0;全局变量
function ChouJiang($count, $rand_num, $num0, $award, $database, $user, $area_selected, $num1, $num2, $num3, $num4, $num5, $num6)
{
    $count++;
    $rand_num = mt_rand(1, 100);
    $award_id;
    if ($rand_num <= $num0) {
        $award_id = 0;
    } elseif ($rand_num <= $num1) {
       $award_id = 1;
    } elseif ($rand_num <= $num2) {
        $award_id = 2;
    } elseif ($rand_num <= $num3) {
        $award_id = 3;
    } elseif ($rand_num <= $num4) {
       $award_id = 4;
    } elseif ($rand_num <= $num5) {
        $award_id = 5;
    } elseif ($rand_num <= $num6) {
        $award_id = 6;
    } else {
        $award_id = 7;
    }
    if ($award[$award_id]['flag']) {//是奖品
            if ($award[$award_id]['total'] > $award[$award_id]['give_out']) {//奖品总数多于已经发出的奖品
                $redeem_code = $database->query("call gaincode(".$award[$award_id]['id'].");")->fetchAll();//如果奖品表中没有该award_id的奖品，返回false
                $redeem_code = $redeem_code[0];
                if ($redeem_code) {//有奖品
                    if (sql($database, $user['id'], $award[$award_id]['id'],  $redeem_code['code'])) {
                        $area_selected['award'] = $award[$award_id]['id'];
                        return $area_selected;
                    } else {
                        ;
                        thank();
                        return $area_selected;
                    }
                } else {//奖品表中没有该奖品了
                    ;
                    thank();
                    return $area_selected;
                }
            } else {//奖品发完了，依然抽到该奖项，
                if ($count < 4) {//给与三次再次抽奖机会
                    return ChouJiang($count, $rand_num, $num0, $award, $database, $user, $area_selected, $num1, $num2, $num3, $num4, $num5, $num6);
                } else {
                    thank();
                }
            }
        } else {//不是奖品
            sqlNotPrize($database, $user['id'], $award[$award_id]['id']);
            $area_selected['award'] = $award[$award_id]['id'];
            return $area_selected;
        }
}

if (isPost()) {
    $api = new api();
    $database = $api->get_db();
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : null;

    if (!$mobile) {
        echo json_encode(array(
            'status' => '503',
            'message' => '手机号输入不能为空',
        ));
        exit;
    }

    if (!preg_match('/^1[34578]\d{9}$/', $mobile)) {
        echo json_encode(array(
            'status' => '504',
            'message' => '手机号输入格式不正确',
        ));
        exit;
    }

    $has_mobile = $database->has('user', array('mobile' => $mobile));
    if (!$has_mobile) {
        echo json_encode(array(
            'status' => '505',
            'message' => '用户不存在，请注册！',
        ));
        exit;
    }
    $activity = $database->get('activity', ['start', 'end', 'status', 'person_day_times', 'person_total_times', 'person_max_times'], ['id[>]' => 0]);
    $time = time();//获得当前时间戳
    if ($time < $activity['start'] || $activity['status'] != 1) {
        echo json_encode(array(
            'status' => '506',
            'message' => '当前活动还未开始！',
        ));
        exit;
    }

    if ($time > $activity['end']) {
        echo json_encode(array(
            'status' => '507',
            'message' => '当前活动已经结束！',
        ));
        exit;
    }

    $user = $database->get('user', ['id', 'count', 'used_chance', 'time', 'today_chance'], ['mobile' => $mobile]);//次数

    if ($activity['person_total_times'] == $user['used_chance']) {
        echo json_encode(array(
            'status' => '508',
            'message' => '所有抽奖机会已用完！',
        ));
        exit;
    }
    $today_start = strtotime(date('Y-m-d'));//今日开始时间0点0分
    if ($user['time'] < $today_start) {//判断是否为今日，不是就把时间更新为今日的，today_chance回归为0，update一次即可
        $update = $database->update('user', ['time' => $time, 'today_chance' => 0], ['id' => $user['id']]);//1
    } elseif ($activity['person_day_times'] <= $user['today_chance']) {//比较次数
        echo json_encode(array(
            'status' => '501',
            'message' => '今日抽奖机会已用完！',
        ));
        exit;
    }
    addTimes($database, $user['id']);
    if ($activity['person_max_times'] <= $user['count']) {//已达到最大种奖次数
        thank();
    }

    $award = $database->select('award', ['id', 'name', 'total', 'give_out', 'chance', 'flag'], ['id[>]' => 0]);
    $area_selected = array();
    $num0 = $award[0]['chance'];
    $num1 = $num0 + $award[1]['chance'];
    $num2 = $num1 + $award[2]['chance'];
    $num3 = $num2 + $award[3]['chance'];
    $num4 = $num3 + $award[4]['chance'];
    $num5 = $num4 + $award[5]['chance'];
    $num6 = $num5 + $award[6]['chance'];//$award[7]['chance']到100了
    $rand_num = mt_rand(1, 100);
    $area_selected = ChouJiang(1, $rand_num, $num0, $award, $database, $user, $area_selected, $num1, $num2, $num3, $num4, $num5, $num6);
    $area_selected['status'] = 200;
    echo json_encode($area_selected);
    exit;
} else {
    echo json_encode(array(
        'status' => '510',
        'message' => '请求方式错误',
    ));
    exit;
}
//已用、今日抽奖机会都加1
function addTimes($database, $user_id)
{
    $database->update('user', ['today_chance[+]' => 1, 'used_chance[+]' => 1], ['id' => $user_id]);//1
}

//不是中奖flag=0使用
function sqlNotPrize($database, $user_id, $award_id)
{
    // Do Nothing
}

//中奖flag=1时使用
function sql($database, $user_id, $award_id, $code)
{
    $update = $database->update('award', ['give_out[+]' => 1], ['id' => $award_id]);//1

    $update1 = $database->update('user', ['count[+]' => 1], ['id' => $user_id]);//1

    //$update2 = $database->update('redeem_code', ['status' => 1], ['id' => $redeem_code_id]);//有数据可更新时，1；没有数据可更新时，0；

    $database->insert('user_award', ['user_id' => $user_id, 'award_id' => $award_id, 'code' => $code, 'time' => time()]);//返回插入数据的id
    if ($update && $update1) {
        return true;
    } else {
        return false;
    }
}

function thank()
{
    echo json_encode(array(
        'status' => 200,
        'award' => 0,
    ));
    exit;
}