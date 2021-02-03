<?php

declare(strict_types=1);
session_start();

/** REQUIRES */
require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Service.php');

verifyConnection('ROLE_ADMIN');

/** VUE & TITRE DE LA PAGE */
$view = 'serviceAdd';
$titlePage = 'Ajout d\'un service';

/********************** Variables globales du programme ***********************/
$title       = '';
$description = '';
$picture     = null;
$price       = null;
$top         = false;
$display     = '';

/** EDIT MODE */
$id          = null;
$oldPicture = null;
$deletePicture = false;


// Variables pour gérer les erreurs des champs input
$errors = [];

/** Instanciation de l'objet Service */
$serviceModel = new Service();
$count = $serviceModel->countOfServices();
/** EDITION : Si l'on est en mode édition la page reçoit un ID en chaine de requête */
if(array_key_exists('id',$_GET))
{
    $service = $serviceModel->findById((int)$_GET['id']);

    // Changement du titre de la page
    $titlePage = 'Edition du service : '.$service['ser_title'];

    /** On rempli le formulaire avec les données existantes, prêtes à être modifiées */
    $id           = (int)$service['ser_id'];
    $title        = $service['ser_title'];
    $description  = $service['ser_description'];
    $oldPicture   = $service['ser_picture'];
    $price        = $service['ser_price'];
    $top          = $service['ser_top'];
    $display      = $service['ser_display'];
}


/** VERIFICATION DE L'ENVOIE DU FORMULAIRE */
if(isset($_POST['title']))
{

    $title          = (isset($_POST['title'])) ? trim($_POST['title']) : '';
    $price          = (isset($_POST['price'])) ? trim($_POST['price']) : '';
    $description    = (isset($_POST['description'])) ? trim($_POST['description'])  :'';
    $oldPicture     = (isset($_POST['oldPicture']))?trim($_POST['oldPicture']):null; /** EDITION : si on édit l'ancienne image n°1 sera passée */
    $top            = (isset($_POST['top']))? true : false;
    $deletePicture  = (isset($_POST['deletePicture']))? true : false;
    $display        = (isset($_POST['display']))? trim($_POST['display']) : 0;

    // Vérification des erreurs du champ "title"
    if(empty($title) || strlen($_POST['title']) < 3)
        $errors['title'] = 'Ce champ est obligatoire et doit avoir au moins 3 caractères';

    // Vérification des erreurs du champ "description"
    if(empty($description))
        $errors['description'] = 'Ce champ est obligatoire';


    // Vérification des erreurs du champ "display", ou si il le nombre choisi existe déjà dans la base
    $displayInBase = $serviceModel->getByDisplay((int)$display);
    if(empty($display))
        $errors['display'] = 'Ce champ est obligatoire';
    if(array_key_exists('id',$_GET))
    {
        if($display !== $service['ser_display'] && $displayInBase !== false)
            $errors['display'] = 'Cet ordre est déjà pris !';
    }
    else
    { 
        if($displayInBase !== false)
            $errors['display'] = 'Cet ordre est déjà pris !';    
    }

    /************ Aucunes erreurs ? Alors on ajoute l'utilisateur dans la base ************/
    if(empty($errors))
    {
        /* Upload du fichier et gestion d'erreur */
        try {
            $picture = uploadFile('picture', 'services');
        }
        catch(DomainException $e)
        {
            $errors['picture'] = $e->getMessage();
        }
        /** EDITION : si on edit et que pas de nouveau Upload on remet l'ancienne image */
        if(empty($picture))
            $picture = $oldPicture;
        /** Si la nouvelle image est différente de l'ancienne, on supprime l'ancienne */
        if($oldPicture !== null && $picture !== $oldPicture)
        {
            deleteFile($oldPicture,'services');
        }    
        
        /** Si il n'y pas d'ID qui est passé alors on ajoute le service dans la base de donnée */
        if($id === null)
        {
        /* Ajout du service */
        $serviceModel->add($title, $description, (int)$display, $price, $picture, $top);
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("Le service <b>$title</b> a bien été ajouté !", 'success');
        }

        /** Sinon, si il y a un ID alors on update, on modifie */
        else
        {
            /** EDITION : Si la cache "supprimer la photo" est cochée alors on la supprime de la base */
            if($deletePicture == true)
            {
                deleteFile($oldPicture,'services');
                $picture = null;
            }
                
            /** EDIT MODE : modification du service */
            $serviceModel->update($id, $title, $description, (int)$display, $price, $picture, $top);
            /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
            addFlashBag("Le service <b>$title</b> a bien été modifé !", 'success');
        }
       
        /** On redirige vers la liste des services */
        header('Location:serviceList.php');
    }
}

require('views/layout.phtml');