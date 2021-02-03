<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Banner.php');

verifyConnection();

$view = 'bannerList';
$titlePage = 'Liste des photos de la bannière';
$display = '';

/* On récupère toutes les photos */
$bannerModel = new Banner();
$banners     = $bannerModel->getAll();

$picture1    = $bannerModel->getByDisplay(1);
$picture2    = $bannerModel->getByDisplay(2);
$picture3    = $bannerModel->getByDisplay(3);
//var_dump($picture3['ban_picture']);exit;

/* On récupère le flashbag si il y en a un */
$flashbag    = getFlashBag();

foreach($banners as $banner)
{
    /** On reçoit la confirmation */
    if(isset($_POST[''.$banner['ban_id'].'']))
    {

        $id    = (int)$_POST[''.$banner['ban_id'].''];
        $title  = $_POST['title-'.$banner['ban_id'].''];
        $picture = $_POST['picture-'.$banner['ban_id'].''];

        if($picture !== null)
            deleteFile($picture,'banner');
        // On supprime l'élément dans la base
        $bannerModel->delete($id);
            
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("La photo <b>$title</b> a bien été supprimée !", 'success');
        header('Location:bannerList.php');
    }
}

if(isset($_POST['order']))
{
    foreach($banners as $banner)
    {
        $id           = (int)$banner['ban_id'];
        $title        = $banner['ban_title'];
        $picture      = $banner['ban_picture'];
        $display      = (int)$_POST['displayNumber-'.$banner['ban_id'].''];

        //$displayArray[$picture['ban_id']] = $display;
        //var_dump($display);exit;

        if(empty($erros))
        {
            $bannerModel->update($id, $picture, $title, $display);
        }
    }

    addFlashBag("L'ordre des photos a bien été modifié !", 'success');
    /** On redirige vers la liste des utilisateurs */
    header('Location:bannerList.php');
}

require ('views/layout.phtml');