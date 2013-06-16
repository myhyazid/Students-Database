<?php
	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	include("$currDir/header.php");
	
	$reset_expiry = 86400; // time validity of reset key in seconds

#_______________________________________________________________________________
# Step 4: Final step; change the password
#_______________________________________________________________________________
	if($_POST['changePassword'] && $_POST['key']){
		echo StyleSheet();
		$expiry_limit = time() - $reset_expiry - 900; // give an extra tolerence of 15 minutes
		$res = sql("select * from membership_users where pass_reset_key='" . makeSafe($_POST['key']) . "' and pass_reset_expiry>$expiry_limit limit 1", $eo);
		
		if($row = mysql_fetch_assoc($res)){
			if($_POST['newPassword'] != $_POST['confirmPassword'] || !$_POST['newPassword']){
				?>
				<div class="Error">
					<?php echo $Translation['password no match']; ?>
					</div>
				<?php
				exit;
			}

			sql("update membership_users set passMD5='" . md5($_POST['newPassword']) . "', pass_reset_expiry=NULL, pass_reset_key=NULL where lcase(memberID)='" . addslashes($row['memberID']) . "'", $eo);

			?>
			<div style="width:500px; margin:0px auto; text-align:left;">
				<div class="TableTitle">
					<?php echo $Translation['password reset done']; ?>
					</div>
				</div>
			<?php
		}else{
			?>
			<div class="Error">
				<?php echo $Translation['password reset invalid']; ?>
				</div>
			<?php
		}
		
		exit;
	}
#_______________________________________________________________________________
# Step 3: This is the special link that came to the member by email. This is
#         where the member enters his new password.
#_______________________________________________________________________________
	if($_GET['key'] != ''){
		echo StyleSheet();
		$expiry_limit = time() - $reset_expiry;
		$res = sql("select * from membership_users where pass_reset_key='" . makeSafe($_GET['key']) . "' and pass_reset_expiry>$expiry_limit limit 1", $eo);
		
		if($row = mysql_fetch_assoc($res)){
			?>
			<div align="center">
				<form method="post" action="membership_passwordReset.php">
					<table border="0" cellspacing="1" cellpadding="4" align="center" width="500">
						<tr>
							<td colspan="2" class="TableHeader">
								<div class="TableTitle"><?php echo $Translation['password change']; ?></div>
								</td>
							</tr>
						<tr>
							<td align="right" class="TableHeader" width="160" <?php echo $highlight; ?>>
								<?php echo $Translation['username']; ?>
								</td>
							<td align="left" class="TableBody" width="340">
								<?php echo $row['memberID']; ?>
								</td>
							</tr>
						<tr>
							<td align="right" class="TableHeader">
								<?php echo $Translation['new password']; ?>
								</td>
							<td align="left" class="TableBody">
								<input type="password" name="newPassword" value="" size="20" class="TextBox">
								</td>
							</tr>
						<tr>
							<td align="right" class="TableHeader">
								<?php echo $Translation['confirm password']; ?>
								</td>
							<td align="left" class="TableBody">
								<input type="password" name="confirmPassword" value="" size="20" class="TextBox">
								</td>
							</tr>
						<tr>
							<td colspan="2" align="right" class="TableHeader">
								<input type="submit" name="changePassword" value="<?php echo $Translation['ok']; ?>">
								</td>
							</tr>
						</table>
						<input type="hidden" name="key" value="<?php echo $_GET['key']; ?>">
					</form>
				</div>
			<?php
		}else{
			?>
			<div class="Error">
				<?php echo $Translation['password reset invalid']; ?>
				</div>
			<?php
		}
		
		exit;
	}
