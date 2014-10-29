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
man_header('编辑文章');

?>
<div id="post-new">
	<form action="wap-post.php" method="post" enctype="multipart/form-data">
	<h3>基础发布</h3>
	<!-- 标题 -->
	<p><input type="text" name="post_title" value="<?php echo esc_attr($post->post_title); ?>" /></p>
	<!-- 内容 -->
	<p><textarea rows="10" name="post_content" class="post-content-text"><?php echo stripslashes($post->post_content); ?></textarea></p>
	<?php if ( current_theme_supports( 'post-formats' ) ): ?>
	<!-- 形式 -->
	<div class="post-new-format">
		<?php $post_format = get_post_format($post->ID); ?>
		<input type="radio" name="post_format" id="post-format-standard" value="" <?php if($post_format=='')echo 'checked="checked"';?> >
		<label for="post-format-standard">标准</label>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-status.php')) : ?><input type="radio" name="post_format" id="post-format-status" value="status" <?php if($post_format=='status')echo 'checked="checked"';?> >
		<label for="post-format-status">状态</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-image.php')) : ?><input type="radio" name="post_format" id="post-format-image" value="image" <?php if($post_format=='image')echo 'checked="checked"';?> >
		<label for="post-format-image">图像</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-aside.php')) : ?><input type="radio" name="post_format" id="post-format-aside" value="aside" <?php if($post_format=='aside')echo 'checked="checked"';?> >
		<label for="post-format-aside">日志</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-quote.php')) : ?><input type="radio" name="post_format" id="post-format-quote" value="quote" <?php if($post_format=='quote')echo 'checked="checked"';?> >
		<label for="post-format-quote">引语</label><?php endif; ?>
		<br>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-link.php')) : ?><input type="radio" name="post_format" id="post-format-link" value="link" <?php if($post_format=='link')echo 'checked="checked"';?> >
		<label for="post-format-link">链接</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-chat.php')) : ?><input type="radio" name="post_format" id="post-format-chat" value="chat" <?php if($post_format=='chat')echo 'checked="checked"';?> >
		<label for="post-format-chat">对话</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-gallery.php')) : ?><input type="radio" name="post_format" id="post-format-gallery" value="gallery" <?php if($post_format=='gallery')echo 'checked="checked"';?> >
		<label for="post-format-gallery">相册</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-audio.php')) : ?><input type="radio" name="post_format" id="post-format-audio" value="audio" <?php if($post_format=='audio')echo 'checked="checked"';?> >
		<label for="post-format-audio">音乐</label><?php endif; ?>
		<?php if(file_exists(WAPBASEPATH.'/'.WAPADMINDIR.'/post-new-video.php')) : ?><input type="radio" name="post_format" id="post-format-video" value="video" <?php if($post_format=='video')echo 'checked="checked"';?> >
		<label for="post-format-video">视频</label><?php endif; ?>
	</div>
	<?php endif; ?>
	<!-- 图片 -->
	<div class="post-new-photo">
		<strong>插入图片：</strong>
		<input type="file" name="photo" class="post-new-photo" id="post-photo-file" /><br>
		<?php echo get_the_post_thumbnail($post_id,'post-thumbnail'); ?>
		<input type="checkbox" name="photo_position" id="post-photo-posotion" value="top" />
		<label for="post-photo-posotion">(文章最前)(插入多张图可先发表，然后通过续写继续插入)</label>
		<input type="checkbox" name="photo_thumbnail" id="post-photo-thumbnail" value="this" />
		<label for="post-photo-thumbnail">(作缩略图)</label>
	</div>
	<!-- 快速提交 -->
	<div class="submit">
		<button type="submit" name="post_status" value="publish">发布</button>
	</div>
	<hr class="line" />
	<h3>补充信息</h3>
	<!-- 分类 -->
	<div class="post-new-category">
		<ul><?php wp_category_checklist($post_id); ?></ul>
	</div>
	<!-- 标签 -->
	<strong>标签：</strong>
	<input type="text" name="post_tags" value="<?php $tags = get_the_tags($post_id);if(!empty($tags))foreach($tags as $tag)echo $tag->name.','; ?>" />
	<!-- 自定义栏目 -->
	<div class="field">
		<strong>自定义栏目</strong>
		<?php $i = 0; ?>
		<?php $post_metas = get_post_custom($post_id);if(!empty($post_metas))foreach($post_metas as $meta_key => $post_meta){
			if(strpos($meta_key,'_') !== 0)foreach($post_meta as $meta_value){
			?>
		<span><input type="text" name="post_meta[<?php echo $i; ?>][meta_key]" value="<?php echo esc_attr($meta_key); ?>" class="meta_key" /></span>
		<span><input type="text" name="post_meta[<?php echo $i; ?>][meta_value]" value="<?php echo esc_attr($meta_value); ?>" class="meta_value" /></span><br>
			<?php
				$i ++;
			}
		} ?>
		<span><input type="text" name="post_meta[<?php echo $i; ?>][meta_key]" value="" class="meta_key" placeholder="meta_key" /></span>
		<span><input type="text" name="post_meta[<?php echo $i; ?>][meta_value]" value="" class="meta_value" placeholder="meta_value" /></span>
	</div>
	<!-- 别名 -->
	<strong>别名：</strong><input type="text" name="post_name" value="<?php echo urldecode($post->post_name); ?>" />
	<!-- 评论选项 -->
	<div class="post-new-comment">
		<strong>评论选项：</strong>
		<input type="checkbox" name="comment_status" id="post-comment-status" value="open" <?php checked($post->comment_status,'open'); ?> />
		<label for="post-comment-status">允许评论</label>
		<input type="checkbox" name="ping_status" id="post-ping-status" value="open" <?php checked($post->ping_status,'open'); ?> />
		<label for="post-ping-status">允许引用</label>
	</div>
	<!-- 状态 -->
	<div class="post-new-status">
		<strong>公开程度：</strong>
		<select name="post_status"><?php echo $post_status = $post->post_status; ?>
			<option value="publish" <?php selected($post_status,'publish'); ?>>公开发布</option>
			<option value="draft" <?php selected($post_status,'draft'); ?>>存稿状态</option>
			<option value="private" <?php selected($post_status,'private'); ?>>保持私密</option>
			<option value="future" <?php selected($post_status,'future'); ?>>待审保存</option>
		</select>
	</div>
	<!-- 密码 -->
	<strong>密码：</strong><input type="text" name="post_password" value="" />
	<?php if($post->post_password)echo '已经有密码，不填不修改'; ?>
	<!-- 提交 -->
	<div class="submit">
		<button type="submit" value="publish">提交</button>
		<button type="submit" name="post_status" value="draft">存稿</button>
		<a href="<?php echo wp_nonce_url('post-append.php?post_id='.$post_id); ?>"><button type="button">续写</button></a>
		<a href="<?php echo wp_nonce_url('wap-post.php?action=delete&post_id='.$post_id); ?>"><button type="button">删除</button></a>
		<a href="<?php the_wap_url('?p='.$post_id); ?>"><button type="button">查看</button></a>
	</div>
	<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
	<input type="hidden" name="action" value="edit" />
	<?php wp_nonce_field(); ?>
	</form>
</div>
<?php

// 从wap-functions.php中获取底部
man_footer();