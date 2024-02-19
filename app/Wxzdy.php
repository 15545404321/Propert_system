<?php
namespace app;
error_reporting(E_ERROR | E_PARSE);
use think\facade\Db;
/* 哲 - 开发 2023-2-9 自定义菜单 */
class Wxzdy {
	/*
		详见文档:https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Creating_Custom-Defined_Menu.html
		button			一级菜单数组，个数应为1~3个
		sub_button		二级菜单数组，个数应为1~5个
		name 			菜单名称 菜单标题，不超过16个字节，子菜单不超过60个字节
		type: view 跳转网页， miniprogram 跳转小程序， click 点击事件 [ 点击弹出文本 | 点击弹出图片]
			view: 			name[导航名],url[跳转地址]
			miniprogram: 	name[导航名],url[固定值:http://mp.weixin.qq.com],appid[小程序appid],pagepath[小程序进入路径]
			click:			name[导航名],key[带入事件名]
			media_id:		name[导航名],media_id[公众号端图片id]
		
		数据库设计:
		*	微信自定义菜单配置表 shop_wx_config
				shop_wx_config_id
				shop_id
				sort 		排序 
				title 		标题:如:							一级菜单,跳转网页,打开小程序...
				pid 		上级id:如:							[0顶级菜单, 非0即为上级id]
				type 		类型:如:							[0顶级菜单, 1跳转链接, 2点击内容, 3跳转小程序, 4展示图片]
				url			跳转:								http://jfjc3.ivimoo.com/
				xcx_appid	小程序:appid:如: 					wxf9281e0e5a42803d
				xcx_url 	小程序:地址:如: 					/pages/index/index
				content		内容:如:							欢迎光临公众号
				cont_code	内容:文本交互code:如:				V001_TEXT
				pic 		图片:本地图片地址:如:				http://jfjc3.ivimoo.com/pic.png
				media_id 	图片:公众号端的图片唯一id:如: 		xzVnfvKShGYhKqitGs4rk1b_N67OtO13Kcy-Gb9LovU_QVnyLFw0UGCvV4VVgLII
		上传菜单调用方法:
			use zhe\Wxzdy;
			$rs = new Wxzdy($getAccessToken,$shop_id);
			$rs2 = $rs->action();
	*/
	private $shop_wx_config = 			"shop_wx_config";		// 数据名-表名
	private $shop_wx_config_id = 		"shop_wx_config_id";	// 数据库id-字段
	private $shop_id = 					"shop_id";				// 店铺id-字段
	private $sort_ = 					"sort";					// 排序-字段
	private $pid = 						"pid";					// 上级id-字段 [0顶级菜单, 非0即为上级id]
	private $name = 					"title";				// 导航名称-字段
	private $type = 					"type";					// 类型-字段 [0顶级菜单=type0, 1跳转链接=type1, 2点击内容=type2, 3跳转小程序=type3, 4展示图片=type4]
	private $type_arr = ["type0"=>0,"type1"=>1,"type2"=>2,"type3"=>3,"type4"=>4];
	private $view_url = 				"url";					// 跳转:跳转-字段
	private $xcx_appid = 				"xcx_appid";			// 小程序:小程序appid-字段
	private $xcx_pagepath = 			"xcx_url";				// 小程序:小程序路径-字段
	private $click_text = 				"content";				// 内容:点击内容-字段
	private $click_code = 				"cont_code";			// 内容:点击code-字段
	private $pic_media_id = 			"media_id";				// 图片:media_id-字段
	
	private $xcx_url = "http://mp.weixin.qq.com";				// 小程序跳转固定地址
	
	private $access_token = '';									// 临时秘钥
	private $json_arr = [];										// 准备上传导航
	
