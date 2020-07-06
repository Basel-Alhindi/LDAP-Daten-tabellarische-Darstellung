<?php
session_start();
require_once "funktionen/alle_funktionen.php";
require_once "classes/NutzerRepository.php";
require_once "classes/DatenbankVerbindung.php";
$db_instanz = DatenbankVerbindung::erzeugeDatenbankVerbindung();
if(!$db_instanz){
    die('Es konnte keine Verbindung zur Datenbank hergestellt werden');
}
$nutzerRepository = new NutzerRepository($db_instanz);
$loginname=$_GET["loginname"];
$nutzer_info=  $nutzerRepository->findeEinNutzer($loginname);
?>
<!DOCTYPE html>
<html>
<head>
  <link href="css/main.css" rel="stylesheet" type="text/css" />
  <title>
      Ein Nutzer
  </title>
</head>
<body>
<?php
require "including/header.php";
if ($_SESSION['bind_check']==true) {
    ?>
    <div id="main" align="center">
        <div id="headline">
            <h1>Info/Ein Nutzer</h1>
        </div>
        <div id="tabelle">
            <table cellpadding="5" cellspacing="5" border="1" >
                <tr>
                    <th>Nr:</th>
                    <th>Vorname:</th>
                    <th>Nachname:</th>
                    <th>Loginname:</th>
                    <th>E-Mail:</th>
                    <th>Letzter Login:</th>
                    <th>Letzte Passwortänderung:</th>
                    <th>Eingetragen:</th>
                    <th>Gruppe:</th>
                </tr>
                <tr>
                    <td class="nummerStyle"><?php echo 1;?></td>
                    <td><?php echo $nutzer_info[0][0]?></td>
                    <td><?php echo $nutzer_info[0][1]?></td>
                    <td><?php echo $nutzer_info[0][2]?></td>
                    <td><?php echo $nutzer_info[0][3]?></td>
                    <td><?php echo $nutzer_info[0][4]?></td>
                    <td><?php echo $nutzer_info[0][5]?></td>
                    <td><?php echo $nutzer_info[0][6]?></td>
                    <td> <a href="nutzer_gruppen.php?loginname=<?php echo $nutzer_info[0][2]; ?>"target="_self"> <?php echo $nutzerRepository->zaehleNutzerGruppen($nutzer_info[0][2]); ?></a></td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}
else {
    header("Location: fehler_seite.html");
}
$db_instanz->close();
?>
</body>
</html>