<?php
ob_start();
require_once(dirname(__FILE__).'/../wap-core.php');
if(!is_user_logged_in() || !current_user_can('edit_others_posts')){
	wap_die('权限不够');
	exit();
}

nocache_headers();
header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

/***************** 头部初始化结束 ************************/

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$post_status = $_GET['post_status'] ? $_GET['post_status'] : 'any';
if($post_status == 'draft')$post_status = array('draft','future');
$args = array(
	'post_status' => $post_status,
	'cat' => $_GET['cat'],
	'tag' => $_GET['tag'],
	'paged' => $paged,
	'posts_per_page' => get_option('posts_per_page'),
	'ignore_sticky_posts' => 1
);
query_posts($args);
$manage_by = $post_status;
if(isset($_GET['cat']))$manage_by = 'cat';
if(isset($_GET['tag']))$manage_by = 'tag';
man_header('文章管理');
?>
<div id="manage-posts">
	<div class="manage-posts-dh">
		<a href="?post_status=any">全部文章</a>
		<a href="?post_status=publish">已发布</a>
		<a href="?post_status=draft">未发布</a>
		<a href="?cat">按分类</a>
		<a href="?tag">按标签</a>
		<a href="<?php echo wp_nonce_url(get_wap_admin_url('wap-post.php?action=delete_trash')); ?>">清理垃圾</a>
	</div>
	<?php if(!(isset($_GET['cat']) && $_GET['cat'] == '') && !(isset($_GET['tag']) && $_GET['tag'] == '')) : ?>
	<div id="manage-posts-by-list">
		<?php while(have_posts()):the_post(); ?>
		<div class="post<?php global $wp_query;if($wp_query->current_post%2 == 1)echo ' even';if($post->post_status == 'draft')echo ' draf'; ?>">
			<h3 class="post-title"><a href="<?php the_wap_url(); ?>/?p=<?php the_ID(); ?>"><?php if(get_the_title())the_title();else echo '无题'; ?><?php if($post->post_status == 'draft')echo '[草稿]';if($post->post_status == 'future')echo '[定时]'; ?></a></h3>
			<div class="post-info">
				<span><?php echo $format = (get_post_format() == '' ? '文章' : get_post_format_string(get_post_format())); ?></span>
				<span><?php the_time('Y-m-d'); ?></span>
				<span><?php the_author(); ?></span>
				<span><?php the_wap_category(','); ?></span>
				<span><?php the_wap_tags(','); ?></span>
				<span><?php the_wap_comment(); ?></span>
			</div>
			<div class="post-manage">
				<span><a href="<?php echo wp_nonce_url(get_wap_admin_url().'post-edit.php?post_id='.$post->ID); ?>">编辑</a></span>
				<span><a href="<?php echo wp_nonce_url(get_wap_admin_url().'post-append.php?post_id='.$post->ID); ?>">续写</a></span>
				<span><a href="<?php echo wp_nonce_url(get_wap_admin_url().'wap-post.php?action=delete&post_id='.$post->ID); ?>">删除</a></span>
				<span><a href="<?php the_wap_url('?p='.$post->ID); ?>">浏览</a></span>
			</div>
		</div>
		<?php endwhile; ?>
		<div class="pagenavi"><?php the_wap_pagenavi(); ?></div>
	</div>
	<?php endif; ?>
	<?php if(isset($_GET['cat']) && $_GET['cat'] == '') : ?>
	<div id="manage-posts-by-category">
		<?php
		$categories = get_categories();
		if(!empty($categories))foreach($categories as $category) :
		?>
		<div class="category">
			<a href="<?php echo get_wap_admin_url().'man-post.php?cat='.$category->term_id; ?>"><?php echo $category->name; ?></a>
			<span class="ui-li-count"><?php echo $category->category_count; ?></span>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if(isset($_GET['tag']) && $_GET['tag'] == '') : ?>
	<div id="manage-posts-by-tag">
		<?php
		$tags = get_tags();
		if(!empty($tags))foreach($tags as $tag) :
		?>
		<span class="tag"><a href="<?php echo get_wap_admin_url().'man-post.php?tag='.$tag->name; ?>"><?php echo $tag->name; ?></a></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
<?php man_footer(); ?>