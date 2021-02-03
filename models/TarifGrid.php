<?php

class tarifgrid
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Insertion d'un tarif
     * @param string $title le titre du tarif
     * @param string $price le prix du tarif
     * @param int $display l'ordre d'affichage du tarif
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $title, string $price, int $display)
    {
        /** ......................... CREATE GRID ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE tarifgrid */
        $sth = $this->dbh->prepare("INSERT INTO tarifgrid 
            (tar_title, tar_price, tar_display) 
            VALUES (:tar_title, :tar_price, :tar_display)");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('tar_title',$title);
        $sth->bindValue('tar_price',$price);
        $sth->bindValue('tar_display',$display, PDO::PARAM_INT);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification d'un tarif
     * @param int $id id du tarif
     * @param string $title le titre du tarif
     * @param string $price le prix du tarif
     * @param int $display l'ordre d'affichage du tarif
     * @return array integer ID de l'élément ajouté
     */
    public function update(int $id, string $title, string $price, int $display)
    {

        /** ......................... UPDATE GRID ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE tarifgrid */
        $sth = $this->dbh->prepare("UPDATE tarifgrid 
                                    SET tar_title=:title, tar_price=:price, tar_display=:display
                                    WHERE tar_id=:id");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('id',$id, PDO::PARAM_INT);
        $sth->bindValue('title',$title);
        $sth->bindValue('price',$price);
        $sth->bindValue('display',$display, PDO::PARAM_INT);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }
    
    /** Recherche d'un tarif
     * @param int $id du tarif
     * @return array les données complètes du tarif en fonction de son id
     */
    public function findById(int $id)
    {
        /** PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM tarifgrid WHERE tar_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    /** Suppression d'un tableau de tarif
     * @param int $id du tarif
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare('DELETE FROM tarifgrid WHERE tar_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

    /** Renvoie tous les tarifs
     * @return array Tous les tarifs
     */
    public function getAll()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM tarifgrid');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $tarifs = $sth->fetchAll();
        
        return $tarifs;
    }

    /** Renvoie tous les tarifs
     * @return array Tous les tarifs classé par "display" (ordre d'affichage sur le front)
     */
    public function orderByDisplay()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM tarifgrid ORDER BY tar_display');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $tarifs = $sth->fetchAll();
        
        return $tarifs;
    }

    public function countOf()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as tar_count FROM tarifgrid');
        $sth->execute();
        $count = $sth->fetch();
        return $count;
    }
}