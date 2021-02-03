<?php

class Banner
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Renvoie toutes les photos de la bannière
     * @return array Toutes les photos de la bannière
     */
    public function getAll()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM banner');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $bannerPictures = $sth->fetchAll();
        
        return $bannerPictures;
    }

    /** Insertion d'une photo
     * @param string $title le titre de la photo
     * @param string $picture la photo de la bannière
     * @param bool $display ordre d'affichage
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $picture, string $title, int $display)
    {

        /** ......................... CREATE BANNER ........................ */
        /** 1. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE banner */
        $sth = $this->dbh->prepare("INSERT INTO banner (ban_picture, ban_title, ban_display) 
                                    VALUES (:ban_picture, :ban_title, :ban_display)");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('ban_picture',$picture);
        $sth->bindValue('ban_title',$title);
        $sth->bindValue('ban_display',$display,PDO::PARAM_INT);
        /** 3. Execution de notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification d'une photo
     * @param int $id l'identifiant de la photo
     * @param string $title le titre de la photo
     * @param string $picture la photo de la bannière
     * @param bool $display ordre d'affichage
     * @return array integer ID de l'élément ajouté
     */
    public function update(int $id, string $picture, string $title, int $display)
    {
        
        /** ........................ UPDATE BANNER ........................ */
        /** 1. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE banner */
        $sth = $this->dbh->prepare("UPDATE banner 
            SET ban_title=:title, ban_picture=:picture, ban_display=:display
            WHERE ban_id=:id");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('id',$id, PDO::PARAM_INT);
        $sth->bindValue('title',$title);
        $sth->bindValue('picture',$picture);
        $sth->bindValue('display',$display, PDO::PARAM_INT);

        /** 3. Execution de notre requête */
        $sth->execute();
    }
    
    /** Suppression d'une photo
     * @param int $id de la photo
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare('DELETE FROM banner WHERE ban_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
    }

    /** Renvoie une photo à partir de son ordre d'affichage
     * @param string $display ordre d'affichage sur le front recherché
     * @return array les données de la photo
     */
    public function getByDisplay(int $display)
    {
        /** .......................DISPLAY IN BASE......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE banner */
        $sth = $this->dbh->prepare('SELECT * FROM banner WHERE ban_display = :checkDisplay');
        /** 3. Lier(Bind) la valeur de l'ID(jeton) en lui précisant que ça doit être un INTEGER */
        $sth->bindValue('checkDisplay',$display);
        /** 4. Executer notre requête */
        $sth->execute();
        /** 5. Je récupère le jeu d'enregistrement */
        $picture = $sth->fetch();
        
        return $picture;
    }

    /** Recherche d'une photo
     * @param int $id de du photo
     * @return array les données complètes de la photo en fonction de son id
     */
    public function findById(int $id)
    {
        /** 2. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM banner WHERE ban_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    public function countOf()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as ban_count FROM banner');
        $sth->execute();
        $count = $sth->fetch();
        return $count;
    }
}