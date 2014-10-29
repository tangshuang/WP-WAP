<?php

/**********************************

先创建一些php函数，以备使用

**********************************/

// 获取当前的URL
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
function wap_redirect_canonical(){
	if(is_singular())return get_permalink();
	if(is_category())return get_category_link($_GET['cat']);
	if(is_tag())return get_tag_link($_GET['tag']);
	if(is_search())return home_url('/?s='.$_GET['s']);
	return home_url();
}

/***************************************************************

手机版要用到的自定义系统函数
基于wordpress系统功能（不包括wordpress主题中的函数）和wap-core.php中的函数和定义

****************************************************************/

// 获取模板路径访问URL
function get_wap_template_url($file = ''){
	return get_wap_url(WAPTEMPLATEDIR.'/'.$file);
}
function the_wap_template_url($file = ''){
	echo get_wap_template_url($file);
}

// 获取模板的函数
function get_wap_template($filename = 'index'){
	$filename = WAPTEMPLATEPATH.'/'.$filename.'.php';
	if(!file_exists($filename))$filename = WAPTEMPLATEPATH.'/index.php';
	if(!wap_install_error_warn()){
		include($filename);
	}
}

// 如果没有正确安装的函数
function wap_install_error_warn(){
	$is_install = get_option('wordpress_wap_install_lock');
	$error_install_warn = '安装错误，请联系作者！<a href="http://www.utubon.com">乌徒帮</a>';
	if($is_install != md5('www.utubon.com')){
		wp_die($error_install_warn);
		exit;
	}
	return false;
}

// 管理面板路径URL
function get_wap_admin_url($url = ''){
	return get_wap_url(WAPADMINDIR.'/'.$url);
}
function the_wap_admin_url($url = ''){
	echo get_wap_admin_url($url);
}

// 显示列表翻页
function the_wap_pagenavi($total_pages = false){
	global $wp, $wp_query, $wp_the_query;
	if(!isset($_GET['paged']))$_GET['paged'] = 1;
	if(!$total_pages)$total_pages = $wp_query->max_num_pages;
	$page_links = paginate_links(array(
		'base' => add_query_arg('paged','%#%'),
		'format' => '',
		'total' => $total_pages,
		'current' => $_GET['paged']
	));
	if($page_links)echo $page_links;	
}

// 显示文章内部的翻页
function the_wap_link_pages($before = false,$after = false,$total_pages = false){
	global $post;
	if(!isset($_GET['page']))$_GET['page'] = 1;
	if(!$total_pages)$total_pages = substr_count($post->post_content,'<!--nextpage-->') + 1;
	$page_links = paginate_links(array(
		'base' => add_query_arg('page','%#%'),
		'format' => '',
		'total' => $total_pages,
		'current' => $_GET['page'],
		'prev_next' => false,
		'next_text' => false
	));
	if($page_links)echo $before.$page_links.$after;
}

// 根据文章别名获取ID
if(!function_exists('get_post_by_slug')):
function get_post_by_slug($slug){
	global $wpdb;
	$post = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE post_name='{$slug}' AND post_status='publish'");
	return $post;
}
endif;

// 显示文章的分类
function the_wap_category($separator = '',$link = true){
	$categories = get_the_category();
	$i = 0;
	foreach($categories as $category){
		if($i == 0) $prev = '';else $prev = $separator;
		if($link)$thelist .= $prev.'<a href="?cat='.$category->term_id.'">'.apply_filters('the_category',$category->name).'</a>';
        else $thelist .= $prev.apply_filters('the_category',$category->name);
		$i ++;
    }
	echo $thelist;
}

// 显示文章的标签
function the_wap_tags($separator = '',$link = true){
    $posttags = get_the_tags();
    if ($posttags) {
        $tag_i = 0;
        foreach($posttags as $tag) {
            if($tag_i == 0) $prev = '';else $prev = $separator;
            if($link)$content .= $prev.'<a href="?tag='.$tag->name.'">'.$tag->name.'</a>';
			else $content .= $prev.$tag->name;
            $tag_i ++;
        }
    }else {
        $content .=  '没有标签';
    }
    echo $content;
}

