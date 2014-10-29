<?php

/*
Plugin Name: wp-wap跳转辅助插件
Plugin URI: http://www.utubon.com
Description: 这个插件是基于wp-wap，用以实现在手机端访问wap时，跳转到特定的wap页面。
Author: frustigor
Version: 1.0
Author URI: http://www.utubon.com
*/

// 获取当前URL
if(!function_exists('current_url')):
function current_url(){
    $pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on"){
        $pageURL .= "s";
    }
    $pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80"){
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }else{
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
endif;
// 跳转到手机页面
function wp_redirect_to_wap(){
	$wap_url = get_option('wap_url');
	if(is_home() || is_front_page()){
		return $wap_url;
	}elseif(is_category()){
		global $cat;
		if(!is_numeric($cat))$cat_id = $cat->cat_ID;
		else $cat_id = $cat;
		return $wap_url.'?cat='.$cat_id;
	}elseif(is_tag()){
		global $tag;
		if($tag->name)$tag_name = $tag->name;
		return $wap_url.'?tag='.$tag_name;
	}elseif(is_single()){
		global $post;
		return $wap_url.'?p='.$post->ID;
	}elseif(is_page()){
		global $post;
		return $wap_url.'?page_id='.$post->ID;
	}else{
		return false;
	}
}
function check_device_and_redirect(){
	$wap_url = trim(get_option('wap_url'));
	if(!$wap_url)return;
	if(
		strpos($_SERVER['HTTP_REFERER'],$wap_url) !== FALSE
		&& strpos(current_url(),$wap_url) === FALSE
		&& !isset($_GET['from-mobile'])
	){
		wp_redirect(wp_redirect_to_wap());
		exit;
	}
}
add_action('template_redirect','check_device_and_redirect',0);

// 显示百度抓取链接
function print_baidu_tc(){
	if(!wp_redirect_to_wap())return;
	echo '<meta http-equiv="Cache-Control" content="no-transform " />';
	echo '<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="'.wp_redirect_to_wap().'" />';
}
add_action('wp_head','print_baidu_tc');

// 当屏幕小的时候，在网页顶部提醒
function wp_alert_wap_url_jump($loaded = true){
	if(!wp_redirect_to_wap())return;
	echo '<script>window.jQuery || document.write(\'<script src="'.get_option('wap_url').'/static/jquery.min.js">\x3C/script>\')</script>';
	echo "<script>";
	if($loaded)echo "jQuery(function($){";
	echo "// 如果屏幕小于720像素，建议进入手机版
		if(jQuery(window).width() < 720){
			jQuery('body').prepend('<div style=\"text-align:center;background:#F4C314;line-height:1.6;font-size:0.9em;padding:.6em 0;\"><a href=\"".wp_redirect_to_wap()."\" style=\"color:#B52623;\">您正在使用小屏幕浏览本站，建议使用手机版阅读！</a></div>');
		}";
	if($loaded)echo "});";
	echo "</script>";
}
// 将wp_alert_wap_url_jump(false)添加到header.php合适的位置，这样用户在进入网站时就可以快速看到，
// 如果你不愿意这样做，将下面这一句放到functions.php中，或直接打开注释
add_action('wp_head','wp_alert_wap_url_jump',999);