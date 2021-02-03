<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../models/User.php');

/******* Varirables globales ******/
$displayConfirmation = false;
$errors = [];
$tokenError = false;

$password = '';
$confirmPassword = '';
$token = $_SESSION['token'];

if(isset($_POST['update']))
{
    $password        = (isset($_POST['password'])) ? $_POST['password'] : '';
    $confirmPassword = (isset($_POST['confirmPassword'])) ? $_POST['confirmPassword'] : '';

    // Le champ mail a t-il un format correct ?
    if(!empty($_POST['email']))
    {
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            $email = $_POST['email'];
        else
            $errors['email'] = 'L\'email renseigné n\'est pas valide';
    }
    else
        $errors['email'] = 'Ce champ est obligatoire';
    
    // Vérification des erreurs du champ "mot de passe"
    if(empty($password) || strlen($password) < 4)
        $errors['password'] = 'Votre mot de passe doit avoir au 5 caractères !';
        
    // Vérification des erreurs du champ "confirmer votre mot de passe"
    if(empty($confirmPassword) || $password != $confirmPassword)
        $errors['confirmPassword'] = 'Les 2 mots de passe ne sont pas les mêmes';
    
    // Vérification du token : on compare si le token de la session et de l'url sont les mêmes
    if($_GET['token'] !== $token)
    {
        $errors['token'] = true;
        $tokenError = true;
    }

    /**** Aucunes erreurs ? Alors on modifie le mot de passe de l'utilisateur dans la BDD ****/
    if(empty($errors) && $tokenError == false)
    {
        /* Récupération de l'utilisateur */
        $userModel = new User();
        //$user = $userModel->getByEmail($email);

        $userModel->updatePassword($email, $password);
        $displayConfirmation = true;
    }
    

}


require('views/updatePassword.phtml');