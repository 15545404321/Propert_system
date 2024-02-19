<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class Yajin

{
	//操作押金
	function afterShopUpdate($data){

		if (empty($data['zjys_id']) || empty($data['tui_status'])){

            return json(['status'=>201,'msg'=>'参数错误']);

        }
		$zjys = Db::name('zjys')->alias('a')
		->field('a.*,b.fybz_name')		
		->leftJoin('fybz b','a.fybz_id = b.fybz_id')
		->where('a.zjys_id',$data['zjys_id'])->find();

        //如果修改状态tui_status==1  不操作
        //如果修改状态tui_status==2  退回
        //如果修改状态tui_status==3  转预收款
        //入应收数据，并且收款状态为已收款
	
		if ($data['tui_status'] == 2){
			//1.入收银台扣款
			$syt_data=[
				 'fcxx_id'			=> $zjys['fcxx_id'],
				 'syt_method'		=> 0,
				 'zkgl_id'			=> 0,
				 'syt_invoice'     	=> 0,
				 'syt_skje'     	=> '-'.$zjys['zjys_bcys'],
				 'syt_zfsj'     	=> $zjys['tui_time'],
				 'syt_zlje'     	=> 0,
				 'syt_bz'     		=> "退款,押金-".$data['zjys_id'],
				 'shop_id'    		=> session('shop.shop_id'),
				 'xqgl_id'     		=> session('shop.xqgl_id'),
				 'member_id'    	=> $zjys['member_id'],
			];
			$sytid = Db::name('syt')->insertGetid($syt_data);
			//2.入应收记录扣款
			$yssj_data= [
				'yssj_fymc'     => $zjys['fybz_name'],
				'yssj_cwyf'     => date('Y-m',$zjys['tui_time']),
				'yssj_kstime'   => $zjys['zjys_jtime'],
				'yssj_jztime'   => $zjys['tui_time'],
				'yssj_fydj'     => $zjys['zjys_dcys'],
				'yssj_ysje'     => '-'.$zjys['zjys_bcys'],
				'fylx_id'       => 5,
				'fybz_id'       => $zjys['fybz_id'],
				'yssj_stuats'   => 1,
				'yssj_fksj'     => $zjys['tui_time'],
				'scys_id'       => 0,
				'shop_id'       => session('shop.shop_id'),
				'xqgl_id'       => session('shop.xqgl_id'),
				'fcxx_id'       => $zjys['fcxx_id'],
				'member_id'     => $zjys['member_id'],
				'sjlx_id'       => 1,
				'syt_id'        => $sytid,
				'zjys_id'       => $zjys['zjys_id']
			];
			Db::name('yssj')->insertGetid($yssj_data);
		}
		//如果修改状态tui_status==3  转预收款
		if ($data['tui_status'] == 3){
            $fydy = Db::name('fydy')->where('fylx_id', 3)->find();

            if (empty($fydy['fydy_name'])) {

                Db::name('zjys')->where('zjys_id',$data['zjys_id'])->update([
                    'tui_status' => 1,
                    'tui_time' => '',
                    'tui_beizhu' => '',
                ]);

                return json(['status'=>201,'msg'=>'费用定义中未定义预收款']);
            }
			//1.入收银台扣款
			$syt_data=[
				 'fcxx_id'			=> $zjys['fcxx_id'],
				 'syt_method'		=> 0,
				 'zkgl_id'			=> 0,
				 'syt_invoice'     	=> 0,
				 'syt_skje'     	=> 0,
				 'syt_zfsj'     	=> $zjys['tui_time'],
				 'syt_zlje'     	=> 0,
				 'syt_bz'     		=> "押金转预存款, 金额：".$zjys['zjys_bcys'],
				 'shop_id'    		=> session('shop.shop_id'),
				 'xqgl_id'     		=> session('shop.xqgl_id'),
				 'member_id'    	=> $zjys['member_id'],
			];
			$sytid = Db::name('syt')->insertGetid($syt_data);

            // 入应收记录扣款
			$yssj_data1 = [
				'yssj_fymc'     => $zjys['fybz_name'],
				'yssj_cwyf'     => date('Y-m',$zjys['tui_time']),
				'yssj_kstime'   => $zjys['zjys_jtime'],
				'yssj_jztime'   => $zjys['tui_time'],
				'yssj_fydj'     => $zjys['zjys_dcys'],
				'yssj_ysje'     => '-'.$zjys['zjys_bcys'],
				'fylx_id'       => 5,
				'fybz_id'       => $zjys['fybz_id'],
				'yssj_stuats'   => 1,
				'yssj_fksj'     => $zjys['tui_time'],
				'scys_id'       => 0,
				'shop_id'       => session('shop.shop_id'),
				'xqgl_id'       => session('shop.xqgl_id'),
				'fcxx_id'       => $zjys['fcxx_id'],
				'member_id'     => $zjys['member_id'],
				'sjlx_id'       => 1,
				'syt_id'        => $sytid,
				'zjys_id'       => $zjys['zjys_id']
			];
			Db::name('yssj')->insertGetid($yssj_data1);
			/*//1.入收银台加款
			$syt_data=[
				 'fcxx_id'			=> $zjys['fcxx_id'],
				 'syt_method'		=> 0,
				 'zkgl_id'			=> 0,
				 'syt_invoice'     	=> 0,
				 'syt_skje'     	=> $zjys['zjys_bcys'],
				 'syt_zfsj'     	=> $zjys['tui_time'],
				 'syt_zlje'     	=> 0,
				 'syt_bz'     		=> "转预存,存入-".$data['zjys_id'],
				 'shop_id'    		=> session('shop.shop_id'),
				 'xqgl_id'     		=> session('shop.xqgl_id'),
				 'member_id'    	=> $zjys['member_id'],
			];
			$sytid = Db::name('syt')->insertGetid($syt_data);*/
			// 入应收记录加款
			$yssj_data2 = [
				'yssj_fymc'     => $fydy['fydy_name'],
				'yssj_cwyf'     => date('Y-m',$zjys['tui_time']),
				'yssj_kstime'   => $zjys['zjys_jtime'],
				'yssj_jztime'   => $zjys['tui_time'],
				'yssj_fydj'     => '',
				'yssj_ysje'     => $zjys['zjys_bcys'],
				'fylx_id'       => 3,
				'fybz_id'       => '',
				'yssj_stuats'   => 1,
				'yssj_fksj'     => $zjys['tui_time'],
				'scys_id'       => 0,
				'shop_id'       => session('shop.shop_id'),
				'xqgl_id'       => session('shop.xqgl_id'),
				'fcxx_id'       => $zjys['fcxx_id'],
				'member_id'     => $zjys['member_id'],
				'sjlx_id'       => 1,
				'syt_id'        => $sytid,
				'zjys_id'       => $zjys['zjys_id']
			];
			Db::name('yssj')->insertGetid($yssj_data2);
		}	
	
	}
	


}