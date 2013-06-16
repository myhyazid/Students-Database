<?php

// Data functions for table pentadbir

// This script and data application were generated by AppGini 5.10
// Download AppGini for free from http://bigprof.com/appgini/download/

function pentadbir_insert(){
	global $Translation;

	if($_GET['insert_x']!=''){$_POST=$_GET;}

	// mm: can member insert record?
	$arrPerm=getTablePermissions('pentadbir');
	if(!$arrPerm[1]){
		return 0;
	}

	$data['pnama'] = makeSafe($_POST['pnama']);
	$data['pnokp'] = makeSafe($_POST['pnokp']);
	$data['ppass'] = makeSafe($_POST['ppass']);
	$data['plevel'] = makeSafe($_POST['plevel']);
	$data['pjawatan'] = makeSafe($_POST['pjawatan']);
	$data['pemel'] = makeSafe($_POST['pemel']);

	// hook: pentadbir_before_insert
	if(function_exists('pentadbir_before_insert')){
		$args=array();
		if(!pentadbir_before_insert($data, getMemberInfo(), $args)){ return FALSE; }
	}

	$o=array('silentErrors' => true);
	sql('insert into `pentadbir` set       `pnama`=' . (($data['pnama'] !== '' && $data['pnama'] !== NULL) ? "'{$data['pnama']}'" : 'NULL') . ', `pnokp`=' . (($data['pnokp'] !== '' && $data['pnokp'] !== NULL) ? "'{$data['pnokp']}'" : 'NULL') . ', `ppass`=' . (($data['ppass'] !== '' && $data['ppass'] !== NULL) ? "'{$data['ppass']}'" : 'NULL') . ', `plevel`=' . (($data['plevel'] !== '' && $data['plevel'] !== NULL) ? "'{$data['plevel']}'" : 'NULL') . ', `pjawatan`=' . (($data['pjawatan'] !== '' && $data['pjawatan'] !== NULL) ? "'{$data['pjawatan']}'" : 'NULL') . ', `pemel`=' . (($data['pemel'] !== '' && $data['pemel'] !== NULL) ? "'{$data['pemel']}'" : 'NULL'), $o);
	if($o['error']!=''){
		echo $o['error'];
		echo "<a href=\"pentadbir_view.php?addNew_x=1\">{$Translation['< back']}</a>";
		exit;
	}

	$recID=mysql_insert_id();

	// hook: pentadbir_after_insert
	if(function_exists('pentadbir_after_insert')){
		$res = sql("select * from `pentadbir` where `pid`='" . makeSafe($recID) . "' limit 1", $eo);
		if($row = mysql_fetch_assoc($res)){
			$data = $row;
		}
		$data['selectedID']=$recID;
		$args=array();
		if(!pentadbir_after_insert($data, getMemberInfo(), $args)){ return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID); }
	}

	// mm: save ownership data
	sql("insert into membership_userrecords set tableName='pentadbir', pkValue='$recID', memberID='".getLoggedMemberID()."', dateAdded='".time()."', dateUpdated='".time()."', groupID='".getLoggedGroupID()."'", $eo);

	return (get_magic_quotes_gpc() ? stripslashes($recID) : $recID);
}

function pentadbir_delete($selected_id, $AllowDeleteOfParents=false, $skipChecks=false){
	// insure referential integrity ...
	global $Translation;
	$selected_id=makeSafe($selected_id);

	// mm: can member delete record?
	$arrPerm=getTablePermissions('pentadbir');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='pentadbir' and pkValue='$selected_id'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='pentadbir' and pkValue='$selected_id'");
	if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
		// delete allowed, so continue ...
	}else{
		return FALSE;
	}

	// hook: pentadbir_before_delete
	if(function_exists('pentadbir_before_delete')){
		$args=array();
		if(!pentadbir_before_delete($selected_id, $skipChecks, getMemberInfo(), $args)){ return FALSE; }
	}

	sql("delete from `pentadbir` where `pid`='$selected_id'", $eo);

	// hook: pentadbir_after_delete
	if(function_exists('pentadbir_after_delete')){
		$args=array();
		pentadbir_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("delete from membership_userrecords where tableName='pentadbir' and pkValue='$selected_id'", $eo);
}

