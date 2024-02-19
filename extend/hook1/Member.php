<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;

class Member

{

/*
    同步修改关联房产，先更新所有，房产信息，
	当member_id为0的改为新member_id，
	查询房产信息，所有member_id等于我的，生成一个新的数组，
	反向更新，会员信息表的，关联房产字段，等于新的数组
*/
    function afterShopGlfangchan($data) {

      if (empty($data['member_id']) ) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }


        $fcxx_ids = explode(',',$data['fcxx_idx']);

        $fcxx_sn = '';

        if (!empty($fcxx_ids)) {
            foreach ($fcxx_ids as $fcxx_ids_item) {
                $ise_fcxx = Db::name('fcxx')->where('fcxx_id',$fcxx_ids_item)->find();

                if ($ise_fcxx['member_id'] != 0 && $ise_fcxx['member_id'] != $data['member_id']) {
                    if ($fcxx_sn == '') {
                        $fcxx_sn = $ise_fcxx['fcxx_fjbh'].'号';
                    } else {
                        $fcxx_sn = $fcxx_sn.'，'.$ise_fcxx['fcxx_fjbh'].'号';
                    }
                }
            }
        }


        $where_fcxx = [];

        $where_fcxx[] = ['fcxx_id','in',$fcxx_ids];

        $where_fcxx[] = ['member_id','=',0];

        $where_fcxx[] = ['xqgl_id','=',session('shop.xqgl_id')];

        Db::name("fcxx")->where($where_fcxx)->update(['member_id'=>$data['member_id']]);//按照数据，更新没有会员的房产

        $fcxx_xxx = Db::name('fcxx')->where('xqgl_id',session('shop.xqgl_id'))->where('member_id',$data['member_id'])->column('fcxx_id');//更新后查询这个人总共有啥房
		
		//提交啥房
		$fang_tj = $fcxx_ids;
		
		//总共有啥房		
		$fang_zg = $fcxx_xxx;
		
		//计算总共以及提交区别
		$fang_cha = array_diff($fang_zg,$fang_tj);
		
		//从新更新member的fcxx字段	
		$fang_shijitijiao = array_diff($fcxx_xxx,$fang_cha);

        //判断是否有未交费情况开始
		//	根据差别的fcxx_id,查询是否有欠费，（抄表、应收费用），如果没有欠费，则可以修改房间
		if (!empty($fang_cha)){
			$fcxx_cbgl = Db::name("cbgl")->where('fcxx_id','in',$fang_cha)->where('cbgl_status',0)->column('fcxx_id');//查询抄表信息中是否有未入账的信息
			$fcxx_yssj = Db::name("yssj")->where('fcxx_id','in',$fang_cha)->where('yssj_stuats',0)->column('fcxx_id');//查询应收数据中，是否有未交费的信息

			if (!empty($fcxx_cbgl) || !empty($fcxx_yssj)) {
				
				$fcxx_xxx_s = implode(',',$fcxx_xxx);//获取本用户所有房产信息，入库
				
        		Db::name("member")->where('member_id',$data['member_id'])->update(['fcxx_idx'=>$fcxx_xxx_s]);//更新会员表的关房产字
				
				return json(['status'=>201,'msg'=>'有抄表信息未入账、或者缴费信息未结算，禁止去除房产关联']);
				
				
			}else{
				
       			Db::name("fcxx")->where('fcxx_id','in',$fang_cha)->update(['member_id'=>0]);//更新房产信息， 
				
				$fcxx_xxx_s = implode(',',$fang_shijitijiao);//或者极差后的房产信息，入库
				
        		Db::name("member")->where('member_id',$data['member_id'])->update(['fcxx_idx'=>$fcxx_xxx_s]);//更新会员表的关房产字段
			}
		}
		
       

        if ($fcxx_sn != '') {
            return json(['status'=>201,'msg'=>$fcxx_sn.' 房产已被关联，请通过房产信息或者过户信息修改户主。']);
        }

        return json(['status'=>200,'msg'=>'同步更新房产信息表成功']);
	}






/*
同步修改车位信息，先更新所有，车位信息，
	当member_id为0的改为新member_id，
	查询，车位信息，所有member_id等于我的，生成一个新的数组，
	反向更新，车位信息，表的关联房产字段，等于新的数组
*/
    function afterShopGlchewei($data) {
/*
^ array:2 [  "member_id" => 73  "cewei_id" => "3008,3009"]
dump($data);
exit;
*/
        if (empty($data['member_id'])) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }


        $cewei_ids = explode(',',$data['cewei_id']);

        $cewei_sn = '';
        foreach ($cewei_ids as $cewei_ids_item) {
            $ise_cewei = Db::name('cewei')->where('cewei_id',$cewei_ids_item)->find();

            if ($ise_cewei['member_id'] != 0 && $ise_cewei['member_id'] != $data['member_id']) {
                if ($cewei_sn == '') {
                    $cewei_sn = $ise_cewei['cewei_name'].'号';
                } else {
                    $cewei_sn = $cewei_sn.'，'.$ise_cewei['cewei_name'].'号';
                }
            }
        }

        $where_cewei = [];

        $where_cewei[] = ['cewei_id','in',$cewei_ids];

