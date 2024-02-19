<?php
namespace hook;
use think\exception\ValidateException;
use think\facade\Db;

class Link
{
    function beforAdminAdd($data){
		return json($data);
	}
	
}