// 显示文章的评论数，并带上链接
function the_wap_comment($link = true){
	if($link)echo '<a href="?p='.get_the_ID().'#comments">';
	comments_number('0条评论','1条评论','%条评论');
	if($link)echo '</a>';
}

/************************************************************************

修复WordPress内部钩子

*********************************************************************/

function wap_excerpt_more($more){
	return '...';
}
add_filter('excerpt_more','wap_excerpt_more',199);

/*******************************************************************************

创建一些用于全局的函数
无论在后台，前台，都可以使用

*******************************************************************************/

// 管理页面的头部
function man_header($title = ''){
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="user-scalable=no,width=device-width">
<meta name="full-screen" content="yes">
<meta name="browsermode" content="application"/>
<title><?php echo $title; ?>-<?php bloginfo('name'); ?></title>
<link href="<?php the_wap_admin_url('style.css'); ?>" rel="stylesheet" />
<link href="<?php the_wap_template_url('style.css'); ?>" rel="stylesheet" />
</head>

<body>
<div id="header">
	<h1><a href="<?php the_wap_url(); ?>"><?php bloginfo('name'); ?></a>-<?php echo $title; ?></h1>
</div><!-- /header -->
<div id="content">
<?php
}

// 管理菜单
function man_menu(){
?>
<div id="manage">
	<h2>管理</h2>
	<div class="warn"><?php $current_user = wp_get_current_user();echo $current_user->display_name.' 欢迎回来！'; ?></div>
	<div class="manage-menu">
		<a href="<?php the_wap_url(); ?>">首页</a>
		<a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new.php')); ?>">添加文章</a>
		<a href="<?php echo wp_nonce_url(get_wap_url('wap-login.php?action=logout')); ?>">安全退出</a>
		<br>
		<br>
		<?php if ( current_theme_supports( 'post-formats' ) ): ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-article.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-article.php')); ?>">文章</a><?php endif; ?>
		<?php if(get_post_format_link('status') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-status.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-status.php')); ?>">状态</a><?php endif; ?>
		<?php if(get_post_format_link('image') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-image.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-image.php')); ?>">图片</a><?php endif; ?>
		<?php if(get_post_format_link('aside') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-aside.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-aside.php')); ?>">日志</a><?php endif; ?>
		<?php if(get_post_format_link('quote') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-quote.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-quote.php')); ?>">引用</a><?php endif; ?>
		<br>
		<?php if(get_post_format_link('gallery') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-gallery.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-gallery.php')); ?>">图集</a><?php endif; ?>
		<?php if(get_post_format_link('link') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-link.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-link.php')); ?>">链接</a><?php endif; ?>
		<?php if(get_post_format_link('chat') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-chat.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-chat.php')); ?>">对话</a><?php endif; ?>
		<?php if(get_post_format_link('audio') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-audio.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-audio.php')); ?>">音乐</a><?php endif; ?>
		<?php if(get_post_format_link('video') && file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-video.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('post-new-video.php')); ?>">视频</a><?php endif; ?>
		<br>
		<br>
		<?php endif; ?>
		<a href="<?php echo wp_nonce_url(get_wap_admin_url('man-post.php')); ?>">文章管理</a>
		<a href="<?php echo wp_nonce_url(get_wap_admin_url('man-comment.php')); ?>">评论管理</a>
		<?php if(file_exists(dirname(__FILE__).'/'.WAPADMINDIR.'/man-cateogry.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('man-category.php')); ?>">分类管理</a><?php endif; ?>
		<?php if(file_exists(dirname(__FILE__).'/'.WAPADMINDIR.'/man-page.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('man-page.php')); ?>">页面管理</a><?php endif; ?>
		<?php if(file_exists(dirname(__FILE__).'/'.WAPADMINDIR.'/man-system.php')) : ?><a href="<?php echo wp_nonce_url(get_wap_admin_url('man-system.php')); ?>">系统管理</a><?php endif; ?>
		Powered By <a href="http://www.utubon.com" target="_blank">乌徒帮</a>
		<!-- 请保留作者链接，如需删去，请联系作者购买（QQ 476206120 请填写加友信息），本套件增加了内部机制，每次安装都会通知作者你安装的地址 -->
	</div>
</div>
<?php
}

// 管理页面的底部
function man_footer(){
?>
</div><!-- // end of content -->
<div id="footer">
	<?php man_menu(); ?>
</div><!-- // end of footer -->
</body>
</html>
<?php
}


// 按照文章形式发布文章时，有些内容是一摸一样的，调用如下函数即可
function post_new_format(){
?>
	<h3>补充信息</h3>
	<!-- 分类 -->
	<div class="post-new-category">
		<ul><?php wp_category_checklist(); ?></ul>
	</div>
	<!-- 标签 -->
	<strong>标签：</strong><input type="text" name="post_tags" value=""/>
	<!-- 评论选项 -->
	<input type="hidden" name="comment_status" value="open" />
	<input type="hidden" name="ping_status" value="open" />
	<!-- 状态 -->
	<input type="hidden" name="post_status" value="publish" />
	<!-- 提交 -->
	<div class="submit">
		<button type="submit" name="post_status" value="publish">发布</button>
		<button type="submit" name="post_status" value="draft">存稿</button>
	</div>
	<h3>简要说明</h3>
	<p>你可以通过<a href="<?php the_wap_url('admin/post-new.php?_wpnonce='.wp_create_nonce()); ?>">完整撰稿</a>撰写有完整选项的文章。</p>
<?php
}

// 创建一个假死函数，有wp_die修改而来
function wap_die( $message, $title = '' ) {
	if(empty($title)){
		if(function_exists('__'))$title = __('WordPress &rsaquo; Error');
		else $title = 'WordPress &rsaquo; Error';
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="user-scalable=no,width=device-width">
<title><?php echo $title; ?>-<?php bloginfo('name'); ?></title>
<style type="text/css">
	html{background:#f9f9f9;}body{background:#fff;color:#333;font-family:sans-serif;margin:2em;padding:1em 2em;-webkit-border-radius:3px;border-radius:3px;border:1px solid #dfdfdf;max-width:90%;font-size:1em;}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px Georgia,"Times New Roman",Times,serif;margin:30px 0 0 0;padding:0;padding-bottom:7px;}#error-page{margin-top:50px;}#error-page p{font-size:14px;line-height:1.5;margin:25px 0 20px;}#error-page code{font-family:Consolas,Monaco,monospace;}ul li{margin-bottom:10px;font-size:14px;}a{color:#21759B;text-decoration:none;}a:hover{color:#D54E21;}.button{font-family:sans-serif;text-decoration:none;font-size:14px !important;line-height:16px;padding:6px 12px;cursor:pointer;border:1px solid #bbb;color:#464646;-webkit-border-radius:15px;border-radius:15px;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;background-color:#f5f5f5;background-image:-ms-linear-gradient(top,#ffffff,#f2f2f2);background-image:-moz-linear-gradient(top,#ffffff,#f2f2f2);background-image:-o-linear-gradient(top,#ffffff,#f2f2f2);background-image:-webkit-gradient(linear,left top,left bottom,from(#ffffff),to(#f2f2f2));background-image:-webkit-linear-gradient(top,#ffffff,#f2f2f2);background-image:linear-gradient(top,#ffffff,#f2f2f2);}.button:hover{color:#000;border-color:#666;}.button:active{background-image:-ms-linear-gradient(top,#f2f2f2,#ffffff);background-image:-moz-linear-gradient(top,#f2f2f2,#ffffff);background-image:-o-linear-gradient(top,#f2f2f2,#ffffff);background-image:-webkit-gradient(linear,left top,left bottom,from(#f2f2f2),to(#ffffff));background-image:-webkit-linear-gradient(top,#f2f2f2,#ffffff);background-image:linear-gradient(top,#f2f2f2,#ffffff);}
</style>
</head>
<body id="error-page">
	<p><?php echo $message; ?></p>
</body>
</html>
<?php
	die();
}

if(file_exists(WAPTEMPLATEPATH.'/functions.php'))include(WAPTEMPLATEPATH.'/functions.php');