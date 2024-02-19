<?php 
/*
 module:		微信菜单控制器
 create_time:	2023-02-14 09:55:02
 author:		
 contact:		
*/

namespace app\admin\model;
use think\Model;
use think\model\concern\SoftDelete;

class ShopWxConfig extends Model {


	use SoftDelete;

	protected $deleteTime = 'delete_time';

	protected $connection = 'mysql';

 	protected $pk = 'shop_wx_config_id';

 	protected $name = 'shop_wx_config';




}