function pentadbir_update($selected_id){
	global $Translation;

	if($_GET['update_x']!=''){$_POST=$_GET;}

	// mm: can member edit record?
	$arrPerm=getTablePermissions('pentadbir');
	$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='pentadbir' and pkValue='".makeSafe($selected_id)."'");
	$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='pentadbir' and pkValue='".makeSafe($selected_id)."'");
	if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){ // allow update?
		// update allowed, so continue ...
	}else{
		return;
	}

	$data['pnama'] = makeSafe($_POST['pnama']);
	$data['pnokp'] = makeSafe($_POST['pnokp']);
	$data['ppass'] = makeSafe($_POST['ppass']);
	$data['plevel'] = makeSafe($_POST['plevel']);
	$data['pjawatan'] = makeSafe($_POST['pjawatan']);
	$data['pemel'] = makeSafe($_POST['pemel']);
	$data['selectedID']=makeSafe($selected_id);

	// hook: pentadbir_before_update
	if(function_exists('pentadbir_before_update')){
		$args=array();
		if(!pentadbir_before_update($data, getMemberInfo(), $args)){ return FALSE; }
	}

	$o=array('silentErrors' => true);
	sql('update `pentadbir` set       `pnama`=' . (($data['pnama'] !== '' && $data['pnama'] !== NULL) ? "'{$data['pnama']}'" : 'NULL') . ', `pnokp`=' . (($data['pnokp'] !== '' && $data['pnokp'] !== NULL) ? "'{$data['pnokp']}'" : 'NULL') . ', `ppass`=' . (($data['ppass'] !== '' && $data['ppass'] !== NULL) ? "'{$data['ppass']}'" : 'NULL') . ', `plevel`=' . (($data['plevel'] !== '' && $data['plevel'] !== NULL) ? "'{$data['plevel']}'" : 'NULL') . ', `pjawatan`=' . (($data['pjawatan'] !== '' && $data['pjawatan'] !== NULL) ? "'{$data['pjawatan']}'" : 'NULL') . ', `pemel`=' . (($data['pemel'] !== '' && $data['pemel'] !== NULL) ? "'{$data['pemel']}'" : 'NULL') . " where `pid`='".makeSafe($selected_id)."'", $o);
	if($o['error']!=''){
		echo $o['error'];
		echo '<a href="pentadbir_view.php?SelectedID='.urlencode($selected_id)."\">{$Translation['< back']}</a>";
		exit;
	}


	// hook: pentadbir_after_update
	if(function_exists('pentadbir_after_update')){
		$res = sql("SELECT * FROM `pentadbir` WHERE `pid`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = mysql_fetch_assoc($res)){
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = $data['pid'];
		$args = array();
		if(!pentadbir_after_update($data, getMemberInfo(), $args)){ return FALSE; }
	}

	// mm: update ownership data
	sql("update membership_userrecords set dateUpdated='".time()."' where tableName='pentadbir' and pkValue='".makeSafe($selected_id)."'", $eo);

}

