<?php 
/*
 module:		基本配置控制器
 create_time:	2022-11-23 20:15:03
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;

class Baseconfig extends Model {


	protected $connection = 'mysql';

 	protected $pk = 'id';

 	protected $name = 'base_config';




}

