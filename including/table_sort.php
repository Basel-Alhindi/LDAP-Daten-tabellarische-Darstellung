<?php
/* DB Daten sortieren */
$sort="ASC";
if (isset($_GET["sort"])) {
    if ($_GET["sort"]=="ASC") {
        $_GET['sort']="DESC";
       $sort=$_GET["sort"];
    }
    elseif ($_GET["sort"]=="DESC") {
        $_GET['sort']="ASC" ;
        $sort=$_GET["sort"];
    }
}
?>