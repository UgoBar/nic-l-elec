<?php

session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');

require('../models/Contact.php');
require('../models/Project.php');
require('../models/Service.php');

verifyConnection();

$view = 'index';
$titlePage = 'Dashboard';

//var_dump($_SESSION);exit;
$flashbag = getFlashBag();

// Compte du nombre de mails reçus
$contactModel = new Contact();
$contactCount = $contactModel->countOfContacts();
$contactCount = $contactCount['con_count'];

// Compte du nombre de projets créés
$projectModel = new Project();
$projectCount = $projectModel->countOfProjects();
$projectCount = $contactCount['pro_count'];

// Compte du nombre de services créés
$serviceModel = new Service();
$serviceCount = $serviceModel->countOfServices();
$serviceCount = $contactCount['ser_count'];

//var_dump($contactCount);exit;

require('views/layout.phtml');
        

     