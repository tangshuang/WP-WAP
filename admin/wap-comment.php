<?php

ob_start();
require_once(dirname(__FILE__).'/../wap-core.php');
nocache_headers();
check_admin_referer();

////////////////// 评论管理 /////////////////////////

if(isset($_REQUEST['action']) && $_REQUEST['action'] != '')
switch($_REQUEST['action']){
case 'delete' :
	// 先用$_GET的方式对删除操作进行提示，而且可以进行放回收站和彻底删除的选择
	if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['comment']) && $_GET['comment'] != ''&& is_numeric((int)$_GET['comment']) && $_GET['comment'] > 0) :
		$comment_id = $_GET['comment'];
		$delete_warn = '<div class="delete">
			确定删除评论吗？操作不可恢复。你可以将文章丢进回收站，以后再用电脑客户端处理。
			<a href="'.get_wap_admin_url('man-comment.php').'">评论管理</a>
			<a href="'.get_wap_url('#manage').'">管理首页</a>
			<a href="'.get_wap_url().'">网站首页</a>
			<form action="'.get_wap_admin_url('wap-comment.php').'" method="post">
				<input type="hidden" name="comment" value="'.$comment_id.'" />
				<button type="submit" name="action" value="delete">彻底删除</button>
				<button type="submit" name="action" value="trash">垃圾评论</button>
				<input type="hidden" name="_wpnonce" value="'.wp_create_nonce().'" />
			</form>
		</div>';
		wap_die($delete_warn);
		exit();
	break;
	endif;
	// 再用接收$_POST而非$_GET的方式真正删除文章
	if(isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['comment']) && is_numeric((int)$_POST['comment'])) :
		wp_delete_comment($_POST['comment']);
		wp_redirect(get_wap_admin_url().'man-comment.php?_wpnonce='.wp_create_nonce(),302);
		exit();
	break;
	endif;
break;
case 'trash' :
	// 先用$_GET的方式对删除操作进行提示，而且可以进行放回收站和彻底删除的选择
	if(isset($_GET['action']) && $_GET['action'] == 'trash' && isset($_GET['comment']) && $_GET['comment'] != ''&& is_numeric((int)$_GET['comment']) && $_GET['comment'] > 0) :
		$comment_id = $_GET['comment'];
		$trash_warn = '<div class="trash">
			确定后可用电脑客户端处理
			<a href="'.get_wap_admin_url('man-comment.php').'">评论管理</a>
			<a href="'.get_wap_url('#manage').'">管理首页</a>
			<a href="'.get_wap_url().'">网站首页</a>
			<form action="'.get_wap_admin_url('wap-comment.php').'" method="post">
				<input type="hidden" name="comment" value="'.$comment_id.'" />
				<button type="submit" name="action" value="trash">垃圾评论</button>
				<input type="hidden" name="_wpnonce" value="'.wp_create_nonce().'" />
			</form>
		</div>';
		wap_die($trash_warn);
		exit();
	break;
	endif;
	// 再用接收$_POST而非$_GET的方式真正删除文章
	if(isset($_POST['action']) && $_POST['action'] == 'trash' && isset($_POST['comment']) && is_numeric((int)$_POST['comment'])) :
		wp_set_comment_status($_POST['comment'],'spam');
		wp_redirect(get_wap_admin_url().'man-comment.php?_wpnonce='.wp_create_nonce(),302);
		exit();
	break;
	endif;
break;
case 'approve' :
	if(isset($_GET['value']) && $_GET['value'] != '')$value = $_GET['value'];
	else wap_die('操作错误，<a href="'.get_wap_admin_url().'man-comment.php?_wpnonce='.wp_create_nonce().'">返回</a>');
	$comment_id = $_GET['comment'];
	if($value == 1)wp_set_comment_status($comment_id,'approve');
	if($value == 0)wp_set_comment_status($comment_id,'hold');
	$redirect_to = get_wap_admin_url().'man-comment.php?_wpnonce='.wp_create_nonce();
	if($value == 1)$redirect_to .= '#comment-'.$comment_id;
	wp_redirect($redirect_to,302);
	exit();
break;
}

