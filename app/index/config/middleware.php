<?php

return [
	'alias' => [
		'JwtAuth'	=>	app\index\middleware\JwtAuth::class,
		'SmsAuth'	=>	app\index\middleware\SmsAuth::class,
		'CaptchaAuth'=>	app\index\middleware\CaptchaAuth::class,
	],	
];
