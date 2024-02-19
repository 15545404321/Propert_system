<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class Shop

{
//修改人员信息
    function beforAdminAdd($data) {
		
        if (empty($data['account'])) {

            return json(['status'=>201,'msg'=>'参数错误']);

        }

        $shop_admin = Db::name('shop_admin')->where('disable','=',1)
            ->where('account','=',$data['account'])->find();

        if (!empty($shop_admin)) {
            return json(['status'=>201,'msg'=>'用户账号名已存在，请更换新的']);
        }
        
	}

}
