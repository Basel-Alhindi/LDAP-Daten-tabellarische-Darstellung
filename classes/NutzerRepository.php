<?php
class NutzerRepository
{
    const GRUPPEN_ART_CONFLUENCE = 'confluence-users';
    const GRUPPEN_ART_JIRA       = 'jira-users';
    const GRUPPEN_ART_GIT        = 'git-users';
    /*  DatenbankVerbindung  */
    private $db_instanz;
    public function __construct(DatenbankVerbindung $db_instanz) {
        $this->db_instanz = $db_instanz;
    }
    public function zaehleNutzer($gruppenArt = '' , $suchbegriff = ''  ){
        return $this->db_instanz->frageNachEinemWert
        ("select
                count(ng.f_nutzer_id)
                from gruppe as g 
				inner join nutzer_gruppen as ng on g.gruppe_id = ng.f_gruppe_id  
				inner join nutzer as n on ng.f_nutzer_id = n.nutzer_id 
                where
                g.gruppe_name like'".$this->db_instanz->escapeString($gruppenArt)."' 
                and n.loginname like '%".$this->db_instanz->escapeString($suchbegriff)."%'");
    }
    public function findeNutzer($gruppenArt = '', $suchbegriff = '', $sort = ''){
        return $this->db_instanz->frageNachName
        ("select
                n.loginname, n.logindatum 
                from nutzer as n inner join nutzer_gruppen as ng on n.nutzer_id=ng.f_nutzer_id 
				inner join gruppe as g on g.gruppe_id=ng.f_gruppe_id
                where
                g.gruppe_name like '".$this->db_instanz->escapeString($gruppenArt)."' 
                and n.loginname like'%".$this->db_instanz->escapeString($suchbegriff)."%'
                order by n.loginname $sort ");
    }
    public function zaehleNutzerGruppen($nutzer_loginname_name = '', $suchbegriff = '' )
    {
        return $this->db_instanz->frageNachEinemWert
        ("select
                count(n.loginname) 
                from nutzer as n 
                inner join nutzer_gruppen as ng on n.nutzer_id=ng.f_nutzer_id
				inner join gruppe as g on ng.f_gruppe_id=g.gruppe_id 
                where 
				n.loginname like'".$this->db_instanz->escapeString($nutzer_loginname_name)."'
                and g.gruppe_name like'%".$this->db_instanz->escapeString($suchbegriff)."%' ");
    }
    public function findeNutzerGruppen($nutzer_loginname_name = '', $suchbegriff = '', $sort = '' )
    {
        return $this->db_instanz->frageNachName
        ("select 
                g.gruppe_name 
                from nutzer as n 
                inner join nutzer_gruppen as ng on n.nutzer_id = ng.f_nutzer_id
				inner join gruppe as g on ng.f_gruppe_id = g.gruppe_id  
				where
				n.loginname like'".$this->db_instanz->escapeString($nutzer_loginname_name)."' 
                and gruppe_name like'%".$this->db_instanz->escapeString($suchbegriff)."%'
                order by g.gruppe_name $sort ");
    }
    public function findeEinNutzer($nutzer = ''){
        return $this->db_instanz->frageNachName
        ("select 
                vorname,nachname,loginname,email,logindatum,pass_aenderung_datum,eingetragen_datum 
                from nutzer 
                where 
                loginname like '".$this->db_instanz->escapeString($nutzer)."'");
    }
}