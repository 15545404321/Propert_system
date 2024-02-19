<?php
namespace hook;
use think\exception\ValidateException;
use support\Log;
use think\facade\Db;

class Base
{
    
	//获取应用后台基本权限节点权限
	function getAdminNode($data){
		$baseNode = [[
			'title' => '基础操作',
			'access' => '/supplier/Base',
			'sortid'=>1,
			'children'=>[
				[
					'title'	=> '重置密码',
					'access'	=>  '/supplier/Base/resetPwd.html',
				],
			],
		]];
		return $baseNode;
	}
	
}