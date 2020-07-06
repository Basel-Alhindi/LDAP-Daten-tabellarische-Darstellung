<?php
session_start();
require_once "classes/DatenbankVerbindung.php";
require_once "classes/GruppenRepository.php";
require_once "including/table_sort.php";
$db_instanz = DatenbankVerbindung::erzeugeDatenbankVerbindung();
if(!$db_instanz){
    die('Es konnte keine Verbindung zur Datenbank hergestellt werden');
}
$gruppenRepository = new GruppenRepository($db_instanz);
$suchbegriff = $_GET["searchitem"];
$count= $gruppenRepository->zaehleGruppen(GruppenRepository::GRUPPEN_ART_JIRA, $suchbegriff);
$gruppe_name= $gruppenRepository->findeGruppen(GruppenRepository::GRUPPEN_ART_JIRA,$suchbegriff,$sort);?>
<!DOCTYPE html>
<html>
<head>
    <link href="css/main.css?<?php echo time(); ?>" rel="stylesheet" type="text/css" />
    <title>
        Jira
    </title>
</head>
<body>
<?php
require "including/header.php";
if ($_SESSION['bind_check']==true) {
    ?>
    <div id="headline" class="test">
        <h1>Jira-gruppen</h1>
    </div>
    <div id="main" align="center">
        <div id="allgemeine_infos">
            <div>
                <h1>Anzahl: <?php echo $count; ?></h1>
            </div>
        </div>
        <div id="sucheOben" align="center">
            <form action="jira.php" method="get" target="_self">
                <p>Hier können Sie alle Jira Gruppen durchsuchen:</p>
                <input type="text" name="searchitem" placeholder="Suche" value="<?php if (isset($_GET['searchitem'])){echo $_GET['searchitem'];} ?>">
                <input type="submit" name="loggin" value="jetzt suchen">
            </form><br />
        </div>
        <div id="tabelle">
            <table>
                <tr onclick="location.href='jira.php?<?php if (isset($_GET["searchitem"])){echo"searchitem=".$_GET["searchitem"];}echo "&sort=".$sort?>'">
                    <th >Nr:</th>
                    <th>Loginname:</th>
                    <th >Mitglieder</th>
                </tr>
                <?php
                for ($e=0;$e<$count;$e++) {
                    ?>
                    <tr onclick="location.href='tools_gruppe_nutzer.php?loginname=<?php echo $gruppe_name[$e][0]?>'">
                        <td class="nummerStyle" ><?php echo $e+1;?></td>
                        <td><?php echo $gruppe_name[$e][0]; ?></td>
                        <td ><?php echo $gruppenRepository->zaehleMitglieder($gruppe_name[$e][0]);?></td>
                    </tr>
                    <?php
                }
                ?>
            </table><br /><br />
        </div>
    </div>
    <?php
	$dbObj->close();	
	?>
</body>
</html>