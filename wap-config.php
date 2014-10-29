<?php

/**
定义手机版的绝对路径地址
你可以定义为自己的绝对地址，以此来保护自己的手机版程序被访问
**/
define('WAPBASEPATH',dirname(__FILE__),true);

/**
定义手机版根目录的访问路径
例如你可以通过wap.utubon.com访问本应用，就定义它
你也可以定义为www.utubon.com/wap的形式
默认为空的时候，使用相对于wordpress根目录的本目录访问，
例如你将手机版放在mobile目录中，你就可以通过www.yourdomain.com/mobile来访问到本应用
注意末尾不要加斜杠： /
**/
define('WAPDOMAIN','',true);

/**
定义手机版的模板目录名称
例如存放模板的目录是template，就定义它
**/
define('WAPTEMPLATEDIR','template',true);

/**
定义手机版的模板目录完整路径，这个是用在include中，而非URL中的路径
注意，该路径末尾不带 /
**/
define('WAPTEMPLATEPATH',dirname(__FILE__).'/'.WAPTEMPLATEDIR,true);

/**
定义手机版的管理界面路径目录
为了安全，你可以定义自己的目录名称
默认为admin目录
**/
define('WAPADMINDIR','admin',true);

/**
为手机版的首页文章列表配置初始化
因为不同的网站对手机版首页的显示需求不同，
因此，你只需要按照query_posts的参数在下面的参数中设置即可
**/
$home_query = array(
	//'category__not_in' => array(),
	'ignore_sticky_posts' => 1,
	//'post__not_in' => get_option('sticky_posts'),
	//'caller_get_posts' => 1,
	'tag__not_in' => array(),
	'meta_key' => '转载',
	'meta_compare' => 'NOT EXISTS'
);

// 定义留言页面的ID
define('MESSAGE_PAGE_ID',31);