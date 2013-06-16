<?php
	$currDir=dirname(__FILE__);
	require("$currDir/incCommon.php");

	// get groupID of anonymous group
	$anonGroupID=sqlValue("select groupID from membership_groups where name='".$adminConfig['anonymousGroup']."'");

	// request to save changes?
	if($_POST['saveChanges']!=''){
		// validate data
		$name=makeSafe($_POST['name']);
		$description=makeSafe($_POST['description']);
		switch($_POST['visitorSignup']){
			case 0:
				$allowSignup=0;
				$needsApproval=1;
				break;
			case 2:
				$allowSignup=1;
				$needsApproval=0;
				break;
			default:
				$allowSignup=1;
				$needsApproval=1;
		}
		###############################
		$user_insert=checkPermissionVal('user_insert');
		$user_view=checkPermissionVal('user_view');
		$user_edit=checkPermissionVal('user_edit');
		$user_delete=checkPermissionVal('user_delete');
		###############################
		$maklumat_insert=checkPermissionVal('maklumat_insert');
		$maklumat_view=checkPermissionVal('maklumat_view');
		$maklumat_edit=checkPermissionVal('maklumat_edit');
		$maklumat_delete=checkPermissionVal('maklumat_delete');
		###############################
		$guru_insert=checkPermissionVal('guru_insert');
		$guru_view=checkPermissionVal('guru_view');
		$guru_edit=checkPermissionVal('guru_edit');
		$guru_delete=checkPermissionVal('guru_delete');
		###############################
		$pentadbir_insert=checkPermissionVal('pentadbir_insert');
		$pentadbir_view=checkPermissionVal('pentadbir_view');
		$pentadbir_edit=checkPermissionVal('pentadbir_edit');
		$pentadbir_delete=checkPermissionVal('pentadbir_delete');
		###############################

		// new group or old?
		if($_POST['groupID']==''){ // new group
			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name'")){
				echo "<div class=\"error\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// add group
			sql("insert into membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval'", $eo);

			// get new groupID
			$groupID=mysql_insert_id();

		}else{ // old group
			// validate groupID
			$groupID=intval($_POST['groupID']);

			if($groupID==$anonGroupID){
				$name=$adminConfig['anonymousGroup'];
				$allowSignup=0;
				$needsApproval=0;
			}

			// make sure group name is unique
			if(sqlValue("select count(1) from membership_groups where name='$name' and groupID!='$groupID'")){
				echo "<div class=\"error\">Error: Group name already exists. You must choose a unique group name.</div>";
				include("$currDir/incFooter.php");
			}

			// update group
			sql("update membership_groups set name='$name', description='$description', allowSignup='$allowSignup', needsApproval='$needsApproval' where groupID='$groupID'", $eo);

			// reset then add group permissions
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='user'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='maklumat'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='guru'", $eo);
			sql("delete from membership_grouppermissions where groupID='$groupID' and tableName='pentadbir'", $eo);
		}

		// add group permissions
		if($groupID){
			// table 'user'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='user', allowInsert='$user_insert', allowView='$user_view', allowEdit='$user_edit', allowDelete='$user_delete'", $eo);
			// table 'maklumat'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='maklumat', allowInsert='$maklumat_insert', allowView='$maklumat_view', allowEdit='$maklumat_edit', allowDelete='$maklumat_delete'", $eo);
			// table 'guru'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='guru', allowInsert='$guru_insert', allowView='$guru_view', allowEdit='$guru_edit', allowDelete='$guru_delete'", $eo);
			// table 'pentadbir'
			sql("insert into membership_grouppermissions set groupID='$groupID', tableName='pentadbir', allowInsert='$pentadbir_insert', allowView='$pentadbir_view', allowEdit='$pentadbir_edit', allowDelete='$pentadbir_delete'", $eo);
		}

		// redirect to group editing page
		redirect("admin/pageEditGroup.php?groupID=$groupID");

	}elseif($_GET['groupID']!=''){
		// we have an edit request for a group
		$groupID=intval($_GET['groupID']);
	}

	include("$currDir/incHeader.php");

	if($groupID!=''){
		// fetch group data to fill in the form below
		$res=sql("select * from membership_groups where groupID='$groupID'", $eo);
		if($row=mysql_fetch_assoc($res)){
			// get group data
			$name=$row['name'];
			$description=$row['description'];
			$visitorSignup=($row['allowSignup']==1 && $row['needsApproval']==1 ? 1 : ($row['allowSignup']==1 ? 2 : 0));

			// get group permissions for each table
			$res=sql("select * from membership_grouppermissions where groupID='$groupID'", $eo);
			while($row=mysql_fetch_assoc($res)){
				$tableName=$row['tableName'];
				$vIns=$tableName."_insert";
				$vUpd=$tableName."_edit";
				$vDel=$tableName."_delete";
				$vVue=$tableName."_view";
				$$vIns=$row['allowInsert'];
				$$vUpd=$row['allowEdit'];
				$$vDel=$row['allowDelete'];
				$$vVue=$row['allowView'];
			}
		}else{
			// no such group exists
			echo "<div class=\"error\">Error: Group not found!</div>";
			$groupID=0;
		}
	}
