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
man_header('发布链接');

?>
<div id="post-new">
	<form action="wap-post.php" method="post">
	<h3>发布链接</h3>
	<!-- 标题 -->
	<p>标题：<input type="text" name="post_title" value="" /></p>
	<!-- 内容 -->
	<p>链接：<input type="url" name="post_content"></p>
	<!-- 形式 -->
	<input type="hidden" name="post_format" value="link" >
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