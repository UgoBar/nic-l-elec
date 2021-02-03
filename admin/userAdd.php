<?php

declare(strict_types=1);
session_start();

/** REQUIRES */
require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/User.php');

verifyConnection('ROLE_ADMIN');

/** VUE & TITRE DE LA PAGE */
$view = 'userAdd';
$titlePage = 'Ajout d\'un utilisateur';

/********************** Variables globales du programme ***********************/

$firstname = '';
$lastname = '';
$email = '';
$password = '';
$confirmPassword = '';
$description = '';
$avatar = null;
$top = false;
$role = 'ROLE_USER';

/** EDIT */
$id = null;
$oldPicture = null;
$deletePicture = false;

// Variables pour gérer les erreurs des champs input
$errors = [];
$userModel = new User();

if(array_key_exists('id',$_GET))
{
    $user = $userModel->findById((int)$_GET['id']);

    // Changement du titre de la page
    $titlePage = 'Edition de l\'utilisateur : '.$user['use_firstname'];

    /** On rempli le formulaire avec les données existantes, prêtes à être modifiées */
    $id              = (int)$user['use_id'];
    $firstname       = $user['use_firstname'];
    $lastname        = $user['use_lastname'];
    $email           = $user['use_email'];
    $description     = $user['use_description'];
    $top             = (int)$user['use_top'] == 1 ? true : false;
    $role            = $user['use_role'];
    $avatar          = '';
    $oldPicture      = $user['use_avatar'];
    $password        = $user['use_password'];
    $confirmPassword = $user['use_password'];
}

/** VERIFICATION DE L'ENVOIE DU FORMULAIRE */
if(isset($_POST['email']))
{

    $firstname          = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
    $lastname           = (isset($_POST['lastname'])) ? trim($_POST['lastname'])  :'';
    $email              = trim($_POST['email']);
    $top                = (isset($_POST['topUser']))? true : false;
    $password           = (isset($_POST['password'])) ? $_POST['password'] : '';
    $description        = (isset($_POST['description'])) ? trim($_POST['description'])  :'';
    $confirmPassword    = (isset($_POST['confirmPassword'])) ? $_POST['confirmPassword'] : '';
    $role               = $_POST['role'];
    $deletePicture      = (isset($_POST['deletePicture']))? true : false;
    $oldPicture         = (isset($_POST['oldPicture']))?trim($_POST['oldPicture']):null; /** EDITION : si on édit l'ancienne image n°1 sera passée */

    // Vérification des erreurs du champ "prénom"
    if(empty($firstname) || strlen($_POST['firstname']) < 3)
        $errors['firstname'] = 'Ce champ est obligatoire et doit avoir au moins 3 caractères';

    // Vérification des erreurs du champ "nom de famille"
    if(empty($lastname) || strlen($_POST['lastname']) < 2)
        $errors['lastname'] = 'Ce champ est obligatoire et doit avoir au moins 2 caractères';
    
    // Vérification des erreurs du champ "email", ou si il est déjà dans la base
    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
        $errors['email'] = 'Il doit y avoir une erreur dans ton email';
    else {
        $mailInBase = $userModel->getByEmail($email);
        /** EDITION */
        if($id !== null)
        {
            if($email !== $user['use_email'] && $mailInBase !== false)
                $errors['email'] = 'Cet email est déjà pris !';
        }
        else
        {
            if($mailInBase !== false)
                $errors['email'] = 'Cet email est déjà pris !';    
        }
    }

    // Vérification des erreurs du champ "mot de passe"
    if(empty($password) || strlen($password) < 4)
        $errors['password'] = 'Votre mot de passe doit avoir au 5 caractères !';
        
    // Vérification des erreurs du champ "confirmer votre mot de passe"
    if(empty($confirmPassword) || $password != $confirmPassword)
        $errors['confirmPassword'] = 'Les 2 mots de passe ne sont pas les mêmes';

    // Vérification du champ "Top"
    $topUser = $userModel->findByTop();
    // EDITION
    if($id !== null)
    {
        /** Si l'ID existe alors on regarde si l'utilisateur est déjà classé top, auquel cas il pourra continuer à l'être
         * sinon on génère une erreur
         */
        if($topUser !== false && $id === $topUser['use_id'])
        {
            $errors['top'] = 'Il ne peut y avoir qu\'un seul utilisateur classé top';
        }
    }
    // Sinon si aucun ID n'est passé et qu'un autre utilisateur est classé top alors on génère une erreur
    else
    {
        if($top === true && $topUser !== false)
            $errors['top'] = 'Il ne peut y avoir qu\'un seul utilisateur classé top';
    }
    
    // Lorsqu'un utilisateur est classé "top" le champ "description" est obligatoire
    if($top == true)
    {
        if(empty($description))
            $errors['description'] = 'Champ obligatoire.';
    }

    /* Upload du fichier et gestion d'erreur */
    try {
        $avatar = uploadFile('avatar', 'users');
    }
    catch(DomainException $e)
    {
        $errors['avatar'] = $e->getMessage();
    }
    // Si aucun avatar n'est passé alors une image par défaut est attribuée
    if($id == null && $avatar == null)
        $avatar = 'default-avatar-white.png';
    
    /************ Aucunes erreurs ? Alors on ajoute l'utilisateur dans la base ************/
    if(empty($errors))
    {
        /** EDITION : si on edit et que pas de nouveau Upload on remet l'ancienne image */
        if($id !== null && empty($avatar))
            $avatar = $oldPicture;
        if($oldPicture !== null && $avatar !== $oldPicture)
        {
            deleteFile($oldPicture,'users');
        }

        if($deletePicture == true)
        {
            deleteFile($oldPicture,'users');
            $avatar = null;
        }
        
        /** Si il n'y pas d'ID qui est passé alors on ajoute le projet dans la base de donnée */
        if($id === null)
        {
            /* Ajout du projet */
            $userModel->add($firstname, $lastname, $email, $password, $description, $avatar, $top, $role);
            /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
            addFlashBag("L'utilisateur <b>$firstname $lastname</b> a bien été ajouté !", 'success');
        }

        else
        {
            if($password !== $user['use_password'])
            {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                /* On hash le mot de passe et on modifie l'utilisateur */
                $userModel->update($id, $firstname, $lastname, $email, $passwordHash, $description, $avatar, $top, $role);
            }
            else
            {
                /* On modifie l'utilisateur */
                $userModel->update($id, $firstname, $lastname, $email, $password, $description, $avatar, $top, $role);
            }
            /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
            addFlashBag("L'utilisateur <b>$firstname $lastname</b> a bien été modifié !", 'success');
        }
        
        /** On redirige vers la liste des utilisateurs */
        header('Location:userList.php');
    }
}

require('views/layout.phtml');