<?php
// This script and data application were generated by AppGini 5.10
// Download AppGini for free from http://bigprof.com/appgini/download/


error_reporting(E_ALL ^ E_NOTICE);

if(function_exists('date_default_timezone_set')) @date_default_timezone_set('America/New_York');
if(function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0);

$currDir=dirname(__FILE__);
include("$currDir/settings-manager.php");
detect_config();
migrate_config();

include("$currDir/config.php");
include("$currDir/incCommon.php");
include("$currDir/ci_input.php");
include("$currDir/datalist.php");
@include("$currDir/hooks/links-navmenu.php");
function sql($statment, &$o){

	/*
		Supported options that can be passed in $o options array (as array keys):
		'silentErrors': If true, errors will be returned in $o['error'] rather than displaying them on screen and exiting.
	*/

	global $Translation;
	global $dbServer, $dbUsername, $dbPassword, $dbDatabase;
	static $connected=false;

	ob_start();

	if(!$connected){
		/****** Connect to MySQL ******/
		if(!extension_loaded('mysql')){
			echo "<div class=Error>ERROR: PHP is not configured to connect to MySQL on this machine. Please see <a href=http://www.php.net/manual/en/ref.mysql.php>this page</a> for help on how to configure MySQL.</div>";
			$e=ob_get_contents(); ob_end_clean(); if($o['silentErrors']){ $o['error']=$e; return FALSE; }else{ echo $e; exit; }
		}

		if(!@mysql_connect($dbServer, $dbUsername, $dbPassword)){
			echo StyleSheet() . "\n\n<div class=Error>";
			echo $Translation["error:"] . mysql_error();
			echo "</div>";
			$e=ob_get_contents(); ob_end_clean(); if($o['silentErrors']){ $o['error']=$e; return FALSE; }else{ echo $e; exit; }
		}

		/****** Connection Charset ********/
		@mysql_query("SET NAMES 'utf8'");

		/****** Select DB ********/
		if(!mysql_select_db($dbDatabase)){
			echo StyleSheet() . "\n\n<div class=Error>";
			echo $Translation["error:"] . mysql_error();
			echo "</div>";
			$e=ob_get_contents(); ob_end_clean(); if($o['silentErrors']){ $o['error']=$e; return FALSE; }else{ echo $e; exit; }
		}

		$connected=true;
	}

	if(!$result = @mysql_query($statment)){
		if(!stristr($statment, "show columns")){
			// retrieve error codes
			$errorNum=mysql_errno();
			$errorMsg=mysql_error();

			echo StyleSheet() . "\n\n<div class=Error>";
			echo "<br /><b>" . $Translation["error:"] . "</b> ".htmlspecialchars($errorMsg)."\n\n<!--\n" . $Translation["query:"] . "\n $statment\n-->\n\n";

			echo "</div>";
			$e=ob_get_contents(); ob_end_clean(); if($o['silentErrors']){ $o['error']=$errorMsg; return FALSE; }else{ echo $e; exit; }
		}
	}

	ob_end_clean();
	return $result;
}

function NavMenus(){
	global $Translation;

	$t = time();
	$menu  = "<select tabindex=\"1\" name=\"nav_menu\" onChange=\"window.location=this.options[this.selectedIndex].value;\">";
	$menu .= "<option value='#' class=SelectedOption style='color:black;'>" . $Translation["select a table"] . "</option>";
	$menu .= "<option value='index.php' class=SelectedOption style='color:black;'>" . $Translation["homepage"] . "</option>";
	if(getLoggedAdmin()){
		$menu .= "<option value='admin/' class=SelectedOption style='color:red;'>" . $Translation['admin area'] . "</option>";
	}
	$arrTables=getTableList();
	if(is_array($arrTables)){
		foreach($arrTables as $tn=>$tc){
			$tChkHL = array_search($tn, array());
			if($tChkHL !== false && $tChkHL !== null) continue;

			$tChkFF = array_search($tn, array());
			if($tChkFF !== false && $tChkFF !== null){
				$searchFirst = '&Filter_x=1';
			}else{
				$searchFirst = '';
			}
			$menu .= "<option value='".$tn."_view.php?t=$t$searchFirst' class=SelectedOption>$tc[0]</option>";
		}
	}

	// custom nav links, as defined in "hooks/links-navmenu.php"
	global $navLinks;
	if(is_array($navLinks)){
		$memberInfo = getMemberInfo();
		foreach($navLinks as $link){
			if(!isset($link['url']) || !isset($link['title'])) continue;
			if($memberInfo['admin'] || @in_array($memberInfo['group'], $link['groups']) || @in_array('*', $link['groups'])){
				$menu .= "<option value=\"{$link['url']}\">{$link['title']}</option>";
			}
		}
	}

	$menu .= "</select>";
	return $menu;
}

function StyleSheet(){
	return '<link rel="stylesheet" type="text/css" href="style.css">';
}

function getUploadDir($dir){
	global $Translation;

	if($dir==""){
		$dir=$Translation['ImageFolder'];
	}

	if(substr($dir, -1)!="/"){
		$dir.="/";
	}

	return $dir;
}

function PrepareUploadedFile($FieldName, $MaxSize, $FileTypes='jpg|jpeg|gif|png', $NoRename=false, $dir=""){
	global $Translation;
	$f = $_FILES[$FieldName];

	$dir=getUploadDir($dir);

	if($f['error'] != 4 && $f['name']!=''){
		if($f['size']>$MaxSize || $f['error']){
			echo StyleSheet()."<div class=Error>".str_replace("<MaxSize>", intval($MaxSize/1024), $Translation['file too large']).". <a href=".$_SERVER['HTTP_REFERER'].">".$Translation["< back"]."</a>.</div>";
			exit;
		}
		if(!preg_match('/\.('.$FileTypes.')$/i', $f['name'], $ft)){
			echo StyleSheet()."<div class=Error>".str_replace("<FileTypes>", str_replace('|', ', ', $FileTypes), $Translation['invalid file type']).". <a href=".$_SERVER['HTTP_REFERER'].">".$Translation["< back"]."</a>.</div>";
			exit;
		}

		if($NoRename){
			$n  = str_replace(' ', '_', $f['name']);
		}else{
			$n  = microtime();
			$n  = str_replace(' ', '_', $n);
			$n  = str_replace('0.', '', $n);
			$n .= $ft[0];
		}

		if(!file_exists($dir)){
			@mkdir($dir, 0777);
		}

		if(!@move_uploaded_file($f['tmp_name'], $dir . $n)){
			echo StyleSheet()."<div class=Error>Error: Couldn't save the uploaded file. Try chmoding the upload folder '".$dir."' to 777. <a href=".$_SERVER['HTTP_REFERER'].">".$Translation["< back"]."</a>.</div>";
			exit;
		}else{
			@chmod($dir.$n, 0666);
			return $n;
		}
	}
	return "";
}
?>