<?php
session_start();
require_once "funktionen/alle_funktionen.php";
require_once "classes/DatenbankVerbindung.php";
require_once "classes/GruppenRepository.php";
$db_instanz = DatenbankVerbindung::erzeugeDatenbankVerbindung();
if(!$db_instanz){
    die('Es konnte keine Verbindung zur Datenbank hergestellt werden');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Übersicht</title>
  <link href="css/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
require "including/header.php";
if ($_SESSION['bind_check']==true) { ?>
    <div id="main">
        <div class="uebersicht" style="clear: both">
            <h1 style="color: #DA2B4C"> Itools Statistik Tool</h1><br /><br />
            <h1> Intranetportal  Gruppe</h1>
        </div><br/>
        <div id="usersGroup" align="center">
            <table class="hin-table">
                <tr>
                    <th>Tools Gruppen</th>
                    <th>Aktive Nutzer</th>
                    <th>Freie Lizenzen </th>
                </tr>
                <tr onclick="location.href='tools_gruppe_nutzer.php?loginname=jira-users'">
                    <td>jira-users</td>
                    <td><?php
                        echo $jira_member= $db_instanz->frageNachEinemWert("select count(ng.f_nutzer_id) as count from gruppe as g, nutzer_gruppen as ng where g.gruppe_id=ng.f_gruppe_id and g.gruppe_name like'jira-users'");

                        ?>
                    </td>
                    <td><?php echo (250-$jira_member); ?></td>
                </tr>
                <tr onclick="location.href='tools_gruppe_nutzer.php?loginname=confluence-users'">
                    <td class="nummerStyle">confluence-users</td>
                    <td><?php
                        echo $conf_member= $db_instanz->frageNachEinemWert("select count(ng.f_nutzer_id) as count from gruppe as g, nutzer_gruppen as ng where g.gruppe_id=ng.f_gruppe_id and g.gruppe_name like'confluence-users'");
                        ?>
                    </td>
                    <td><?php
                        echo (500-$conf_member); ?>
                    </td>
                </tr>
                <tr onclick="location.href='tools_gruppe_nutzer.php?loginname=git-users'">
                    <td class="nummerStyle">git-users</td>
                    <td><?php
                        echo $git_member= $db_instanz->frageNachEinemWert("select count(ng.f_nutzer_id) as count from gruppe as g, nutzer_gruppen as ng where g.gruppe_id=ng.f_gruppe_id and g.gruppe_name like'git-users'");
                        ?>
                    </td>
                    <td>∞</td>
                </tr>
            </table>
        </div>
        <div class="uebersicht">
            <h1>LDAP Gruppen</h1><br />
        </div>
        <div id="usersGroup" align="center">
            <table class="hin-table">
                <tr>
                    <th>Projektmanagement-Software</th>
                    <th>Gruppen </th>
                </tr>
                <tr>
                    <td class="tools_listetd" onclick="location.href='confluence.php'" >
                        <img src="img/confluence-logo.png" alt="conf logo" width="70" >
                    </td>
                    <td><?php echo $db_instanz->frageNachEinemWert("select count(gruppe_id) as count from gruppe where gruppe_name like'conf%'")?></td>
                </tr>
                <tr>
                    <td class="tools_listetd" onclick="location.href='jira.php'" >
                        <img src="img/jira.png" alt="jira logo" width="80">
                    </td>
                    <td><?php echo $db_instanz->frageNachEinemWert("select count(gruppe_id) as count from gruppe where gruppe_name like'jira%'");?></td>
                </tr>
                <tr>
                    <td class="tools_listetd" onclick="location.href='git.php'" >
                        <img src="img/git.png" alt="git logo" width="80">
                    </td>
                    <td><?php echo $db_instanz->frageNachEinemWert("select count(gruppe_id) as count from gruppe where gruppe_name like'git%'");?></td>
                </tr>
            </table>
        </div><br/><br />
        <h1>Übersicht Projekte</h1><br />
        <div id="usersGroup" align="center">
            <table class="hin-table">
                <tr>
                    <th>Bezeichnung</th>
                    <th>Mitglieder </th>
                </tr>
                <tr onclick="location.href='kunden.php'">
                    <td class="nummerStyle">Kunden</td>
                    <td><?php echo $db_instanz->frageNachEinemWert("select count(kunde_id) as count from kunde");?></td>
                </tr>
                <tr onclick="location.href='freelancer_zahl.php?type=3'">
                    <td>Freelancer</td>
                    <td><?php echo $db_instanz->frageNachEinemWert("select count(nutzer_id) as count from nutzer where f_nutzer_typ_id=3");?> </td>
                </tr>
                <tr onclick="location.href='freelancer_zahl.php?type=4'">
                    <td>Extern Freelancer</td>
                    <td><?php echo $db_instanz->frageNachEinemWert(" select count(nutzer_id) as count from nutzer where f_nutzer_typ_id='4'"); ?> </td>
                </tr>
                <tr onclick="location.href='partner.php'" >
                    <td>Partner</td>
                    <td><?php echo $db_instanz->frageNachEinemWert(" select count(partner_id) as count from partner"); ?> </td>
                </tr>
            </table>
        </div><br/>
        <div id="headline">
            <h1>Suche</h1>
        </div>
        <div class="search_formolar_tools_liste">
            <form action="search.php" method="get" target="_self">
                <p>Hier können Sie nach alle LDAP Users durchsuchen.</p>
                <input type="submit" name="loggin" value="jetzt suchen">

            </form><br />
        </div>
        </div>
    </div>
    <?php
}
else {
    // Login fehlgeschlagen / Benutzer nicht vorhanden
    header("Location: index.php");
}
$db_instanz->close();
?>
</body>
</html>