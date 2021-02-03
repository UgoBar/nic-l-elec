<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Project.php');

verifyConnection('ROLE_ADMIN');

$view = 'projectDisplay';
$titlePage = 'Changer l\'ordre d\'affichage des projets';

$errors = [];
$display = '';
$flashbag = getFlashBag();

/* On récupère tous les utilisateurs */
$projectModel = new Project();
$projects = $projectModel->orderByDisplay();
$countTab = $projectModel->countOfProjects();
$count = (int)$countTab['pro_count'];
// $displayArray = [];

if(isset($_POST['formsubmit']))
{
    foreach($projects as $project)
    {
        $id           = (int)$project['pro_id'];
        $title        = $project['pro_title'];
        $summary      = $project['pro_summary'];
        $description  = $project['pro_description'];
        $picture1     = $project['pro_picture1'];
        $picture2     = $project['pro_picture2'];
        $picture3     = $project['pro_picture3'];
        $top          = $project['pro_top']==1?true:false;
        $display      = (int)$_POST['displayNumber-'.$project['pro_id'].''];

        //$displayArray[$project['pro_id']] = $display;

        if(empty($erros))
        {
            $projectModel->update($id, $title,$summary, $description, (int)$display, $picture1, $picture2, $picture3, $top);
        }
    }

    addFlashBag("L'ordre des projets a bien été modifié !", 'success');
    /** On redirige vers la liste des utilisateurs */
    header('Location:projectDisplay.php');
}




require ('views/layout.phtml');