<?php

class Service
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Renvoie tous les services
     * @return array Tous les services
     */
    public function getAll()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM `service`');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $services = $sth->fetchAll();
        
        return $services;
    }

    /** Renvoie tous les services
     * @return array Tous les services classé par "display" (ordre d'affichage sur le front)
     */
    public function orderByDisplay()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM `service` ORDER BY ser_display');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $services = $sth->fetchAll();
        
        return $services;
    }
    
    /** Insertion d'un service
     * @param string $title le titre du service
     * @param string $description le contenu détaillé du service
     * @param int $display la position d'affichage sur le front
     * @param string $price le prix du service
     * @param string $picture la photo du service
     * @param bool $top est-ce que le service est classé en top ?
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $title, string $description, int $display, ?string $price, ?string $picture, ?bool $top)
    {

        /** ....................... CREATE SERVICE ........................ */
        $sth = $this->dbh->prepare("INSERT INTO `service` 
            (ser_title, ser_description, ser_picture, ser_price, ser_top, ser_display) 
            VALUES (:ser_title, :ser_description, :ser_picture, :ser_price, :ser_top, :ser_display)");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('ser_title',$title);
        $sth->bindValue('ser_description',$description);
        $sth->bindValue('ser_picture',$picture);
        $sth->bindValue('ser_price',$price);
        $sth->bindValue('ser_top',$top,PDO::PARAM_BOOL);
        $sth->bindValue('ser_display', $display, PDO::PARAM_INT);
        /** 3. Execution de notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification d'un service
     * @param string $title le titre du service
     * @param string $description le contenu détaillé du service
     * @param int $display la position d'affichage sur le front
     * @param string $price le prix du service
     * @param string $picture la photo du service
     * @param bool $top est-ce que le service est classé en top ?
     * @return array integer ID de l'élément ajouté
     */
    public function update(int $id, string $title, string $description, int $display, ?string $price, ?string $picture, ?bool $top)
    {
        
        /** ....................... UPDATE SERVICE ........................ */
        $sth = $this->dbh->prepare("UPDATE `service` 
            SET ser_title=:title, ser_description=:content, ser_picture=:picture, ser_price=:price, ser_top=:topService, ser_display=:display
            WHERE ser_id=:id");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('id',$id, PDO::PARAM_INT);
        $sth->bindValue('title',$title);
        $sth->bindValue('content',$description);
        $sth->bindValue('picture',$picture);
        $sth->bindValue('price',$price);
        $sth->bindValue('topService',$top, PDO::PARAM_BOOL);
        $sth->bindValue('display',$display, PDO::PARAM_INT);

        /** 3. Execution de notre requête */
        $sth->execute();
    }
    
    /** Suppression d'un service
     * @param int $id du service
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare('DELETE FROM `service` WHERE ser_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
    }
    
    /** Recherche d'un service classé "top"
     * @return array les services classés "top"
     */
    public function findByTop()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM `service` WHERE ser_top = 1 ORDER BY ser_display');
        /** 2. Execution de notre requête */
        $sth->execute();
        $topServices = $sth->fetchAll();
        return $topServices;
    }

    /** Recherche d'un service
     * @param int $id de du service
     * @return array les données complètes du service en fonction de son id
     */
    public function findById(int $id)
    {
        /** 2. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM `service` WHERE ser_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    /** Renvoie un service à partir de son ordre d'affichage
     * @param string $display ordre d'affichage sur le front recherché
     * @return array les données du service
     */
    public function getByDisplay(int $display)
    {
        /** .......................DISPLAY IN BASE......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE orders */
        $sth = $this->dbh->prepare('SELECT * FROM `service` WHERE ser_display = :checkDisplay');
        /** 3. Lier(Bind) la valeur de l'ID(jeton) en lui précisant que ça doit être un INTEGER */
        $sth->bindValue('checkDisplay',$display);
        /** 4. Executer notre requête */
        $sth->execute();
        /** 5. Je récupère le jeu d'enregistrement */
        $service = $sth->fetch();
        
        return $service;
    }

    public function countOfServices()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as ser_count FROM `service`');
        $sth->execute();
        $count = $sth->fetch();
        return $count;
    }
    public function sixFirstServices()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM `service` WHERE ser_display <= 6 ORDER BY ser_display');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $firstSix = $sth->fetchAll();

        return $firstSix;
    }

    public function sixPlusServices()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM `service` WHERE ser_display > 6 ORDER BY ser_display');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $sixPlus = $sth->fetchAll();

        return $sixPlus;
    }
}