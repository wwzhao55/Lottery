<?php
require 'init.php';
$activity = $db->get('activity','*',['id[>]'=>0]);
//获取奖品信息
if(isGet()){
	if(isAjax()){
		$awards = $db->select('award','*',['id[>]'=>0]);		
		if($awards){
			echo json_encode(array(
				'status'=>'success',
				'message'=>'获取成功',
				'data'=>$awards,
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
		$main_html = file_get_contents('../resource/cms/award.html');
		require '../resource/cms/layer.html';
		exit;
	}
	
}else if(isPost()){
	$id = isset($_POST['id'])?$_POST['id']:null;
	$name = isset($_POST['name'])?$_POST['name']:null;
	$detail = isset($_POST['detail'])?$_POST['detail']:null;
	$desc = isset($_POST['desc'])?$_POST['desc']:null;
	$total = isset($_POST['total'])?$_POST['total']:null;
	$chance = isset($_POST['chance'])?$_POST['chance']:null;
	$flag = isset($_POST['flag'])?$_POST['flag']:null;
	$get_rule = isset($_POST['get_rule'])?$_POST['get_rule']:null;
	$get_url = isset($_POST['get_url'])?$_POST['get_url']:null;
	
	$update_info = [];

	if($id==null){
		echo json_encode(array(
			'status'=>'fail',
			'message'=>'id不能为空'
			));
		exit;
	}

	if($get_rule!=null){
		$update_info['get_rule'] = $get_rule;
	}
	if($get_url!=null){
		$update_info['get_url'] = $get_url;
	}
	if($name!=null){
		$update_info['name'] = $name;
	}
	if($desc!=null){
		$update_info['desc'] = $desc;
	}
	if($total!=null){
		$update_info['total'] = $total;
	}
	if($chance!=null){
		$update_info['chance'] = $chance;
	}	
	if($detail!=null){
		$update_info['detail'] = $detail;
	}
	if($flag!=null){
		$update_info['flag'] = $flag;
	}	
	if($upload->hasFile('img')){
		$upload->set('path','../uploads');
		if($upload->upload('img')){
			$img_url = 'uploads/'.$upload->getFileName();
			$update_info['img'] = $img_url;
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'图片上传失败'
				));
			exit;
		}
	}

	if($upload->hasFile('redeem_code')){
		$upload->set('path','../uploads/redeem_code');
		if($upload->upload('redeem_code')){
			$img_url = '../uploads/redeem_code/'.$upload->getFileName();
			$redeem_codes = file_get_contents($img_url);
			$is_window_option = strstr($redeem_codes,"\r\n");
			if($is_window_option){
				$redeem_code_array = explode("\r\n", $redeem_codes);
			}else{
				$redeem_code_array = explode("\n", $redeem_codes);
			}
			

			$db->action(function($db) use($redeem_code_array,$update_info,$id){
				foreach ($redeem_code_array as $redeem_code) {
					if ($redeem_code != '')	{
						if(strlen($redeem_code)){
							$redeem_code = substr($redeem_code,0,20);
						}								
						$db->insert('redeem_code',['award_id'=>$id,'code'=>$redeem_code,'status'=>0]);
					}
						
				}
				$db->update('award',$update_info,['id'=>$id]);
				
				$awards = $db->select('award','*',['id[>]'=>0]);
			    $chance = 0;//奖品总概率
				foreach ($awards as $award) {
					$chance += $award['chance'];
				}
				if($chance!=100){
					$db->update('activity',['status'=>2],['id[>]'=>0]);
				}else{
					$db->update('activity',['status'=>0],['id[>]'=>0]);
				}								
			});
			echo json_encode(array(
					'status'=>'success',
					'message'=>'数据更新成功',
					));
				exit;
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'数据更新失败',
				));
			exit;
			
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'文件上传失败',
				));
			exit;
		}
	}else{
		$result = $db->update('award',$update_info,['id'=>$id]);
		if($result){
			$activity_info = $db->get('activity','*',['status[<]'=>3]);
			$awards = $db->select('award','*',['id[>]'=>0]);
		    $chance = 0;//奖品总概率
			foreach ($awards as $award) {
				$chance += $award['chance'];
			}
			if($chance!=100){
				$db->update('activity',['status'=>2],['id[>]'=>0]);
			}else{
				$db->update('activity',['status'=>0],['id[>]'=>0]);
			}
			create_template($activity_info,$awards);
			echo json_encode(array(
				'status'=>'success',
				'message'=>'数据更新成功',
				));
			exit;
		}else{
			echo json_encode(array(
				'status'=>'fail',
				'message'=>'数据更新失败!',
				));
			exit;
		}
	}			

}