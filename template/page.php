<?php
$post_id = $page_id = $_GET['page_id'];$post = $page = get_page($page_id);
?>
<?php get_wap_template('header'); ?>
<?php if($post) : ?>
<div id="article">
	<h2 class="article-title">
		<a href="<?php the_wap_url('?p='.$post->ID); ?>"><?php echo $post->post_title; ?></a>
	</h2>
	<?php if(!isset($_GET['reply'])) : ?>
	<div class="article-content">
		<?php echo wpautop($post->post_content); ?>
	</div>
	<?php endif; ?>
	<div class="article-comments">
		<?php
		$comments = get_comments(array('post_id' => $post_id));
		if(!empty($comments)) : echo '<h3>回复</h3>';
		foreach($comments as $comment) :
			if(isset($_GET['reply']) && $_GET['reply'] != $comment->comment_ID)continue;
			// 处理提示正在审核
			if($comment->comment_approved == '0' && $comment->comment_author != $_COOKIE['comment_author'])continue;
		?>
		<div id="comment-<?php echo $comment->comment_ID; ?>" class="comment<?php if(isset($_GET['comment']) && $_GET['comment'] == $comment->comment_ID)echo ' current'; ?>">
			<div class="comment-content"><?php comment_text($comment->comment_ID); ?></div>
			<div class="comment-info">
				<span>#<?php echo $comment->comment_ID; ?>楼</span>
				<?php if($comment->comment_parent){ ?><span>回复<a href="?page_id=<?php echo $post_id; ?>#comment-<?php echo $comment->comment_parent; ?>">@<?php echo $comment->comment_parent; ?>楼</a></span><?php } ?>
				<span>由<?php echo strip_tags($comment->comment_author); ?>发表</span>
				<span><?php echo $comment->comment_date; ?></span>
				<span><a href="?page_id=<?php echo $post_id; ?>&reply=<?php echo $comment->comment_ID; ?>">回复</a></span>
			</div>
			<?php if($comment->comment_approved == '0')echo '<div class="comment-info">第一次在本站发表回复需要审核...</div>'; ?>
		</div>
		<?php endforeach;endif; ?>
	</div><!-- // end of commets-list -->
	<div class="article-reply">
		<?php if(isset($_GET['reply']) && is_numeric((int)$_GET['reply']))$reply = $_GET['reply']; ?>
		<?php if('open' == $post->comment_status) : if(get_option('comment_registration') && !is_user_logged_in()){echo '您必须登录才能评论';}else{ ?>
		<h3><?php if($reply){echo '回复@'.$reply.'楼 <a href="?page_id='.$post_id.'#comment-'.$reply.'">取消</a>';}else{echo '评论';} ?></h3>
		<form action="<?php the_wap_url('wap-comment.php'); ?>" method="post" data-ajax="false">
			<?php if(is_user_logged_in()): ?>
			<div>您已经以<?php $current_user = get_userdata(get_current_user_id());echo $current_user->display_name; ?>的身份登录 <a href="<?php echo wp_nonce_url(get_wap_url('wap-login.php?action=logout')); ?>">注销</a></div>
			<?php else : ?>
			<div data-role="fieldcontain">
				<label for="author">昵称：</label><input name="author" type="text" value="<?php echo $_COOKIE['comment_author']; ?>" /><br>
				<label for="email">邮箱：</label><input name="email" type="text" value="<?php echo $_COOKIE['comment_author_email']; ?>" /><br>
				<label for="url">主页：</label><input name="url" type="text" value="<?php echo $_COOKIE['comment_author_url']; ?>" data-inline="true" /><br>
			</div>
			<?php endif; ?>
			<textarea name="comment" rows="5"></textarea><br>
			<button type="submit" data-mini="true" data-theme="b">评论</button>
			<input type="hidden" name="comment_post_ID" value="<?php echo $post_id; ?>" />
			<input type="hidden" name="comment_parent" value="<?php echo $reply = ($reply ? $reply : '0'); ?>" />
			<?php wp_nonce_field(); ?>
		</form>
		<?php } else : echo '评论关闭'; endif; ?>
	</div><!-- // end of reply -->
</div><!-- // end of article -->
<?php else : // 如果有文章 ?>
	<p>对不起，没有这个页面~~</p>
<?php endif; ?>
<?php get_wap_template('footer'); ?>