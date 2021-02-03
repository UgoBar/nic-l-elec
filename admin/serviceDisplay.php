<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Service.php');

verifyConnection('ROLE_ADMIN');

$view = 'serviceDisplay';
$titlePage = 'Changer l\'ordre d\'affichage des services';

$errors = [];
$display = '';
$flashbag = getFlashBag();

/* On récupère tous les utilisateurs */
$serviceModel = new Service();
$services = $serviceModel->orderByDisplay();
$countTab = $serviceModel->countOfServices();
$count = (int)$countTab['ser_count'];

$displayArray = [];

if(isset($_POST['formsubmit']))
{
    foreach($services as $service)
    {
        $id           = (int)$service['ser_id'];
        $title        = $service['ser_title'];
        $description  = $service['ser_description'];
        $picture      = $service['ser_picture'];
        $price        = $service['ser_price'];
        $top          = $service['ser_top']==1?true:false;
        $display      = (int)$_POST['displayNumber-'.$service['ser_id'].''];

        $displayArray[$service['ser_id']] = $display;

        if(empty($erros))
        {
            $serviceModel->update($id, $title, $description, (int)$display, $price, $picture, $top);
        }
    }

    addFlashBag("L'ordre des services a bien été modifié !", 'success');
    /** On redirige vers la liste des utilisateurs */
    header('Location:serviceDisplay.php');
}




require ('views/layout.phtml');