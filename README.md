物业管理系统 —— 九福网络研发出品
===============

> 运行环境要求Nginx+PHP7.4以上+MySQL5.7。
>
> 此项目为前后端完全分离，后台使用tp6框架开发，前端为uniapp+原生PHP开发，多物业管理
>
> 后台可开多个物业账号，无限制
>
> 后台地址为{$domain}/admin
>
> 账号：admin
>
> 密码：123123qaz
>
> 物业端管理地址：{$domain}/shop
>
> 账号：jiufu
>
> 密码：123123qaz

## 安装

后台程序安装

~~~
代码下载后解压到目标目录
伪静态选择thinkphp
运行目录选择public
~~~

数据库安装

~~~
将数据库文件导入即可
~~~

手机端安装

~~~
【接口】新建一个站点，将目录中的手机端接口文件夹解压到新的网站目录中，添加伪静态
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
其中app/api文件夹为接口文件，Login.php文件57行修改为自己的站点域名
【前端代码】将文件夹中PropertyClient.rar下载解压即可，更换utils/request.js文件中的业务域名，改成【接口】网站的域名，打包h5上传至【接口】站点的mobile文件夹中
~~~

微信公众号配置
~~~
将前后端域名添加到微信公众号【网页授权域名、js接口安全域名、业务域名】
配置微信商户号【微信支付安全目录】
~~~

## 目录结构

目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
├─config                应用配置目录
├─route                 路由定义目录
│  ├─route.php          路由定义

├─public                根目录
├─extend                扩展类库目录
├─runtime               应用的运行时目录
├─vendor                第三方类库目录
├─composer.json         composer 定义文件
├─README.md             README 文件
├─think                 命令行入口文件
~~~
