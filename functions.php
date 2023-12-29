<?php

function OldController($id) {
    include "connect.php";
    $q1 = $dbu->query("SELECT * From Controllers Where ControllerID = '".$id."'");
    $r1 = $q1->fetch(PDO::FETCH_ASSOC);	
    $OldContInfo['naam']=$r1['Naam'];
    $OldContInfo['link']=$r1['Link'];
    $OldContInfo['UserName']=$r1['UserName'];
    $OldContInfo['Password']=$r1['Password'];
    return $OldContInfo;
}

function NewController($id) {
    include "connect.php";
    $q1 = $dbu->query("SELECT * From Controllers Where ControllerID = '".$id."'");
    $r1 = $q1->fetch(PDO::FETCH_ASSOC);	
    $NewContInfo['naam']=$r1['Naam'];
    $NewContInfo['link']=$r1['Link'];
    $NewContInfo['UserName']=$r1['UserName'];
    $NewContInfo['Password']=$r1['Password'];
    return $NewContInfo;
}

function login($id) {
        $OldController = OldController($id);
		$BaseUrl = trim($OldController['link']);
		$UserName = $OldController['UserName'];
		$Password = $OldController['Password'];
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $BaseUrl . '/api/login');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'unifises');  //could be empty, but cause problems on some hosts
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $UserName, 'password' => $Password]));
		$content = curl_exec($ch);
		return $ch;
}

function loginTarget($id) {
        $NewController = NewController($id);
		$BaseUrl = trim($NewController['link']);
		$UserName = $NewController['UserName'];
		$Password = $NewController['Password'];
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $BaseUrl . '/api/login');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'unifises2');  //could be empty, but cause problems on some hosts
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $UserName, 'password' => $Password]));
		$content = curl_exec($ch);
		return $ch;
}

function ProcesReturn($ch, $content) {
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$headers     = substr($content, 0, $header_size);
	$body        = trim(substr($content, $header_size));
	curl_close($ch); 
	$json = json_decode($body, true);
	return $json['data'];
}

function ProcesReturnCL($ch, $content) {
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$headers     = substr($content, 0, $header_size);
	$body        = trim(substr($content, $header_size));
	curl_close($ch); 
	$json = json_decode($body, true);
	return $json;
}

function GetActionOld($Endpoint, $id) {
	$ch = login($id);
	curl_setopt($ch, CURLOPT_URL, $Endpoint);
	curl_setopt($ch, CURLOPT_POST, false);
	$content = curl_exec($ch);
	return ProcesReturn($ch, $content);
}

function GetActionNew($Endpoint, $id) {
	$ch = loginTarget($id);
	curl_setopt($ch, CURLOPT_URL, $Endpoint);
	curl_setopt($ch, CURLOPT_POST, false);
	$content = curl_exec($ch);
	return ProcesReturn($ch, $content);
}

function PostAction($EndPoint, $InputJson) {
	$ch = login();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_URL, $EndPoint);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $InputJson);
	$content = curl_exec($ch);
	return ProcesReturn($ch, $content);
}

function PostActionTarget($EndPoint, $InputJson) {
	$ch = loginTarget();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_URL, $EndPoint);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $InputJson);
	$content = curl_exec($ch);
	return ProcesReturnCL($ch, $content);
}

function GetSiteID($SiteName) {
    $OldController=OldController();
    $Sites = GetActionOld($OldController['link'].'/api/self/sites');
    foreach($Sites as $Site) {
        if($Site['name']==$SiteName) {
            return $Site['_id'];   
        }
    }
}

//Site Functions

//EXPORT SITE
function ExportSite($SiteName) {
    $OldController=OldController();
    $InputExport = json_encode(array(
            'cmd' => 'export-site'
        ));
    return PostAction($OldController['link']."/api/s/".$SiteName."/cmd/backup", $InputExport);
}

