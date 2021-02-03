<?php

session_start();

require('../config/config.php');
require('../lib/bdd.php');
require('../lib/functions.php');

verifyConnection();

$view = 'noUserTop';
$titlePage = 'ERREUR';

//var_dump($_SESSION);

require('views/layout.phtml');