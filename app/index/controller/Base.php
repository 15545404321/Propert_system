<?php 

namespace app\index\controller;
use think\facade\Validate;
use think\facade\Filesystem;
use think\Image;
use think\exception\ValidateException;


class Base extends Common{
	
	
	/**
	* @api {get} /Base/captcha 01、图片验证码地址
	* @apiGroup Base
	* @apiVersion 1.0.0
	* @apiDescription  图片验证码
	*/
	public function captcha(){
		$data['data'] = captcha();
		$data['status'] = 200;
		return json($data);
	}

}

