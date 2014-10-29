<!DOCTYPE HTML>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="initial-scale=1, width=device-width, maximum-scale=1, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="browsermode" content="application"/>
<title><?php wp_title(''); ?></title>
<link href="<?php the_wap_template_url('style.css'); ?>" rel="stylesheet" />
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico">
<?php $paged = get_query_var('page');if(is_single()): ?>
<link rel="canonical" href="<?php the_permalink();echo ($paged >= 2 ? $paged.'/' : ''); ?>" />
<?php endif; ?>
</head>

<body>
<div id="header">
	<h1><a href="<?php the_wap_url(); ?>"><?php bloginfo('name'); ?>手机版</a></h1>
	<div id="nav">
		<span><a href="<?php the_wap_url('#'); ?>">首页</a></span>
		<span><a href="<?php the_wap_url('?comment-list#'); ?>">评论</a></span>
		<span><a href="<?php the_wap_url('?category-list#'); ?>">分类</a></span>
		<span><a href="<?php the_wap_url('?tag-list#'); ?>">标签</a></span>
		<span><a href="<?php $massage_page_id = get_post_by_slug('message')->ID;if(!$massage_page_id)$massage_page_id = MESSAGE_PAGE_ID;the_wap_url('?page_id='.$massage_page_id); ?>">留言</a></span>
	</div>
</div><!-- /header -->
<div id="content">
