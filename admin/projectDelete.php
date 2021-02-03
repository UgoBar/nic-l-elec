<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/Project.php');

verifyConnection('ROLE_ADMIN');

$projectModel = new Project();
$projects = $projectModel->getAll();

foreach($projects as $project)
{
    /** On reçoit la confirmation */
    if(isset($_POST[''.$project['pro_id'].'']))
    {

        $id       = (int)$_POST[''.$project['pro_id'].''];
        $title    = $_POST['title-'.$project['pro_id'].''];
        $picture1 = $_POST['picture1-'.$project['pro_id'].''];
        $picture2 = $_POST['picture2-'.$project['pro_id'].''];
        $picture3 = $_POST['picture3-'.$project['pro_id'].''];

        /** On supprime les images du serveur */ 
        if($picture1 !== null)
            deleteFile($picture1,'projects');
        if($picture2 !== null)
            deleteFile($picture2,'projects');
        if($picture3 !== null)
            deleteFile($picture3,'projects');
        // On supprime l'élément dans la base
        $projectModel->delete($id);
            
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("Le projet <b>$title</b> a bien été supprimé !", 'success');
        header('Location:projectList.php');
    }
}