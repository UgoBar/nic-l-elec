<?php

declare(strict_types=1);
session_start();

/** REQUIRES */
require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Banner.php');

verifyConnection('ROLE_ADMIN');

/** VUE & TITRE DE LA PAGE */
$view = 'bannerAdd';
$titlePage = 'Ajout d\'une photo';

/********************** Variables globales du programme ***********************/
$title       = '';
$picture     = null;
$display     = null;

/** EDIT MODE */
$id          = null;
$oldPicture = null;
$deletePicture = false;


// Variables pour gérer les erreurs des champs input
$errors = [];

/** Instanciation de l'objet Banner */
$bannerModel = new Banner();
$banners = $bannerModel->getAll();
$count = $bannerModel->countOf();

/** EDITION : Si l'on est en mode édition la page reçoit un ID en chaine de requête */
if(array_key_exists('id',$_GET))
{
    $banner = $bannerModel->findById((int)$_GET['id']);

    // Changement du titre de la page
    $titlePage = 'Edition de la photo : '.$banner['ban_title'];

    /** On rempli le formulaire avec les données existantes, prêtes à être modifiées */
    $id           = (int)$banner['ban_id'];
    $title        = $banner['ban_title'];
    $oldPicture   = $banner['ban_picture'];
    $display      = $banner['ban_display'];
}


/** VERIFICATION DE L'ENVOIE DU FORMULAIRE */
if(isset($_POST['title']))
{

    $title          = (isset($_POST['title'])) ? trim($_POST['title']) : '';
    $oldPicture     = (isset($_POST['oldPicture']))?trim($_POST['oldPicture']):null; /** EDITION : si on édit l'ancienne image sera passée */
    $display        = (int)$_POST['display'];
    $deletePicture  = (isset($_POST['deletePicture']))? true : false;

    // Vérification des erreurs du champ "title"
    if(empty($title) || strlen($_POST['title']) < 3)
        $errors['title'] = 'Ce champ est obligatoire et doit avoir au moins 3 caractères';

    //var_dump($display); var_dump($errors); exit;

    /************ Aucunes erreurs ? Alors on ajoute l'utilisateur dans la base ************/
    if(empty($errors))
    {
        /** Upload du fichier et gestion d'erreur */
        try
        {
            $picture = uploadFile('picture', 'banner');
        }
        catch(DomainException $e)
        {
            $errors['picture'] = $e->getMessage();
        }
        /** EDITION : si on edit et que pas de nouveau Upload on remet l'ancienne image 
         * Sinon, si l'utilisateur upload une nouvelle image on supprime l'ancienne
        */
        if(empty($picture))
            $picture = $oldPicture;
        if($oldPicture !== null && $picture !== $oldPicture)
        {
            deleteFile($oldPicture,'banner');
        }

        /** Si il n'y pas d'ID qui est passé alors on ajoute la photo dans la base de donnée */
        if($id === null)
        {
        /* Ajout d'une photo */
        $bannerModel->add($picture, $title, $display);
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("La photo <b>$title</b> a bien été ajouté !", 'success');
        }

        /** Sinon, si il y a un ID alors on update, on modifie */
        else
        {
            /** EDITION : Si la cache "supprimer la photo" est cochée alors on la supprime de la base */
            if($deletePicture == true)
            {
                deleteFile($oldPicture,'banner');
                $picture = 'empty';
            }
                
            /** EDIT MODE : modification de la photo */
            $bannerModel->update($id, $picture, $title, $display);
            /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
            addFlashBag("La photo <b>$title</b> a bien été modifé !", 'success');
        }
       
        /** On redirige vers la liste des utilisateurs */
        header('Location:bannerList.php');
    }
}

require('views/layout.phtml');