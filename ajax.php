<?php
include "functions.php";
require_once('Class.Migrate.php');


if($_REQUEST["actie"]=="MigrateSite") {
    
	$sites = $_REQUEST["sites"];
    $source = $_REQUEST["source"];
    $destination = $_REQUEST["destination"];
	$SitesArr = explode(",", $sites);

	foreach($SitesArr as $SiteName) {
        $SiteMigrate = new Migrate($SiteName, $source, $destination);
        $DownloadLink = $SiteMigrate->ExportSite();
        $FileName = $SiteMigrate->DownloadUNF($DownloadLink);
        $uploadID = $SiteMigrate->UploadFile($FileName);
        $SiteDesc=$uploadID['data'][0]['sites'][0]['desc'];
        $ImportOutput = $SiteMigrate->ImportSite($uploadID['data'][0]['backup_id'], $SiteDesc);
        $json = $SiteMigrate->MigrateDevices();
        
        //$q2 = $dba->prepare("Insert into test (hive) values ('".$SiteName."')");
        //$q2->execute();
    }
    ob_end_clean();
    echo "OK";
}

if($_REQUEST["actie"]=="DeleteSite") {
	$sites = $_REQUEST["sites"];
    $source = $_REQUEST["source"];
	$SitesArr = explode(",", $sites);

	foreach($SitesArr as $SiteName) {
        $SiteDelete = new DeleteSite($SiteName, $source);
        $SiteDelete->RemoveSite();
    }
    ob_end_clean();
    echo "OK";
}

?>