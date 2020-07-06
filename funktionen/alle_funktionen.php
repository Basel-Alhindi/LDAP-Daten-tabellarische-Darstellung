<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Date Convert, für datenbank_einfuegen.php
/// //////////////////////////////////////////////////////////////////////////////////////////////////////////////
function datumKonvertLdap($mydate){
    if ($mydate==""){return "0000-00-00"; }
    $winInterval = round($mydate / 10000000);
    // substract seconds from 1601-01-01 -> 1970-01-01
    $unixTimestamp = ($winInterval - 11644473600);
    $unixTimestamp = date("Y-m-d H:i:s", $unixTimestamp);
    return $unixTimestamp;
}
/////////////////////////////
///                       ///
///  Datenbank Funktionen ///
///                       ///
/////////////////////////////
function freelancer_identifizierer($queryorg,$ldap_connect,$db_instanz,$typ_nr){
    $dn = $queryorg.",OU=organisation,OU=interactive,DC=itools,DC=intern";
    $nur_dieses = array("cn");
    $sr=ldap_list($ldap_connect, $dn, "cn=*", $nur_dieses);
    $info = ldap_get_entries($ldap_connect, $sr);
    foreach ($info as $freeLancerName) {
        $freeLancerName=$freeLancerName['cn'][0];
        $nutzer_id=$db_instanz->frageNachEinemWert(" select nutzer_id from nutzer where loginname='$freeLancerName'");
        $db_instanz->datenbankEinfuegen(" update nutzer set f_nutzer_typ_id='$typ_nr' where nutzer_id='$nutzer_id'");
    }
}
