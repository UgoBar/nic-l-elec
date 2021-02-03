<?php

class Contact
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Renvoie tous les mails reçus via le formulaire de contact
     * @return array Tous les mails reçus
     */
    public function getAll()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM contact');
        /** 2. Executer notre requête */
        $sth->execute();
        /** 3. Je récupère le jeu d'enregistrement */
        $contacts = $sth->fetchAll();
        
        return $contacts;
    }

    /** Insertion d'un mail de contact
     * @param string $name le nom de l'expediteur
     * @param string $email le mail de l'expediteur
     * @param string $content le contenu du mail envoyé
     * @param Datetime $createdAt la date d'envoie du mail
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $name, string $email, string $content, Datetime $createdAt)
    {

        /** ......................... CREATE CONTACT ...................... */
        /** 1. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE banner */
        $sth = $this->dbh->prepare("INSERT INTO contact (con_name, con_email, con_content, con_createdAt) 
                                    VALUES (:con_name, :email, :content, :createdAt)");

        /** 2. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('con_name',$name);
        $sth->bindValue('email',$email);
        $sth->bindValue('content',$content);
        $sth->bindValue('createdAt',$createdAt->format('Y-m-d H:i:s'));
        /** 3. Execution de notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    public function countOfContacts()
    {
        $sth = $this->dbh->prepare('SELECT COUNT(*) as con_count FROM contact');
        $sth->execute();
        $count = $sth->fetch();
        return $count;
    }
}