<?php
include "functions.php";
if($_GET['actie']=='migreer') {
    $Sites = $_POST['Site'];
    foreach($Sites as $Site) {
        //MigrateSite($Site);   
    }
}

$SourceID = $_POST['Source'];
$DestinationID = $_POST['Destination'];
if($SourceID==$DestinationID) {
    echo "Source en destination zijn hetzelfde. Kan niet he?!";
    die();
}

$OldController=OldController($SourceID);
$ControllerSites=GetActionOld($OldController['link'].'/api/self/sites', $SourceID)
?>


<html>
<head>
    <style>
        #checkboxes label {
            float: left;
        }
        #checkboxes ul {
            margin: 0;
            list-style: none;
            float: left;
        }
    </style>
</head>
<body>
    <form action="listssites.php?actie=migreer" method="post">
    <div id="checkboxes">
        <label><?=$OldController['naam'];?></label>
        <ul>
            <?
            foreach($ControllerSites as $ControllerSite) {
            ?>
            <li><input type="checkbox" name="Site[]" value="<?=$ControllerSite['name'];?>"> <?=$ControllerSite['desc'];?></li>
            <? } ?>
        </ul>
    </div>
        
        
        
    </form>
</body>
</html>