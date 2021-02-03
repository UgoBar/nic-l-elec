<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/TarifGrid.php');

verifyConnection('ROLE_ADMIN');

$gridModel = new TarifGrid();
$titleError = null;
$priceError = null;

/** VERIFICATION DE L'ENVOIE DU FORMULAIRE */
if(isset($_POST['add']))
{
    $title          = (isset($_POST['title'])) ? trim($_POST['title']) : '';
    $price          = (isset($_POST['price'])) ? trim($_POST['price']) : '';
    $display        = (isset($_POST['display']))? trim($_POST['display']) : 0;

    // Vérification des erreurs du champ "title"
    if(empty($title) || strlen($_POST['title']) < 3)
        $titleError = 'Le libellé est obligatoire et doit faire minimum 3 caractères';

    if(empty($price))
        $priceError = 'Renseignez un prix';
    
    if($titleError !== null && $priceError == null)
    {
        $_SESSION['prix'] = $price;
        $_SESSION['display'] = $display;
        addFlashBag("<b>Impossible d'ajouter un nouveau tarif</b> : <br>$titleError", 'danger');
        header('Location:tarifGrid.php');
    }

    if($priceError !== null && $titleError == null)
    {
        $_SESSION['titre'] = $title;
        $_SESSION['display'] = $display;
        addFlashBag("<b>Impossible d'ajouter un nouveau tarif</b> : <br> $priceError", 'danger');
        header('Location:tarifGrid.php');
    }

    if($titleError !== null && $priceError !== null)
    {
        $_SESSION['prix'] = '';
        $_SESSION['titre'] = '';
        addFlashBag("<b>Impossible d'ajouter un nouveau tarif</b> : <br>$titleError <br> $priceError", 'danger');
        header('Location:tarifGrid.php');
    }

    if(empty($titleError) && empty($priceError))
    {
        $_SESSION['prix'] = '';
        $_SESSION['titre'] = '';
        $_SESSION['display'] = '';
        $gridModel->add($title, $price, (int)$display);
        //On ajoute à la session flashbag un message qui sera afficher sur la liste
        addFlashBag("Le tarif <b>$title</b> a bien été ajouté !", 'success');
        //On redirige vers la liste des utilisateurs 
        header('Location:tarifGrid.php');
    }

}