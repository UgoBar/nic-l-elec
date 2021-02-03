<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/User.php');

verifyConnection('ROLE_ADMIN');

$userModel = new User();
$users = $userModel->getAll();

foreach($users as $user)
{
    /** On reçoit la confirmation */
    if(isset($_POST[''.$user['use_id'].'']))
    {

        $id    = (int)$_POST[''.$user['use_id'].''];
        $name = $_POST['name-'.$user['use_id'].''];
        $picture = $_POST['picture-'.$user['use_id'].''];

        if($picture !== null)
            deleteFile($picture,'users');

        // On supprime l'élément dans la base
        $userModel->delete($id);
        
        /** Si l'utilisateur vient de supprimer son compte alors il est déconnecté */
        if($user['use_email'] == $_SESSION['user']['use_email'])
            deconnexion();
        /** Sinon il est redirigé vers la page des utilisateurs */
        else
        {
            /** On ajoute à la session flashbag un message qui sera afficher sur la liste */
            addFlashBag("L'utilisateur <b>$name</b> a bien été supprimé !", 'success');
            header('Location:userList.php');
        }
        
    }
}