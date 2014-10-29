<?php if(!defined('ABSPATH'))exit; ?>
<?php get_wap_template('header'); ?>
<?php if(isset($_GET['comment-list'])) : ?>
<div id="comment-list" class="list">
	<h2>最新评论</h2>
	<?php
	global $wpdb;
	$paged = ($_GET['paged'] != '') ? $_GET['paged'] : 1;
	$offset = 10;
	$start = $paged*$offset - $offset;
	$comments = $wpdb->get_results( "SELECT * FROM $wpdb->comments 
									WHERE comment_approved = '1' 
									ORDER BY comment_date DESC 
									LIMIT $start,$offset;");
	$total_num = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments
									WHERE comment_approved = '1';");
	$total_pages = $total_num/$offset;
	$total_pages = ((int)$total_pages < $total_pages ? (int)$total_pages+1 : (int)$total_pages);
	foreach($comments as $key => $comment) :
	?>
	<div class="li comment<?php if($key%2 == 1)echo ' even'; ?>">
		<div class="comment-content"><?php comment_text($comment->comment_ID); ?></div>
		<div class="comment-info">
			<span>#<?php echo $comment->comment_ID; ?>楼</span>
			<?php if($comment->comment_parent){ ?><span>回复<a href="<?php echo('?p='.$comment->comment_post_ID.'#comment-'.$comment->comment_ID); ?>">@<?php echo $comment->comment_parent; ?>楼</a></span><?php } ?>
			<span><?php echo $comment->comment_date; ?></span>
			<span>由<?php echo strip_tags($comment->comment_author); ?>发表在<a href="<?php echo('?p='.$comment->comment_post_ID); ?>"><?php echo get_the_title($comment->comment_post_ID); ?></a></span>
		</div>
	</div>
	<?php endforeach; ?>
	<div class="pagenavi"><?php the_wap_pagenavi($total_pages); ?></div><!-- // end of post-list -->
</div><!-- // end of commet-lsit -->
<?php elseif(isset($_GET['category-list'])) : ?>
<div id="category-list" class="list">
	<h2>分类</h2>
	<ul>
		<?php wp_list_categories('hide_empty=0&title_li=&orderby=ID&order=ASC'); ?>
	</ul>
	<?php
	$categories = get_categories();
	if(!empty($categories) && 0)foreach($categories as $category) :
	?>
		<div class="category li">
			<a href="<?php echo('?cat='.$category->term_id); ?>"><?php echo $category->name; ?></a>
			<span>有文章<?php echo $category->category_count; ?>篇</span>
		</div>
	<?php endforeach; ?>
</div><!-- // end of category-list -->
<?php elseif(isset($_GET['tag-list'])) : ?>
<div class="list">
	<h2>标签</h2>
	<div id="tag-list">
	<?php
	$tags = get_tags();
	if(!empty($tags))foreach($tags as $tag) :
	?>
	<span><a href="<?php echo('?tag='.$tag->name); ?>"><?php echo $tag->name; ?></a></span> |
	<?php endforeach; ?>
	</div>
</div><!-- // end of tag-list -->
<?php else : ?>
<div id="post-list" class="list">
	<?php if(is_category()): ?><h2><?php single_cat_title(); ?></h2><?php endif; ?>
	<?php if(is_tag()): ?><h2><?php single_tag_title('',true); ?></h2><?php endif; ?>
	<?php if(is_home() || is_front_page()): ?><h2>最新文章</h2><?php
    global $wp_query;
    $query_vars = $wp_query->query_vars;
    global $home_query;
    $query_vars = array_merge($query_vars,$home_query);
    query_posts($query_vars);
	endif;
	while(have_posts()):the_post();
    global $wp_query;
    $post_format = get_post_format();
  ?>
	<div class="li post<?php if($wp_query->current_post%2 == 1)echo ' even'; ?>">
		<?php if($post_format == '' || $post_format == 'audio' || $post_format == 'video') : ?><h3 class="post-title"><a href="<?php echo('?p='.get_the_ID()); ?>"><?php if(get_the_title())the_title();else echo '无题'; ?></a><?php if(is_sticky())echo '<span style="color: #ff4b33;">【置顶】</span>'; ?></h3><?php endif; ?>
		<?php if(has_post_thumbnail()) : ?><div class="thumb"><a href="<?php echo('?p='.get_the_ID()); ?>"><?php the_post_thumbnail('full'); ?></a></div><?php endif; ?>
    <div class="excerpt"><a href="<?php echo('?p='.get_the_ID()); ?>"><?php the_excerpt(); ?></a></div>
		<div class="post-info">
			<span><?php the_time('Y-m-d H:i'); ?></span>
		  <span><?php echo $format = ($post_format == '' ? '文章' : get_post_format_string($post_format)); ?></span><br>
			<span>发布者：<?php the_author(); ?></span>
			<span>分类：<?php the_wap_category(','); ?></span>
			<?php if(get_the_tags()) : ?><span>标签：<?php the_wap_tags(','); ?></span><?php endif; ?>
			<span><?php the_wap_comment(); ?></span>
		</div>
	</div>
	<?php endwhile; ?>
	<div class="pagenavi"><?php the_wap_pagenavi(); ?></div><!-- // end of post-list -->
</div>
<?php endif; ?>
<?php get_wap_template('footer'); ?>