//        $where_cewei[] = ['member_id','=',0];

        $where_cewei[] = ['xqgl_id','=',session('shop.xqgl_id')];

//        $cewei_cc = Db::name("cewei")->whereNull('member_id')->where($where_cewei)->select()->toArray();//按照数据，更新没有会员的车位

        Db::name("cewei")->whereNull('member_id')
            ->where($where_cewei)->update(['member_id'=>$data['member_id']]);//按照数据，更新没有会员的车位

        $cewei_xxx = Db::name('cewei')->where('xqgl_id',session('shop.xqgl_id'))
            ->where('member_id',$data['member_id'])->column('cewei_id');//更新后查询这个人总共有啥车位
		
		
		//提交啥车位
		$cewei_tj = $cewei_ids;
		
		//总共有啥车位	
		$cewei_zg = $cewei_xxx;
		
		//计算总共以及提交区别
		$cewei_cha = array_diff($cewei_zg,$cewei_tj);
		
		//从新更新member的cewei字段	
		$cewei_shijitijiao = array_diff($cewei_xxx,$cewei_cha);
		
	
		if (!empty($cewei_cha)){
       		Db::name("cewei")->where('cewei_id','in',$cewei_cha)->update(['member_id'=>'']);//更新cewei信息，
		}
				

        $cewei_xxx_s = implode(',',$cewei_shijitijiao);

        Db::name("member")->where('member_id',$data['member_id'])->update(['cewei_id'=>$cewei_xxx_s]);//更新会员表的关联车位字段

        if ($cewei_sn != '') {
            return json(['status'=>201,'msg'=>$cewei_sn.' 车位已被关联，请通过车位表修改户主信息。']);
        }
		
        return json(['status'=>200,'msg'=>'同步更新车位表成功']);
		

    }
	









/*
同步修改车辆信息，先更新所有，车辆信息，
	当member_id为0的改为新member_id，
	查询，车辆信息，所有member_id等于我的，生成一个新的数组，
	反向更新，车辆信息，表的关联房产字段，等于新的数组
*/
    function afterShopGlcar($data) {
/*
array:2 [  "member_id" => 73  "car_id" => "1,2,3,4,5"]
*/
		
        if (empty($data['member_id'])) {
            return json(['status'=>201,'msg'=>'参数错误']);
        }


        $car_ids = explode(',',$data['car_id']);

        $car_sn = '';
        foreach ($car_ids as $car_ids_item) {
            $ise_car = Db::name('car')->where('car_id',$car_ids_item)->find();

            if ($ise_car['member_id'] != 0 && $ise_car['member_id'] != $data['member_id']) {
                if ($car_sn == '') {
                    $car_sn = $ise_car['car_name'];
                } else {
                    $car_sn = $car_sn.'，'.$ise_car['car_name'];
                }
            }
        }

        $where_car = [];

        $where_car[] = ['car_id','in',$car_ids];

        $where_car[] = ['member_id','=',0];

        $where_car[] = ['xqgl_id','=',session('shop.xqgl_id')];

        Db::name("car")->where($where_car)->update(['member_id'=>$data['member_id']]);//按照数据，更新没有会员的车

        $car_xxx = Db::name('car')->where('xqgl_id',session('shop.xqgl_id'))->where('member_id',$data['member_id'])->column('car_id');//更新后查询这个人总共有啥车
		
		//提交啥车位
		$car_tj = $car_ids;
		
		//总共有啥车位	
		$car_zg = $car_xxx;
		
		//计算总共以及提交区别
		$car_cha = array_diff($car_zg,$car_tj);
		
		//从新更新member的car字段	
		$car_shijitijiao = array_diff($car_xxx,$car_cha);
		
	
		if (!empty($car_cha)){
       		Db::name("car")->where('car_id','in',$car_cha)->update(['member_id'=>0]);//更新car信息，
		}
			

        $car_xxx_s = implode(',',$car_shijitijiao);

        Db::name("member")->where('member_id',$data['member_id'])->update(['car_id'=>$car_xxx_s]);//更新会员表的关联车字段

        if ($car_sn != '') {
            return json(['status'=>201,'msg'=>$car_sn.'， 车辆已被关联，请通过车辆表修改车主信息。']);
        }

        return json(['status'=>200,'msg'=>'同步更新车辆表成功']);

    }


    function beforShopDelete($data) {

        $ids = explode(',',$data);

        foreach ($ids as $id) {
            $count = Db::name('yssj')->where('member_id',$id)->count();
            if (!empty($count)) {
                return json(['status'=>201,'msg'=>'选中客户有费用发生，不可删除']);
            }
        }

    }
    
}