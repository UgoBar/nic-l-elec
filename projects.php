<?php

require('config/config.php');
require('lib/bdd.php');
require('lib/functions.php');

require('models/Project.php');

$view = 'projects';
$title = 'Mes réalisations';
$metaDescription = 'Description des projets réalisés par Nic.L Elec dans le cadre d\'interventions sur l\'île d\'Oléron chez des particuliers ou des entreprises pour des installations electriques.';

$projectModel = new Project;
$projects     = $projectModel->orderByDisplay();
$url          = 'uploads/projects/';

require('views/layout.phtml');