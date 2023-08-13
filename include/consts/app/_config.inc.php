<?php

/*
 * portfolio - portfolio
 * © Vincent, 2016
 * _config.inc | PHP - Fichier de constantes
 *
 * 	@author : PV
 * 	@date : 04 déc. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

/**
 * Paramètres de configuration de l'appplication
 */
// gestion d'erreur 
ini_set('error_reporting', E_ALL);      // en phase de développement
//ini_set('error_reporting', 0);        // en phase de production 

define('DEV_MODE', 0);   // en phase de développement
define('USER_MODE', 1);  // en phase de production
// constantes pour l'accès à la base de données
// Serveur MySql
define('DB_SERVER', 'portfolibkroot.mysql.db');
// Nom de la base de données
define('DB_DATABASE', 'portfolibkroot');
// Nom d'utilisateur pour se connecter à la base de données
define('DB_USER', 'portfolibkroot');
// Mot de passe pour se connecter à la base de données
define('DB_PWD', '20Vin100Cent');

// La dsn en entier
define('DSN', 'mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE . ';charset=utf8');

// PDO
define('PDO_EXCEPTION_VALUE', -99);

// Admin du site
define('MAIL_ADMIN','vincent.philippe18@gmail.com');

// Constantes utilisées pour l'affichage des erreurs
define('ERROR', 0);
define('WARNING', 1);
define('INFO', 2);
define('SUCCESS', 3);