function pentadbir_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0){
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm=getTablePermissions('pentadbir');
	if(!$arrPerm[1] && $selected_id==''){ return ''; }
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != ''){
		$dvprint = true;
	}


	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');

	if($selected_id){
		// mm: check member permissions
		if(!$arrPerm[2]){
			return "";
		}
		// mm: who is the owner?
		$ownerGroupID=sqlValue("select groupID from membership_userrecords where tableName='pentadbir' and pkValue='".makeSafe($selected_id)."'");
		$ownerMemberID=sqlValue("select lcase(memberID) from membership_userrecords where tableName='pentadbir' and pkValue='".makeSafe($selected_id)."'");
		if($arrPerm[2]==1 && getLoggedMemberID()!=$ownerMemberID){
			return "";
		}
		if($arrPerm[2]==2 && getLoggedGroupID()!=$ownerGroupID){
			return "";
		}

		// can edit?
		if(($arrPerm[3]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[3]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[3]==3){
			$AllowUpdate=1;
		}else{
			$AllowUpdate=0;
		}

		$res = sql("select * from `pentadbir` where `pid`='".makeSafe($selected_id)."'", $eo);
		$row = mysql_fetch_array($res);
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
	}else{
	}

	// code for template based detail view forms

	// open the detail view template
	if($dvprint){
		$templateCode = @file_get_contents('./templates/pentadbir_templateDVP.html');
	}else{
		$templateCode = @file_get_contents('./templates/pentadbir_templateDV.html');
	}

	// process form title
	$templateCode=str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Detail View', $templateCode);
	$templateCode=str_replace('<%%RND1%%>', $rnd1, $templateCode);
	// process buttons
	if($arrPerm[1]){ // allow insert?
		if(!$selected_id) $templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="positive" id="insert" name="insert_x" value="1" onclick="return pentadbir_validateData();"><img src="addNew.gif" /> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '<button tabindex="2" type="submit" class="positive" id="insert" name="insert_x" value="1" onclick="return pentadbir_validateData();"><img src="addNew.gif" /> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']){
		$backAction = 'parent.Modalbox.hide(); return false;';
	}else{
		$backAction = '$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id){
		$templateCode=str_replace('<%%DVPRINT_BUTTON%%>', '<button tabindex="2" type="submit" id="dvprint" name="dvprint_x" value="1" onclick="$$(\'form\')[0].writeAttribute(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;"><img src="print-preview.gif" /> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate){
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '<button tabindex="2" type="submit" class="positive" id="update" name="update_x" value="1" onclick="return pentadbir_validateData();"><img src="update.gif" /> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

			// set records to read only if user can't insert new records
			if(!$arrPerm[1]){
				$jsReadOnly.="\n\n\tif(document.getElementsByName('pid').length){ document.getElementsByName('pid')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('pnama').length){ document.getElementsByName('pnama')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('pnokp').length){ document.getElementsByName('pnokp')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('ppass').length){ document.getElementsByName('ppass')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('plevel').length){ document.getElementsByName('plevel')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('pjawatan').length){ document.getElementsByName('pjawatan')[0].readOnly=true; }\n";
				$jsReadOnly.="\n\n\tif(document.getElementsByName('pemel').length){ document.getElementsByName('pemel')[0].readOnly=true; }\n";

				$noUploads=true;
			}
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3){ // allow delete?
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '<button tabindex="2" type="submit" class="negative" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');"><img src="delete.gif" /> ' . $Translation['Delete'] . '</button>', $templateCode);
		}else{
			$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', '<button tabindex="2" type="submit" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '"><img src="deselect.gif" /> ' . $Translation['Back'] . '</button>', $templateCode);
	}else{
		$templateCode=str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode=str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button tabindex="2" type="submit" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '"><img src="deselect.gif" /> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// process combos

	// process foreign key links
	if($selected_id){
	}

	// process images
	$templateCode=str_replace('<%%UPLOADFILE(pid)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(pnama)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(pnokp)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(ppass)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(plevel)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(pjawatan)%%>', '', $templateCode);
	$templateCode=str_replace('<%%UPLOADFILE(pemel)%%>', '', $templateCode);

	// process values
	if($selected_id){
		$templateCode=str_replace('<%%VALUE(pid)%%>', htmlspecialchars($row['pid'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pid)%%>', urlencode($urow['pid']), $templateCode);
		$templateCode=str_replace('<%%VALUE(pnama)%%>', htmlspecialchars($row['pnama'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pnama)%%>', urlencode($urow['pnama']), $templateCode);
		$templateCode=str_replace('<%%VALUE(pnokp)%%>', htmlspecialchars($row['pnokp'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pnokp)%%>', urlencode($urow['pnokp']), $templateCode);
		$templateCode=str_replace('<%%VALUE(ppass)%%>', htmlspecialchars($row['ppass'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ppass)%%>', urlencode($urow['ppass']), $templateCode);
		$templateCode=str_replace('<%%VALUE(plevel)%%>', htmlspecialchars($row['plevel'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(plevel)%%>', urlencode($urow['plevel']), $templateCode);
		$templateCode=str_replace('<%%VALUE(pjawatan)%%>', htmlspecialchars($row['pjawatan'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pjawatan)%%>', urlencode($urow['pjawatan']), $templateCode);
		$templateCode=str_replace('<%%VALUE(pemel)%%>', htmlspecialchars($row['pemel'], ENT_QUOTES), $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pemel)%%>', urlencode($urow['pemel']), $templateCode);
	}else{
		$templateCode=str_replace('<%%VALUE(pid)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pid)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(pnama)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pnama)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(pnokp)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pnokp)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(ppass)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(ppass)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(plevel)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(plevel)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(pjawatan)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pjawatan)%%>', urlencode(''), $templateCode);
		$templateCode=str_replace('<%%VALUE(pemel)%%>', '', $templateCode);
		$templateCode=str_replace('<%%URLVALUE(pemel)%%>', urlencode(''), $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans){
		$templateCode=str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode=str_replace('<%%', '<!--', $templateCode);
	$templateCode=str_replace('%%>', '-->', $templateCode);

	// hide links to inaccessible tables
	if($_POST['dvprint_x']==''){
		$templateCode.="\n\n<script>\n";
		$arrTables=getTableList();
		foreach($arrTables as $name=>$caption){
			$templateCode.="\tif(document.getElementById('".$name."_link')!=undefined){\n";
			$templateCode.="\t\tdocument.getElementById('".$name."_link').style.visibility='visible';\n";
			$templateCode.="\t}\n";
			for($i=1; $i<10; $i++){
				$templateCode.="\tif(document.getElementById('".$name."_plink$i')!=undefined){\n";
				$templateCode.="\t\tdocument.getElementById('".$name."_plink$i').style.visibility='visible';\n";
				$templateCode.="\t}\n";
			}
		}

		$templateCode.=$jsReadOnly;

		if(!$selected_id){
		}

		$templateCode.="\n</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode.="<script>";
	$templateCode.="document.observe('dom:loaded', function() {";


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode=preg_replace('/blank.gif" rel="lightbox\[.*?\]"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	// hook: pentadbir_dv
	if(function_exists('pentadbir_dv')){
		$args=array();
		pentadbir_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}
?>