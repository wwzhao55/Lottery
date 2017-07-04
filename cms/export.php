<?php
require 'init.php';
$activity = $db->get('activity','*',['id[>]'=>0]);
//获取奖品信息
if(isGet()){
	if(isAjax()){
		$page = isset($_GET['page'])?$_GET['page']:1;
		$count = $db->count('user_award',['id[>]'=>0]);
		$from = ($page-1)*10;
		$lists = $db->select('user_award','*',[
			'AND'=>['id[>]'=>0],
			'LIMIT'=>[$from,10]
			]);
		foreach ($lists as &$list) {
			$list['mobile'] = $db->get('user','mobile',['id'=>$list['user_id']]);
			$list['award_name'] = $db->get('award','name',['id'=>$list['award_id']]);
		}

		if($lists){
			echo json_encode(array(
				'status'=>'success',
				'message'=>'获取成功',
				'data'=>$lists,
				'count'=>$count,
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
		$export = isset($_GET['export'])?$_GET['export']:null;
		if($export==null){
			$main_html = file_get_contents('../resource/cms/export.html');
			require '../resource/cms/layer.html';
			exit;
		}else{
			$count = $db->count('user_award',['id[>]'=>0]);
			$times = intval($count/10000);
			file_put_contents("../uploads/user_award.csv","");
			$fp = fopen("../uploads/user_award.csv","a"); //打开csv文件，如果不存在则创建
			fwrite($fp,iconv('UTF-8' ,'gbk' ,"手机号,OpenID,奖品名称,兑换码,获奖时间\r\n" )); //写入数据
			for($i=0;$i<=$times;$i++){				
				$lists = $db->select('user_award','*',[
					'AND'=>['id[>]'=>0],
					'LIMIT'=>[$i*10000+1,10000]
					]);
				foreach ($lists as $list) {
					$data = array();
					$data['mobile']= $db->get('user','mobile',['id'=>$list['user_id']]);
					$data['openid'] = $db->get('user','openid',['id'=>$list['user_id']]);
					$data['award_name'] = $db->get('award','name',['id'=>$list['award_id']]);
					$data['code'] = $list['code'];
					$data['time'] = date('Y-m-d',$list['time']);
					$str = implode(',',$data);
					$str .="\r\n";
					fwrite($fp,iconv('UTF-8' ,'gbk' ,$str )); //写入数据
				}
			}
			fclose($fp); //关闭文件句柄
			header("Location:../uploads/user_award.csv");
		}
	}
}else if(isPost()){
	
}