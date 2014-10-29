<?php

/***********************************************************
*
* wordpress手机版，试用于所有能上网的手机（除极度老版的wml上网手机）
* 作者：否子戈
* 作者主页：http://www.utubon.com
* 版权所有，源码开源，用于商业用途请联系作者
*
************************************************************/

// 把WP引用过来，使本应用支持WP
require_once(dirname(__FILE__).'/../wp-load.php');
require_once(dirname(__FILE__).'/../wp-admin/includes/admin.php');
if(is_multisite() || (defined('MULTISITE') && MULTISITE)){
	wp_die('本应用不支持多站点下使用');
}

// 定义了根路径和模板路径
require_once(dirname(__FILE__).'/wap-config.php');

// 定义手机版的首页URL
function get_wap_url($url = ''){
	if(WAPDOMAIN){
		return WAPDOMAIN.'/'.$url;
	}else{
		$basedir = basename(dirname(__FILE__));
		return get_bloginfo('url').'/'.$basedir.'/'.$url;
	}
}
function the_wap_url($url = ''){
	echo get_wap_url($url);
}
if(get_option('wap_url') != get_wap_url()){
	update_option('wap_url',get_wap_url());
}

// 控制COOKIE的路径，使登录验证有效
if(strpos(get_wap_url(),get_option('home')) !== FALSE){
    define('COOKIEPATH',preg_replace('|https?://[^/]+|i','',get_option('home').'/'));
	define('SITECOOKIEPATH',preg_replace('|https?://[^/]+|i','',get_option('siteurl').'/'));
}else{
    define('COOKIEPATH',preg_replace('|https?://[^/]+|i','',get_wap_url().'/'));
    define('SITECOOKIEPATH',preg_replace('|https?://[^/]+|i','',get_wap_url().'/'));
}

// 安装和邮件通知套件作者
$current_url = 'http';
if(@$_SERVER["HTTPS"] == "on"){
	$current_url .= "s";
}
$current_url .= "://";
if($_SERVER["SERVER_PORT"] != "80"){
	$current_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
}else{
	$current_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
}
if(get_option('wordpress_wap_install_lock') != '2dacbfc8ed57e79e1b751489be7f5faf'){
	$mail_to_utubon = wp_mail('frustigor@163.com','有人安装wordpress手机版通知','有新的wordpress手机版安装，安装地址为'.$current_url);
	if($mail_to_utubon != false)update_option('wordpress_wap_install_lock','2dacbfc8ed57e79e1b751489be7f5faf');
	else wp_die('请确保您的wordpress能发送邮件');
}

// 初始化wordpress
function _wap($query_vars = ''){
	global $wp, $wp_query, $wp_the_query;

    $wp->init();
    $wp->parse_request($query_vars);
    @header('X-Pingback: '. get_bloginfo('pingback_url'));
    if ( is_user_logged_in() )
        nocache_headers();
    @header('Content-Type:'.get_option('html_type').'; charset='.get_option('blog_charset'));
    //@header('Content-Type:vnd.wap.xhtml+xml');
    $wp->query_posts('ignore_sticky_posts=1&caller_get_posts=1');
    $wp->handle_404();
    $wp->register_globals();

	// 不显示置顶文章
	//$wp->ignore_sticky_posts = 1;
	//$wp->caller_get_posts = 1;

    do_action_ref_array('wp', array(&$wp));

	if( !isset($wp_the_query) )
		$wp_the_query = $wp_query;
}
_wap('pagename=&category_name=&attachment=&name=&static=&subpost=&post_type=&page_id=');

// 处理网页的标题
function my_wap_title($title){
	global $page,$paged;
	if(is_home() || is_front_page()){
		$title = get_bloginfo('name').'手机版';
	}else{
		$title .= '-'.get_bloginfo('name').'手机版';
	}
	if($paged >= 2 || $page >= 2){
		$title .= '-'.sprintf('第%s页',max($paged,$page));
	}
	$title = trim($title);
	return $title;
}
remove_filter('wp_title','my_title',101);
add_filter('wp_title','my_wap_title',102);

// 引入WAP函数库
require_once(dirname(__FILE__).'/wap-functions.php');