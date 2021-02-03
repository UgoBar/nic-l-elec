<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/User.php');

verifyConnection();

$view = 'userList';
$titlePage = 'Liste des utilisateurs';

try {
    /* On récupère tous les utilisateurs */
    $userModel = new User();
    $users = $userModel->getAll();
    
    $flashbag = getFlashBag();
    
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
} 

require ('views/layout.phtml');