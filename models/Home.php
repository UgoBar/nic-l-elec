<?php

class Home
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Insertion d'un texte de présentation
     * @param string $title le titre du texte
     * @param string $description le contenu
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $title, string $description)
    {
        /** ......................... CREATE HOME ........................ */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE home */
        $sth = $this->dbh->prepare("INSERT INTO home 
            (hom_title, hom_description) 
            VALUES (:hom_title, :hom_description)");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('hom_title',$title);
        $sth->bindValue('hom_description',$description);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification du texte de présentation
     * @param int $id id du texte de présentation
     * @param string $title le titre du texte
     * @param string $description le contenu
     * @return array integer ID de l'élément ajouté
     */
    public function update(int $id, string $title, string $description)
    {

        /** ......................... UPDATE HOME ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE home */
        $sth = $this->dbh->prepare("UPDATE home 
                                    SET hom_title=:title, hom_description=:hom_description
                                    WHERE hom_id=:id");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('id',$id, PDO::PARAM_INT);
        $sth->bindValue('title',$title);
        $sth->bindValue('hom_description',$description);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }
    
    /** Suppression du texte de présentation
     * @param int $id du texte de présentation
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare('DELETE FROM home WHERE hom_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

    /** Renvoie tous les texte d'intro
     * @return array Tous les textes d'intro
     */
    public function getAll()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM home');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $home = $sth->fetchAll();
        
        return $home;
    }

    public function countOf()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as hom_count FROM home');
        $sth->execute();
        $count = $sth->fetch();
        return $count;
    }
}