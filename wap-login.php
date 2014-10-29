<?php

require_once(dirname(__FILE__).'/wap-core.php');
check_admin_referer();

// 先设置一个COOKIE，测试浏览器是否支持COOKIE
setcookie('TEST_COOKIE','WP Cookie check',0,COOKIEPATH,COOKIE_DOMAIN);
if(SITECOOKIEPATH != COOKIEPATH)setcookie('TEST_COOKIE','WP Cookie check',0,SITECOOKIEPATH,COOKIE_DOMAIN);

// 判断$_GET['action']进行登录和退出操作
$action = $_GET['action'];
switch($action){
	case 'logout' : 
		wp_logout();
		wp_clearcookie();
		$redirect_to = get_wap_url('?do=logout#manage');
		wp_redirect($redirect_to,302);
		exit();
	break;
	case 'login' :
	default:
		$user_login = '';
		$user_pass = '';
		$rememberme = '';
		$using_cookie = false;
		$error = '';
		$redirect_to = get_wap_url('?do=logged#manage');
		if($_POST && empty($_COOKIE['TEST_COOKIE']))$error = '浏览器不支持cookies';
		if($_POST){
			$user_login = $_POST['login'];
			if(empty($user_login))$error = '用户名不能为空 ';
			$user_login = sanitize_user($user_login);
			$user_pass  = $_POST['password'];
			if(empty($user_pass))$error .= '密码不能为空';
			$rememberme = $_POST['rememberme'];
		}else{
			$cookie_login = wp_get_cookie_login();
			if(!empty($cookie_login)){
				$using_cookie = true;
				$user_login = $cookie_login['login'];
				$user_pass = $cookie_login['password'];
			}
		}
		do_action_ref_array('wp_authenticate',array(&$user_login,&$user_pass));
		if($user_login && $user_pass && empty($error)){
			$user = new WP_User(0,$user_login);
			if(wp_login($user_login,$user_pass,$using_cookie)){
				if(!$using_cookie)wp_setcookie($user_login,$user_pass,false,'','',$rememberme);
				do_action('wp_login',$user_login);
				wp_redirect($redirect_to,302);
				exit();
			}else{
				if($using_cookie)$error = '浏览器session问题';
				else $error = '请检查用户名和密码';
			 }
		}else{
			$error = '请检查用户名和密码';
		}
		if($error)$redirect_to = get_wap_url().'?do=logerror&msg='.urlencode($error).'#manage';
		wp_redirect($redirect_to,302);
		exit;
	  break;
}