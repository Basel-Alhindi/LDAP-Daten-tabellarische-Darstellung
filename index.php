<?php
session_start();
require_once "including/ldap_daten.php";
?>
<!DOCTYPE html>
<html>
<head>
    <link href="css/main.css" rel="stylesheet" type="text/css" />
    <title>Itools Statistik Tool</title>
</head>
<body>
<div class="header">
    <a href="tools_liste.php" alt="Home">
        <img src="img/logo.png" alt="Logo" />
    </a>
</div>
<?php
if (isset($_POST["username"])&& isset($_POST['passwort'])) {
    $username = $_POST["username"];
    $password = $_POST["passwort"];
    if ($ldap_connect = ldap_connect($ldapAddress, $ldapPort)) {
        ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_connect, LDAP_OPT_REFERRALS, 0);
        // Authentifizierung des Benutzers
        if ($bind = ldap_bind($ldap_connect, $domain . "\\" . $username, $password)) {
            $_SESSION['bind_check']=$bind;
            $dn = "DC=itools,DC=intern";
            $person="itools_ma_pm";
            $fields = "(|(samaccountname=$person))";
            $search = ldap_search($ldap_connect, $dn, $fields);
            $info = ldap_get_entries($ldap_connect,$search);
            $itools_ma_mitglied=array();
            for ($i=0;$i<=$info[0]["member"]["count"];$i++) {
                // itools-ma member aufrufen
                $itools_ma_mitglied[$i]=$info[0]["member"][$i];
                $gruppen_name = explode(",", $itools_ma_mitglied[$i]);
                $itools_ma_mitglied[$i]= substr($gruppen_name[0], 3 );
            }
            //Einlogin beschrÃ¤nkung
            if (!(in_array($username,$itools_ma_mitglied,true ))) {
                header("Location: fehler_berechtigung.html");
                exit();
            }
            if ($_GET["last"]=="conf") {
                header("location: confluence.php");
            }
            elseif ($_GET["last"]=="jira") {
                header("location: jira.php");
            }
            elseif ($_GET["last"]=="git") {
                header("location: git.php");
            }
            else {
                header("location: tools_liste.php");
            }
        }
        else {
            // Login fehlgeschlagen / Benutzer nicht vorhanden
            header("Location: fehler_seite.html");
        }
    }
    else {
        header("Location: fehler_seite.html");
        //Verbindung fehlgeschlagen
    }
}
else {
    ?>
    <div id="main" align="center">
        <div class="uebersicht">
            <h1>Itools Statistik Tool</h1>
        </div>
        <div id="formolar">
            <form action="index.php" method="post" target="_self">
                <p>Benutzername:</p>
                <input type="text" name="username" placeholder="Benutzername">
                <p>Passwort:</p>
                <input type="password" name="passwort" placeholder="Password"><br>
                <input type="hidden" name="last" value="<?php if(isset($_GET['last'])){echo $_GET['last'];} ?>"><br>
                <br><input type="submit" name="login" value="Login">
            </form>
        </div>
    </div>
    <?php
}
?>
</body>
</html>