	public function __construct($access_token,$shop_id_val) {

		$this->access_token = $access_token?:'';
		$arr = [];			// 最终数组
		// 查询顶级菜单
		$wx = Db::name($this->shop_wx_config)->where($this->shop_id,$shop_id_val)
            ->where($this->pid,0)->order($this->sort_,'asc')->select()->toArray();

		foreach ($wx as $k => $v) {
			// 查询子菜单
			$wxs = Db::name($this->shop_wx_config)
                ->where($this->shop_id,$shop_id_val)
                ->where($this->pid,$v[$this->shop_wx_config_id])
                ->order($this->sort_,'asc')->select()->toArray();

			if (count($wxs) == 0) {		// 顶级
				$arr[] = $this->caidan($v);
			} else {	// 子级
				$zi = [];
				foreach ($wxs as $kk => $vv) {
					$zi[] = $this->caidan($vv);
				}
				$arr[] = ['name'=>$v[$this->name],'sub_button'=>$zi];
			}
		}
		$arr = ['button'=>$arr];
//        dump($arr);exit;
        $this->json_arr = $arr?:[];
	}
	// 菜单类型返回
	public function caidan($arr) {
		$rs = [];
		if ($arr[$this->type] == $this->type_arr['type1']) {
			$rs['type'] = 'view';
			$rs['name'] = $arr[$this->name];
			$rs['url'] = $arr[$this->view_url];
		} else if ($arr[$this->type] == $this->type_arr['type2']) {
			$rs['type'] = 'click';
			$rs['name'] = $arr[$this->name];
			$rs['key'] = $arr[$this->click_code];
		} else if ($arr[$this->type] == $this->type_arr['type3']) {
			$rs['type'] = 'miniprogram';
			$rs['name'] = $arr[$this->name];
			$rs['url'] = $this->xcx_url;
			$rs['appid'] = $arr[$this->xcx_appid];
			$rs['pagepath'] = $arr[$this->xcx_pagepath];
		} else if ($arr[$this->type] == $this->type_arr['type4']) {
			$rs['type'] = 'media_id';
			$rs['name'] = $arr[$this->name];
			$rs['media_id'] = $arr[$this->pic_media_id];
		}
		return $rs;
	}
	// 添加菜单
	public function action(){
		$json = self::_json_encode_($this->json_arr);
//		dump($json);exit;
		$rsj = self::curlAction("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token,$json);
		$rs = json_decode($rsj,1);
		if ($rs['errcode'] == 0) {
			return true;
		} else {
			return $rsj;
		}
	}
	// 微信自定义菜单 特殊json格式
	public static function _json_encode_($arr) {
		if (count($arr) == 0) return "[]";
		$parts = array ();
		$is_list = false;
		$keys = array_keys ( $arr );
		$max_length = count ( $arr ) - 1;
		if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) {
			$is_list = true;
			for($i = 0; $i < count ( $keys ); $i ++) {
				if ($i != $keys [$i]) {
					$is_list = false;
					break;
				}
			}
		}
		foreach ( $arr as $key => $value ) {
			if (is_array ( $value )) {
				if ($is_list)
					$parts [] = self::_json_encode_ ( $value );
				else
					$parts [] = '"' . $key . '":' . self::_json_encode_ ( $value );
			} else {
				$str = '';
				if (! $is_list)
					$str = '"' . $key . '":';
				if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
					$str .= $value;
				elseif ($value === false)
				$str .= 'false';
				elseif ($value === true)
				$str .= 'true';
				else
					$str .= '"' . addslashes ( $value ) . '"';
				$parts [] = $str;
			}
		}
		$json = implode ( ',', $parts );
		if ($is_list)
			return '[' . $json . ']';
		return '{' . $json . '}';
	}
	public static function curlAction($url,$data=[],$header=[],$type='POST'){
		$data = empty($data) ? array() : $data;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		if($type == 'POST'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}else if($type == 'PUT'){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
			$header[] = "X-HTTP-Method-Override: $type"; 
		}else if($type == 'GET'){
		}else if($type == 'DELETE'){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		}
		if(!empty($header)){
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		}    
		$result = curl_exec ($ch);
		if($result == FALSE){
			$result = "{\"code\":\"1\",\"info\":\"网络错误，请重试！\"}";
		}
		curl_close($ch);
		return $result;
	}
}