//DOWNLOAD UNF FIle
function DownloadUNF2($file_url) {
	$file_name = basename($file_url); 
	$save_to = 'du/'.$file_name;
	
	$ch = login();
	curl_setopt($ch, CURLOPT_POST, 0); 
	curl_setopt($ch,CURLOPT_URL,$file_url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HEADER, false);
	$file_content = curl_exec($ch);
	curl_close($ch);

	$downloaded_file = fopen($save_to, 'w');
	fwrite($downloaded_file, $file_content);
	fclose($downloaded_file);
	return $file_name;

}

//UPLOAD FILE
function UploadFile($File) {
    $NewController=NewController();
	$ch = loginTarget();
	curl_setopt_array($ch, array(
	  CURLOPT_URL => $NewController['link'].'/upload/backup',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => array('filename'=> new CURLFILE('UnifiMigrate/du/'.$File)), // '/var/www/t00r.nl/unifi/UnifiMigrate/up.unf'
	  CURLOPT_HTTPHEADER => array(
		"X-Requested-With: XMLHttpRequest",
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36",
		"Accept: */*"
	  ),
	));
	$content = curl_exec($ch);
	$json = ProcesReturnCL($ch, $content);
	curl_close($ch);
	return $json;
}



//IMPORT SITE
function ImportSite($BackupID, $SiteDesc) {
    $NewController=NewController();
    $InputImport = json_encode(array(
            'backup_id' => $BackupID, 
            'cmd' => 'import-site',
            'site_desc' => $SiteDesc 
        ));
    return PostActionTarget($NewController['link']."/api/s/default/cmd/backup", $InputImport);
}

function MigrateDevices2($SiteName) {
    $OldController=OldController();
	$Devices = GetActionOld($OldController['link']."/api/s/".$SiteName."/stat/device");
	foreach($Devices as $Device) {
        SetInform($Device['mac'],$SiteName);
    }
}

//INFORM
function SetInform($mac,$SiteName) {
    $InputJson = json_encode(array(
		'cmd' => 'migrate',
		'mac' => $mac,
		'inform_url' => $NewController['link'].':8080/inform'
	));
    return PostAction($OldController['link']."/api/s/".$SiteName."/cmd/devmgr", $InputJson);
}

//Delete Site
function DeleteSit3($SiteName) {
    $SiteID = GetSiteID($SiteName);
    $OldController=OldController();
    $InputJson = json_encode(array(
		'cmd' => 'delete-site',
		'site' => $SiteID
	));
    return PostAction($OldController['link']."/api/s/default/cmd/sitemgr", $InputJson);
}

function GetSiteName($SiteName, $id) {
    $OldContr = OldController($id);
    $Sites = GetActionOld($OldContr['link'].'/api/self/sites', $id);
    foreach($Sites as $Site) {
        if($Site['name']==$SiteName) {
            return $Site['desc'];   
        }
    }
}

function SiteNameToLocation($site, $id) {
	include "/var/www/scripts/connect.php";
	$q1 = $dbu->query("Select * From Locaties Where SiteName = '".$site."'");
	$r1 = $q1->fetch(PDO::FETCH_ASSOC);
    if(empty($r1['LocatieName'])) {
        return GetSiteName($site, $id);
    } else {
	   return $r1['LocatieName'];
    }
}

function MigrateSite2($SiteName) {
    $OldController=OldController();
    //Op de oude server een export starten
    $url = ExportSite($SiteName);
    //Download URL maken
    $url = $OldController['link'].$url[0]['url'];
    //UNF downloaden
    $downloadUNF = DownloadUNF($url);
    //UNF importeren naar nieuwe controller
    $uploadID = UploadFile($downloadUNF);
    //Sitedesc vastleggen.
    $SiteDesc=$uploadID['data'][0]['sites'][0]['desc'];
    //Site actief maken aan de hand van backupID
    $ImportOutput = ImportSite($uploadID['data'][0]['backup_id'], $SiteDesc);
    //Devices in de oude omgeving een inform geven naar de nieuwe controller.
    MigrateDevices($SiteName);
    //Oude site verwijderen. 15 seconden pauze inlassen om alles te laten migreren.
    
    //DeleteSite($SiteName);
}
?>