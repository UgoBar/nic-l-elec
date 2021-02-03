<?php

require('config/config.php');
require('lib/bdd.php');
require('lib/functions.php');

require('models/User.php');

$view = 'about';
$title = 'A propos de Nicolas Langiller';
$metaDescription = 'Derrière Nic.L Elec, Nicolas Langiller. Un peu d\'histoire sur un artisan Oléronnais passioné.';

$userModel = new User;
$topUser   = $userModel->findByTop();
$avatar    = 'uploads/users/'.$topUser['use_avatar'];

require('views/layout.phtml');