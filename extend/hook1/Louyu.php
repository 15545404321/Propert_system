<?php

namespace hook;

use think\exception\ValidateException;

use support\Log;

use think\facade\Db;



class Louyu

{

	function afterShopAdd($data){

	    for ($i = 1; $i <= $data['louyu_dysl']; $i++) {
			

	        $danyuan = [

	            'shop_id' => $data['shop_id'],

                'xqgl_id' => $data['xqgl_id'],

                'louyu_pid' => $data['louyu_id'],

                'louyu_name' => $i."单元",

                'louyu_lyqz' => $data['louyu_name']

            ];

	        $danyuan_id = Db::name('louyu')->insertGetid($danyuan);

            $fangjianadd = [];
			
			//底商添加开始
			
			if ($i==1 && $data['louyu_dscs']>0 && $data['louyu_ycjh']>0){
				
				for ($dscs = 1; $dscs <= $data['louyu_dscs']; $dscs++) {
					
					for ($ycjh = 1; $ycjh <= $data['louyu_ycjh']; $ycjh++) {
						
						if ($ycjh < 10) {
                            $fjbh = ''.$dscs.'-0'.$ycjh;
                        } else {
                            $fjbh = ''.$dscs.'-'.$ycjh;
                        }
						
						$fangjianadd[] = [
							'shop_id'	=> $data['shop_id'],
							'xqgl_id' 	=> $data['xqgl_id'],
							'louyu_id' 	=> $data['louyu_id'],
							'fcxx_jzmj' => 0,
							'fcxx_tnmj'	=> 0,
							'fwlx_id'	=> 2,
							'member_id'	=> 0,
							'fcxx_ghjl'	=> 0,
							'fcxx_szlc'	=> $dscs,
							'fcxx_fjbh'	=> $fjbh,
                            'fcxx_lcpx'	=> $dscs,
						];

					}
					
				}
			
			}
			//底商添加结束

            //负楼层添加开始
            if ($data['louyu_flcs']>0){

                for ($flc = 1; $flc <= $data['louyu_flcs']; $flc++) {
                    $flc_txt = '负'.$flc;
                    for ($chzs = 1; $chzs <= $data['louyu_chzs']; $chzs++) {
                        if ($chzs < 10) {
                            $fjbh = $flc_txt.'0'.$chzs;
                        } else {
                            $fjbh = $flc_txt.$chzs;
                        }

                        $fangjianadd[] = [
                            'shop_id'	=> $data['shop_id'],
                            'xqgl_id' 	=> $data['xqgl_id'],
                            'louyu_id' 	=> $danyuan_id,
                            'fcxx_jzmj' => 0,
                            'fcxx_tnmj'	=> 0,
                            'fwlx_id'	=> 1,
                            'member_id'	=> 0,
                            'fcxx_ghjl'	=> 0,
                            'fcxx_szlc'	=> $flc_txt,
                            'fcxx_fjbh'	=> $fjbh,
                            'fcxx_lcpx'	=> $flc,
                        ];
                    }


                }
            }
			//负楼层添加结束
			
			//楼层添加开始
	        for ($lc = $data['louyu_dscs']+1; $lc <= $data['louyu_lczs']; $lc++) {
				
				
				//户数循环
                for ($chzs = 1; $chzs <= $data['louyu_chzs']; $chzs++) {

                    if ($chzs < 10) {
                        $fjbh = ''.$lc.'0'.$chzs;
                    } else {
                        $fjbh = ''.$lc.$chzs;
                    }

                    //添加房间
                    $fangjianadd[] = [
                        'shop_id'	=> $data['shop_id'],
                        'xqgl_id' 	=> $data['xqgl_id'],
                        'louyu_id' 	=> $danyuan_id,
                        'fcxx_jzmj' => 0,
                        'fcxx_tnmj'	=> 0,
                        'fwlx_id'	=> 1,
                        'member_id'	=> 0,
                        'fcxx_ghjl'	=> 0,
                        'fcxx_szlc'	=> $lc,
                        'fcxx_fjbh'	=> $fjbh,
                        'fcxx_lcpx'	=> $lc,
                    ];

                }

	        }
			//楼层添加结束

	        $fcxx = new \app\shop\model\Fcxx();
            $fcxx->saveAll($fangjianadd);
	    }

        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);

	}


	//追加单元
	function afterShopJiaDy($data) {

        $dy_count = Db::name('louyu')->where('louyu_pid',$data['louyu_id'])->count();

        $louyu_info = Db::name('louyu')->where('louyu_id',$data['louyu_id'])->find();

        for ($i = $dy_count+1; $i <= $dy_count + $data['louyu_dysl']; $i++) {

            $danyuan = [

                'shop_id' => $louyu_info['shop_id'],

                'xqgl_id' => $louyu_info['xqgl_id'],

                'louyu_pid' => $data['louyu_id'],

                'louyu_name' => $i."单元",

                'louyu_lyqz' => $louyu_info['louyu_name']

            ];

           $dy_id = Db::name('louyu')->insertGetid($danyuan);
		   
		     //追加负楼层添加开始
			 
			$fangjianadd = [];
			 
            if ($louyu_info['louyu_flcs']>0){

                for ($flc = 1; $flc <= $louyu_info['louyu_flcs']; $flc++) {
                    $flc_txt = '负'.$flc;
                    for ($chzs = 1; $chzs <= $louyu_info['louyu_chzs']; $chzs++) {
                        if ($chzs < 10) {
                            $fjbh = $flc_txt.'0'.$chzs;
                        } else {
                            $fjbh = $flc_txt.$chzs;
                        }
                        $fangjianadd[] = [
                            'shop_id'	=> $louyu_info['shop_id'],
                            'xqgl_id' 	=> $louyu_info['xqgl_id'],
                            'louyu_id' 	=> $dy_id,
                            'fcxx_jzmj' => 0,
                            'fcxx_tnmj'	=> 0,
                            'fwlx_id'	=> 1,
                            'member_id'	=> 0,
                            'fcxx_ghjl'	=> 0,
                            'fcxx_szlc'	=> $flc_txt,
                            'fcxx_fjbh'	=> $fjbh,
                            'fcxx_lcpx'	=> $flc,
                        ];
                    }


                }
            }
			//追加负楼层添加结束
			
			//追加楼层添加开始
	        for ($lc = $louyu_info['louyu_dscs']+1; $lc <= $louyu_info['louyu_lczs']; $lc++) {				
				
				//户数循环
                for ($chzs = 1; $chzs <= $louyu_info['louyu_chzs']; $chzs++) {

                    if ($chzs < 10) {
                        $fjbh = ''.$lc.'0'.$chzs;
                    } else {
                        $fjbh = ''.$lc.$chzs;
                    }

                    //添加房间
                    $fangjianadd[] = [
                        'shop_id'	=> $louyu_info['shop_id'],
                        'xqgl_id' 	=> $louyu_info['xqgl_id'],
                        'louyu_id' 	=> $dy_id,
                        'fcxx_jzmj' => 0,
                        'fcxx_tnmj'	=> 0,
                        'fwlx_id'	=> 1,
                        'member_id'	=> 0,
                        'fcxx_ghjl'	=> 0,
                        'fcxx_szlc'	=> $lc,
                        'fcxx_fjbh'	=> $fjbh,
                        'fcxx_lcpx'	=> $lc,
                    ];

                }

	        }
			//追加楼层添加结束
			$fcxx = new \app\shop\model\Fcxx();
            $fcxx->saveAll($fangjianadd);

        }

		


        return json(['status'=>200,'data'=>$data,'msg'=>'添加成功']);

    }
	
	//前置钩子作废
	function beforShopDelete($data){
		
	}

	//同步删除房间
	function afterShopDelete($data){
		$shuzu = Db::name('louyu')->where('louyu_id','=',$data)->whereOr('louyu_pid','=',$data)->column('louyu_id');
		$shuzu[]=$data;
		$where=[];
		$where[]=['louyu_id','in',$shuzu];
		$chaxun = Db::name('fcxx')->where($where)->delete();	
		
		return json(['status'=>200,'data'=>$data,'msg'=>'同步删除成功']);
		
	}

	//追加楼层
	function beforShopJiaLc($data)
    {
        $dy_num = Db::name('louyu')->where('louyu_pid', $data['louyu_id'])->column('louyu_id');
        $louyu_info = Db::name('louyu')->where('louyu_id', $data['louyu_id'])->find();

        $fangjianadd = [];
        foreach ($dy_num as $dy_num_item) {

            for ($lc = $louyu_info['louyu_lczs'] + 1; $lc <= $louyu_info['louyu_lczs'] + $data['louyu_lczs'];  $lc++) {

                //户数循环
                for ($chzs = 1; $chzs <= $louyu_info['louyu_chzs']; $chzs++) {

                    if ($chzs < 10) {
                        $fjbh = ''.$lc.'0'.$chzs;
                    } else {
                        $fjbh = ''.$lc.$chzs;
                    }

                    //添加房间
                    $fangjianadd[] = [
                        'shop_id'	=> $louyu_info['shop_id'],
                        'xqgl_id' 	=> $louyu_info['xqgl_id'],
                        'louyu_id' 	=> $dy_num_item,
                        'fcxx_jzmj' => 0,
                        'fcxx_tnmj'	=> 0,
                        'fwlx_id'	=> 1,
                        'member_id'	=> 0,
                        'fcxx_ghjl'	=> 0,
                        'fcxx_szlc'	=> $lc,
                        'fcxx_fjbh'	=> $fjbh,
                        'fcxx_lcpx'	=> $lc,
                    ];

                }
            }

        }

        $fcxx = new \app\shop\model\Fcxx();
        $fcxx->saveAll($fangjianadd);

    }

    
	function afterShopUpdate($data)
    {
        if (empty($data['louyu_id'])) {
            return json(['status'=>201,'data'=>$data,'msg'=>'参数错误']);
        }
        Db::name('louyu')->where('louyu_pid',$data['louyu_id'])->update(['louyu_lyqz'=>$data['louyu_name']]);
        return json(['status'=>200,'data'=>$data,'msg'=>'操作完成']);
    }

}