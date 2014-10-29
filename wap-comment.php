<?php

require_once(dirname(__FILE__).'/wap-core.php');
nocache_headers();
check_admin_referer();

$comment_post_ID = (int) $_POST['comment_post_ID'];
$status = $wpdb->get_row("SELECT post_status,comment_status FROM $wpdb->posts WHERE ID='$comment_post_ID'");
if(empty($status->comment_status)) {
	do_action('comment_id_not_found',$comment_post_ID);
	wap_die('没有文章');
}elseif('closed' ==  $status->comment_status){
	do_action('comment_closed',$comment_post_ID);
	wap_die('评论关闭');
}elseif(in_array($status->post_status,array('draft','pending'))){
	do_action('comment_on_draft',$comment_post_ID);
	wap_die('暂不支持');
}

$comment_author       = trim(strip_tags($_POST['author']));
$comment_author_email = trim($_POST['email']);
$comment_author_url   = trim($_POST['url']);
$comment_content      = trim($_POST['comment']);
$comment_parent       = trim($_POST['comment_parent']);

// If the user is logged in
$user = wp_get_current_user();
if($user->ID){
	$comment_author       = $wpdb->escape($user->display_name);
	$comment_author_email = $wpdb->escape($user->user_email);
	$comment_author_url   = $wpdb->escape($user->user_url);
	if(current_user_can('unfiltered_html')){
		if(wp_create_nonce('unfiltered-html-comment_'.$comment_post_ID) != $_POST['_wp_unfiltered_html_comment']){
			kses_remove_filters(); // start with a clean slate
			kses_init_filters(); // set up the filters
		}
	}
}else{
	if(get_option('comment_registration'))wap_die('必须登录才能评论');
}

$comment_type = '';

if(get_option('require_name_email') && !$user->ID){
	if(6 > strlen($comment_author_email) || '' == $comment_author)wap_die('昵称邮箱必须填写');
	elseif(!is_email($comment_author_email))wap_die('邮箱格式不对');
}

if('' == $comment_content)wap_die('内容不能为空');

$commentdata = compact('comment_post_ID','comment_author','comment_author_email','comment_author_url','comment_content','comment_parent','comment_type','user_ID');

$comment_id = wp_new_comment($commentdata);

$comment = get_comment($comment_id);
if(!$user->ID) {
	setcookie('comment_author', $comment->comment_author, time() + 30000000);
	setcookie('comment_author_email', $comment->comment_author_email, time() + 30000000);
	setcookie('comment_author_url', clean_url($comment->comment_author_url), time() + 30000000);
}
if($comment_parent != '0'){
	$redirect_to = get_wap_url('?p='.$comment_post_ID.'#comment-'.$comment_parent);
}else{
	$redirect_to = get_wap_url('?p='.$comment_post_ID.'#comments');
}

$message = '回复成功！<br><a href="'.$redirect_to.'">继续阅读</a> <a href="'.get_wap_url().'">返回首页</a>';
if(current_user_can('edit_others_posts'))$message .= ' <a href="'.get_wap_admin_url('man-comment.php').'">评论管理</a>';
wap_die($message);

exit;