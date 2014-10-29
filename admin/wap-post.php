<?php

ob_start();
require_once(dirname(__FILE__).'/../wap-core.php');
if(!is_user_logged_in() || !current_user_can('edit_posts')){
	wp_redirect(get_wap_url('#manage'), 302);
	exit();
}
nocache_headers();
header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));
check_admin_referer();

/***************** 头部初始化结束 ************************/

$current_user = get_userdata(get_current_user_id());

if(isset($_REQUEST['action']) && $_REQUEST['action'] != '')
switch($_REQUEST['action']){
case 'post' :
	$post_id = $_POST['post_id'];
	$post_format = $_POST['post_format'];
	$post_meta_key = $_POST['meta_key'];
	$post_meta_value = $_POST['meta_value'];
	$post = get_post($post_id);// 这个$post可以在下文重复利用
	// 设置更新参数
	$post->ID = $post_id;
	$post->post_title = $_POST['post_title'];
	$post->post_content = $_POST['post_content'];
	$post->post_name = $_POST['post_name'] ? $_POST['post_name'] : $_POST['post_title'];
	$post->post_password = $_POST['post_password'];
	$post->post_status = $_POST['post_status'] ? $_POST['post_status'] : 'publish';
	$post->comment_status = $_POST['comment_status'] == 'open' ? 'open' : 'closed';
	$post->ping_status = $_POST['ping_status'] == 'open' ? 'open' : 'closed';
	$post->post_category = $_POST['post_category'];
	// 处理图片，将图片作为特色图片，并且插入文章开头
	if($_FILES['photo']['error'] == 0 && $_FILES['photo']['size'] > 0) :
		include_once(ABSPATH.'/wp-admin/includes/file.php');
		$filename = $_FILES['photo']['name'];
		$_FILES['photo']['name'] = iconv('UTF-8','GBK',$_FILES['photo']['name']);
		$uploaded_file = wp_handle_upload($_FILES['photo'],array('test_form' => false));
		$file = $uploaded_file['file'];
		$new_file = iconv('GBK','UTF-8',$file);
		$url = iconv('GBK','UTF-8',$uploaded_file['url']);
		$type = $uploaded_file['type'];
		if(isset($uploaded_file['error']))wap_die($uploaded_file['error']);
		if($post_format == 'image')$post->post_title = preg_replace('/\.[^.]+$/','',$filename);
		$attachment = array(
			'guid' => $url, 
			'post_mime_type' => $type,
			'post_title' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment($attachment,$new_file,$post_id);
		include_once(ABSPATH.'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id,$file);
		$attach_data['file'] = iconv('GBK','UTF-8',$attach_data['file']);
		foreach($attach_data['sizes'] as $key => $sizes){
			$sizes['file'] = iconv('GBK','UTF-8',$sizes['file']);
			$attach_data['sizes'][$key]['file'] = $sizes['file'];
		}
		wp_update_attachment_metadata($attach_id,$attach_data);
		set_post_thumbnail($post,$attach_id);
		if($_POST['photo_position'] == 'top')$post->post_content = '<img src="'.$url.'" />'."\n\n".$_POST['post_content'];
		else $post->post_content = $_POST['post_content']."\n\n".'<img src="'.$url.'" />';
		if($post_format == 'image'):$post->post_content = $_POST['post_content'];endif;
	else :
		if($post_format == 'image')wap_die('图片上传失败，请重试');
	endif;
	if($post_format == 'chat'){
		date_default_timezone_set('PRC');
		$post->post_title = $current_user->display_name.'在'.date("Y年m月d日 H:i:s").'对'.($who = $_POST['post_title'] ? $_POST['post_title'] : '某人').'说';
	}
	if($post_format == 'video' && isset($_POST['video']))$post->post_content = $_POST['video']."\n\n".$_POST['post_content'];
	if($post_format == 'status'){
		date_default_timezone_set('PRC');
		$post->post_title = $current_user->display_name.'在'.date("Y年m月d日 H:i:s").'的状态';
	}
	$post->post_content = stripslashes($post->post_content);
	//$post->post_content = str_replace('<span class="by-phone">（手机发布）</span>','',$post->post_content).'<span class="by-phone">（手机发布）</span>';

	wp_update_post($post);
	// 处理标签
	$post_tags = $_POST['post_tags'];// String
	$post_tags = str_replace(_x(',','tag delimiter'),',',$post_tags);
	$post_tags = explode(',',trim($post_tags," \n\t\r\0\x0B,"));// Array
	wp_set_post_terms($post_id,$post_tags,'post_tag');//wp_set_post_terms第三个参数默认值是post_tag，第四个参数默认为false，将replace而非append
	// 处理文章形式
	set_post_format($post_id,$post_format);
	// 增加自定义栏目
	update_post_meta($post_id,$post_meta_key,$post_meta_value) or add_post_meta($post_id,$post_meta_key,$post_meta_value,true);
	wap_post_published($post_id);
	exit();
break;
case 'edit' :
	$post_id = $_POST['post_id'];
	$post_format = $_POST['post_format'];
	$post_meta = $_POST['post_meta'];
	$post = get_post($post_id);// 这个$post可以在下文重复利用
	// 设置更新参数
	$post->ID = $post_id;
	$post->post_title = $_POST['post_title'];
	$post->post_content = $_POST['post_content'];
	$post->post_name = $_POST['post_name'];
	$post->post_password = $_POST['post_password'];
	$post->post_status = $_POST['post_status'];
	$post->comment_status = $_POST['comment_status'] == 'open' ? 'open' : 'closed';
	$post->ping_status = $_POST['ping_status'] == 'open' ? 'open' : 'closed';
	$post->post_category = $post_category;
	// 处理图片，将图片作为特色图片，并且插入文章开头
	if($_FILES['photo']['error'] == 0 && $_FILES['photo']['size'] > 0) :
		include_once(ABSPATH.'/wp-admin/includes/file.php');
		$filename = $_FILES['photo']['name'];
		$_FILES['photo']['name'] = iconv('UTF-8','GBK',$_FILES['photo']['name']);
		$uploaded_file = wp_handle_upload($_FILES['photo'],array('test_form' => false));
		$file = $uploaded_file['file'];
		$new_file = iconv('GBK','UTF-8',$file);
		$url = iconv('GBK','UTF-8',$uploaded_file['url']);
		$type = $uploaded_file['type'];
		if(isset($uploaded_file['error']))wap_die($uploaded_file['error']);
		$attachment = array(
			'guid' => $url, 
			'post_mime_type' => $type,
			'post_title' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment($attachment,$new_file,$post_id);
		include_once(ABSPATH.'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id,$file);
		$attach_data['file'] = iconv('GBK','UTF-8',$attach_data['file']);
		foreach($attach_data['sizes'] as $key => $sizes){
			$sizes['file'] = iconv('GBK','UTF-8',$sizes['file']);
			$attach_data['sizes'][$key]['file'] = $sizes['file'];
		}
		wp_update_attachment_metadata($attach_id,$attach_data);
		if($_POST['photo_thumbnail'] == 'this')set_post_thumbnail($post,$attach_id);
		if($_POST['photo_position'] == 'top')$post->post_content = '<a href="'.$url.'"><img src="'.$url.'" /></a>'."\n\n".$_POST['post_content'];
		else $post->post_content = $_POST['post_content']."\n\n".'<a href="'.$url.'"><img src="'.$url.'" /></a>';
	endif;
	$post->post_content = stripslashes($post->post_content);
	//$post->post_content = str_replace('<span class="by-phone">（手机发布）</span>','',$post->post_content).'<span class="by-phone">（手机发布）</span>';

	wp_update_post($post);
	$post_tags = $_POST['post_tags'];// String
	$post_tags = str_replace(_x(',','tag delimiter'),',',$post_tags);
	$post_tags = explode(',',trim($post_tags," \n\t\r\0\x0B,"));// Array
	wp_set_post_terms($post_id,$post_tags,'post_tag');//wp_set_post_terms第三个参数默认值是post_tag，第四个参数默认为false，将replace而非append
	set_post_format($post_id,$post_format);
	foreach($post_meta as $meta){
		if(isset($meta['meta_key']) && $meta['meta_key'] != '')update_post_meta($post_id,$meta['meta_key'],$meta['meta_value']);		
	}
	wap_post_published($post_id);
	exit();
break;
case 'append' :
	$post_id = $_POST['post_id'];
	$post = get_post($post_id);// 这个$post可以在下文重复利用
	// 设置更新参数
	$post->ID = $post_id;
	// 处理图片，将图片作为特色图片，并且插入文章开头
	if($_FILES['photo']['error'] == 0 && $_FILES['photo']['size'] > 0) :
		include_once(ABSPATH.'/wp-admin/includes/file.php');
		$filename = $_FILES['photo']['name'];
		$_FILES['photo']['name'] = iconv('UTF-8','GBK',$_FILES['photo']['name']);
		$uploaded_file = wp_handle_upload($_FILES['photo'],array('test_form' => false));
		$file = $uploaded_file['file'];
		$new_file = iconv('GBK','UTF-8',$file);
		$url = iconv('GBK','UTF-8',$uploaded_file['url']);
		$type = $uploaded_file['type'];
		if(isset($uploaded_file['error']))wap_die($uploaded_file['error']);
		$attachment = array(
			'guid' => $url, 
			'post_mime_type' => $type,
			'post_title' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment($attachment,$new_file,$post_id);
		include_once(ABSPATH.'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id,$file);
		$attach_data['file'] = iconv('GBK','UTF-8',$attach_data['file']);
		foreach($attach_data['sizes'] as $key => $sizes){
			$sizes['file'] = iconv('GBK','UTF-8',$sizes['file']);
			$attach_data['sizes'][$key]['file'] = $sizes['file'];
		}
		wp_update_attachment_metadata($attach_id,$attach_data);
		if($_POST['photo_thumbnail'] == 'this')set_post_thumbnail($post,$attach_id);
		if($_POST['photo_position'] == 'top')$post->post_content .= '<img src="'.$url.'" />'."\n\n".$_POST['post_content'];
		else $post->post_content .= $_POST['post_content']."\n\n".'<img src="'.$url.'" />';
	else :
		$post->post_content .= $_POST['post_content'];
	endif;
	$post->post_content = stripslashes($post->post_content);
	$post->post_status = $_POST['post_status'];
	wp_update_post($post);
	set_post_format($post_id,$post_format);
	if(!empty($post_meta))foreach($post_meta as $meta){
		if(isset($meta['meta_key']) && $meta['meta_key'] != '')update_post_meta($post_id,$meta['meta_key'],$meta['meta_value']);		
	}
	wap_post_published($post_id);
	exit();
break;
case 'delete' :
	// 先用$_GET的方式对删除操作进行提示，而且可以进行放回收站和彻底删除的选择
	if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['post_id']) && $_GET['post_id'] != ''&& is_numeric((int)$_GET['post_id']) && $_GET['post_id'] > 0) :
		$post_id = $_GET['post_id'];
		$delete_warn = '<div class="delete" data-rel="dialog">
			确定删除文章吗？操作不可恢复。你可以将文章丢进回收站，以后再用电脑客户端处理。
			<a href="'.get_wap_url('?p='.$post_id).'">查看该文</a>
			<a href="'.get_wap_url('#manage').'">管理首页</a>
			<a href="'.get_wap_url().'">网站首页</a>
			<form action="'.get_wap_admin_url('wap-post.php').'" method="post">
				<input type="hidden" name="post_id" value="'.$post_id.'" />
				<input type="hidden" name="action" value="delete" />
				<button type="submit" name="delete_type" value="force">彻底删除</button>
				<button type="submit" name="delete_type" value="trash">放回收站</button>
				<input type="hidden" name="_wpnonce" value="'.wp_create_nonce().'" />
			</form>
		</div>';
		wap_die($delete_warn);
		exit();
	break;
	endif;
	// 再用接收$_POST而非$_GET的方式真正删除文章
	if(isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['post_id']) && is_numeric((int)$_POST['post_id'])) :
		$post_id = $_POST['post_id'];
		if($_POST['delete_type'] == 'trash'){ // 只把文章放在回收站，而不是从数据库中删除
			$post = object();
			$post->ID = $post_id;
			$post->post_status = 'trash';
			wp_update_post($post);
			wap_post_deleted();
			exit();
			break;
		}
		delete_post_thumbnail($post_id);
		global $wpdb;
		$attachs = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_parent='$post_id' AND post_type='attachment' AND post_mime_type LIKE 'image/%';");
		foreach($attachs as $attach){
			wp_delete_attachment($attach->ID,true);
		}
		wp_delete_post($post_id,true);
		$wpdb->query("ALTER TABLE $wpdb->posts AUTO_INCREMENT = $post_id");
		wap_post_deleted();
		exit();
	break;
	endif;
break;
case 'delete_trash' :
	// 先用GET的方式提示是否删除所有无用文章
	if(isset($_GET['action']) && $_GET['action'] == 'delete_trash') :
		$trash_warn = '<div class="delete" data-rel="dialog">
			删除那些无用的文章（包括自动草稿、版本、回收站），清理数据库
			<a href="'.get_wap_admin_url('man-post.php').'">文章管理</a>
			<a href="'.get_wap_url('#manage').'">管理首页</a>
			<a href="'.get_wap_url().'">网站首页</a>
			<form action="'.get_wap_admin_url('wap-post.php').'" method="post">
				<input type="hidden" name="action" value="delete_trash" />
				<button type="submit">确认清理</button>
				<input type="hidden" name="_wpnonce" value="'.wp_create_nonce().'" />
			</form>
		</div>';
		wap_die($trash_warn);
		exit();
	endif;
	if(isset($_POST['action']) && $_POST['action'] == 'delete_trash') :
		// 删除数据库中所有的无用的文章
		global $wpdb;
		$autodraf_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status='auto-draft' OR post_type='revision' OR post_status ='trash'");
		if(!empty($autodraf_posts))foreach($autodraf_posts as $delete)wp_delete_post($delete,true);
		$posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts");
		$i = 0;
		foreach($posts as $post){
			$i = max($i,$post);
			$i ++;
		}
		$wpdb->query("ALTER TABLE $wpdb->posts AUTO_INCREMENT = $i");// 删除之后，修改自定添加文章的起点
		wap_post_deleted();
		exit();
	endif;
	wap_die('操作错误 <a href="'.get_wap_url().'">返回首页</a>');
break;
default :
	if(isset($_GET['post_id']) && $_GET['post_id'] != '') :
		$post_id = $_GET['post_id'];
		$publish_success = '<div id="published">
			<a href="'.wp_nonce_url(get_wap_admin_url().'post-append.php?post_id='.$post_id).'">续写文章</a>
			<a href="'.wp_nonce_url(get_wap_admin_url().'post-edit.php?post_id='.$post_id).'">重新编辑</a>
			<a href="'.wp_nonce_url(get_wap_admin_url('post-new.php')).'">写新文章</a>
			<a href="'.get_wap_url('?p='.$post_id).'">查看该文</a>
			<a href="'.get_wap_url('#manage').'">管理首页</a>
			<a href="'.get_wap_url().'">网站首页</a>
		</div>';
		wap_die($publish_success,'发布成功');
		exit;
	else : 
		$delete_success = '<div id="deleted">
			操作(删除文章)成功
			<a href="'.wp_nonce_url(get_wap_admin_url('post-new.php')).'">写新文章</a>
			<a href="'.get_wap_url('#manage').'">管理首页</a>
			<a href="'.get_wap_url().'">网站首页</a>
		</div>';
		wap_die($delete_success,'已经删除');
		exit;
	endif;
}// end switch

function wap_post_published($post_id){
	$publish_success = '<div id="published">
		<a href="'.wp_nonce_url(get_wap_admin_url().'post-append.php?post_id='.$post_id).'">续写文章</a>
		<a href="'.wp_nonce_url(get_wap_admin_url().'post-edit.php?post_id='.$post_id).'">重新编辑</a>
		<a href="'.wp_nonce_url(get_wap_admin_url('post-new.php')).'">写新文章</a>
		<a href="'.get_wap_url('?p='.$post_id).'">查看该文</a>
		<a href="'.get_wap_url('#manage').'">管理首页</a>
		<a href="'.get_wap_url().'">网站首页</a>
	</div>';
	wap_die($publish_success,'发布成功');
	exit;
}
function wap_post_deleted(){
	$delete_success = '<div id="deleted">
		操作(删除文章)成功
		<a href="'.wp_nonce_url(get_wap_admin_url('post-new.php')).'">写新文章</a>
		<a href="'.get_wap_url('#manage').'">管理首页</a>
		<a href="'.get_wap_url().'">网站首页</a>
	</div>';
	wap_die($delete_success,'已经删除');
	exit;
}