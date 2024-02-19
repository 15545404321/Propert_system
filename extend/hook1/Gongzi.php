<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class Gongzi

{

	
//添加工资
    function afterShopAdd($data) {		
        if (empty($data['shop_admin_id']) || empty($data['xzpici_id']) || is_array($data['shop_admin_id'])) {
            return json(['status'=>201,'msg'=>'必须填写发放人员以及发放批次']);
        }		
        $zje = Db::name('gongzi')->where('xzpici_id','=',$data['xzpici_id'])->sum('gz_jine');
        $zrs = Db::name('gongzi')->where('xzpici_id','=',$data['xzpici_id'])->count();		
		$xzpici=[
			'xz_jine' => $zje,
			'xz_ren' => $zrs,
			];		
        Db::name('Xzpici')->where('xzpici_id','=',$data['xzpici_id'])->update($xzpici);
		return json(['status'=>200,'msg'=>'同步修改批次薪资成功']);		
	}
//修改工资
	function afterShopUpdate($data) {	

        if (empty($data['shop_admin_id']) || empty($data['xzpici_id']) || is_array($data['shop_admin_id'])) {
            return json(['status'=>201,'msg'=>'必须填写发放人员以及发放批次']);
        }	
        $zje = Db::name('gongzi')->where('xzpici_id','=',$data['xzpici_id'])->sum('gz_jine');
        $zrs = Db::name('gongzi')->where('xzpici_id','=',$data['xzpici_id'])->count();		
		$xzpici=[
			'xz_jine' => $zje,
			'xz_ren' => $zrs,
			];		
        Db::name('Xzpici')->where('xzpici_id','=',$data['xzpici_id'])->update($xzpici);
		return json(['status'=>200,'msg'=>'同步修改批次薪资成功']);		
	}
//删除同步 前置钩子
	function beforShopDelete($data) {

        if (empty($data)) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }		
        $pici = Db::name('gongzi')->where('gongzi_id','=',$data)->value('xzpici_id');
		
        Db::name('gongzi')->where('gongzi_id','=',$data)->delete();	// 查完了批次后删除，否则计数不准确
		
        $zje = Db::name('gongzi')->where('xzpici_id','=',$pici)->sum('gz_jine');
        $zrs = Db::name('gongzi')->where('xzpici_id','=',$pici)->count();		
		$xzpici=[
			'xz_jine' => $zje,
			'xz_ren' => $zrs,
			];	
        Db::name('Xzpici')->where('xzpici_id','=',$pici)->update($xzpici);
		//return json(['status'=>200,'msg'=>'同步修改批次薪资成功']);		
	}
}