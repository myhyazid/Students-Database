<?php
// This script and data application were generated by AppGini 5.10
// Download AppGini for free from http://bigprof.com/appgini/download/

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/guru.php");
	include("$currDir/guru_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('guru');
	if(!$perm[0]){
		echo StyleSheet();
		echo "<div class=\"error\">".$Translation['tableAccessDenied']."</div>";
		echo '<script language="javaScript">setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "guru";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV=array(   
		"`guru`.`gid`" => "gid",
		"`guru`.`gnama`" => "gnama",
		"`guru`.`gnokp`" => "gnokp",
		"`guru`.`gpass`" => "gpass",
		"`guru`.`glevel`" => "glevel",
		"`guru`.`gjawatan`" => "gjawatan",
		"`guru`.`gpersatuan`" => "gpersatuan"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`guru`.`gid`',
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV=array(   
		"`guru`.`gid`" => "gid",
		"`guru`.`gnama`" => "gnama",
		"`guru`.`gnokp`" => "gnokp",
		"`guru`.`gpass`" => "gpass",
		"`guru`.`glevel`" => "glevel",
		"`guru`.`gjawatan`" => "gjawatan",
		"`guru`.`gpersatuan`" => "gpersatuan"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters=array(   
		"`guru`.`gid`" => "Gid",
		"`guru`.`gnama`" => "Gnama",
		"`guru`.`gnokp`" => "Gnokp",
		"`guru`.`gpass`" => "Gpass",
		"`guru`.`glevel`" => "Glevel",
		"`guru`.`gjawatan`" => "Gjawatan",
		"`guru`.`gpersatuan`" => "Gpersatuan"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS=array(   
		"`guru`.`gid`" => "gid",
		"`guru`.`gnama`" => "gnama",
		"`guru`.`gnokp`" => "gnokp",
		"`guru`.`gpass`" => "gpass",
		"`guru`.`glevel`" => "glevel",
		"`guru`.`gjawatan`" => "gjawatan",
		"`guru`.`gpersatuan`" => "gpersatuan"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array();

	$x->QueryFrom="`guru` ";
	$x->QueryWhere='';
	$x->QueryOrder='';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingMultiSelection = 0;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 3;
	$x->TablePaginationAlignment = 0;
	$x->QuickSearchText = $Translation["quick search"];
	$x->ScriptFileName = "guru_view.php";
	$x->RedirectAfterInsert = "guru_view.php?SelectedID=#ID#";
	$x->TableTitle = "Guru";
	$x->TableIcon = "table.gif";
	$x->PrimaryKey = "`guru`.`gid`";

	$x->ColWidth   = array(  150, 150, 150, 150, 150, 150);
	$x->ColCaption = array("Gnama", "Gnokp", "Gpass", "Glevel", "Gjawatan", "Gpersatuan");
	$x->ColNumber  = array(2, 3, 4, 5, 6, 7);

	$x->Template = 'templates/guru_templateTV.html';
	$x->SelectedTemplate = 'templates/guru_templateTVS.html';
	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `guru`.`gid`=membership_userrecords.pkValue and membership_userrecords.tableName='guru' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `guru`.`gid`=membership_userrecords.pkValue and membership_userrecords.tableName='guru' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}elseif($perm[2]==3){ // view all
		// no further action
	}elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`guru`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: guru_init
	$render=TRUE;
	if(function_exists('guru_init')){
		$args=array();
		$render=guru_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: guru_header
	$headerCode='';
	if(function_exists('guru_header')){
		$args=array();
		$headerCode=guru_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include("$currDir/header.php"); 
	}else{
		ob_start(); include("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: guru_footer
	$footerCode='';
	if(function_exists('guru_footer')){
		$args=array();
		$footerCode=guru_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include("$currDir/footer.php"); 
	}else{
		ob_start(); include("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?><div style="text-align: center; font-size: 10px;">Powered by <a href="http://bigprof.com/appgini/" target="_blank">BigProf AppGini 5.10</a></div>
