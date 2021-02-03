<?php

session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/User.php');

verifyConnection('ROLE_ADMIN');

$view = 'profile';
$titlePage = 'Page de profil';
$flashbag = getFlashBag();

/** Instanciation de l'objet User */
$userModel = new User();
$user = $userModel->findByTop();

/** Si il existe bien un utilisateur on affiche la page, sinon on redirige vers une page d'erreur */
if(!empty($user))
{
    $flashbag = getFlashBag();

    $firstname       = $user['use_firstname'];
    $lastname        = $user['use_lastname'];
    $email           = $user['use_email'];
    $password        = $user['use_password'];
    $confirmPassword = $user['use_password'];
    $description     = $user['use_description'];
    $avatar          = '';
    $oldPicture      = $user['use_avatar'];
    $top             = true;
    $role            = $user['use_role'];

    $errors          = [];
    $deletePicture   = false;

    /** Si le formulaire est soumis alors on update la base de donée */
    if(isset($_POST['firstname']))
    {

        $id                 = (int)$user['use_id'];
        $firstname          = (isset($_POST['firstname'])) ? trim($_POST['firstname']) : '';
        $lastname           = (isset($_POST['lastname'])) ? trim($_POST['lastname'])  :'';
        $email              = trim($_POST['email']);
        $top                = true;
        $description        = (isset($_POST['description'])) ? trim($_POST['description'])  :'';
        $password           = (isset($_POST['password'])) ? $_POST['password'] : '';
        $confirmPassword    = (isset($_POST['confirmPassword'])) ? $_POST['confirmPassword'] : '';
        $oldPicture         = (isset($_POST['oldPicture']))?trim($_POST['oldPicture']):null; /** EDITION : si on édit l'ancienne image n°1 sera passée */
        $deletePicture      = (isset($_POST['deletePicture']))? true : false;

        // Vérification des erreurs du champ "prénom"
        if(empty($firstname) || strlen($_POST['firstname']) < 3)
            $errors['firstname'] = 'Ce champ est obligatoire et doit avoir au moins 3 caractères';

        // Vérification des erreurs du champ "nom de famille"
        if(empty($lastname) || strlen($_POST['lastname']) < 2)
            $errors['lastname'] = 'Ce champ est obligatoire et doit avoir au moins 2 caractères';
        
        // Vérification des erreurs du champ "email", ou si il est déjà dans la base
        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
            $errors['email'] = 'Le champ est vide ou il y a une erreur dans ton email';
        else {
            $mailInBase = $userModel->getByEmail($email);
            if($email !== $user['use_email'] && $mailInBase !== false)
                $errors['email'] = 'Cet email est déjà pris !';
        }

        /** Si il n'y a pas d'erreurs alors on update le profil et on ajoute un message Flashbag*/
        if(empty($errors))
        { 
                /* UPLOAD IMAGE / GESTION ERREUR */
            try {
                $avatar = uploadFile('avatar', 'users');
            }
            catch(DomainException $e) {
                $errors['avatar'] = $e->getMessage();
            }
            /** EDITION : si on edit et que pas de nouveau Upload on remet l'ancienne image */
            if(empty($avatar))
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

            $userModel->update($id, $firstname, $lastname, $email, $password, $description, $avatar, $top, $role);

            addFlashBag("Le profil a bien été modifié !", 'success');
            /** On redirige vers la liste des utilisateurs */
            header('Location:profile.php');
        }
    }
    require('views/layout.phtml');
}
/** Si il n'existe pas d'utilisateur "TOP" alors on redirige vers une page d'erreur */
else
{
    header('Location:noUserTop.php');
}