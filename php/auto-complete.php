<?php
	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");

	// auto complete queries for all lookup fields of the application.

	// receive and validate user input
	$val=makeSafe(iconv('UTF-8', 'UTF-8', $_POST['val']));
	$t=$_POST['t'];
	$f=$_POST['f'];
	if($val=='') die('<ul></ul>');
	if($autoComplete[$t][$f]=='') die($Translation['error:'].' Invalid table or field.');

	// does the current user have view access to the requested table?
	$arrPerm=getTablePermissions($t);
	if(!$arrPerm[1] && !$arrPerm[3]) die($Translation['tableAccessDenied']); // quit if user has no insert or edit permissions

	// get the second column of the query (the one to search)
	$query=$autoComplete[$t][$f];
	if(!preg_match('/^select .*?,(.*) from /i', $query, $m)) die($Translation['error:'].' Invalid SQL.');
	$searchField=$m[1];

	// prepare the query
	if(!preg_match('/ order by .+/i', $query)){ // if we don't have an order by clause, append one
		$query.=' ORDER BY 2';
	}
	if(strpos(strtolower($query), ' where ')){ // if we have a where clause, add and AND condition
		$query=str_ireplace(' where ', ' WHERE ( ', $query);
		$query=str_ireplace(' order by ', " ) AND $searchField LIKE '%$val%' ORDER BY ", $query);
	}else{
		$query=str_ireplace(' order by ', " WHERE $searchField LIKE '%$val%' ORDER BY ", $query);
	}
	if(!preg_match('/ limit .+/i', $query)) $query.=' LIMIT 50';

	$out='<ul>';
	$res=sql($query, $eo);
	while($row=mysql_fetch_row($res)){
		$out.="<li id=\"$row[0]\">".highlight($val, $row[1])."</li>";
	}
	if($out=='<ul>') $out .= '<li>'.$Translation['No matches found!'].'</li>';
	$out.='</ul>';

	echo $out;
