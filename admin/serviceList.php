<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Service.php');

verifyConnection();

$view = 'serviceList';
$titlePage = 'Liste des services';

try {
    /* On récupère tous les services */
    $serviceModel = new Service();
    $services = $serviceModel->getAll();
    
    $flashbag = getFlashBag();
    
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
} 

require ('views/layout.phtml');