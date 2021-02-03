<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../models/User.php');

/********************** Variables globales du programme ***********************/
// Variables contenant les informations des champs input
$email = '';
$mdp = '';

$displayConfirmation = false;  
$mailTo = '';

// Variables pour gérer les erreurs en fonction de l'input
$error = false;
$mailError = false;

//var_dump($_SESSION);exit;

//password_verify($password,$user->password)

/*************** Vérification des champs input du formulaire ******************/

if(isset($_POST['connexion']))
{
    // Le champ mail a t-il un format correct ?
    if(!empty($_POST['email']))
    {
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            $email = $_POST['email'];
        else
            $error = true;
    }
    else
        $error = true;
    
    // Le champ mot de passe est supérieur à 5 caractère ?
    if(strlen($_POST['mdp']) >= 5)
        $mdp = $_POST['mdp'];
    else
        $error = true;
    
    /* Récupération de l'utilisateur */
    $userModel = new User();
    $user = $userModel->getByEmail($email);
    /** Je vérifie si l'email est déjà présent dans la base de donnée */
    if ($user !== false && password_verify($mdp, $user['use_password']) === true ){
            $_SESSION['connected'] = true;
            $_SESSION['user'] = $user;
            header('Location:index.php');
    } else {
        $error = true;
    }
}

if(isset($_POST['updatePassword']))
{
    // Le champ mail a t-il un format correct ?
    if(!empty($_POST['mailTo']))
    {
        if(filter_var($_POST['mailTo'], FILTER_VALIDATE_EMAIL))
            $mailTo = $_POST['mailTo'];
        else
            $mailError = true;
    }
    else
        $mailError = true;
    
    /* Récupération de l'utilisateur */
    $userModel = new User();
    $user = $userModel->getByEmail($mailTo);
    //$mail = new PHPMailer();
    /** Je vérifie si l'email est déjà présent dans la base de donnée */
    if ($user !== false){
        
        // Création d'un token aléatoire
        $token = bin2hex(random_bytes(20));

        // Envoie de l'email avec un token dans le lien cliquable
        $subject = "Reinitialisation du mot de passe";
        $msg = '<html><body>';
        $msg .= "Pour réinitialiser ton mot de passe cliques sur ce lien : https://www.nic-l-elec.fr/admin/updatePassword.php?token=$token";
        $msg .= '</body></html>';
        $msg = wordwrap($msg,70);
        
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: Nic.L Elec <admin@nic-l-elec.fr>'."\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();
            
        // Si il n'y a pas d'erreurs alors on envoi le mail et on affiche la confirmation
        if($mailError !== true) {
            mail($mailTo, $subject, $msg, $headers);
            $displayConfirmation = true;
        }

        // On stock le token dans la session
        $_SESSION['token'] = $token;
        $_SESSION['userMail'] = $mailTo;

    } else {
        $mailError = true;
    }
}

require('views/login.phtml');
        

     