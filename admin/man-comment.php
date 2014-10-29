<?php
ob_start();
require_once(dirname(__FILE__).'/../wap-core.php');
if(!is_user_logged_in() || !current_user_can('moderate_comments')){
	wap_die('权限不够');
	exit();
}

nocache_headers();
header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

/***************** 头部初始化结束 ************************/

man_header('评论管理');

global $wpdb;
$page = ($_GET['page'] != '') ? $_GET['page'] : 1;
$offset = 10;
$start = $page*$offset - 10;
$comments = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->comments 
								WHERE (comment_approved = '0' OR comment_approved = '1')
								AND (comment_type != 'pingback' AND comment_type != 'trackback')
								ORDER BY comment_approved ASC,comment_date DESC 
								LIMIT $start,$offset");
if(!empty($comments)) :
$total = $wpdb->get_var( "SELECT FOUND_ROWS()" );
$total = ceil($total/$offset);
$page_links = paginate_links(array(
	'base' => add_query_arg( 'page', '%#%' ),
	'format' => '',
	'total' => $total,
	'current' => $page
));
?>
<div id="manage-comments">
	<h2>评论列表</h2>
	<?php
	foreach($comments as $comment) :
		$comment_stauts = $comment->comment_approved;
	?>
	<div id="comment-<?php echo $comment->comment_ID; ?>" class="comment<?php if($comment_stauts == 0)echo ' hold';if($comment_stauts == 1 && $_GET['comment'] == $comment->comment_ID)echo ' approved'; ?>">
		<div class="comment-content"><?php if($comment_stauts == 0)echo '<strong>[待审]</strong>'; ?><?php comment_text($comment->comment_ID); ?></div>
		<div class="comment-info">
			<span>#<?php echo $comment->comment_ID; ?>楼</span>
			<?php if($comment->comment_parent){ ?><span>回复<a href="<?php the_wap_url('?p='.$comment->comment_post_ID.'#comment-'.$comment->comment_parent); ?>">@<?php echo $comment->comment_parent; ?>楼</a></span><?php } ?>
			<span><?php echo $comment->comment_date; ?></span>
			<span>由<?php if($comment->comment_author_url)echo '<a href="'.$comment->comment_author_url.'" target="_blank">';echo strip_tags($comment->comment_author);if($comment->comment_author_url)echo '</a>'; ?>发表在<a href="<?php echo get_wap_url('?p='.$comment->comment_post_ID); ?>"><?php echo get_the_title($comment->comment_post_ID); ?></a></span>
		</div>
		<div class="comment-manage">
			<span><a href="<?php echo wp_nonce_url(get_wap_admin_url('wap-comment.php?action=delete&comment='.$comment->comment_ID)); ?>">删除</a></span>
			<?php
			if($comment_stauts == 0)
				echo '<span><a href="'.wp_nonce_url(get_wap_admin_url('wap-comment.php?action=approve&comment='.$comment->comment_ID.'&value=1')).'">审核</a></span> ';
			if($comment_stauts == 1)
				echo '<span><a href="'.wp_nonce_url(get_wap_admin_url('wap-comment.php?action=approve&comment='.$comment->comment_ID.'&value=0')).'">重审</a></span> ';
			?>
			<span><a href="<?php echo wp_nonce_url(get_wap_admin_url('wap-comment.php?action=trash&comment='.$comment->comment_ID)); ?>">垃圾评论</a></span>
			<span><a href="<?php the_wap_url('?p='.$comment->comment_post_ID.'&reply='.$comment->comment_ID); ?>">回复</a></span>
		</div>
	</div>
	<?php endforeach; ?>
	<div class="pagenavi"><?php echo $page_links; ?></div>
</div><!-- // end of manage-comments -->
<?php
endif; // 当存在评论时结束

man_footer();
