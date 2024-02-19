<?php 
/*
 module:		文章管理控制器
 create_time:	2023-02-10 10:55:19
 author:		
 contact:		
*/

namespace app\shop\validate;
use think\validate;

class Wzgl extends validate {


	protected $rule = [
		'wzgl_title'=>['require'],
		'wzgl_img'=>['require'],
		'wzgl_futitle'=>['require'],
		'wzgl_neirong'=>['require'],
		'wzfl_id'=>['require'],
	];

	protected $message = [
		'wzgl_title.require'=>'文章标题不能为空',
		'wzgl_img.require'=>'文章首图不能为空',
		'wzgl_futitle.require'=>'文章简述不能为空',
		'wzgl_neirong.require'=>'文章内容不能为空',
		'wzfl_id.require'=>'所属分类不能为空',
	];

	protected $scene  = [
		'add'=>['wzgl_title','wzgl_img','wzgl_futitle','wzgl_neirong','wzfl_id'],
		'update'=>['wzgl_title','wzgl_img','wzgl_futitle','wzgl_neirong','wzfl_id'],
	];



}

