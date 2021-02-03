<?php

declare(strict_types=1);
session_start();

/** REQUIRES */
require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Project.php');

verifyConnection('ROLE_ADMIN');

/** VUE & TITRE DE LA PAGE */
$view = 'projectAdd';
$titlePage = 'Ajout d\'un projet';

/********************** Variables globales du programme ***********************/
$title       = '';
$summary     = '';
$description = '';
$picture1    = null;
$picture2    = null;
$picture3    = null;
$top         = false;
$display     = '';

/** EDIT MODE */
$id          = null;
$oldPicture1 = null;
$oldPicture2 = null;
$oldPicture3 = null;
$deletePicture1 = false;
$deletePicture2 = false;
$deletePicture3 = false;


// Variables pour gérer les erreurs des champs input
$errors = [];

/** Instanciation de l'objet Projet */
$projectModel = new Project();
$count = $projectModel->countOfProjects();
/** EDITION : Si l'on est en mode édition la page reçoit un ID en chaine de requête */
if(array_key_exists('id',$_GET))
{
    $project = $projectModel->findById((int)$_GET['id']);

    // Changement du titre de la page
    $titlePage = 'Edition du projet : '.$project['pro_title'];

    /** On rempli le formulaire avec les données existantes, prêtes à être modifiées */
    $id           = (int)$project['pro_id'];
    $title        = $project['pro_title'];
    $summary      = $project['pro_summary'];
    $description  = $project['pro_description'];
    $oldPicture1  = $project['pro_picture1'];
    $oldPicture2  = $project['pro_picture2'];
    $oldPicture3  = $project['pro_picture3'];
    $top          = $project['pro_top'];
    $display      = $project['pro_display'];
}


/** VERIFICATION DE L'ENVOIE DU FORMULAIRE */
if(isset($_POST['title']))
{
    $projectModel = new Project();

    $title          = (isset($_POST['title'])) ? trim($_POST['title']) : '';
    $summary        = (isset($_POST['summary'])) ? trim($_POST['summary']) : '';
    $description    = (isset($_POST['description'])) ? trim($_POST['description'])  :'';
    $oldPicture1    = (isset($_POST['oldPicture1']))?trim($_POST['oldPicture1']):null; /** EDITION : si on édit l'ancienne image n°1 sera passée */
    $oldPicture2    = (isset($_POST['oldPicture2']))?trim($_POST['oldPicture2']):null; /** EDITION : si on édit l'ancienne image n°2 sera passée */
    $oldPicture3    = (isset($_POST['oldPicture3']))?trim($_POST['oldPicture3']):null; /** EDITION : si on édit l'ancienne image n°3 sera passée */
    $top            = (isset($_POST['top']))? true : false;
    $deletePicture1 = (isset($_POST['deletePicture1']))? true : false;
    $deletePicture2 = (isset($_POST['deletePicture2']))? true : false;
    $deletePicture3 = (isset($_POST['deletePicture3']))? true : false;
    $display        = (isset($_POST['display']))? trim($_POST['display']) : 0;

    // Vérification des erreurs du champ "title"
    if(empty($title) || strlen($_POST['title']) < 3)
        $errors['title'] = 'Ce champ est obligatoire et doit avoir au moins 3 caractères';

    // Vérification des erreurs du champ "summary"
    if(empty($summary))
        $errors['summary'] = 'Ce champ est obligatoire';

    // Vérification des erreurs du champ "description"
    if(empty($description))
        $errors['description'] = 'Ce champ est obligatoire';

    // Vérification des erreurs du champ "display", ou si il le nombre choisi existe déjà dans la base
    $displayInBase = $projectModel->getByDisplay((int)$display);
    if(empty($display))
        $errors['display'] = 'Ce champ est obligatoire';
    if(array_key_exists('id',$_GET))
    {
        if($display !== $project['pro_display'] && $displayInBase !== false)
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
            $picture1 = uploadFile('picture1', 'projects');
            $picture2 = uploadFile('picture2', 'projects');
            $picture3 = uploadFile('picture3', 'projects');
        }
        catch(DomainException $e)
        {
            $errors['picture1'] = $e->getMessage();
            $errors['picture2'] = $e->getMessage();
            $errors['picture3'] = $e->getMessage();
        }
        /** EDITION : si on edit et que pas de nouveau Upload on remet l'ancienne image */
        if(empty($picture1))
            $picture1 = $oldPicture1;
        if(empty($picture2))
            $picture2 = $oldPicture2;
        if(empty($picture3))
            $picture3 = $oldPicture3;
        /** Si il y a une nouvelle image et qu'elle est différente de l'ancienne alors on supprime l'ancienne */
        if($oldPicture1 !== null && $picture1 !== $oldPicture1)
            deleteFile($oldPicture1,'projects');
        if($oldPicture2 !== null && $picture2 !== $oldPicture2)
            deleteFile($oldPicture2,'projects');
        if($oldPicture3 !== null && $picture3 !== $oldPicture3)
            deleteFile($oldPicture3,'projects');

        /** Si il n'y pas d'ID qui est passé alors on ajoute le projet dans la base de donnée */
        if($id === null)
        {
        /* Ajout du projet */
        $projectModel->add($title, $summary, $description, (int)$display, $picture1, $picture2, $picture3, $top);
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("Le projet <b>$title</b> a bien été ajouté !", 'success');
        }

        /** Sinon, si il y a un ID alors on update, on modifie */
        else
        {
            /** EDITION : Si la cache "supprimer la photo" est cochée alors on la supprime de la base */
            if($deletePicture1 == true)
            {
                deleteFile($oldPicture1,'projects');
                $picture1 = null;
            }
            if($deletePicture2 == true)
            {
                deleteFile($oldPicture2,'projects');
                $picture2 = null;
            }
            if($deletePicture3 == true)
            {
                deleteFile($oldPicture3,'projects');
                $picture3 = null;
            }
                
            /** EDIT MODE : modification du projet */
            $projectModel->update($id, $title,$summary, $description, (int)$display, $picture1, $picture2, $picture3, $top);
            /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
            addFlashBag("Le projet <b>$title</b> a bien été modifé !", 'success');
        }
       
        /** On redirige vers la liste des utilisateurs */
        header('Location:projectList.php');
    }
}

require('views/layout.phtml');