#_______________________________________________________________________________
# Step 2: Send email to member containing the reset link
#_______________________________________________________________________________
	if($_POST['reset']){
		$username=makeSafe(strtolower(trim($_POST['username'])));
		$email=isEmail($_POST['email']);

		if((!$username && !$email) || ($username==$adminConfig['adminUsername'])){
			redirect("membership_passwordReset.php?emptyData=1");
		}

		echo StyleSheet();

		$res=sql("select * from membership_users where lcase(memberID)='$username' or email='$email' limit 1", $eo);
		if(!$row=mysql_fetch_assoc($res)){
			?>
			<div class="Error">
				<?php echo $Translation['password reset invalid']; ?>
				</div>
			<?php
			exit;
		}else{
			// avoid admin password change
			if($row['memberID']==$adminConfig['adminUsername']){
				?>
				<div class="Error">
					<?php echo $Translation['password reset invalid']; ?>
					</div>
				<?php
				exit;
			}

			// generate and store password reset key, if no valid key already exists
			$no_valid_key = ($row['pass_reset_key'] == '' || ($row['pass_reset_key'] != '' && $row['pass_reset_expiry'] < (time() - $reset_expiry)));
			$key = ($no_valid_key ? md5(microtime()) : $row['pass_reset_key']);
			$expiry = ($no_valid_key ? time() + $reset_expiry : $row['pass_reset_expiry']);
			@mysql_query("update membership_users set pass_reset_key='$key', pass_reset_expiry='$expiry' where memberID='" . addslashes($row['memberID']) . "'");
			//$_SESSION['resetKey']=$key;
			//$_SESSION['resetUsername']=$row['memberID'];

			// determine password reset URL
			$ResetLink = application_url("membership_passwordReset.php?key=$key");

			// send reset instructions
			@mail($row['email'], $Translation['password reset subject'], str_replace('<ResetLink>', $ResetLink, $Translation['password reset message']), "From: ".$adminConfig['senderName']." <".$adminConfig['senderEmail'].">");
		}

		// display confirmation
		?>
		<div style="width:500px; margin:0px auto; text-align:left;">
			<div class="TableTitle">
				<?php echo $Translation['password reset ready']; ?>
				</div>
			</div>
		<?php
		exit;
	}

#_______________________________________________________________________________
# Step 1: get the username or email of the member who wants to reset his password
#_______________________________________________________________________________
	echo StyleSheet();

	if($_GET['emptyData']){
		$highlight="style=\"color: red;\"";
	}

	?>


	<div align="center">
		<form method="post" action="membership_passwordReset.php">
			<table border="0" cellspacing="1" cellpadding="4" align="center" width="500">
				<tr>
					<td colspan="2" class="TableHeader">
						<div class="TableTitle"><?php echo $Translation['password reset']; ?></div>
						</td>
					</tr>
				<tr>
					<td colspan="2" class="TableBody" align="left">
						<div class="TableBody"><?php echo $Translation['password reset details']; ?></div>
						</td>
					</tr>
				<tr>
					<td align="right" class="TableHeader" width="160" <?php echo $highlight; ?>>
						<?php echo $Translation['username']; ?>
						</td>
					<td align="left" class="TableBody" width="340">
						<input type="text" name="username" value="" size="20" class="TextBox">
						</td>
					</tr>
				<tr>
					<td align="right" class="TableHeader" <?php echo $highlight; ?>>
						<?php echo '<i>'.$Translation['or'].':</i> '.$Translation['email']; ?>
						</td>
					<td align="left" class="TableBody">
						<input type="text" name="email" value="" size="45" class="TextBox">
						</td>
					</tr>
				<tr>
					<td colspan="2" align="right" class="TableHeader">
						<input type="submit" name="reset" value="<?php echo $Translation['ok']; ?>">
						</td>
					</tr>
				<tr>
					<td colspan="2" align="center" class="TableHeader">
						<?php echo $Translation['browse as guest']; ?>
						</td>
					</tr>
				</table>
			</form>
		<br /><br />
		<div class="TableFooter">
			<div style="text-align: center; font-size: 10px;">Powered by <a href="http://bigprof.com/appgini/" target="_blank">BigProf AppGini 5.10</a></div>
			</div>
		</div>
	<?php
?>
<?php include("$currDir/footer.php"); ?>
