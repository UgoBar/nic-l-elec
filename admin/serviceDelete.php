<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Service.php');

verifyConnection('ROLE_ADMIN');

$serviceModel = new Service();
$services = $serviceModel->getAll();

foreach($services as $service)
{
    /** On reçoit la confirmation */
    if(isset($_POST[''.$service['ser_id'].'']))
    {

        $id    = (int)$_POST[''.$service['ser_id'].''];
        $title  = $_POST['title-'.$service['ser_id'].''];
        $picture = $_POST['picture-'.$service['ser_id'].''];

        if($picture !== null)
            deleteFile($picture,'services');
        // On supprime l'élément dans la base
        $serviceModel->delete($id);
            
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("Le service <b>$title</b> a bien été supprimé !", 'success');
        header('Location:serviceList.php');
    }
}