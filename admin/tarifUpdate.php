<?php

declare(strict_types=1);
session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');
require('../models/TarifGrid.php');

verifyConnection('ROLE_ADMIN');

$gridModel = new TarifGrid();
$tarifs = $gridModel->getAll();

$titleError = null;
$priceError = null;
$asBaseChanged = false;
$errors = false;


foreach($tarifs as $tarif)
{
    /** On reçoit la confirmation */
    if(isset($_POST[''.$tarif['tar_id'].'']))
    {

        $id    = (int)$_POST[''.$tarif['tar_id'].''];
        $title = $_POST['title-'.$tarif['tar_id'].''];

        // On supprime l'élément dans la base
        $gridModel->delete($id);
            
        /** On ajoute à la session flashbag un message qui sera afficher sur la liste*/
        addFlashBag("Le tarif <b>$title</b> a bien été supprimé !", 'success');
        header('Location:tarifGrid.php');
    }
}


if(isset($_POST['update']))
{
    /** EDIT MODE */
    foreach($tarifs as $tarif)
    {
        $id    = (int)$tarif['tar_id'];
        $title = $_POST['title-'.$tarif['tar_id'].''];
        $price = $_POST['price-'.$tarif['tar_id'].''];
        $display = $_POST['display-'.$tarif['tar_id'].''];

        $baseTitle = $tarif['tar_title'];
        $basePrice = $tarif['tar_price'];
        
        // Vérification des erreurs du champ "title"
        if(empty($title) || strlen($_POST['title-'.$tarif['tar_id'].'']) < 3)
            $titleError = 'Le libellé est obligatoire et doit faire minimum 3 caractères';

        // Vérification des erreurs du champ "prix"
        if(empty($price))
            $priceError = 'Renseignez un prix';

        if(empty($titleError) && empty($priceError)) // Aucunes erreurs ? Alors on modifie les entrées dans la base
        {
            $gridModel->update($id, $title, $price, (int)$display);
        }

        if($title !== $baseTitle)
            $asBaseChanged = true;
        if($price !== $basePrice)
            $asBaseChanged = true;
    }

    if($titleError !== null && $priceError == null)
    {
        $errors = true;
        addFlashBag("<b>Impossible de modifie le ou les tarif(s)</b> : <br>$titleError", 'danger');
        header('Location:tarifGrid.php');
    }

    else if($priceError !== null && $titleError == null)
    {
        $errors = true;
        addFlashBag("<b>Impossible de modifie le ou les tarif(s)</b> : <br> $priceError", 'danger');
        header('Location:tarifGrid.php');
    }

    else if($titleError !== null && $priceError !== null)
    {
        $errors = true;
        addFlashBag("<b>Impossible de modifie le ou les tarif(s)</b> : <br>$titleError <br> $priceError", 'danger');
        header('Location:tarifGrid.php');
    }

    if($errors == false && $asBaseChanged == false) // Si aucunes modif on actualise la page
    {
        header('Location:tarifGrid.php');
    }
    if($errors == false && $asBaseChanged == true) // Si il y a eu des modifs on ajoute un message flash et on actualise la page
    {
        addFlashBag("La modification a bien été prise en compte !", 'success');
        header('Location:tarifGrid.php');
    }
}