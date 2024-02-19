<?php 

namespace app\index\controller;
use think\facade\Log;
use think\facade\Db;
use think\facade\Validate;
use think\facade\Filesystem;
use think\exception\ValidateException;
use think\Image;

class Upload extends Common{
	
	
	
	/**
	* @api {post} /Upload/upload 01、图片上传
	* @apiGroup Upload
	* @apiVersion 1.0.0
	* @apiDescription  图片上传
	
	* @apiHeader {String} Authorization 用户授权token
	* @apiHeaderExample {json} Header-示例:
	* "Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOjM2NzgsImF1ZGllbmNlIjoid2ViIiwib3BlbkFJZCI6MTM2NywiY3JlYXRlZCI6MTUzMzg3OTM2ODA0Nywicm9sZXMiOiJVU0VSIiwiZXhwIjoxNTM0NDg0MTY4fQ.Gl5L-NpuwhjuPXFuhPax8ak5c64skjDTCBC64N_QdKQ2VT-zZeceuzXB9TqaYJuhkwNYEhrV3pUx1zhMWG7Org"
	
	* @apiParam (失败返回参数：) {object}     	array 返回结果集
	* @apiParam (失败返回参数：) {string}     	array.status 返回错误码  201
	* @apiParam (失败返回参数：) {string}     	array.msg 返回错误消息
	* @apiParam (成功返回参数：) {string}     	array 返回结果集
	* @apiParam (成功返回参数：) {string}     	array.status 返回错误码 200
	* @apiParam (成功返回参数：) {string}     	array.data 返回图片地址
	* @apiSuccessExample {json} 01 成功示例
	* {"status":"200","data":"操作成功"}
	* @apiErrorExample {json} 02 失败示例
	* {"status":" 201","msg":"操作失败"}
	*/
	public function upload(){
		$file = $this->request->file('file');
		$upload_config_id = $this->request->post('upload_config_id');
		$file_type = upload_replace(config('base_config.filetype')); //上传黑名单过滤
		if(!Validate::fileExt($file,$file_type)){
			throw new ValidateException('文件类型验证失败');
		}
		
		if(!Validate::fileSize($file,config('base_config.filesize') * 1024 * 1024)){
			throw new ValidateException('文件大小验证失败');
		}
		$filepath = $this->getFile($file);
		if($filepath){
			return json(['status'=>200,'data'=>$filepath,'filestatus'=>true]);
		}else{
			$edit = $this->request->post('edit');	//检测是否编辑器上传  如果是则不走oss客户端传
			if(config('my.oss_status') && config('my.oss_upload_type') == 'client' && !$edit){
				switch(config('my.oss_default_type')){
					case 'qiniuyun';
						$data['serverurl'] = config('my.qny_oss_client_uploadurl');
						$data['domain'] = config('my.qny_oss_domain');
						$data['token'] = $this->getQnyToken();
					break;
					
					case 'ali':
						$options = array();
						$expire = 30;  //设置该policy超时时间是30s. 即这个policy过了这个有效时间，将不能访问。
						$now = time();
						$end = $now + $expire;
						$options['expiration'] = $this->gmtIso8601($end); /// 授权过期时间
						$conditions = array();
						array_push($conditions, array('bucket'=>'xhadmin'));
						
						//$callbackUrl = 'http://b.cdlfvip.com/admin/Login/aliOssCallBack';	//oss异步回调地址，通过这个地址返回上传的文件名
						$callbackUrl = '{:url("admin/Login/aliOssCallBack")}';

						$callback_param = array('callbackUrl'=>$callbackUrl,
							'callbackBody'=>'${object}',
							'callbackBodyType'=>"application/x-www-form-urlencoded");
						$callback_string = json_encode($callback_param);

						$base64_callback_body = base64_encode($callback_string);
						
						$content_length_range = array();
						array_push($content_length_range, 'content-length-range');
						array_push($content_length_range, 0);
						array_push($content_length_range, 2048 * 1024 * 1024);
						array_push($conditions, $content_length_range);
						$options['conditions'] = $conditions;
						$policy = base64_encode(stripslashes(json_encode($options)));
						$sign = base64_encode(hash_hmac('sha1',$policy,config('my.ali_oss_accessKeySecret'), true));
						
						$data['serverurl'] = $this->getendpoint(config('my.ali_oss_endpoint'));
						$data['sign'] = $sign;
						$data['policy'] = $policy;
						$data['callback'] = $base64_callback_body;
						$data['OSSAccessKeyId'] = config('my.ali_oss_accessKeyId');
					break;
				}
				$data['key'] = $this->getFileName().'/'.$file->hash('md5').'.'.$file->extension();
				$data['type'] = config('my.oss_default_type');
				return json(['status'=>200,'data'=>$data]);
			}else{
				if($url = $this->up($file,$upload_config_id)){
					return json(['status'=>200,'data'=>$url]);
				}
			}
		}
	}
	
