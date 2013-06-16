<?php
	// check this file's MD5 to make sure it wasn't called before
	$prevMD5=@implode('', @file(dirname(__FILE__).'/setup.md5'));
	$thisMD5=md5(@implode('', @file("./updateDB.php")));
	if($thisMD5==$prevMD5){
		$setupAlreadyRun=true;
	}else{
		// set up tables
		if(!isset($silent)){
			$silent=true;
		}

		// set up tables
		setupTable('user', "create table if not exists `user` (   `id` INT unsigned not null auto_increment , primary key (`id`), `nama` VARCHAR(40) , `nokp` VARCHAR(40) , `pass` VARCHAR(40) , `level` VARCHAR(40) , `type` VARCHAR(40) ) CHARSET utf8", $silent, array( "ALTER TABLE user ADD `field1` VARCHAR(40)","ALTER TABLE user ADD `field2` VARCHAR(40)","ALTER TABLE `user` CHANGE `field2` `nama` VARCHAR(40) ","ALTER TABLE `user` DROP `field1`","ALTER TABLE user ADD `field2` VARCHAR(40)","ALTER TABLE `user` CHANGE `field2` `n` VARCHAR(40) ","ALTER TABLE `user` DROP `n`","ALTER TABLE `user` DROP `nama`","ALTER TABLE user ADD `field1` VARCHAR(40)","ALTER TABLE `user` CHANGE `field1` `id` VARCHAR(40) ","ALTER TABLE `user` CHANGE `id` `id` INT unsigned not null auto_increment ","ALTER TABLE user ADD `field2` VARCHAR(40)","ALTER TABLE `user` CHANGE `field2` `nama` VARCHAR(40) ","ALTER TABLE user ADD `field3` VARCHAR(40)","ALTER TABLE user ADD `field4` VARCHAR(40)","ALTER TABLE `user` CHANGE `field3` `nokp` VARCHAR(40) ","ALTER TABLE user ADD `field5` VARCHAR(40)","ALTER TABLE `user` CHANGE `field4` `pass` VARCHAR(40) ","ALTER TABLE user ADD `field6` VARCHAR(40)","ALTER TABLE `user` CHANGE `field5` `level` VARCHAR(40) ","ALTER TABLE `user` CHANGE `field6` `type` VARCHAR(40) "));
		setupTable('maklumat', "create table if not exists `maklumat` (   `mid` INT unsigned not null auto_increment , primary key (`mid`), `mnama` VARCHAR(40) , `mtarikh` VARCHAR(40) , `mpengerusi` VARCHAR(40) , `mnaib` VARCHAR(40) , `msu` VARCHAR(40) , `mguru` VARCHAR(40) , `mmaklumat` VARCHAR(40) ) CHARSET utf8", $silent, array( "ALTER TABLE maklumat ADD `field1` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field1` `id` VARCHAR(40) ","ALTER TABLE `maklumat` CHANGE `id` `id` INT unsigned not null auto_increment ","ALTER TABLE maklumat ADD `field2` VARCHAR(40)","ALTER TABLE `maklumat` DROP `field2`","ALTER TABLE `maklumat` DROP `id`","ALTER TABLE maklumat ADD `field1` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field1` `mid` VARCHAR(40) "," ALTER TABLE `maklumat` CHANGE `mid` `mid` INT ","ALTER TABLE `maklumat` CHANGE `mid` `mid` INT not null "," ALTER TABLE `maklumat` CHANGE `mid` `mid` INT not null auto_increment "," ALTER TABLE `maklumat` CHANGE `mid` `mid` INT unsigned not null auto_increment ","ALTER TABLE maklumat ADD `field2` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field2` `mnama` VARCHAR(40) ","ALTER TABLE maklumat ADD `field3` VARCHAR(40)","ALTER TABLE maklumat ADD `field4` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field3` `mtarikh` VARCHAR(40) ","ALTER TABLE maklumat ADD `field5` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field4` `mpengerusi` VARCHAR(40) ","ALTER TABLE maklumat ADD `field6` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field5` `mnaib` VARCHAR(40) ","ALTER TABLE maklumat ADD `field7` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field6` `msu` VARCHAR(40) ","ALTER TABLE maklumat ADD `field8` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field7` `mcarta` VARCHAR(40) ","ALTER TABLE `maklumat` CHANGE `field8` `mguru` VARCHAR(40) ","ALTER TABLE `maklumat` DROP `mcarta`","ALTER TABLE maklumat ADD `field8` VARCHAR(40)","ALTER TABLE `maklumat` CHANGE `field8` `mmaklumat` VARCHAR(40) ","ALTER TABLE `maklumat` ADD PRIMARY KEY (`mid`)"));
		setupTable('guru', "create table if not exists `guru` (   `gid` INT unsigned not null auto_increment , primary key (`gid`), `gnama` VARCHAR(40) , `gnokp` VARCHAR(40) , `gpass` VARCHAR(40) , `glevel` VARCHAR(40) , `gjawatan` VARCHAR(40) , `gpersatuan` VARCHAR(40) ) CHARSET utf8", $silent, array( "ALTER TABLE guru ADD `field1` VARCHAR(40)","ALTER TABLE `guru` CHANGE `field1` `gid` VARCHAR(40) "," ALTER TABLE `guru` CHANGE `gid` `gid` INT ","ALTER TABLE `guru` CHANGE `gid` `gid` INT not null "," ALTER TABLE `guru` CHANGE `gid` `gid` INT not null auto_increment "," ALTER TABLE `guru` CHANGE `gid` `gid` INT unsigned not null auto_increment ","ALTER TABLE guru ADD `field2` VARCHAR(40)","ALTER TABLE guru ADD `field3` VARCHAR(40)","ALTER TABLE `guru` CHANGE `field2` `gnama` VARCHAR(40) ","ALTER TABLE guru ADD `field4` VARCHAR(40)","ALTER TABLE `guru` CHANGE `field3` `gnokp` VARCHAR(40) ","ALTER TABLE guru ADD `field5` VARCHAR(40)","ALTER TABLE `guru` CHANGE `field4` `gpass` VARCHAR(40) ","ALTER TABLE guru ADD `field6` VARCHAR(40)","ALTER TABLE `guru` CHANGE `field5` `glevel` VARCHAR(40) ","ALTER TABLE `guru` CHANGE `field6` `gjawatan` VARCHAR(40) ","ALTER TABLE guru ADD `field7` VARCHAR(40)","ALTER TABLE `guru` CHANGE `field7` `gpersatuan` VARCHAR(40) ","ALTER TABLE `guru` ADD PRIMARY KEY (`gid`)"));
		setupTable('pentadbir', "create table if not exists `pentadbir` (   `pid` INT unsigned not null auto_increment , primary key (`pid`), `pnama` VARCHAR(40) , `pnokp` VARCHAR(40) , `ppass` VARCHAR(40) , `plevel` VARCHAR(40) , `pjawatan` VARCHAR(40) , `pemel` VARCHAR(40) ) CHARSET utf8", $silent, array( "ALTER TABLE pentadbir ADD `field1` VARCHAR(40)","ALTER TABLE `pentadbir` CHANGE `field1` `pid` VARCHAR(40) "," ALTER TABLE `pentadbir` CHANGE `pid` `pid` INT ","ALTER TABLE `pentadbir` CHANGE `pid` `pid` INT not null "," ALTER TABLE `pentadbir` CHANGE `pid` `pid` INT not null auto_increment "," ALTER TABLE `pentadbir` CHANGE `pid` `pid` INT unsigned not null auto_increment ","ALTER TABLE pentadbir ADD `field2` VARCHAR(40)","ALTER TABLE pentadbir ADD `field3` VARCHAR(40)","ALTER TABLE `pentadbir` CHANGE `field2` `pnama` VARCHAR(40) ","ALTER TABLE pentadbir ADD `field4` VARCHAR(40)","ALTER TABLE `pentadbir` CHANGE `field3` `pnokp` VARCHAR(40) ","ALTER TABLE pentadbir ADD `field5` VARCHAR(40)","ALTER TABLE `pentadbir` CHANGE `field4` `ppass` VARCHAR(40) ","ALTER TABLE pentadbir ADD `field6` VARCHAR(40)","ALTER TABLE `pentadbir` CHANGE `field5` `plevel` VARCHAR(40) ","ALTER TABLE `pentadbir` CHANGE `field6` `pjawatan` VARCHAR(40) ","ALTER TABLE pentadbir ADD `field7` VARCHAR(40)","ALTER TABLE `pentadbir` CHANGE `field7` `pemel` VARCHAR(40) ","ALTER TABLE `pentadbir` ADD PRIMARY KEY (`pid`)"));


		// save MD5
		if($fp=@fopen(dirname(__FILE__).'/setup.md5', 'w')){
			fwrite($fp, $thisMD5);
			fclose($fp);
		}
	}


	function setupIndexes($tableName, $arrFields){
		if(!is_array($arrFields)){
			return false;
		}

		foreach($arrFields as $fieldName){
			if(!$res=@mysql_query("SHOW COLUMNS FROM `$tableName` like '$fieldName'")){
				continue;
			}
			if(!$row=@mysql_fetch_assoc($res)){
				continue;
			}
			if($row['Key']==''){
				@mysql_query("ALTER TABLE `$tableName` ADD INDEX `$fieldName` (`$fieldName`)");
			}
		}
	}


	function setupTable($tableName, $createSQL='', $silent=true, $arrAlter=''){
		global $Translation;
		ob_start();

		echo "<div style=\"padding: 5px; border-bottom:solid 1px silver; font-family: verdana, arial; font-size: 10px;\">";

		// is there a table rename query?
		if(is_array($arrAlter)){
			$matches=array();
			if(preg_match("/ALTER TABLE `(.*)` RENAME `$tableName`/", $arrAlter[0], $matches)){
				$oldTableName=$matches[1];
			}
		}

		if($res=@mysql_query("select count(1) from `$tableName`")){ // table already exists
			if($row=@mysql_fetch_array($res)){
				echo str_replace("<TableName>", $tableName, str_replace("<NumRecords>", $row[0],$Translation["table exists"]));
				if(is_array($arrAlter)){
					echo '<br />';
					foreach($arrAlter as $alter){
						if($alter!=''){
							echo "$alter ... ";
							if(!@mysql_query($alter)){
								echo "<font color=red>".$Translation["failed"]."</font><br />";
								echo "<font color=red>".$Translation["mysql said"]." ".mysql_error()."</font><br />";
							}else{
								echo "<font color=green>".$Translation["ok"]."</font><br />";
							}
						}
					}
				}else{
					echo $Translation["table uptodate"];
				}
			}else{
				echo str_replace("<TableName>", $tableName, $Translation["couldnt count"]);
			}
		}else{ // given tableName doesn't exist

			if($oldTableName!=''){ // if we have a table rename query
				if($ro=@mysql_query("select count(1) from `$oldTableName`")){ // if old table exists, rename it.
					$renameQuery=array_shift($arrAlter); // get and remove rename query

					echo "$renameQuery ... ";
					if(!@mysql_query($renameQuery)){
						echo "<font color=red>".$Translation["failed"]."</font><br />";
						echo "<font color=red>".$Translation["mysql said"]." ".mysql_error()."</font><br />";
					}else{
						echo "<font color=green>".$Translation["ok"]."</font><br />";
					}

					if(is_array($arrAlter)) setupTable($tableName, $createSQL, false, $arrAlter); // execute Alter queries on renamed table ...
				}else{ // if old tableName doesn't exist (nor the new one since we're here), then just create the table.
					setupTable($tableName, $createSQL, false); // no Alter queries passed ...
				}
			}else{ // tableName doesn't exist and no rename, so just create the table
				echo str_replace("<TableName>", $tableName, $Translation["creating table"]);
				if(!@mysql_query($createSQL)){
					echo "<font color=red>".$Translation["failed"]."</font><br />";
					echo "<font color=red>".$Translation["mysql said"].mysql_error()."</font>";
				}else{
					echo "<font color=green>".$Translation["ok"]."</font>";
				}
			}
		}

		echo "</div>";

		$out=ob_get_contents();
		ob_end_clean();
		if(!$silent){
			echo $out;
		}
	}
?>