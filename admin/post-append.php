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

if(!isset($_GET['post_id']) || $_GET['post_id'] == '' || !is_numeric((int)$_GET['post_id']) || $_GET['post_id'] <= 0)wap_die('文章不存在');
$post_id = (int)$_GET['post_id'];
if(!current_user_can('edit_post',$post_id))wap_die('权限不够');
// 获取要编辑的文章
$post = get_post_to_edit($post_id);

// 从wap-functions.php中获取头部
man_header('续写文章');

?>
<div id="post-new">
	<form action="wap-post.php" method="post" enctype="multipart/form-data">
	<!-- 标题 -->
	<p><strong>标题：</strong><?php echo esc_attr($post->post_title); ?></p>
	<!-- 内容 -->
	<strong>已有内容：</strong><?php echo wpautop($post->post_content); ?>(若换行请再输入两个空行)<br>
	<textarea rows="10" name="post_content" class="post-content-text"></textarea>
	<!-- 图片 -->
	<div class="insert-photo">
		<strong>插入图片：</strong>
		<input type="file" name="photo" id="photo" /><br>
		<input type="checkbox" name="photo_position" value="top"<?php if($post_content[0] == '')echo ' checked="true"'; ?> /><span>(内容最前)</span>
		<input type="checkbox" name="photo_thumbnail" value="this" /><span>(作缩略图)</span><br>
		<span>插入多张图可先发表，然后通过续写继续插入</span>
	</div>
	<!-- 提交 -->
	<div class="submit">
		<button type="submit" name="post_status" value="publish">发布</button>
		<button type="submit" name="post_status" value="draft">存稿</button>
		<a href="<?php echo wp_nonce_url('post-edit.php?post_id='.$post_id); ?>"><button type="button">编辑</button></a>
		<a href="<?php echo wp_nonce_url('wap-post.php?action=delete&post_id='.$post_id); ?>"><button type="button">删除</button></a>
		<a href="<?php the_wap_url('?p='.$post_id); ?>"><button type="button">查看</button></a>
	</div>
	<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
	<input type="hidden" name="action" value="append" />
	<?php wp_nonce_field(); ?>
	</form>
</div><!-- //end of post-new -->
<?php

// 从wap-functions.php中获取底部
man_footer();