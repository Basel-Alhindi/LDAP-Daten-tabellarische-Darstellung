<?php
session_start();
require_once "classes/DatenbankVerbindung.php";
require_once "classes/GruppenRepository.php";
require_once "classes/NutzerRepository.php";
require_once "including/table_sort.php";
$db_instanz = DatenbankVerbindung::erzeugeDatenbankVerbindung();
if(!$db_instanz){
    die('Es konnte keine Verbindung zur Datenbank hergestellt werden');
}
$gruppenRepository = new GruppenRepository($db_instanz);
$nutzerRepository = new NutzerRepository($db_instanz);
$suchbegriff = $_GET["searchitem"];
$count= $nutzerRepository->zaehleNutzerGruppen($_GET["loginname"],$suchbegriff);
$gruppe_name= $nutzerRepository->findeNutzerGruppen($_GET["loginname"],$suchbegriff,$sort);
?>
<!DOCTYPE html>
<html>
<head>
    <link href="css/main.css" rel="stylesheet" type="text/css" />
    <title>
        Nutzer Gruppen
    </title>
</head>
<body>
<?php
require "including/header.php";
if ($_SESSION['bind_check']==true) {
    ?>
    <div id="main" align="center">
        <div id="headline">
            <h1>Itools Statistik tools</h1>
            <p>Gruppen für: <?php echo $_GET['loginname'] ?></p>
        </div>
        <div id="sucheOben" align="center">
            <form action="nutzer_gruppen.php" method="get" target="_self">
                <input type="hidden" name="loginname" value ="<?php echo $_GET["loginname"]; ?>">
                <input type="text" name="searchitem" placeholder="Suche" value="<?php if (isset($_GET['searchitem'])){echo $_GET['searchitem'];} ?>">
                <input type="submit" name="loggin" value="jetzt suchen">
            </form><br/>
        </div>
        <div id="tabelle">
            <table cellpadding="5" cellspacing="5" border="1" >
                <tr onclick="location.href='nutzer_gruppen.php?<?php
                echo "loginname=". $_GET['loginname']."&searchitem=". $_GET['searchitem']."&sort=". $_GET['sort']=$sort;
                ?>'">
                    <th >Nr:</th>
                    <th >Gruppen </th>
                    <th >Mitglieder</th>
                </tr>
                <?php
                    for ($e=0;$e<$count;$e++){
                        ?>
                        <tr onclick="location.href='tools_gruppe_nutzer.php?loginname=<?php
                        echo $gruppe_name[$e][0];
                        ?>'">
                            <td><?php echo $e+1 ?></td>
                            <td><?php echo $gruppe_name[$e][0] ?></td>
                            <td ><?php echo $gruppenRepository->zaehleMitglieder($gruppe_name[$e][0])?></td>
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
    header("Location: fehler_seite.html");
}
$db_instanz->close();
?>
</body>
</html>
