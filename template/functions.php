<?php

add_filter( 'excerpt_length', 'wap_excerpt_length', 999 );
function wap_excerpt_length( $length ) {
	return 120;
}

add_filter('the_content','filter_content_heading_tag');
function filter_content_heading_tag($content){
	$content = str_replace('<h4','<h5',$content);
	$content = str_replace('</h4>','</h5>',$content);
	$content = str_replace('<h3','<h4',$content);
	$content = str_replace('</h3>','</h4>',$content);
	$content = str_replace('<h2','<h3',$content);
	$content = str_replace('</h2>','</h3>',$content);
	return $content;
}

add_filter('the_excerpt','filter_excerpt_tail');
function filter_excerpt_tail($excerpt) {
  $excerpt = str_replace('点评该文 &raquo;','',$excerpt);
  return $excerpt;
}