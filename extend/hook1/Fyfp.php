<?php
namespace hook;
use think\exception\ValidateException;
use support\Log;
use think\facade\Db;

class Fyfp
{
    function beforShopAdd($data) {

        $fyfp = Db::name('fyfp')
            ->where('fydy_id',$data['fydy_id'])
//            ->where('fybz_id',$data['fybz_id'])
            ->where('fcxx_id',$data['fcxx_id'])
            ->column('fcxx_id');

        if (!empty($fyfp)) {
            return json(['status'=>201,'data'=>$data,'msg'=>'该房间的分配已存在']);
        }

    }

    function afterShopAdd($data) {

        $fcxx_fwlx_id = Db::name('fcxx')->where('fcxx_id',$data['fcxx_id'])->value('fwlx_id');

        Db::name('fyfp')->where('fcxx_id',$data['fcxx_id'])->update(['fwlx_id'=>$fcxx_fwlx_id]);

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);
    }

	function beforShopPlAdd($data) {

	    $louyu_id = $data['louyu_id'];

	    $danyuan_id = Db::name('louyu')->where('louyu_pid',$louyu_id)->column('louyu_id');

	    if ($data['fwlx_id'] != 1){
            $danyuan_id[] = $louyu_id;
        }

        $loucen = [];
        if ($data['zheng_fu'] == 1) {
            for ($i = $data['start_loucen'];$i <= $data['end_loucen'];$i++) $loucen[] = $i;
        } else {
            for ($i = $data['start_loucen'];$i <= $data['end_loucen'];$i++) $loucen[] = '负'.$i;
        }

	    $fcxx = Db::name('fcxx')->whereIn('louyu_id',$danyuan_id)
            ->where('fcxx_szlc','in',$loucen)
            ->where('fwlx_id',$data['fwlx_id'])->column('louyu_id','fcxx_id');

        $fcxx_ids = Db::name('fcxx')->whereIn('louyu_id',$danyuan_id)
            ->where('fwlx_id',$data['fwlx_id'])->column('fcxx_id');

	    $fyfp = Db::name('fyfp')
            ->where('fydy_id',$data['fydy_id'])
//            ->where('fybz_id',$data['fybz_id'])
            ->whereIn('fcxx_id',$fcxx_ids)
            ->column('fcxx_id');

        $fyfp_data = [];

	    foreach ($fcxx as $fcxx_key => $fcxx_item) {

	        if (in_array($fcxx_key,$fyfp)) {
               continue;
            } else {
                $fyfp_data[] = [
                    'fcxx_id'       => $fcxx_key,
                    'fybz_id'       => $data['fybz_id'],
                    'fyfp_znj'      => '',
                    'fyfp_fzxs'     => '',
                    'fydy_id'       => $data['fydy_id'],
                    'louyu_id'      => $fcxx_item,
                    'fwlx_id'       => $data['fwlx_id']
                ];
            }
        }

	    $fyfp = new \app\shop\model\Fyfp();
	    $fyfp->saveAll($fyfp_data);

        return json(['status'=>200,'data'=>$data,'msg'=>'分配成功']);
    }
	
}