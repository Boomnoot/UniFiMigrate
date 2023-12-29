<?php
include "connect.php";
include "functions.php";
if($_POST['request']=='confirm') {
    $SourceID = $_POST['Source'];
    $DestinationID = $_POST['Destination'];
} 
$kind = $_POST['actie'];

$OldController=OldController($SourceID);
$NewController=NewController($DestinationID);
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
	<link href="style.css" rel="stylesheet">
	<title>Confirm</title>

</head>

<body>
    <form action="Confirm.php" method="post" id="hiddenform">
        <input type="hidden" name="Source" value="<?=$SourceID;?>">
        <input type="hidden" name="Destination" value="<?=$DestinationID;?>">
        <?
        foreach($_POST['Site'] as $siteval) {
            echo '<input type="hidden" name="Site[]" value="'. $siteval. '">';
        }
        ?>
        <input type="hidden" name="request" value="confirm">
        <input type="hidden" name="actie" value="" id="actie">
    </form>
        
    <div class="container-fluid">   
        <div class="row">
            <div class="col-md-12">
                <div class="product-content">
                    <div class="row justify-content-center align-items-center">
                        <?
                        if($kind=='Migrate') {
                        ?>    
                        <div>
                            <label>Weet u zeker dat u de volgende site(s) wilt migreren?<br>
                            Van <b><?=$OldController['naam'];?></b> naar <b><?=$NewController['naam'];?></b></label>
                            <ul class="confirm">
                            <? foreach($_POST['Site'] as $Site) { 
                            $AllSites .= $Site.",";
                            ?>
                                <li><?=$Site;?></li>

                            <? } ?>
                            </ul>
                            <input type="hidden" id="sites" value="<?=json_encode($_POST['Site']);?>">
                            <br>
                            <? $AllSites = rtrim(trim($AllSites), ','); ?>
                            <button type="submit" class="btn w-25 btn-success" onClick="MigrateSite('<?=$AllSites;?>', '<?=$SourceID;?>', '<?=$DestinationID;?>');" id="ja">Ja</button> 
				            <button onClick="window.location='SelectSites.php'" class="btn w-25 btn-danger" id="nee">Nee</button>
                            
                        <? } 
                        if($kind=='Delete') {
                        ?>    
                        <div>
                            <label>Weet u zeker dat u de volgende site(s) wilt verwijderen van <b><?=$OldController['naam'];?></b>?</label>
                            
                            <ul class="confirm">
                            <? foreach($_POST['Site'] as $Site) { 
                            $AllSites .= $Site.",";
                            ?>
                                <li><?=$Site;?></li>

                            <? } ?>
                            </ul>
                            <input type="hidden" id="sites" value="<?=json_encode($_POST['Site']);?>">
                            <br>
                            <? $AllSites = rtrim(trim($AllSites), ','); ?>
                            <button type="submit" class="btn w-25 btn-success" onClick="DeleteSite('<?=$AllSites;?>', '<?=$SourceID;?>', '<?=$DestinationID;?>');" id="ja">Ja</button> 
				            <button onClick="window.location='SelectSites.php'" class="btn w-25 btn-danger" id="nee">Nee</button>
                        <? } ?>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
