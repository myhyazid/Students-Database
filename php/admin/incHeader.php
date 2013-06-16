<!doctype html public "-//W3C//DTD html 4.0 //en">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Membership Management</title>

		<script src="../resources/jquery/js/jquery.min.js"></script>
		<script>var $j = jQuery.noConflict();</script>
		<script language="JavaScript1.2" src="toolTips.js" type="text/javascript"></script>
		<script src="../resources/lightbox/js/prototype.js"></script>
		<script src="../resources/lightbox/js/scriptaculous.js?load=effects,builder,dragdrop,controls"></script>
		<script src="../resources/modalbox/modalbox.js"></script>
		<script>

			// VALIDATION FUNCTIONS FOR VARIOUS PAGES

			function jsValidateMember(){
				var p1=document.getElementById('password').value;
				var p2=document.getElementById('confirmPassword').value;
				if(p1=='' || p1==p2){
					return true;
				}else{
					Modalbox.show('<div class="highlight" style="width: 90%; margin: 0;">Password doesn\'t match.</div>', { title: "Error" });
					return false;
				}
			}

			function jsValidateEmail(address){
				var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				if(reg.test(address) == false){
					Modalbox.show('<div class="highlight" style="width: 90%; margin: 0;">Invalid Email Address</div>', { title: "Error" });
					return false;
				}else{
					return true;
				}
			}

			function jsShowWait(){
				return window.confirm("Sending mails might take some time. Please don't close this page until you see the 'Done' message.");
			}

			function jsValidateAdminSettings(){
				var p1=document.getElementById('adminPassword').value;
				var p2=document.getElementById('confirmPassword').value;
				if(p1=='' || p1==p2){
					return jsValidateEmail(document.getElementById('senderEmail').value);
				}else{
					Modalbox.show('<div class="highlight" style="width: 90%; margin: 0;">Password doesn\'t match.</div>', { title: "Error" });
					return false;
				}
			}

			function jsConfirmTransfer(){
				var sg=document.getElementById('sourceGroupID').options[document.getElementById('sourceGroupID').selectedIndex].text;
				var sm=document.getElementById('sourceMemberID').value;
				var dg=document.getElementById('destinationGroupID').options[document.getElementById('destinationGroupID').selectedIndex].text;
				if(document.getElementById('destinationMemberID')){
					var dm=document.getElementById('destinationMemberID').value;
				}
				if(document.getElementById('dontMoveMembers')){
					var dmm=document.getElementById('dontMoveMembers').checked;
				}
				if(document.getElementById('moveMembers')){
					var mm=document.getElementById('moveMembers').checked;
				}

				//confirm('sg='+sg+'\n'+'sm='+sm+'\n'+'dg='+dg+'\n'+'dm='+dm+'\n'+'mm='+mm+'\n'+'dmm='+dmm+'\n');

				if(dmm && !dm){
					Modalbox.show('<div>Please complete step 4 by selecting the member you want to transfer records to.</div>', { title: "Info", afterHide: function(){ document.getElementById('destinationMemberID').focus(); } });
					return false;
				}

				if(mm && sm!='-1'){
					return window.confirm('Are you sure you want to move member \''+sm+'\' and his data from group \''+sg+'\' to group \''+dg+'\'?');
				}
				if((dmm || dm) && sm!='-1'){
					return window.confirm('Are you sure you want to move data of member \''+sm+'\' from group \''+sg+'\' to member \''+dm+'\' from group \''+dg+'\'?');
				}

				if(mm){
					return window.confirm('Are you sure you want to move all members and data from group \''+sg+'\' to group \''+dg+'\'?');
				}

				if(dmm){
					return window.confirm('Are you sure you want to move data of all members of group \''+sg+'\' to member \''+dm+'\' from group \''+dg+'\'?');
				}
			}

			function showDialog(dialogId){
				$$('.dialog-box').invoke('addClassName', 'hidden-block');
				$(dialogId).removeClassName('hidden-block');
				return false
			};

			function hideDialogs(){
				$$('.dialog-box').invoke('addClassName', 'hidden-block');
				return false
			};
		</script>

		<link rel="stylesheet" type="text/css" href="adminStyles.css">
		<link rel="stylesheet" type="text/css" href="../resources/modalbox/modalbox.css">

		<style>
			.dialog-box{
				background-color: white;
				border: 1px solid silver;
				border-radius: 10px 10px 10px 10px;
				box-shadow: 0 3px 100px silver;
				left: 30%;
				padding: 10px;
				position: absolute;
				top: 20%;
				width: 40%;
			}
			.hidden-block{
				display: none;
			}
		</style>
	</head>
	<body>
		<!-- tool tips support -->
		<div id="TipLayer" style="visibility:hidden;position:absolute;z-index:1000;top:-100"></div>
		<script language="JavaScript1.2" src="toolTipData.js" type="text/javascript"></script>
		<!-- /tool tips support -->

		<div align="center">
		<a href="pageHome.php" class="navLink">Admin Home</a> |
		<a href="../" class="navLink">Users' Area</a> |
		<a href="pageViewGroups.php" class="navLink">View Groups</a> |
		<a href="pageEditGroup.php" class="navLink">Add Group</a> |
		<a href="pageViewMembers.php" class="navLink">View Members</a> |
		<a href="pageEditMember.php" class="navLink">Add Member</a> |
		<a href="pageViewRecords.php" class="navLink">View Members' Records</a> |
		<a href="pageUploadCSV.php" class="navLink">Import CSV data</a> |
		<a href="pageHome.php?signOut=1" class="navLink">Sign Out</a>

<?php
	if(!strstr($_SERVER['PHP_SELF'], 'pageSettings.php') && $adminConfig['adminUsername']=='admin' && $adminConfig['adminPassword']==md5('admin')){
		$noSignup=TRUE;
		?>
		<div class="error">
			<i>Attention!</i>
			<br />You are using the default admin username and password. This is a huge security
			risk. Please change the admin password from the
			<a href="pageSettings.php">Admin Settings</a> page <i>immediately</i>.
			</div>
		<?php
	}elseif(!strstr($_SERVER['PHP_SELF'], 'pageSettings.php') && $adminConfig['adminPassword'] == md5('admin')){
		$noSignup = TRUE;
		?>
		<div class="error">
			<i>Attention!</i>
			<br />You are using the default admin password. This is a huge security
			risk. Please change the admin password from the
			<a href="pageSettings.php">Admin Settings</a> page <i>immediately</i>.
			</div>
		<?php
	}
?>
