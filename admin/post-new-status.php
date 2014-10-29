<?php
ob_start();
require_once(dirname(__FILE__).'/../wap-core.php');
if(!is_user_logged_in() || !current_user_can('edit_posts')){
	wap_die('权限不够');
	exit();
}

nocache_headers();
header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

/***************** 头部初始化结束 ************************/

check_admin_referer();

// 开始创建新的文章
$post = get_default_post_to_edit('',true);

// 从wap-functions.php中获取头部
man_header('发布状态');

?>
<div id="post-new">
	<form action="wap-post.php" method="post" enctype="multipart/form-data">
	<h3>发布状态</h3>
	<!-- 标题 -->
	<?php date_default_timezone_set('PRC'); ?>
	<input type="hidden" name="post_title" value="<?php echo date("Y年m月d日 H:i:s").'的状态'; ?>" />
	<!-- 内容 -->
	<p><textarea rows="10" name="post_content" class="post-content-text"></textarea></p>
	<!-- 形式 -->
	<input type="hidden" name="post_format" value="status" >
	<!-- 图片 -->
	<div class="insert-photo">
		<strong>附带图片</strong>
		<input type="file" name="photo" class="post-new-photo" id="post-photo-file" />
		<input type="hidden" name="photo_position" value="bottom" />
	</div>
	<!-- 快速提交 -->
	<div class="post-new-submit">
		<button type="submit" name="post_status" value="publish">发布</button>
	</div>
	<hr class="line" />
	<?php post_new_format(); ?>
	<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
	<input type="hidden" name="action" value="post" />
	<?php wp_nonce_field(); ?>
	</form>
</div>
<?php

// 从wap-functions.php中获取底部
man_footer();