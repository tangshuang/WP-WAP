</div><!-- // end of content -->
<div id="footer">
	<div id="search">
		<form action="<?php the_wap_url(); ?>" method="get">
			<input type="search" name="s" placeholder="填写搜索词" />
			<button type="submit">搜索</button>
		</form>
	</div>
	<?php if(!is_user_logged_in()) : ?>
		<form action="wap-login.php?action=login" class="login" method="post" id="manage">
			<?php
			if(isset($_GET['do']) && $_GET['do'] != '') :
				echo '<div class="login-warning">';
				switch($_GET['do']){
					case 'logout' : echo '退出成功';
						break;
					case 'logerror' : if(isset($_GET['msg']))echo urldecode($_GET['msg']);
						break;
				}
				echo '</div>';
			endif;
			?>
			用户名：<input type="text" name="login" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" /><br>
			密&nbsp;&nbsp;码：<input type="password" name="password" value="" /><br>
			<input name="rememberme" id="rememberme" type="checkbox" value="forever" checked="true" /><label for="rememberme">记住我</label><br>
			<button type="submit">登录</button>
			<?php wp_nonce_field(); ?>
		</form>
	<?php else : ?>
		<?php man_menu(); ?>
	<?php endif; ?>
</div><!-- // end of footer -->
<div id="fixed-nav">
	<span><a href="#manage">管理</a></span>
	<span><a href="<?php echo wap_redirect_canonical(); ?>?from-mobile">电脑版</a></span>
	<span><a href="#header">顶部↑</a></span>
</div>
<div id="for-hidden" style="display:none;">
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Faccee0c7176ba170e483637d1989cc8b' type='text/javascript'%3E%3C/script%3E"));
</script>
</div>
</body>
</html>