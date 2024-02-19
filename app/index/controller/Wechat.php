<?php 
/*
 module:		微信菜单控制器
 create_time:	2023-02-13 10:30:26
 author:		
 contact:		
*/

namespace app\index\controller;
use app\Wxzdy;
use think\facade\Db;

class Wechat {

/*start*/
	/*
 	* @Description  数据列表
 	*/
	function index(){

        // Token验证 将微信转过来的数据原样返回
        if(isset($_GET['echostr'])) {
            echo $_GET['echostr'];
            exit;
        }

    }
/*end*/
}


// URL: http://newwy.jf.ivimoo.com/index/Wechat/index
// Token: wyToken
// EncodingAESKey: b9UsctPppkcXtpmLt70WybOiENQKL7KQAleRROKvgt0
// 消息加解密方式: 明文模式