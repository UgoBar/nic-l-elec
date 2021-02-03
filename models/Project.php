<?php

class Project
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Renvoie tous les projets
     * @return array Tous les projets
     */
    public function getAll()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM project');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $projects = $sth->fetchAll();
        
        return $projects;
    }

    /** Renvoie tous les projets
     * @return array Tous les projets classé par "display" (ordre d'affichage sur le front)
     */
    public function orderByDisplay()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM project ORDER BY pro_display');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $projects = $sth->fetchAll();
        
        return $projects;
    }
    
    /** Insertion d'un projet
     * @param string $title le titre du projet
     * @param string $summary Une courte description du projet
     * @param string $description le contenu détaillé du projet
     * @param int $display la position d'affichage sur le front
     * @param string $picture1 la 1ère photo du projet
     * @param string $picture2 la 2ème photo du projet
     * @param string $picture3 la 3ème photo du projet
     * @param bool $top est-ce que le projet est classé en top ?
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $title, string $summary, string $description, int $display, ?string $picture1, ?string $picture2, ?string $picture3, ?bool $top)
    {

        /** ....................... CREATE PROJECT ........................ */
        /** 1. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE project */
        $sth = $this->dbh->prepare("INSERT INTO project 
            (pro_title, pro_summary, pro_description, pro_picture1, pro_picture2, pro_picture3, pro_top, pro_display) 
            VALUES (:pro_title, :pro_summary, :pro_description, :pro_picture1, :pro_picture2, :pro_picture3, :pro_top, :pro_display)");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('pro_title',$title);
        $sth->bindValue('pro_summary',$summary);
        $sth->bindValue('pro_description',$description);
        $sth->bindValue('pro_picture1',$picture1);
        $sth->bindValue('pro_picture2',$picture2);
        $sth->bindValue('pro_picture3',$picture3);
        $sth->bindValue('pro_top',$top,PDO::PARAM_BOOL);
        $sth->bindValue('pro_display', $display, PDO::PARAM_INT);
        /** 3. Execution de notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification d'un projet
     * @param int $id l'identifiant du projet
     * @param string $title le titre du projet
     * @param string $summary Une courte description du projet
     * @param string $description le contenu détaillé du projet
     * @param int $display la position d'affichage sur le front
     * @param string $picture1 la 1ère photo du projet
     * @param string $picture2 la 2ème photo du projet
     * @param string $picture3 la 3ème photo du projet
     * @param bool $top est-ce que le projet est classé en top ?
     * @return array integer ID de l'élément ajouté
     */
    public function update(int $id, string $title, string $summary, string $description, int $display, ?string $picture1, ?string $picture2, ?string $picture3, ?bool $top)
    {
        
        /** ....................... UPDATE PROJECT ........................ */
        /** 1. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE project */
        $sth = $this->dbh->prepare("UPDATE project 
            SET pro_title=:title, pro_summary=:summary, pro_description=:content, pro_picture1=:picture1, pro_picture2=:picture2, pro_picture3=:picture3, pro_top=:topProject, pro_display=:display
            WHERE pro_id=:id");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('id',$id, PDO::PARAM_INT);
        $sth->bindValue('title',$title);
        $sth->bindValue('summary',$summary);
        $sth->bindValue('content',$description);
        $sth->bindValue('picture1',$picture1);
        $sth->bindValue('picture2',$picture2);
        $sth->bindValue('picture3',$picture3);
        $sth->bindValue('topProject',$top, PDO::PARAM_BOOL);
        $sth->bindValue('display',$display, PDO::PARAM_INT);

        /** 3. Execution de notre requête */
        $sth->execute();
    }
    
    /** Suppression d'un projet
     * @param int $id du projet
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare('DELETE FROM project WHERE pro_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
    }
    
    /** Recherche d'un projet classé "top"
     * @return array les projets classés "top"
     */
    public function findByTop()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM project WHERE pro_top = 1 ORDER BY pro_display');
        /** 2. Execution de notre requête */
        $sth->execute();
        $topProjects = $sth->fetchAll();
        return $topProjects;
    }

    /** Recherche d'un projet
     * @param int $id de du projet
     * @return array les données complètes du projet en fonction de son id
     */
    public function findById(int $id)
    {
        /** 2. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM project WHERE pro_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    /** Renvoie un projet à partir de son ordre d'affichage
     * @param string $display ordre d'affichage sur le front recherché
     * @return array les données du projet
     */
    public function getByDisplay(int $display)
    {
        /** .........*..............DISPLAY IN BASE......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE project */
        $sth = $this->dbh->prepare('SELECT * FROM project WHERE pro_display = :checkDisplay');
        /** 3. Lier(Bind) la valeur de l'ID(jeton) en lui précisant que ça doit être un INTEGER */
        $sth->bindValue('checkDisplay',$display);
        /** 4. Executer notre requête */
        $sth->execute();
        /** 5. Je récupère le jeu d'enregistrement */
        $project = $sth->fetch();
        
        return $project;
    }

    public function countOfProjects()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as pro_count FROM project');
        $sth->execute();
        $count = $sth->fetch();
        return $count;
    }
}