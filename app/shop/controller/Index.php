<?php

namespace app\shop\controller;

use think\facade\Db;

class Index extends Admin {

	public function index(){

		return view('index');

	}
	//后台首页主体内容

	public function main(){
		

		if(!$this->request->isPost()){

			return view('main');

		}else{		
		
		//折线图数据
            $where=[];
            $where[] =['xqgl_id','=',session("shop.xqgl_id")];
            $where[] =['shop_id','=',session("shop.shop_id")];

            $day = [1,2,3,4,5,6,7,8,9,10,11,12];
            $day_data = [];
            foreach ($day as $day_item) {
                $month = date('Y-'.$day_item,time());
                $day_data[] = Db::name('yssj')
                    ->where($where)
                    ->whereMonth('yssj_kstime', $month)
                    ->where('yssj_stuats',1)->sum('yssj_ysje');
            }

			$echat_data['day_count'] = [

				'title'=>'月度收费',

				'day'=> $day,	//每月

				'data'=> $day_data	//每月数据

			];

			

			if(config('my.show_home_chats',true)){

				$data['echat_data'] = $echat_data;

			}


			$data['card_data'] = $this->getCardData();
			
			$data['menus'] = $this->getMenuLink();

			$data['status'] = 200;

			return json($data);

		}

	}


	//首页统计数据

	private function getCardData(){
		$where=[];
		$where[] =['xqgl_id','=',session("shop.xqgl_id")];
		$where[] =['shop_id','=',session("shop.shop_id")];
		//楼座数量
		$lznum = Db::name('louyu')->where($where)->where('louyu_pid is Null')->count();
		$glmj = Db::name('fcxx')->where($where)->sum('fcxx_jzmj');
		
		//房间数量		
		$fjnum = Db::name('fcxx')->where($where)->count();
		$fjnumz = Db::name('fcxx')->where($where)->where('member_id','<>',0)->count();
		
		//车位数量		
		$cwnum = Db::name('cewei')->where($where)->count();
		$cwnumz = Db::name('cewei')->where($where)->where('member_id is Null')->count();
		
		//车辆统计		
		$carnum = Db::name('car')->where($where)->count();
		$carnumz = Db::name('car')->where($where)->where('car_type','=',1)->count();
		
		//居民统计		
		$membernum = Db::name('member')->where($where)->count();
		$membernumz = Db::name('fcxx')->where($where)->where('member_id','<>',0)->count();

		// 本年收费
		$yssj_member_to_year_1 = Db::name('yssj')->where($where)
            ->whereYear('yssj_kstime', date('Y',time()))
            ->where('yssj_stuats',1)
            ->group('member_id')->count();

		// 本年欠费
        $yssj_member_to_year_0 = Db::name('yssj')->where($where)
            ->whereYear('yssj_kstime', date('Y',time()))
            ->where('yssj_stuats',0)
            ->group('member_id')->count();

		// 历年欠费
		$yssj_member_all_year_0 = Db::name('yssj')->where($where)
            ->where('yssj_kstime','<' , strtotime(date('Y-01-01',time())))
            ->where('yssj_stuats',0)
            ->group('member_id')->count();

		$card_data = [	//头部统计数据

			[

			  'title_icon'=>"el-icon-user",

			  'card_title'=> "楼座数量",

			  'card_cycle'=> "个",

			  'card_cycle_back_color'=> "#409EFF",

			  'bottom_title'=> "管理面积",

			  'vist_num'=> $lznum,

			  'vist_all_num'=> $glmj,

			  'vist_all_icon'=> "el-icon-trophy",

			],

			[

			  'title_icon'=> "el-icon-download",

			  'card_title'=> "房间数量",

			  'card_cycle'=> "个",

			  'card_cycle_back_color'=> "#67C23A",

			  'bottom_title'=> "入驻总量",

			  'vist_num'=> $fjnum,

			  'vist_all_num'=> $fjnumz,

			  'vist_all_icon'=> "el-icon-download",

			],

			[

			  'title_icon'=> "el-icon-wallet",

			  'card_title'=> "车位数量",

			  'card_cycle'=> "个",

			  'card_cycle_back_color'=> "#F56C6C",

			  'bottom_title'=> "使用数量",

			  'vist_num'=> $cwnum,

			  'vist_all_num'=> $cwnumz,

			  'vist_all_icon'=> "el-icon-coin",

			],

			[

			  'title_icon'=> "el-icon-coordinate",

			  'card_title'=> "车辆数量",

			  'card_cycle'=> "台",

			  'card_cycle_back_color'=> "#E6A23C",

			  'bottom_title'=> "固定车辆",

			  'vist_num'=> $carnum,

			  'vist_all_num'=> $carnumz,

			  'vist_all_icon'=> "el-icon-data-line",

			],

			[

			  'title_icon'=> "el-icon-coordinate",

			  'card_title'=> "本年收费",

			  'card_cycle'=> "户",

			  'card_cycle_back_color'=> "#9B40FF",

			  'bottom_title'=> "总用户",

			  'vist_num'=> $yssj_member_to_year_1,

			  'vist_all_num'=> $membernumz,

			  'vist_all_icon'=> "el-icon-data-line",

			],

			[

			  'title_icon'=> "el-icon-coordinate",

			  'card_title'=> "本年欠费",

			  'card_cycle'=> "户",

			  'card_cycle_back_color'=> "#37C697",

			  'bottom_title'=> "总用户",

			  'vist_num'=> $yssj_member_to_year_0,

			  'vist_all_num'=> $membernumz,

			  'vist_all_icon'=> "el-icon-data-line",

			],

			[

			  'title_icon'=> "el-icon-coordinate",

			  'card_title'=> "历年欠费",

			  'card_cycle'=> "户",

			  'card_cycle_back_color'=> "#AC13A5",

			  'bottom_title'=> "总用户",

			  'vist_num'=> $yssj_member_all_year_0,

			  'vist_all_num'=> $membernumz,

			  'vist_all_icon'=> "el-icon-data-line",

			],

			[

			  'title_icon'=> "el-icon-coordinate",

			  'card_title'=> "小区居民",

			  'card_cycle'=> "人",

			  'card_cycle_back_color'=> "#FF6266",

			  'bottom_title'=> "户主数量",

			  'vist_num'=> $membernum,

			  'vist_all_num'=> $membernumz,

			  'vist_all_icon'=> "el-icon-data-line",

			],

		];

		

		return $card_data;

	}

		//获取首页快捷导航

	private function getMenuLink(){

		if(config('my.show_home_menu',true)){

			$data = Db::name('menu')->field('title,menu_pic,controller_name,url')->where('app_id',298)->where('home_show',1)->limit(8)->select()->toArray();

			foreach($data as $k=>$v){

				if(!$v['url']){

					$data[$k]['url'] = (string)url('shop/'.str_replace('/','.',$v['controller_name']).'/index');

				}else{

					$data[$k]['url'] = $v['url'];

				}

			}

			return $data;

		}

	}

	

}