<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class ShopAdmin

{

	
//修改人员信息
    function afterShopUpdate($data) {
		
        if (empty($data['shop_admin_id']) || is_array($data['shop_admin_id'])) {

            return json(['status'=>201,'msg'=>'参数错误']);

        }
		
		$ryxxup=['xqgl_id' => $data['xqgl_id']];
        Db::name('Ryxx')->where('shop_admin_id','=',$data['shop_admin_id'])->update($ryxxup);
		return json(['status'=>200,'msg'=>'同步修改员工成功']);
		
	}


}