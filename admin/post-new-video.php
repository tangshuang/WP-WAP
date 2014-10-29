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

// 开始创建新的文章
$post = get_default_post_to_edit('',true);

// 从wap-functions.php中获取头部
man_header('发布视频');

?>
<div id="post-new">
	<form action="wap-post.php" method="post">
	<h3>收藏发布视频</h3>
	<!-- 标题 -->
	<p>视频名称：<input type="text" name="post_title" value="" /></p>
	<!-- 视频地址或代码 -->
	<input type="hidden" name="meta_key" value="audio" />
	<p>视频所在页面的地址（优酷、土豆等）或视频播放器的代码：<br /><textarea name="video" rows="5"></textarea></p>
	<!-- 内容 -->
	<p>视频描述：<br><textarea rows="10" name="post_content"></textarea></p>
	<!-- 形式 -->
	<input type="hidden" name="post_format" value="video" >
	<!-- 快速提交 -->
	<div class="submit">
		<button type="submit" name="storage_type" value="upload">联网发布</button>
		<button type="submit" name="storage_type" value="location">本地保存</button>
	</div>
	<?php post_new_format(); ?>
	<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
	<input type="hidden" name="action" value="post" />
	<?php wp_nonce_field(); ?>
	</form>
</div>
<?php

// 从wap-functions.php中获取底部
man_footer();