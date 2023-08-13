<?php

/*
 * portfolio - portfolio
 * © Vincent, 2016
 * c_portefeuille.php | PHP - Fichiers de routage vers les différentes action ou option découlant de l'use case portefeuille
 *
 * 	@author : PV
 * 	@date : 16 déc. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Modele\Bll\Projets;
use Modele\Bll\Tags;
use Modele\Reference\Notification;
use Application\WorkBench as WB;

$_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);

$action = (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';
$option = (isset($_REQUEST['option']) && !empty($_REQUEST['option'])) ? $_REQUEST['option'] : '';
$dataArray = (isset($_POST['array']) && !empty($_POST['array'])) ? $_POST['array'] : array();

switch ($action) {
    default:
        $limitBegin = (isset($dataArray['limit_begin']) && !empty($dataArray['limit_begin'])) ? $dataArray['limit_begin'] : 0;
        $limitEnd = (isset($dataArray['limit_end']) && !empty($dataArray['limit_end'])) ? $dataArray['limit_end'] : NB_PROJETS_PAR_PAGE;
        $allProjet = Projets::chargerLesProjets();
        $pagination = (Render::displayPagination($allProjet, NB_PROJETS_PAR_PAGE, 'portefeuille', '', '', 'returnAjax', 0));
        $renderingProjets = "";
        $projetRecent = Projets::listerProjets($limitBegin, $limitEnd);
        foreach ($projetRecent as $projet) {
            $renderingProjets .= Render::affichProject($projet);
        }
        include('../Vues/v_accueil_portefeuille.html');
        break;
}
?>
