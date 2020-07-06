<?php
session_start();
require_once "classes/DatenbankVerbindung.php";
require_once "classes/NutzerRepository.php";
require_once "including/table_sort.php";
$db_instanz = DatenbankVerbindung::erzeugeDatenbankVerbindung();
if(!$db_instanz){
    die('Es konnte keine Verbindung zur Datenbank hergestellt werden');
}
$suchbegriff = $_GET['searchitem'];
$nutzerRepository = new NutzerRepository($db_instanz);
$count= $nutzerRepository->zaehleNutzer(NutzerRepository::GRUPPEN_ART_JIRA, $suchbegriff);
$mitglied_name= $nutzerRepository->findeNutzer(NutzerRepository::GRUPPEN_ART_JIRA,$suchbegriff,$sort);
?>
<!DOCTYPE html>
<html>
<head>
    <link href="css/main.css" rel="stylesheet" type="text/css" />
    <title>Mitglieder</title>
</head>
<body>
<?php
require "including/header.php";
if ($_SESSION['bind_check']==true) {
    ?>
    <div id="main" align="center">
        <div id="headline">
            <h1 >Itools Statistik /Gruppen Mitglieder</h1>
        </div>
        <div id="allgemeine_infos">
                <h1>Anzahl: <?php echo $count; ?></h1>
        </div>
        <div id="sucheOben" align="center">
            <form action="tools_gruppe_nutzer.php" method="get" target="_self">
                <p> Mitglieder Suche der Gruppe<?php echo"'". $_GET["loginname"]."'" ?></p>
                <input type="hidden" name="loginname" value ="<?php echo $_GET["loginname"]; ?>">
                <input type="text" name="searchitem" placeholder="Suche" value="<?php if (isset($_GET['searchitem'])){echo $_GET['searchitem'];} ?>">
                <input type="submit" name="loggin" value="jetzt suchen">
            </form><br />
        </div>
        <div id="tabelle">
            <table cellpadding="5" cellspacing="5" border="3" >
                <tr>
                    <th>Nr:</th>
                    <th title="Nach Name sortieren" onclick="location.href='tools_gruppe_nutzer.php?<?php
                    echo "loginname=". $_GET['loginname']."&searchitem=". $_GET['searchitem']."&sort=". $_GET['sort']=$sort;
                    ?>'"> Mitglieder der <?php echo $_GET["loginname"];?> </th>
                    <th title="Nach login sortieren" onclick="location.href='tools_gruppe_nutzer.php?<?php
                    echo "loginname=". $_GET['loginname']."&searchitem=". $_GET['searchitem']."&sort=". $_GET['sort']=$sort;
                    ?>'"> Letzter Login: </th>
                    <th>Gruppe</th>
                </tr>
                <?php
                for ($e=0;$e<$count;$e++){
                    ?>
                    <tr onclick="location.href='ldap_user.php?loginname=<?php
                    echo $mitglied_name[$e][0];
                    ?>'">
                        <td><?php echo $e+1 ?></td>
                        <td><?php echo $mitglied_name[$e][0]?></td>
                        <td><?php echo $mitglied_name[$e][1];?></td>
                        <td><?php echo $nutzerRepository->zaehleNutzerGruppen($mitglied_name[$e][0]) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table><br /><br />
        </div>
    </div>
    <?php
}
else {
    header("Location: index.php");
}
$db_instanz->close();
?>
</body>
</html>
