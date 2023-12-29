<?

include "connect.php";
include "functions.php";
?>

<html>
<head>
</head>
<body>
    <form action="listssites.php?actie=ShowSites" method="post">
        <table>
            <tr>
                <td>Source Controller</td>
                <td>
                    <select name="Source">
                    <?
                    $q1 = $dbu->query("SELECT * From Controllers Where Migrate = 1");
                    while($r1 = $q1->fetch(PDO::FETCH_ASSOC)) {		
                    ?>
                        <option value="<?=$r1['ControllerID'];?>"><?=$r1['Naam'];?></option>
                    <? } ?>
                    </select>
                </td>
                <td>Destination Controller</td>
                <td>
                    <select name="Destination">
                    <?
                    $q1 = $dbu->query("SELECT * From Controllers Where Migrate = 1");
                    while($r1 = $q1->fetch(PDO::FETCH_ASSOC)) {		
                    ?>
                        <option value="<?=$r1['ControllerID'];?>"><?=$r1['Naam'];?></option>
                    <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <input type="submit" value="Migreren">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
