<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/TarifGrid.php');

verifyConnection();

$view = 'tarifGrid';
$titlePage = 'Grille Tarifaire';

$title = '';
$price = '';

$_SESSION['prix'] = '';
$_SESSION['titre'] = '';
$_SESSION['display'] = '';


try {
    /* On récupère tous les utilisateurs */
    $gridModel = new TarifGrid();
    $tarifs = $gridModel->orderByDisplay();

    /** On compte le nombre de tarifs existants en base */
    $countOfTarif = $gridModel->countOf();
    $count = (int)$countOfTarif['tar_count'];

    $flashbag = getFlashBag();
    
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

require ('views/layout.phtml');