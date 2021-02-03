<?php

require('config/config.php');
require('lib/bdd.php');
require('lib/functions.php');

require('models/Service.php');
require('models/TarifGrid.php');

$view = 'services';
$title = 'Mes services';
$metaDescription = 'Grille tarifaire des services proposés par Nic.L Elec, électricien sur l\'île d\'Oléron.';

$serviceModel     = new Service;
$firstSixServices = $serviceModel->sixFirstServices(); // Récupération des six premiers services
$sixPlusServices  = $serviceModel->sixPlusServices(); // Récupération des services après les 6 premiers
$url              = 'uploads/services/';

$tarifModel   = new tarifgrid;
$tarifs       = $tarifModel->getAll();

require('views/layout.phtml');