<?php

require('config/config.php');
require('lib/bdd.php');
require('lib/functions.php');

require('models/Banner.php');
require('models/Home.php');
require('models/Project.php');
require('models/Service.php');

$view = 'index';
$title = 'Electricien - Oléron';
$metaDescription = 'Electricien originaire de l\'île d\'Oléron, Nic.L Elec propose ses services pour l\'installation, la réparation et l\'optimisation de votre matériel électrique.';

/****** Instanciation des classes *****/
// Bannière
$bannerModel     = new Banner;

$pictureDisplay1 = $bannerModel->getByDisplay(1);
$picture1        = $pictureDisplay1['ban_picture'];

$pictureDisplay2 = $bannerModel->getByDisplay(2);
$picture2        = $pictureDisplay2['ban_picture'];

$pictureDisplay3 = $bannerModel->getByDisplay(3);
$picture3        = $pictureDisplay3['ban_picture'];

// Introduction
$homeModel      = new Home;
$introductions  = $homeModel->getAll();
//var_dump($introductions);exit; // si vide = RETURN 'empty'

// Projets classés top
$projectModel = new Project;
$topProjects  = $projectModel->findByTop();
//var_dump($count);exit;

// Services classés top
$serviceModel = new Service;
$topServices  = $serviceModel->findByTop();
$countTopProjects = sizeof($topProjects);


require('views/layout.phtml');