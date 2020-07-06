<?php
class GruppenRepository
{
    const GRUPPEN_ART_CONFLUENCE = 'conf';
    const GRUPPEN_ART_JIRA       = 'jira';
    const GRUPPEN_ART_GIT        = 'git';
    /**
     * @var DatenbankVerbindung
     */
    private $db_instanz;

    public function __construct(DatenbankVerbindung $db_instanz) {
        $this->db_instanz = $db_instanz;
    }
    public function zaehleGruppen($gruppenArt = '' , $suchbegriff = ''  ){
        return $this->db_instanz->frageNachEinemWert
        ("select 
                        count(gruppe_id) 
                        from gruppe
                        where 
                        gruppe_name like '".$this->db_instanz->escapeString($gruppenArt)."%' 
                        and gruppe_name like'%".$this->db_instanz->escapeString($suchbegriff)."%'");
    }
    public function findeGruppen($gruppenArt = '', $suchbegriff = '' ,$sort = ''){
        return $this->db_instanz->frageNachName
        ("select
                        gruppe_name 
                        from gruppe 
                        where 
                        gruppe_name like '".$this->db_instanz->escapeString($gruppenArt)."%'
                        and gruppe_name like'%".$this->db_instanz->escapeString($suchbegriff)."%'
                        Order by gruppe_name $sort ");
    }
    public function zaehleMitglieder($gruppe_name = '')
    {
        return $this->db_instanz->frageNachEinemWert
        ("select
                        count(g.gruppe_name) 
                        from gruppe as g 
						inner join nutzer_gruppen as ng on g.gruppe_id=ng.f_gruppe_id 
                        and gruppe_name like'%".$this->db_instanz->escapeString($gruppe_name)."%'");
    }
}
