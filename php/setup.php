<?php
	/* initial preps and includes */
	error_reporting(E_ALL ^ E_NOTICE);
	if(function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0);
	$curr_dir = dirname(__FILE__);
	include("$curr_dir/settings-manager.php");
	include("$curr_dir/defaultLang.php");
	include("$curr_dir/language.php");
	if(!extension_loaded('mysql')){
		?><div class="Error">ERROR: PHP is not configured to connect to MySQL on this machine. Please see <a href=http://www.php.net/manual/en/ref.mysql.php>this page</a> for help on how to configure MySQL.</div><?php
		exit;
	}



	/*
		Determine execution scenario ...
		this script is called in 1 of 5 scenarios:
			1. to display the setup instructions no $_GET['show-form']
			2. to display the setup form $_GET['show-form'], no $_POST['test'], no $_POST['submit']
			3. to test the db info, $_POST['test'] no $_POST['submit']
			4. to save setup data, $_POST['submit']
			5. to show final success message, $_GET['finish']
		below here, we determine which scenario is being called
	*/
	$submit = $test = $form = $finish = false; 
	(isset($_POST['submit'])   ? $submit = true :
	(isset($_POST['test'])     ?   $test = true :
	(isset($_GET['show-form']) ?   $form = true :
	(isset($_GET['finish'])    ? $finish = true :
		false))));


	/* some function definitions */
	function undo_magic_quotes($str){
		return (get_magic_quotes_gpc() ? stripslashes($str) : $str);
	}

	function isEmail($email){
		if(preg_match('/^([*+!.&#$¦\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $email)){
			return $email;
		}else{
			return FALSE;
		}
	}


	/* if config file already exists, no need to continue */
	if(!$finish && detect_config(false)){
		@header('Location: index.php');
		exit;
	}



	/* include page header, unless we're testing db connection (ajax) */
	if(session_id()){ @session_write_close(); }
	@session_name('new_db');
	@session_start();
	$_REQUEST['Embedded'] = 1; /* to prevent displaying the navigation bar */
	$x->TableTitle = $Translation['Setup Data']; /* page title */
	if(!$test) include("$curr_dir/header.php");

	if($submit || $test){

		/* receive posted data */
		if($submit){
			$username = strtolower($_POST['username']);
			$email = isEmail($_POST['email']);
			$password = $_POST['password'];
			$confirmPassword = $_POST['confirmPassword'];
		}
		$db_name = str_replace('`', '', $_POST['db_name']);
		$db_password = $_POST['db_password'];
		$db_server = $_POST['db_server'];
		$db_username = $_POST['db_username'];

		/* validate data */
		$errors = array();
		if($submit){
			if(strlen($username) < 4){
				$errors[] = $Translation['username empty'];
			}
			if(strlen($password) < 4 || trim($password) != $password){
				$errors[] = $Translation['password invalid'];
			}
			if($password != $confirmPassword){
				$errors[] = $Translation['password no match'];
			}
			if(!$email){
				$errors[] = $Translation['email invalid'];
			}
		}

		/* test database connection */
		if(!($connection = @mysql_connect($db_server, $db_username, $db_password))){
			$errors[] = $Translation['Database connection error'];
		}
		if($connection !== false && !@mysql_select_db($db_name)){
			// attempt to create the database
			if(!@mysql_query("CREATE DATABASE IF NOT EXISTS `$db_name`")){
				$errors[] = @mysql_error();
			}elseif(!@mysql_select_db($db_name)){
				$errors[] = @mysql_error();
			}
		}

		/* in case of validation errors, output them and exit */
		if(count($errors)){
			if($test){
				echo 'ERROR!';
				exit;
			}

			?>
				<div style="max-width: 500px; width: 70%; margin: 30px auto; padding: 10px; border: dotted 1px red;">
					<h2 style="color: red;"><?php echo $Translation['The following errors occured']; ?></h2>
					<div class="Error"><ul><li><?php echo implode('</li><li>', $errors); ?></li></ul></div>
					<div class="buttons" style="height: 30px;"><a class="negative" href="#" onclick="history.go(-1); return false;"><?php echo $Translation['< back']; ?></a>
				</div>
			<?php
			include("$curr_dir/footer.php");
			exit;
		}

		/* if db test is successful, output success message and exit */
		if($test){
			echo 'SUCCESS!';
			exit;
		}

		/* create database tables */
		$silent = false;
		include("$curr_dir/updateDB.php");
		/*
		if($setupAlreadyRun){
			?>
				<div><?php echo $Translation['setup performed'] . @date(' r', filemtime($curr_dir . '/setup.md5')); ?></div>
				<br /><br />

				<div style=\"font-size: 10px;\"><?php echo $Translation['delete md5']; ?></div>
				<br /><br />
			<?php
			exit;
		}
		*/


		/* attempt to save db config file */
		$new_config = array(
			'dbServer' => undo_magic_quotes($db_server),
			'dbUsername' => undo_magic_quotes($db_username),
			'dbPassword' => undo_magic_quotes($db_password),
			'dbDatabase' => undo_magic_quotes($db_name),
			'adminConfig' => array(
				'adminUsername' => undo_magic_quotes($username),
				'adminPassword' => md5($password),
				'notifyAdminNewMembers' => 0,
				'defaultSignUp' => 1,
				'anonymousGroup' => 'anonymous',
				'anonymousMember' => 'guest',
				'groupsPerPage' => 10,
				'membersPerPage' => 10,
				'recordsPerPage' => 10,
				'custom1' => 'Full Name',
				'custom2' => 'Address',
				'custom3' => 'City',
				'custom4' => 'State',
				'MySQLDateFormat' => '%m/%d/%Y',
				'PHPDateFormat' => 'n/j/Y',
				'PHPDateTimeFormat' => 'm/d/Y, h:i a',
				'senderName' => 'Membership management',
				'senderEmail' => $email,
				'approvalSubject' => 'Your membership is now approved',
				'approvalMessage' => "Dear member,\n\nYour membership is now approved by the admin. You can log in to your account here:\nhttp://{$_SERVER['HTTP_HOST']}" . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "\n\nRegards,\nAdmin" 
			)
		);

		$save_result = save_config($new_config);
		if($save_result !== true){
			// display instructions for manually creating them if saving not successful
			$folder_path_formatted = '<b>' . dirname(__FILE__) . '</b>';
			?>
				<div style="max-width: 800px; width: 70%; margin: 30px auto; padding: 30px; border: solid 2px red; font-family: arial; border-radius: 4px;">
					<img src="logo.png" style="margin-bottom: 15px;" />
					<div class="Error"><?php echo $Translation['error:'] . ' ' . $save_result['error']; ?></div>
					<?php printf($Translation['failed to create config instructions'], $folder_path_formatted); ?>
					<pre style="border: solid 2px darkgreen; padding: 10px; color: lightgreen; background-color: black; overflow: scroll; font-size: large;"><?php echo htmlspecialchars($save_result['config']); ?></pre>
				</div>
			<?php
			exit;
		}


		/* sign in as admin if everything went ok */
		$_SESSION['adminUsername'] = $username;
		$_SESSION['memberID'] = $username;
		$_SESSION['memberGroupID'] = 2; // this should work fine in most cases



		/* redirect to finish page using javascript */
		?>
		<div id="manual-redir">If not redirected automatically, <a href="<?php echo basename(__FILE__); ?>?finish=1">click here</a>!</div>
		<script>
			var a = window.location.href;
			window.location = a + '?finish=1';
		</script>
		<?php

		// exit
		include("$curr_dir/footer.php");
		exit;
	}elseif($finish){
		detect_config();
		@include("$curr_dir/config.php");
	}
?>

	<div style="margin: 0 auto; width: 550px;">
	<?php if(!$form && !$finish){ /* show instructions */ ?>

		<div id="intro1" class="instructions">
			<img src="logo.png" />
			<?php echo $Translation['setup intro 1']; ?>
			<div class="buttons"><button class="positive" id="show-intro2" type="button"><?php echo $Translation['Continue']; ?></button></div>
		</div>

		<div id="intro2" class="instructions" style="display: none;">
			<img src="logo.png" />
			<?php echo $Translation['setup intro 2']; ?>
			<div class="buttons"><button class="positive" id="show-login-form" type="button"><?php echo $Translation['Lets go']; ?></button></div>
		</div>

	<?php }elseif($form){ /* show setup form */ ?>

		<form method="post" action="<?php echo basename(__FILE__); ?>" onSubmit="return jsValidateSetup();" id="login-form" style="display: none;">
			<h1>
				<?php echo $Translation['Setup Data']; ?>
			</h1>
			<fieldset id="database" style="background: #E9E5D9 right 10px bottom 10px no-repeat url('mysql.png');">
				<legend><?php echo $Translation['Database Information']; ?></legend>

				<label for="db_server"><?php echo $Translation['mysql server']; ?></label>
				<input type="text" placeholder="<?php echo $Translation['mysql server']; ?>" id="db_server" name="db_server" value="localhost"/>
				<div id="db_test"></div>

				<label for="db_name"><?php echo $Translation['mysql db']; ?></label>
				<input type="text" placeholder="<?php echo $Translation['mysql db']; ?>" id="db_name" name="db_name"/>

				<label for="db_username"><?php echo $Translation['mysql username']; ?></label>
				<input type="text" placeholder="<?php echo $Translation['mysql username']; ?>" id="db_username" name="db_username"/>

				<label for="db_password"><?php echo $Translation['mysql password']; ?></label>
				<input type="password" placeholder="<?php echo $Translation['mysql password']; ?>" id="db_password" name="db_password"/>
			</fieldset>

			<fieldset id="inputs">
				<legend><?php echo $Translation['Admin Information']; ?></legend>

				<label for="username"><?php echo $Translation['username']; ?></label>
				<input type="text" required="" placeholder="<?php echo $Translation['username']; ?>" id="username" name="username">

				<div style="float: left;">
					<label for="password"><?php echo $Translation['password']; ?></label>
					<input style="width: 200px;" type="password" required="" placeholder="<?php echo $Translation['password']; ?>" id="password" name="password">
				</div>
				<div style="float: right;">
					<label for="confirmPassword"><?php echo $Translation['confirm password']; ?></label>
					<input style="width: 200px;" type="password" required="" placeholder="<?php echo $Translation['confirm password']; ?>" id="confirmPassword" name="confirmPassword">
				</div>
				<div style="clear: both;"></div>

				<label for="email"><?php echo $Translation['email']; ?></label>
				<input type="text" required="" placeholder="<?php echo $Translation['email']; ?>" id="email" name="email">
			</fieldset>

			<fieldset id="inputs">
				<div class="buttons"><button class="positive" value="submit" id="submit" type="submit" name="submit"><?php echo $Translation['Submit']; ?></button></div>
			</fieldset>
		</form>

	<?php }elseif($finish){ ?>

		<?php
			// make sure this is an admin
			if(!$_SESSION['adminUsername']){
				?>
				<div id="manual-redir">If not redirected automatically, <a href="index.php">click here</a>!</div>
				<script>
					window.location = 'index.php';
				</script>
				<?php
				exit;
			}
		?>

		<div class="instructions">
			<img src="logo.png" />
			<?php echo $Translation['setup finished']; ?>
			<ul id="next-actions">
				<li><a href="index.php"><b><?php echo $Translation['setup next 1']; ?></b></a></li>
				<li><a href="admin/pageUploadCSV.php"><?php echo $Translation['setup next 2']; ?></a></li>
				<li><a href="admin/pageHome.php"><?php echo $Translation['setup next 3']; ?></a></li>
			</ul>
		</div>

	<?php } ?>

		<div style="text-align: center; font-size: 10px;">Powered by <a href="http://bigprof.com/appgini/" target="_blank">BigProf AppGini 5.10</a></div>
	</div>

	<div id="help-bubble" style="display: none;"><p class="triangle-border left"></p></div>

	<script>
	<?php if(!$form && !$finish){ ?>
		document.observe("dom:loaded", function() {
			$('show-intro2').observe('click', function(){
				$('intro1').hide();
				$('intro2').appear({ duration: 2 });
			});
			$('show-login-form').observe('click', function(){
				var a = window.location.href;
				window.location = a + '?show-form=1';
			});
		});
	<?php }elseif($form){ ?>
		document.observe("dom:loaded", function() {
			/* help instructions */
			$('db_name').help = '<?php echo addslashes($Translation['db_name help']); ?>';
			$('db_server').help = '<?php echo addslashes($Translation['db_server help']); ?>';
			$('db_password').help = '<?php echo addslashes($Translation['db_password help']); ?>';
			$('db_username').help = '<?php echo addslashes($Translation['db_username help']); ?>';
			$('username').help = '<?php echo addslashes($Translation['username help']); ?>';
			$('confirmPassword').help = '<?php echo addslashes($Translation['password help']); ?>';
			$('email').help = '<?php echo addslashes($Translation['email help']); ?>';

			/* password strength feedback */
			$('password').observe('keyup', function(){
				ps = passwordStrength($F('password'), $F('username'));

				if(ps == 'strong'){
					$('password').removeClassName('redBG').removeClassName('yellowBG').addClassName('greenBG');
					$('password').title = '<?php echo htmlspecialchars($Translation['Password strength: strong']); ?>';
				}else if(ps == 'good'){
					$('password').removeClassName('redBG').removeClassName('greenBG').addClassName('yellowBG');
					$('password').title = '<?php echo htmlspecialchars($Translation['Password strength: good']); ?>';
				}else{
					$('password').removeClassName('greenBG').removeClassName('yellowBG').addClassName('redBG');
					$('password').title = '<?php echo htmlspecialchars($Translation['Password strength: weak']); ?>';
				}
			});

			/* inline feedback of confirm password */
			$('confirmPassword').observe('keyup', function(){
				if($F('confirmPassword') != $F('password') || !$F('confirmPassword').length){
					$('confirmPassword').removeClassName('greenBG').addClassName('redBG');
				}else{
					$('confirmPassword').removeClassName('redBG').addClassName('greenBG');
				}
			});

			/* inline feedback of email */
			$('email').observe('change', function(){
				if(validateEmail($F('email'))){
					$('email').removeClassName('redBG').addClassName('greenBG');
				}else{
					$('email').removeClassName('greenBG').addClassName('redBG');
				}
			});

			/* prepare help bubble */
			$('help-bubble').absolutize().hide();
			$$('input').each(function(ie){
				ie.observe('focus', function(){
					updateHelp(ie.id);
				});
			});

			$('login-form').appear({ duration: 2 });
			setTimeout("$('db_name').focus();", 2000);

			$('db_name').observe('change', function(){ db_test(); });
			$('db_password').observe('change', function(){ db_test(); });
			$('db_server').observe('change', function(){ db_test(); });
			$('db_username').observe('change', function(){ db_test(); });
		});

		/* validate data before submitting */
		function jsValidateSetup(){
			var p1 = $F('password');
			var p2 = $F('confirmPassword');
			var user = $F('username');
			var email = $F('email');

			/* passwords not matching? */
			if(p1 != p2){
				Modalbox.show('<div class="Error" style="width: 90%; margin: 0;"><?php echo addslashes($Translation['password no match']); ?></div>', { title: "<?php echo addslashes($Translation['error:']); ?>", afterHide: function(){ $('confirmPassword').focus(); } });
				return false;
			}

			/* user exists? */
			if($('usernameNotAvailable').visible()){
				Modalbox.show('<div class="Error" style="width: 90%; margin: 0;"><?php echo addslashes($Translation['username exists']); ?></div>', { title: "<?php echo addslashes($Translation['error:']); ?>", afterHide: function(){ $('username').focus(); } });
				return false;
			}

			return true;
		}

		/* display the help bubble next to the input box provided */
		var showHelpBubble = true;
		function updateHelp(fieldID){
			if(!showHelpBubble) return;

			if(fieldID == 'password') fieldID = 'confirmPassword';

			$('help-bubble').hide('slow');
			if(!$(fieldID).help) return;
			$$('#help-bubble p')[0].update($(fieldID).help + '<br/><a style="font-size: x-small;" href="#" onclick="showHelpBubble=false; $(\'help-bubble\').fade(); return false;"><?php echo addslashes($Translation['Hide']); ?></a>');
			$('help-bubble').setStyle({
				top: ($(fieldID).viewportOffset().top + $(fieldID).getHeight() / 2 - $('help-bubble').getHeight() / 2 + document.viewport.getScrollOffsets().top) + 'px',
				left: ($(fieldID).viewportOffset().left + $(fieldID).getWidth()) + 'px',
				width: '300px'
			}).appear({ to: 0.9 });

		}

		/* test db info */
		var db_test_in_progress = false;
		function db_test(){
			if(db_test_in_progress) return;

			if($F('db_name').length && $F('db_username').length && $F('db_server').length && $$('#db_password:focus') == ''){
				setTimeout(function(){
					if(db_test_in_progress) return;

					new Ajax.Request(
						'<?php echo basename(__FILE__); ?>', {
							method: 'post',
							parameters: {
								db_name: $F('db_name'),
								db_server: $F('db_server'),
								db_password: $F('db_password'),
								db_username: $F('db_username'),
								test: 1
							},
							onCreate: function() {
								db_test_in_progress = true;
							},
							onSuccess: function(resp) {
								if(resp.responseText == 'SUCCESS!'){
									$('db_test').removeClassName('error').addClassName('success').update('<?php echo addslashes($Translation['Database info is correct']); ?>').appear();
								}else if(resp.responseText.match(/^ERROR!/)){
									$('db_test').removeClassName('success').addClassName('error').update('<?php echo addslashes($Translation['Database connection error']); ?>').show();
									Effect.Shake('db_test');
								}
							},
							onComplete: function() {
								db_test_in_progress = false;
							}
						}
					);
				}, 1000);
			}
		}
	<?php } ?>
	</script>

	<style>
		#login-form{ width: 500px; }
		legend{ font-weight: bold; font-family: arial; font-size: large; background-color: White; border-radius: 4px; border:2px solid #316B40; padding: 3px 20px; }
		#email,#custom1,#custom2,#custom3,#custom4{ width: 450px !important; }
		#usernameAvailable,#usernameNotAvailable{ cursor: pointer; }
		.greenBG{ border-color: Green !important; background-color: LightGreen !important; }
		.yellowBG{ border-color: Gold !important; background-color: LightYellow !important; }
		.redBG{ border-color: Red !important; background-color: LighRed !important; }

		/* Help bubble */
		.triangle-border.left:before{
			border-color: transparent #5A8F00;
			border-width: 15px 30px 15px 0;
			bottom: auto;
			left: -30px;
			top: 10px;
		}
		.triangle-border:before{
			border-color: #5A8F00 transparent;
			border-style: solid;
			border-width: 20px 20px 0;
			bottom: -20px;
			content: "";
			display: block;
			left: 40px;
			position: absolute;
			width: 0;
		}
		.triangle-border.left:after{
			border-color: transparent #FFFFFF;
			border-width: 9px 21px 9px 0;
			bottom: auto;
			left: -21px;
			top: 16px;
		}
		.triangle-border:after{
			border-color: #FFFFFF transparent;
			border-style: solid;
			border-width: 13px 13px 0;
			bottom: -13px;
			content: "";
			display: block;
			left: 47px;
			position: absolute;
			width: 0;
		}
		.triangle-border.left{
			margin-left: 30px;
		}
		.triangle-border.right:before{
			border-color: transparent #5A8F00;
			border-width: 15px 0 15px 30px;
			bottom: auto;
			left: auto;
			right: -30px;
			top: 10px;
		}
		.triangle-border:before{
			border-color: #5A8F00 transparent;
			border-style: solid;
			border-width: 20px 20px 0;
			bottom: -20px;
			content: "";
			display: block;
			left: 40px;
			position: absolute;
			width: 0;
		}
		.triangle-border.right:after{
			border-color: transparent #FFFFFF;
			border-width: 9px 0 9px 21px;
			bottom: auto;
			left: auto;
			right: -21px;
			top: 16px;
		}
		.triangle-border:after{
			border-color: #FFFFFF transparent;
			border-style: solid;
			border-width: 13px 13px 0;
			bottom: -13px;
			content: "";
			display: block;
			left: 47px;
			position: absolute;
			width: 0;
		}
		.triangle-border.right{
			margin-right: 30px;
		}
		.triangle-border{
			background: none repeat scroll 0 0 #FFFFFF;
			border: 5px solid #5A8F00;
			border-radius: 10px 10px 10px 10px;
			color: #333333;
			margin: 1em 0 3em;
			padding: 15px;
			position: relative;
		}
		.instructions{
			padding: 30px;
			margin: 40px auto;
			border: solid 1px silver;
			border-radius: 4px;
			background-color: White;
		}
		.instructions img{ display: block; margin: 0 auto 40px; }
		.instructions .buttons{
			display: block;
			height: 1px;
			margin: 30px auto;
		}
		body{ background-color: #f8f8f8; font-family: Arial,Helvetica,sans-serif; }
		#db_test{
			float: right;
			font-weight: bold;
			padding: 10px;
			width: 150px;
		}
		#db_test.error{
			background-color: lightyellow;
			border: 2px solid red;
			border-radius: 4px;
			color: red;
		}
		#db_test.success{
			background-color: lightgreen;
			border: 2px solid green;
			border-radius: 4px;
			color: green;
		}
		ul#next-actions li a{ text-decoration: none; color: navy; }
		ul#next-actions li a:hover{ text-decoration: underline; }
		ul#next-actions li { margin: 20px 0; }
	</style>

<?php include("$curr_dir/footer.php"); ?>
