<?php
require_once "including/dbInstanzDaten.php";
 class DatenbankVerbindung
{
    private static $instanz ;
    private $dbVerbindung ;
     //privater Konstruktur, man soll FactoryMEthode verwenden
     private function __construct() { }
     /**
      *  maskiert eine Zeichenfolge für die Verwendung in eine Datenbankabfrage
      * @param $string
      * @return mixed
      */
    public function escapeString($string){
        return $this->dbVerbindung->real_escape_string($string);
    }
     /**
      * @return bool|DatenbankVerbindung
      */
    public static function erzeugeDatenbankVerbindung()
    {
        if (isset(self::$instanz)) {
            return self::$instanz;
        }
        $tempInstanz=new self();
        global $db_host;
        global $db_user;
        global $db_password;
        global $db_name;
        $canConnect = $tempInstanz->connect($db_host, $db_user, $db_password, $db_name);
        if (!$canConnect) {
            return false;
        }
        self::$instanz=$tempInstanz;
        return self::$instanz;
    }
    //Datenbankverbindung
    public function connect($db_host, $db_user, $db_password, $db_name)
    {
        $link = mysqli_connect($db_host, $db_user, $db_password, $db_name);
        if (!$link) {
            error_log('Could not connect to database!');
            return false;
        } else {
            $this->dbVerbindung = $link;
        }
        return true;
    }
     //Schließt die geöffnete Datenbankverbindung Method.
     public function close()
     {
         mysqli_close($this->dbVerbindung);
     }
    // Nach Entität Anzahl fragen Method
    public function frageNachEinemWert($sql_query)
    {
        if (!$result = mysqli_query($this->dbVerbindung, $sql_query)) {
            echo "ERROR: Could not execute $sql_query. " . mysqli_error($this->dbVerbindung);
        }
        $row = mysqli_fetch_row($result);//holt die erste Ergebniszeile.
        if (!$row) {
            return false;
        }
        return $row[0];//gibt die erste Spalte aus dem Ergebnis zurück.
    }
     public function frageNachName($sql_query)
     {
         if (!$result = mysqli_query($this->dbVerbindung, $sql_query)) {
             echo "ERROR: Could not execute $sql_query. " . mysqli_error($this->dbVerbindung);
         }
         $row = mysqli_fetch_all($result);//holt die erste Ergebniszeile.
         if (!$row) {
             return false;
         }
         return $row;
     }
    public function datenbankEinfuegen($sql_query)
    {
        if (!$result = mysqli_query($this->dbVerbindung, $sql_query)) {
            echo "ERROR: Could not able to execute $sql_query. " . mysqli_error($this->dbVerbindung);
        }
    }
    public function datenbankEntleeren($sql_query)
    {
        if (!$result = mysqli_query($this->dbVerbindung, $sql_query)) {
            echo "ERROR: Could not able to execute $sql_query. " . mysqli_error($this->dbVerbindung);
        }
    }
}