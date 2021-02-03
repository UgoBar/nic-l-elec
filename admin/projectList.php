<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Project.php');

verifyConnection();

$view      = 'projectList';
$titlePage = 'Liste des projets';

try {
    /* On récupère tous les services */
    $projectModel = new Project();
    $projects = $projectModel->getAll();
    
    $flashbag = getFlashBag();
    
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
} 

require ('views/layout.phtml');