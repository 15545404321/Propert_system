<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;


class Ghjl
{
//过户记录
    function afterShopAdd($data) {
		
        if (empty($data['ghjl_id']) || empty($data['ghjl_jiesuan']) || empty($data['member_idb']) || empty($data['member_id']) || (empty($data['fcxx_id']) && empty($data['cewei_id']))){

            return json(['status'=>201,'msg'=>'参数错误']);

        }
		
///////////////////////////////////////////////////////////////		
		// 原房主结算
		if ($data['ghjl_jiesuan'] == 1){
			
			$msg = "";
			if(!empty($data['fcxx_id'])){
				//查询抄表信息中是否有未入账的信息
				$fcxx_cbgl = Db::name("cbgl")->where('fcxx_id',$data['fcxx_id'])->where('cbgl_status',0)->column('fcxx_id');
				//查询应收数据中，是否有未交费的信
				$fcxx_yssj = Db::name("yssj")->where('fcxx_id',$data['fcxx_id'])->where('yssj_stuats',0)->column('fcxx_id');
				
				
				if (!empty($fcxx_cbgl)) {		
					$msg.= $msg."【抄表未入账】";	
				}
				
				if (!empty($fcxx_yssj)) {		
					$msg.= $msg."【相关费用没有结算】";	
				}
				
			}elseif(!empty($data['cewei_id'])){
				
				$msg.= $msg."";	
			
			}
			if(!empty($msg)){
				//如果有未结算的费用，则删除上一步添加的信息
				Db::name('ghjl')->where('ghjl_id',$data['ghjl_id'])->delete();
				return json(['status'=>201,'msg'=>$msg.'请先结算相关费用再来过户']);
			}
		
		}
///////////////////////////////////////////////////////////////		
		// 新房主结算
		if ($data['ghjl_jiesuan'] == 2){
			//更新抄表未入账的信息
			Db::name("cbgl")->where('fcxx_id',$data['fcxx_id'])->where('cbgl_status',0)->update(['member_id'=>$data['member_idb']]);
			//更新应收数据中，是否有未交费的信
			Db::name("yssj")->where('fcxx_id',$data['fcxx_id'])->where('yssj_stuats',0)->update(['member_id'=>$data['member_idb']]);
		
		}		
///////////////////////////////////////////////////////////////		
        $fcxx_data = [
            'member_id'=> $data['member_idb'],
            'fcxx_ghjl'=> 1
        ];
		//过户房产	

		$where =[];
		$where[] =['fcxx_id','=',$data['fcxx_id']];
		$where[] =['member_id','=',$data['member_id']];

        Db::name('fcxx')->where($where)->update($fcxx_data);
		
		//过户车位	
		
		$cwwhere =[];
		$cwwhere[] =['cewei_id','=',$data['cewei_id']];
		$cwwhere[] =['member_id','=',$data['member_id']];

        Db::name('cewei')->where($cwwhere)->update($fcxx_data);
        return json(['status'=>200,'msg'=>'修改成功']);

    }
	
	
	
	
	
	
	
	
//回退过户记录
    function beforShopTuihui($data) {
        if (empty($data['ghjl_id']) || is_array($data['ghjl_id'])) {

            return json(['status'=>201,'msg'=>'参数错误']);

        }
		// 如果是2，执行回退
		if ($data['gh_tui'] == 2){
				
			$where =[];
			$where[] =['ghjl_id','=',$data['ghjl_id']];
			$huit = Db::name('ghjl')->where($where)->find();
			
			
			//根据查询的过户记录数据，判断并且修改,如果是没有回退的才能修改
			if($huit['gh_tui'] == 1){
				
				 $ht_data = [
					'member_id'=> $huit['member_id']
				];
				if ($huit['fcxx_id'] > 0){
					$ress = Db::name('fcxx')->where('fcxx_id','=',$huit['fcxx_id'])->update($ht_data);//回退房产	
							
				}
					
				if ($huit['cewei_id'] > 0){
					Db::name('cewei')->where('cewei_id','=',$huit['cewei_id'])->update($ht_data);//回退车位
				}
				
				// 新房主结算
				if ($huit['ghjl_jiesuan'] == 2){
					//更新抄表未入账的信息
					Db::name("cbgl")->where('fcxx_id',$huit['fcxx_id'])->where('cbgl_status',0)->update(['member_id'=>$huit['member_id']]);
					//更新应收数据中，是否有未交费的信
					Db::name("yssj")->where('fcxx_id',$huit['fcxx_id'])->where('yssj_stuats',0)->update(['member_id'=>$huit['member_id']]);
				
				}		
				
			}
		}
	}

}