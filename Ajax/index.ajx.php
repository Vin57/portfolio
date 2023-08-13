<?php

/* portfolio - portfolio
 * © Vincent, 2016
 * index.ajx | PHP - Fichier de routes principale (router en fonction des données recu par le script ajax _redirect)
 *
 * 	@author : PV
 * 	@date : 16 déc. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

include('../include/consts/_globals.php');
include('../include/_util.inc.php');
include('../include/_app.inc.php');
include('../include/_render.php');

use Modele\Reference\Notification;
use Application\WorkBench;
use Main\Autoloader;

require_once('../Modele/Autoloader.php');
Autoloader::register();

session_start();
// Tableau pour la gestion et l'affichage des notifications
$_SESSION['tbNotifications'] = (isset($_SESSION['tbNotifications']) && !empty($_SESSION['tbNotifications'])) ? ($_SESSION['tbNotifications']) : array();
// On charge l'utilisateur au cas ou sont état aurait changé
$_SESSION['connected_user'] = (isset($_SESSION['connected_user']) && !empty($_SESSION['connected_user'])) ? (Modele\Bll\Membres::chargerMembreParId($_SESSION['connected_user']->getId())) : null;
$uc = (isset($_POST['uc']) && !empty($_POST['uc'])) ? $_POST['uc'] : '';
// Si l'utilisateur vient de se connecter
if (!isset($_SESSION['connected_user']) || (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MEMBRE))) {
    switch ($uc) {
        case 'header':
            include('../Vues/v_header.html');
            break;
        case 'accueil':
            $_SESSION['tbNotifications'] = WorkBench::showNotification($_SESSION['tbNotifications']);
            include('../Vues/v_accueil.html');
            break;
        case 'portefeuille':
            include '../Controleurs/c_portefeuille.php';
            break;
        case 'article':
            include '../Controleurs/c_article.php';
            break;
        case 'tag':
            include '../Controleurs/c_tag.php';
            break;
        case 'projet':
            include '../Controleurs/c_projet.php';
            break;
        case 'authentification':
            include '../Controleurs/c_authentification.php';
            break;
        case 'membre':
            include '../Controleurs/c_membre.php';
            break;
        case 'competence':
            include '../Controleurs/c_competence.php';
            break;
        default:
            include '../Vues/Composite/v_404.html';
            break;
    }
} else {
    include('../Vues/Composite/v_ban.html');
}

Render::HideWaitMessage("patientMessage");
?>