?>
<h1><?php echo ($groupID ? "Edit Group '$name'" : "Add New Group"); ?></h1>
<?php if($anonGroupID==$groupID){ ?>
	<div class="status">Attention! This is the anonymous group.</div>
<?php } ?>
<input type="checkbox" id="showToolTips" value="1" checked><label for="showToolTips">Show tool tips as mouse moves over options</label>
<form method="post" action="pageEditGroup.php">
	<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="right" class="tdFormCaption" valign="top">
				<div class="formFieldCaption">Group name</div>
				</td>
			<td align="left" class="tdFormInput">
				<input type="text" name="name" <?php echo ($anonGroupID==$groupID ? "readonly" : ""); ?> value="<?php echo $name; ?>" size="20" class="formTextBox">
				<br />
				<?php if($anonGroupID==$groupID){ ?>
					The name of the anonymous group is read-only here.
				<?php }else{ ?>
					If you name the group '<?php echo $adminConfig['anonymousGroup']; ?>', it will be considered the anonymous group<br />
					that defines the permissions of guest visitors that do not log into the system.
				<?php } ?>
				</td>
			</tr>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Description</div>
				</td>
			<td align="left" class="tdFormInput">
				<textarea name="description" cols="50" rows="5" class="formTextBox"><?php echo $description; ?></textarea>
				</td>
			</tr>
		<?php if($anonGroupID!=$groupID){ ?>
		<tr>
			<td align="right" valign="top" class="tdFormCaption">
				<div class="formFieldCaption">Allow visitors to sign up?</div>
				</td>
			<td align="left" class="tdFormInput">
				<?php
					echo htmlRadioGroup(
						"visitorSignup",
						array(0, 1, 2),
						array(
							"No. Only the admin can add users.",
							"Yes, and the admin must approve them.",
							"Yes, and automatically approve them."
						),
						($groupID ? $visitorSignup : $adminConfig['defaultSignUp'])
					);
				?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		<tr>
			<td colspan="2" class="tdFormHeader">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td class="tdFormHeader" colspan="5"><h2>Table permissions for this group</h2></td>
						</tr>
					<?php
						// permissions arrays common to the radio groups below
						$arrPermVal=array(0, 1, 2, 3);
						$arrPermText=array("No", "Owner", "Group", "All");
					?>
					<tr>
						<td class="tdHeader"><div class="ColCaption">Table</div></td>
						<td class="tdHeader"><div class="ColCaption">Insert</div></td>
						<td class="tdHeader"><div class="ColCaption">View</div></td>
						<td class="tdHeader"><div class="ColCaption">Edit</div></td>
						<td class="tdHeader"><div class="ColCaption">Delete</div></td>
						</tr>
				<!-- user table -->
					<tr>
						<td class="tdCaptionCell" valign="top">User</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(user_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="user_insert" value="1" <?php echo ($user_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("user_view", $arrPermVal, $arrPermText, $user_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("user_edit", $arrPermVal, $arrPermText, $user_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("user_delete", $arrPermVal, $arrPermText, $user_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- maklumat table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Maklumat</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(maklumat_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="maklumat_insert" value="1" <?php echo ($maklumat_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("maklumat_view", $arrPermVal, $arrPermText, $maklumat_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("maklumat_edit", $arrPermVal, $arrPermText, $maklumat_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("maklumat_delete", $arrPermVal, $arrPermText, $maklumat_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- guru table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Guru</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(guru_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="guru_insert" value="1" <?php echo ($guru_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("guru_view", $arrPermVal, $arrPermText, $guru_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("guru_edit", $arrPermVal, $arrPermText, $guru_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("guru_delete", $arrPermVal, $arrPermText, $guru_delete, "highlight");
							?>
							</td>
						</tr>
				<!-- pentadbir table -->
					<tr>
						<td class="tdCaptionCell" valign="top">Pentadbir</td>
						<td class="tdCell" valign="top">
							<input onMouseOver="stm(pentadbir_addTip, toolTipStyle);" onMouseOut="htm();" type="checkbox" name="pentadbir_insert" value="1" <?php echo ($pentadbir_insert ? "checked class=\"highlight\"" : ""); ?>>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("pentadbir_view", $arrPermVal, $arrPermText, $pentadbir_view, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("pentadbir_edit", $arrPermVal, $arrPermText, $pentadbir_edit, "highlight");
							?>
							</td>
						<td class="tdCell">
							<?php
								echo htmlRadioGroup("pentadbir_delete", $arrPermVal, $arrPermText, $pentadbir_delete, "highlight");
							?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<tr>
			<td colspan="2" align="right" class="tdFormFooter">
				<input type="submit" name="saveChanges" value="Save changes">
				</td>
			</tr>
		</table>
</form>


<?php
	include("$currDir/incFooter.php");
?>