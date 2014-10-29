<?php
ob_start();
require_once('wap-load.php');
if(!is_user_logged_in() || !current_user_can('edit_posts')){
	wap_die('权限不够');
	exit();
}

nocache_headers();
header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

/***************** 头部初始化结束 ************************/

check_admin_referer();

wap_die('非常抱歉，手机版暂时不支持发布图集，您可以用发布单张图片，续写时继续上传图片，最后编辑修改文章类型来发布图集，虽然很麻烦，但只能这样了，再次表示抱歉！<a href="'.get_wap_url('/post-new-image.php?_wpnonce='.wp_create_nonce()).'">发布图片</a>');