	//开始上传
	protected function up($file,$upload_config_id){
		if(config('my.oss_status')){
			$url = \utils\oss\OssService::OssUpload(['tmp_name'=>$file->getPathname(),'extension'=>$file->extension()]);
		}else{
			$filename = Filesystem::disk('public')->putFile($this->getFileName(),$file,'uniqid');
			$url = config('base_config.domain').config('filesystem.disks.public.url').'/'.$filename;
			if($upload_config_id){
				$this->thumb(config('filesystem.disks.public.url').'/'.$filename,$upload_config_id);
			}
		}
		
		if(explode('/',$file->getMime())[0] == 'image'){
			Db::name('file')->insert(['filepath'=>$url,'hash'=>$file->hash('md5'),'create_time'=>time()]);
		}
		
		return $url;
	}
	
	//获取上传的文件完整路径
	private function getFileName(){
		return app('http')->getName().'/'.date(config('my.upload_subdir'));
	}
	
	
	//检测数据库的同图片的路径是否存在 存在则返回
	private function getFile($file){
		$filepath = Db::name('file')->where('hash',$file->hash('md5'))->value('filepath');
		if($filepath  && config('my.check_file_status')){
			return $filepath;
		}
	}
	
	//生成缩略图
	private function thumb($imagesUrl,$upload_config_id){
		$imagesUrl = '.'.$imagesUrl;
		$configInfo = Db::name("upload_config")->where('id',$upload_config_id)->find();
		if($configInfo){ 
			$image = Image::open($imagesUrl);
			$targetimages = $imagesUrl;
			
			//当设置不覆盖,生成新的文件
			if(!$configInfo['upload_replace']){
				$fileinfo = pathinfo($imagesUrl);
				$targetimages = $fileinfo['dirname'].'/s_'.$fileinfo['basename'];
				copy($imagesUrl,$targetimages);
			}
			
			//生成缩略图
			if($configInfo['thumb_status']){
				$image->thumb($configInfo['thumb_width'], $configInfo['thumb_height'],$configInfo['thumb_type'])->save($targetimages);
			}
			
			//生成水印
			if(config('base_config.water_status') && config('base_config.water_position')){
				$image->water(config('my.water_img'),config('base_config.water_position'),config('base_config.water_alpha'))->save($targetimages); 
			}
		}
	}
	
	//获取七牛云上传的token
	private function getQnyToken(){
		$auth = new \Qiniu\Auth(config('my.qny_oss_accessKey'), config('my.qny_oss_secretKey'));
		$upToken = $auth->uploadToken(config('my.qny_oss_bucket'));
		return $upToken;
	}
	
	//获取阿里云oss客户端上传地址
	private function getendpoint($str){
		if(strpos(config('my.ali_oss_endpoint'),'aliyuncs.com') !== false){
			if(strpos($str,'https') !== false){
				$point = 'https://'.config('my.ali_oss_bucket').'.'.substr($str,8);
			}else{
				$point = 'http://'.config('my.ali_oss_bucket').'.'.substr($str,7);
			}	
		}else{
			$point = config('my.ali_oss_endpoint');
		}
		return $point;
	}
	
	//阿里云oss客户端上传 授权过期时间
	private function gmtIso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
    
}