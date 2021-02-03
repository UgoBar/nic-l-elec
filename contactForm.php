<?php

require('config/config.php');
require('lib/bdd.php');
require('lib/functions.php');
require('models/Contact.php');


/** varirables globales */
$name      = '';
$email     = '';
$subject   = '';
$content   = '';
$createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));

$errors          = [];
$displayMailSent = false;
//$createdAt = new DateTime('now', new DateTimeZone('Europe/Paris'));


/**** Le formulaire de contact est-il envoyé ? ****/
if(isset($_POST['contactMe']))
{
    $name          = (isset($_POST['name'])) ? trim($_POST['name']) : '';
    $subject       = (isset($_POST['subject'])) ? trim($_POST['subject']) : '';
    $email         = trim($_POST['email']);
    $content       = (isset($_POST['content'])) ? trim($_POST['content'])  :'';

    // Vérification des erreurs du champ "name"
    if(empty($name) || strlen($_POST['name']) < 3)
        $errors['name'] = 'Ce champ est obligatoire. Minimum : 3 caractères';
    
    // Vérification des erreurs du champ "email"
    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
        $errors['email'] = 'Email invalide ou manquant';
    
    if(empty($content))
        $errors['content'] = 'Ce champ est obligatoire.';

    if(empty($subject))
        $errors['subject'] = 'Ce champ est obligatoire.';

    /** Si il n'y a aucunes erreurs alors on rentre les données en base */
    if(empty($errors))
    {

        /** Variables mail */ 
        $mailTo  = 'langiller.nicolas@gmail.com';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From: Nic.L Elec <admin@nic-l-elec.fr>'."\r\n";
        $headers .= 'Reply-To: '.$email."\r\n";
        $headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion();

        //var_dump($to);var_dump($subject);var_dump($content);var_dump($headers);exit;
        //mail($to, $subject, $content, $headers);
        mail($mailTo, $subject, $content, $headers);

        // Ajout dans la base de données
        $contactModel = new Contact;
        $contactModel->add($name, $email, $content, $createdAt);

        // On stock le contenu envoyé pour l'afficher sur la page de confirmation et on redirige vers cette dernière.
        header('Location:/mailSent');
        
    }
}