<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;


class Xqgl

{
//新增小区
    function beforShopAdd($data) {

		$sx = Db::name('shop')->where('shop_id',$data['shop_id'])->field('restrict_num')->find();
		$sxs = Db::name('xqgl')->where('shop_id',$data['shop_id'])->count();
		if ($sx['restrict_num'] <= $sxs){
			return json(['status'=>201,'msg'=>'已经达到上限']);		
		}

    }
//修改小区
	function beforShopUpdate($data){
		
		$sx = Db::name('shop')->where('shop_id',$data['shop_id'])->field('restrict_num')->find();
		$sxs = Db::name('xqgl')->where('shop_id',$data['shop_id'])->count();
		if ($sx['restrict_num'] < $sxs){
			return json(['status'=>201,'msg'=>'已经达到上限,请删除多余小区或者联系管理员']);		
		}	
	}

}