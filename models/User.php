<?php

class User
{
    private $dbh;
    
    public function __construct()
    {
        $this->dbh = dbConnexion();
    }
    
    /** Renvoie un utilisateur à partir de son email
     * @param string $email email recherché
     * @return array les données complètes de l'utilisateur
     */
    public function getByEmail(string $email)
    {
        /** ........................MAIL IN BASE......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE orders */
        $sth = $this->dbh->prepare('SELECT * FROM user WHERE use_email = :checkMail');
        /** 3. Lier(Bind) la valeur de l'ID(jeton) en lui précisant que ça doit être un INTEGER */
        $sth->bindValue('checkMail',$email);
        /** 4. Executer notre requête */
        $sth->execute();
        /** 5. Je récupère le jeu d'enregistrement */
        $user = $sth->fetch();
        
        return $user;
    }
    
    /** Renvoie tous les utilisateurs
     * @return array Tous les utilisateurs
     */
    public function getAll()
    {
        /** 2. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM user');
        /** 3. Executer notre requête */
        $sth->execute();
        /** 4. Je récupère le jeu d'enregistrement */
        $users = $sth->fetchAll();
        
        return $users;
    }
    
    /** Insertion d'utilisateurs
     * @param string $firstname le prénom
     * @param string $lastname le nom de famille
     * @param string $email l'email de l'utilisateur
     * @param string $password le mot de passe de l'utilisateur
     * @param string $description laïus affiché dans la section A propos
     * @param string|null $avatar l'avatar de l'utilisateur
     * @param bool $top utilisateur affiché dans l'onglet "A propos" du front
     * @return array integer ID de l'élément ajouté
     */
    public function add(string $firstname, string $lastname, string $email, string $password, string $description, ?string $avatar, bool $top, string $role = 'ROLE_USER')
    {
                
        //cryptage du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        /** ......................... CREATE USER ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE orders */
        $sth = $this->dbh->prepare("INSERT INTO user 
            (use_firstname, use_lastname, use_email, use_password, use_description, use_avatar, use_top, use_role) 
            VALUES (:use_firstname, :use_lastname, :use_email, :use_password, :use_description, :use_avatar,:use_top, :use_role)");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('use_firstname',$firstname);
        $sth->bindValue('use_lastname',$lastname);
        $sth->bindValue('use_email',$email);
        $sth->bindValue('use_password',$passwordHash);
        $sth->bindValue('use_description',$description);
        $sth->bindValue('use_avatar',$avatar);
        $sth->bindValue('use_top',$top,PDO::PARAM_BOOL);
        $sth->bindValue('use_role',$role);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification d'utilisateurs
     * @param int $id id de l'utilisateur
     * @param string $firstname le prénom
     * @param string $lastname le nom de famille
     * @param string $email l'email de l'utilisateur
     * @param string $password le mot de passe de l'utilisateur
     * @param string $description laïus affiché dans la section A propos
     * @param string|null $avatar l'avatar de l'utilisateur
     * @param bool $top utilisateur affiché dans l'onglet "A propos" du front
     * @return array integer ID de l'élément ajouté
     */
    public function update(int $id, string $firstname, string $lastname, string $email, string $password, string $description, ?string $avatar, bool $top, string $role='ROLE_USER')
    {
                
        //cryptage du mot de passe
        //$passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        /** ......................... UPDATE USER ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE orders */
        $sth = $this->dbh->prepare("UPDATE user 
            SET use_firstname=:firstname, use_lastname=:lastname, use_email=:email, use_password=:use_password, use_description=:use_description, use_avatar=:avatar, use_top=:use_top, use_role=:use_role 
            WHERE use_id=:id");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('id',$id, PDO::PARAM_INT);
        $sth->bindValue('firstname',$firstname);
        $sth->bindValue('lastname',$lastname);
        $sth->bindValue('email',$email);
        $sth->bindValue('use_password',$password);
        $sth->bindValue('use_description',$description);
        $sth->bindValue('avatar',$avatar);
        $sth->bindValue('use_top',$top,PDO::PARAM_BOOL);
        $sth->bindValue('use_role',$role);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }

    /** Modification du mot de passe
     * @param string $email l'email de l'utilisateur
     * @param string $password le mot de passe de l'utilisateur
     * @return array integer ID de l'élément ajouté
     */
    public function updatePassword(string $email, string $password)
    {
                
        //cryptage du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        /** ......................... UPDATE USER ......................... */
        /** 2. PREPARATION DE LA REQUÊTE SQL POUR RECUPERER LA TABLE orders */
        $sth = $this->dbh->prepare("UPDATE user 
            SET use_password=:use_password
            WHERE use_email=:email");            
        /** 3. Lier(Bind) les valeurs aux jetons (token comme dans south park) */
        $sth->bindValue('email',$email);
        $sth->bindValue('use_password',$passwordHash);
        /** 4. Executer notre requête */
        $sth->execute();
        
        /** on retourne la clef primaire de l'élément ajouté en base ! */
        return $this->dbh->lastInsertId();
    }
    
    /** Suppression d'un utilisateur
     * @param int $id de l'utilisateur
     */
    public function delete(int $id)
    {
        $sth = $this->dbh->prepare('DELETE FROM user WHERE use_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
    }
    
    /** Recherche d'un utilisateur
     * @param int $id de l'utilisateur
     * @return array les données complètes de l'utilisateur en fonction de son id
     */
    public function findById(int $id)
    {
        /** 2. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM user WHERE use_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch();
    }

    /** Recherche d'un utilisateur classé "top"
     * @return array l'utilisateur' classés "top"
     */
    public function findByTop()
    {
        /** 1. PREPARATION DE LA REQUÊTE SQL */
        $sth = $this->dbh->prepare('SELECT * FROM user WHERE use_top = 1');
        /** 2. Execution de notre requête */
        $sth->execute();
        $topUser = $sth->fetch();
        return $topUser;
    }
}