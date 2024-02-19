<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class Xzpici

{
//批量添加结算
    function afterShopAdd($data) {

        if (empty($data['xzpici_id']) || is_array($data['xzpici_id'])) {

            return json(['status'=>201,'msg'=>'参数错误']);

        }
		//查询当前物业小区的应发工资人员信息
		$where=[];
		$where[]=['ryxx_zaizhi','=',1];
		$where[] = ['shop_id','=',session('shop.shop_id')];
		$where[] = ['xqgl_id','=',$data['xqgl_id']];
        $zz_yuangong = Db::name('ryxx')->where($where)->field('shop_admin_id,ryxx_xinzi')->select();
		
  		$xz_pici_data=[];
		$I=0;
		$GZ=0;
		//循环加入数组
		foreach ($zz_yuangong as $vo) {
			$xz_pici_data[] = [
	
				'shop_id'    		 => session('shop.shop_id'), 
				'xqgl_id'    		 => $data['xqgl_id'], 
				'shop_admin_id'    	 => $vo['shop_admin_id'],
				'xz_ffdate'    	 => $data['xz_ffdate'],
				'gz_zhouqi'    	 => $data['xz_zhouqi'],
				'gz_jine'    	 => $vo['ryxx_xinzi'],
				
				'xzpici_id'    	 => $data['xzpici_id'],
				'gz_kqsh'    	 => 0,
				'gz_kjsh'    	 => 0,
				'gz_zjlsh'    	 => 0,
				'addtime'    	 => $data['addtime']
				
			];
		$I=$I+1;
		$GZ=$GZ+$vo['ryxx_xinzi'];
		}
		
		//修改批次人员、工资总和
		$xg_xz=[
			'xz_ren'=>$I,
			'xz_jine'=>$GZ
		];
        Db::name('Xzpici')->where('xzpici_id','=',$data['xzpici_id'])->update($xg_xz);
		
		$Gongzi = new \app\shop\model\Gongzi();
		$Gongzi->saveAll($xz_pici_data);
        return json(['status'=>200,'msg'=>'批量添加结算成功']);

    }
	
//批量修改结算
    function afterShopUpdate($data) {
		$Gongzi=[
		
				'xz_ffdate'    	 => $data['xz_ffdate'],
				'gz_zhouqi'    	 => $data['xz_zhouqi']
		
		];
        Db::name('Gongzi')->where('xzpici_id','=',$data['xzpici_id'])->update($Gongzi);
		return json(['status'=>200,'msg'=>'批量修改结算成功']);
		
	}
	
//批量删除结算
    function beforShopDelete($data) {
        $pici = Db::name('Gongzi')->where('xzpici_id','=',$data)->find();
//		dump($pici);
//		exit;
		if ($pici){
			return json(['status'=>200,'msg'=>'不可以删除']);
		}
	}


}