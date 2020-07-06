<?php
require_once "funktionen/alle_funktionen.php";
require_once "including/ldap_daten.php";
require_once "classes/DatenbankVerbindung.php";
$db_instanz = DatenbankVerbindung::erzeugeDatenbankVerbindung();
if(!$db_instanz){
    die('Es konnte keine Verbindung zur Datenbank hergestellt werden');
}
/**
 * Die komplette Datenbank Entität entleren
 */
$db_instanz->datenbankEntleeren("delete from nutzer");
$db_instanz->datenbankEntleeren("delete from gruppe");
$db_instanz->datenbankEntleeren("delete from kunde");
$db_instanz->datenbankEntleeren("delete from nutzer_gruppen");
$db_instanz->datenbankEntleeren("delete from partner");
/**
 * verbinden zum ldap server
 */
if ($ldapConnect = ldap_connect($ldapAddress, $ldapPort)) {
    ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapConnect, LDAP_OPT_REFERRALS, 0);
    if ($bind = ldap_bind($ldapConnect, $domain . "\\" . $username, $password)) {//Bindung zu einem LDAP Verzeichnis
        $dn = "DC=itools,DC=intern";
        $fields = "(|(samaccountname=*))";
        $search = ldap_search($ldapConnect, $dn, $fields);//Suche im LDAP Baum
        $info = ldap_get_entries($ldapConnect, $search);//Liefere alle Ergebnis-Einträge
        $anzahl = ldap_count_entries($ldapConnect, $search);//Zähle die Anzahl der Einträge bei einer Suche
        /**
         * Erforderliche Info in variable speichern
         */
        for ($i = 0; $i < $anzahl; $i++) {
            $vorname = $db_instanz->escapeString(@$info[$i]["givenname"][0]);
            $nachname = $db_instanz->escapeString(@$info[$i]["sn"][0]);
            $loginname = $db_instanz->escapeString(@$info[$i]["samaccountname"][0]);
            $gruppenName = $db_instanz->escapeString( @$info[$i]["cn"][0]);
            $email = $db_instanz->escapeString(@$info[$i]["mail"][0]);
            $logindatum = $db_instanz->escapeString(@$info[$i]["lastlogontimestamp"][0]);
            $logindatum = datumKonvertLdap($logindatum);
            $passAenderungDatum = $db_instanz->escapeString(@$info[$i]["pwdlastset"][0]);
            $passAenderungDatum = datumKonvertLdap($passAenderungDatum);
            $wannErstelltDatum =$db_instanz->escapeString( @$info[$i]["whencreated"][0]);
            $wannErstelltDatum = substr($wannErstelltDatum, 0, 4)
                . "-" . substr($wannErstelltDatum, 4, 2)
                . "-" . substr($wannErstelltDatum, 6, 2);
            for ($w = 0; $w < @$info[$i]["objectclass"]["count"]; $w++) {
                $ldap_user_accounts[$i]['objectclass'] = @$info[$i]["objectclass"][$w];
            }
            /**
              * Nutzer oder Gruppe / Infos in den Datenbanktabellen einfügen
              */
            if ($ldap_user_accounts[$i]['objectclass'] == "user") {
                $db_instanz->datenbankEinfuegen
                ("INSERT INTO nutzer(f_nutzer_typ_id,vorname,nachname,loginname,email,logindatum,pass_aenderung_datum,eingetragen_datum)
                          VALUE (null,'$vorname','$nachname','$loginname','$email','$logindatum','$passAenderungDatum','$wannErstelltDatum')");
            } elseif ($ldap_user_accounts[$i]['objectclass'] == "group") {
                $db_instanz->datenbankEinfuegen("INSERT INTO gruppe(gruppe_name,eingetragen_datum)
                                                         VALUE ('$gruppenName','$wannErstelltDatum')");
            }
            $w = 0;
        }
        /**
         * Im LDAP Server Nach Gruppen-Memberof für jeder Nutzer suchen/ Infos in den Zwischentabelle "nutzer_gruppen" einfügen.
         */
        $row = $db_instanz->frageNachName("select nutzer_id,loginname from nutzer");
        foreach ($row as $value) {
            $fields = "(|(samaccountname=$value[1]))";
            $search = ldap_search($ldapConnect, $dn, $fields);
            $info = ldap_get_entries($ldapConnect, $search);
            $anzahl = ldap_count_entries($ldapConnect, $search);
            $ldap_user_accounts[$value[1]] = @$info[0]["memberof"];
            $count = $ldap_user_accounts[$value[1]]["count"];
            unset($ldap_user_accounts[$value[1]]["count"]);//Array [count] löschen für die Tabellen sortierung
            for ($e = 0; $e < $count; $e++) {
                $gruppen_name_link = $ldap_user_accounts[$value[1]][$e];
                $gruppen_name = explode(",", $gruppen_name_link);//explode die redundanten Datei, die aus LDAP kommt.
                $gruppen_user = substr($gruppen_name[0], 3);
                //nach gruppe_id suchen für jeder gruppe.
                $row_gruppe_id = $db_instanz->frageNachEinemWert("select gruppe_id from gruppe where gruppe_name='$gruppen_user'");
                $db_instanz->datenbankEinfuegen("INSERT INTO nutzer_gruppen(f_nutzer_id,f_gruppe_id) VALUE($value[0],$row_gruppe_id)");
            }
        }
        /**
         *  Mitglieder der Mitarbeiter-Gruppe "itools_ma" in der "nutzer" Tabelle als Mitarbeiter definieren
         */
        $row = $db_instanz->frageNachName("select 
                                                    n.nutzer_id 
                                                    from nutzer as n,nutzer_gruppen as ng, gruppe as g 
                                                    where n.nutzer_id=ng.f_nutzer_id 
                                                    and ng.f_gruppe_id=g.gruppe_id 
                                                    and g.gruppe_name like'itools_ma%' 
                                                    group by n.nutzer_id");
        foreach ($row as $value) {
            $db_instanz->datenbankEinfuegen("update nutzer set f_nutzer_typ_id=1 where nutzer_id=$value[0]");
        }
        /**
         * Mitglieder der Projektmanager-Gruppe "itools_ma_pm" in der "nutzer" Tabelle als Projektmanager definieren
         */
        $row = $db_instanz->frageNachName("select 
                                                    n.nutzer_id 
                                                    from nutzer as n,nutzer_gruppen as ng, gruppe as g
                                                    where n.nutzer_id=ng.f_nutzer_id 
                                                    and ng.f_gruppe_id=g.gruppe_id 
                                                    and g.gruppe_name like'itools_ma_pm' 
                                                    group by n.loginname");
        foreach ($row as $value) {
            $db_instanz->datenbankEinfuegen("update nutzer set f_nutzer_typ_id=2 where nutzer_id=$value[0]");
        }
        /**
         * Im LDAP Server Nach Kunden Info suchen/ Infos in den Datenbanktabelle "kunde" einfügen.
         */
        $dn = "OU=kunden,OU=interactive,DC=itools,DC=intern";//nach alle Kunden suchen
        $nur_dieses = array("ou");
        $sr = ldap_list($ldapConnect, $dn, "ou=*", $nur_dieses);
        $info = ldap_get_entries($ldapConnect, $sr);
        for ($i = 0; $i < $info['count']; $i++) {
            $kunden_name = $info[$i]['ou'][0];
            $db_instanz->datenbankEinfuegen("INSERT INTO kunde(kunde_name) VALUE ('$kunden_name')");
            $dn = " OU=" . $kunden_name . ",OU=kunden,OU=interactive,DC=itools,DC=intern";//nach Memberof eines Kunden suchen
            $nur_dieses = array("cn");
            $sr = ldap_list($ldapConnect, $dn, "cn=*", $nur_dieses);
            $info1 = ldap_get_entries($ldapConnect, $sr);
            $kunden_id = $db_instanz->frageNachEinemWert("select kunde_id from kunde where kunde_name like '$kunden_name'");
            for ($w = 0; $w < $info1['count']; $w++) {
                $memberof_kunde = $info1[$w]['cn'][0];
                //Insert f_kunde_id in Nutzer Tabelle, um die mit Kunde Tabelle zu verknüpfen
                $db_instanz->datenbankEinfuegen(" update nutzer set f_kunde_id='$kunden_id' where loginname='$memberof_kunde'");
            }
        }
        /**
         * Datenbanktabelle "partner" einfügen
         */
        $dn = "OU=_extern-partner,OU=organisation,OU=interactive,DC=itools,DC=intern";
        $nur_dieses = array("ou");
        $sr = ldap_list($ldapConnect, $dn, "ou=*", $nur_dieses);
        $info = ldap_get_entries($ldapConnect, $sr);//gebt list[extern-partner] züruck
        for ($i = 0; $i < $info["count"]; $i++) {
            $partner_name = $info[$i]["ou"][0];
            $db_instanz->datenbankEinfuegen(" insert into partner (partner_name) VALUE ('$partner_name')");
        }
        $fields = "(|(samaccountname=*))";
        $search = ldap_search($ldapConnect, $dn, $fields);
        $info = ldap_get_entries($ldapConnect, $search);//gebt list[Mitglieder jeder extern-partner] züruck
        for ($i = 0; $i < $info["count"]; $i++) {
            $partnerMitarbeiterName[$i] = $info[$i]["dn"];
            $partnerMitarbeiterNameExplode = explode(",", $partnerMitarbeiterName[$i]);
            $partnerMitarbeiterName[$i] = substr($partnerMitarbeiterNameExplode[0], 3);
            $partnerName[$i] = substr($partnerMitarbeiterNameExplode[1], 3);
            $partner_id = $db_instanz->frageNachEinemWert("select partner_id as id from partner where partner_name like'{$partnerName[$i]}'");
            $db_instanz->datenbankEinfuegen("update nutzer set f_partner_id=$partner_id where loginname like'$partnerMitarbeiterName[$i]'");
        }
        //nr:3 ist die ID für Freelancer in nutzer_typ Tabelle
        freelancer_identifizierer("OU=_freelancer", $ldapConnect,  $db_instanz, 3);
        //nr:4 ist die ID für Externe Freelancer in nutzer_typ Tabelle
        freelancer_identifizierer("OU=extern-freelancer", $ldapConnect, $db_instanz, 4);
    } else {
        echo "ldap_bind: Could not able to connect, check the Username and Password";
    }
} else {
    echo "ldap_connect: Could not able to connect";
}
ldap_close($ldapConnect);
$db_instanz->close();
header("Location: tools_liste.php");
?>