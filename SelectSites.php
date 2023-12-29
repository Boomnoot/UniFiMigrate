<?php
include "connect.php";
include "functions.php";
if($_POST['actie']=='SetController') {
    $SourceID = $_POST['Source'];
    $DestinationID = $_POST['Destination'];
} 
    if(empty($SourceID)) { $SourceID = 1; } 
    if(empty($DestinationID)) { $DestinationID = 2; } 

$OldController=OldController($SourceID);
$OldControllerSites=GetActionOld($OldController['link'].'/api/self/sites', $SourceID);

$NewController=NewController($DestinationID);
$NewControllerSites=GetActionNew($NewController['link'].'/api/self/sites', $DestinationID);
?>

<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="description" content="SiteSelector for UnifiMigrate">
	<meta name="author" content="Gerben Nooteboom">	
	<link href="../inc/favicon.ico" rel="icon" type="image/x-icon" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" crossorigin="anonymous">

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="ajax.js" crossorigin="anonymous"></script>
    <script src="../../inc/js/fa.js" crossorigin="anonymous"></script>
	<link href="style.css" rel="stylesheet">
    <link href="../inc/util.css" rel="stylesheet">
    
	<title>SelectSites</title>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="padding-left: 0px;">
    <div class="container-fluid"> 
        <a class="navbar-brand" href="#">
            <img src="/inc/logo.png" height="50" alt=""><img class="m-l-15" src="/inc/unifi.png" height="50" alt="">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/" title="Home"><i class="fas fa-home"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../login/logout.php" title="Uitloggen"><i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container-fluid">   
        <div class="row">
            <div class="col-md-12">
                <div class="product-content">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-warning" onclick="SetKindSubmit('Migrate');">Migrate site(s)</button>
                            <button class="btn btn-danger" onclick="SetKindSubmit('Delete');">Delete site(s)</button>
                            <span class="m-l-20 p-t-5 m-b-2 " id="result"></span>
                        </div>
                        <div class="col-md-6 m-t-1 ">    
                            <form class="form-inline" action="SelectSites.php" method="post" id="ContrSwitcher">
                                <input type="hidden" name="actie" value="SetController">
                                <div class="form-group mr-2">
                                    <label>Source Controller:&nbsp;&nbsp; </label>
                                    <select class="form-control" name="Source" id="source">
                                        <?
                                        $q1 = $dbu->query("SELECT * From Controllers Where Migrate = 1");
                                        while($r1 = $q1->fetch(PDO::FETCH_ASSOC)) {	
                                            if($SourceID==$r1['ControllerID']) {
                                                $selected='selected'; } else { $selected='';   
                                            }
                                        ?>
                                        <option <?=$selected;?> value="<?=$r1['ControllerID'];?>"><?=$r1['Naam'];?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label class="" for="inputPassword">Destination Controller:&nbsp;&nbsp; </label>
                                    <select class="form-control" name="Destination" id="Destination">
                                        <?
                                        $q1 = $dbu->query("SELECT * From Controllers Where Migrate = 1");
                                        while($r1 = $q1->fetch(PDO::FETCH_ASSOC)) {		
                                            if($DestinationID==$r1['ControllerID']) {
                                                $selected='selected'; } else { $selected='';   
                                            }
                                        ?>
                                        <option <?=$selected;?> value="<?=$r1['ControllerID'];?>"><?=$r1['Naam'];?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            
                                <button class="btn btn-dark float-right"  type="button" id="sub" onClick="CheckForm();">Apply</button>
                           </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
			<div class="col-md-6">
				<div class="product-content">
                    <form action="Confirm.php" method="post" id="MainForm">
                    <div id="checkboxes" class="overflow-auto"  style="max-height: 100%;">
                        <input type="hidden" name="actie" id="actie">
                        <input type="hidden" name="request" value="confirm">
                        <input type="hidden" name="Source" value="<?=$SourceID;?>">
                        <input type="hidden" name="Destination" value="<?=$DestinationID;?>">
                        <label class="m-l-20">Aanwezig sites: <?=$OldController['naam'];?></label>
                        <ul>
                            <?
                            foreach($OldControllerSites as $ControllerSite) {
                            ?>
                            <li><input type="checkbox" name="Site[]" value="<?=$ControllerSite['name'];?>"> <?=$ControllerSite['desc'];?> (<?=$ControllerSite['name'];?>)</li>
                            <? } ?>
                        </ul>
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
				<div class="product-content">
                    <div id="checkboxes" class="overflow-auto"  style="max-height: 100%;">
                        <label class="m-l-20">Aanwezig sites: <?=$NewController['naam'];?></label>
                        <ul>
                            <?
                            foreach($NewControllerSites as $ControllerSite) {
                            ?>
                            <li> <?=$ControllerSite['desc'];?></li>
                            <? } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
