<?php
	error_reporting(E_ALL ^ E_NOTICE);
	if(function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0);
	ob_start();
	$currDir=dirname(__FILE__);
	include("$currDir/../settings-manager.php");

	// check if initial setup was performed or not
	detect_config();
	migrate_config();

	include($currDir . '/../config.php');
	include("$currDir/incFunctions.php");

	// check sessions config
	$noPathCheck=True;
	$arrPath=explode(';', ini_get('session.save_path'));
	$save_path=$arrPath[count($arrPath)-1];
	if(!$noPathCheck && !is_dir($save_path)){
		?>
		<link rel="stylesheet" type="text/css" href="adminStyles.css">
		<center>
		<div class="status">
			Your site is not configured to support sessions correctly. Please edit your php.ini file and change the value of <i>session.save_path</i> to a valid path.
			<br /><br />
			Current session.save_path value is '<?php echo $save_path; ?>'.
			</div>
			</center>
		<?php
		exit;
	}
	if(session_id()){ session_write_close(); }
	$configured_save_handler = @ini_get('session.save_handler');
	if($configured_save_handler != 'memcache' && $configured_save_handler != 'memcached')
		@ini_set('session.save_handler', 'files');
	@ini_set('session.serialize_handler', 'php');
	@ini_set('session.use_cookies', '1');
	@ini_set('session.use_only_cookies', '1');
	@session_cache_limiter('private, must-revalidate');
	@session_name('new_db');
	session_start();
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header('Content-Type: text/html; charset=UTF-8');


	// check if membership system exists
	setupMembership();


	########################################################################

	// do we have an admin log out request?
	if($_GET['signOut']==1){
		logOutUser();
		?><META HTTP-EQUIV="Refresh" CONTENT="0;url=index.php"><?php
		exit;
	}

	// is there a logged user?
	if(!$uname=getLoggedAdmin()){
		// is there a user trying to log in?
		if(!checkUser($_POST['username'], $_POST['password'])){
			// display login form
			include("$currDir/pageLogin.php");
			exit;
		}else{
			redirect('admin/pageHome.php');
		}
	}

?>