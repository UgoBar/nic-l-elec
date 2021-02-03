<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Home.php');

verifyConnection('ROLE_ADMIN');

$view = 'intro';
$titlePage = 'Texte d\'introduction de la page d\'accueil';

/** Instanciation de l'objet Home */
$homeModel = new Home();
$count     = $homeModel->countOf();

/************* VARIABLES GLOBALES ***********/
$title       = '';
$description = '';
$deleteIntro = false;
$errors      = [];
/* On récupère le flashbag si il y en a un */
$flashbag    = getFlashBag();

/** EDIT MODE : Si il y a déjà une entrée dans la base de donnée alors on la récupère
 *  et on injecte les données dans le formulaire*/
if($count['hom_count'] !== '0')
{
    $home = $homeModel->getAll();

    foreach($home as $intro)
    {
        $id          = (int)$intro['hom_id'];
        $title       = $intro['hom_title'];
        $description = $intro['hom_description'];
    }
}

/** Si le formulaire est soumis alors on met à jour la base de donnée */
if(isset($_POST['title']))
{
    $title          = (isset($_POST['title'])) ? trim($_POST['title']) : '';
    $description    = (isset($_POST['description'])) ? trim($_POST['description'])  :'';
    $deleteIntro    = (isset($_POST['deleteIntro']))? true : false;
    
    /** Si il n'y a aucune introduction qui existe alors on rempli le formulaire */
    if($count['hom_count'] === '0')
    {
        $homeModel->add($title, $description);
        addFlashBag("L'introduction $title a bien été ajoutée' !", 'success');
    }
    else
    {
        if($deleteIntro === true)
        {
            $homeModel->delete($id);
            addFlashBag("L'introduction $title a bien été supprimée !", 'success');
        }
        else
        {
            $homeModel->update($id, $title, $description);
            addFlashBag("L'introduction a bien été modifiée !", 'success');
        }
    }
    header('Location:intro.php');
}

require ('views/layout.phtml');