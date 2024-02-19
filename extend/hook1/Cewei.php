<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

//use \app\shop\model\Cbgl as CbglM;



class Cewei

{
//批量添加车位
    function afterShopAdd($data) {
		
        if (empty($data['cewei_id'])) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }
		$ceweiadd = [];
		if ($data['cw_pltj'] == 2){		
		
//			if ($data['cw_ks'] < $data['cw_js']){
				
				for ($i=1; $i<$data['cw_num']; $i++){
				
					$ceweiadd[] = [
						'shop_id'     => $data['shop_id'],	
						'xqgl_id'     => $data['xqgl_id'],	
						'cewei_name'  => $data['cewei_name']+$i,
						'cwlx_id'     => $data['cwlx_id'],
						'tccd_id'     => $data['tccd_id'],
						'cwqy_id'     => $data['cwqy_id'],
						'cwzt_id'     => $data['cwzt_id'],
						'cewei_cwmj'  => $data['cewei_cwmj'],
						'cewei_start_time'     => $data['cewei_start_time'],
						'cewei_end_time'     => $data['cewei_end_time'],
						'px'     => $data['cewei_name']+$i
					];	
				}
				//修改最后一个车位排序
				$cewei_px = Db::name("cewei")->field('cewei_id')->where('cewei_id',$data['cewei_id'])->value('cewei_name');
				Db::name("cewei")->where('cewei_id',$data['cewei_id'])->update(['px'=>$cewei_px]);

				$cewei = new \app\shop\model\Cewei();
				$cewei->saveAll($ceweiadd);
				
        		return json(['status'=>200,'msg'=>'批量添加车位成功']);
				
			/*}else{
				return json(['status'=>201,'msg'=>'起始编号不能小于结束编号']);
			}*/
		}

    }


    function beforShopZcgl($data) {

        $count = Db::name('yssj')->where('cewei_id','in',[$data['cewei_id']])->count();
        if (!empty($count)) {
            return json(['status'=>201,'msg'=>'该车位有费用发生，不可直接修改资产关联']);
        }

    }

}