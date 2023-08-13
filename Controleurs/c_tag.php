<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * c_tag | c_tag.php - Fichiers de routage vers les différentes action ou option découlant de l'use case tag
 *
 * 	@author :
 * 	@date : 14 avr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Modele\Bll\Tags;
use Modele\Reference\Notification;
use Application\WorkBench as WB;

$_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);

$action = (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';
$option = (isset($_REQUEST['option']) && !empty($_REQUEST['option'])) ? $_REQUEST['option'] : '';
$dataArray = (isset($_POST['array']) && !empty($_POST['array'])) ? $_POST['array'] : array();

switch ($action) {
    case 'chercherTag':
        $value = (isset($dataArray['objet']) && !empty(trim($dataArray['objet']))) ? htmlspecialchars($_POST['array']['objet']) : NULL;
        $search = str_replace("'", "\'", $value);
        if (!empty($value)) {
            if (strlen($value) > MAX_LEN_NOM_TAG) {
                $errLenTag = new Notification("Aucun tag ne peux dépasser " . MAX_LEN_NOM_TAG . " charactéres", WARNING);
                $_SESSION['tbNotifications'][] = $errLenTag;
            } else {
                $lesTags = Tags::chercherTag($search);
                if (!$lesTags) {
                    if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
                        // Le tableau des tags est vide (aucun résultat trouvé pour la recherche courrante) on peux l'ajouter si on à les droits
                        echo('<a onclick="'
                        . 'var table = {\'newTag\' : \'' . $search . '\'};'
                        . 'sendToAjax(\'tag\',\'ajouterTag\',null,table,\'#researchTag\',false,\'Ajout du tag en cours\',true)"'
                        . '>Créer le tag : <span style="word-wrap: break-word;"><strong>' . $value . '</strong>?</span></a>');
                    } else {
                        echo('Le tag ' . $value . ' n\'existe pas');
                    }
                } else {
                    echo('Votre tag se trouve dans la liste ? (cliquez dessus pour l\'ajouter)<br>');
                    $TagsNoms = array();
                    foreach ($lesTags as $tag) {
                        echo(Render::DisplayAddingTag($tag, 'returnTag',Array('badge','searchedBadge')));
                        $TagsNoms[] = $tag->getNom();
                    }
                    if (!Utilities::array_contains($TagsNoms, $value, false, true)) {
                        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
                            // Le tableau des tags contient bien des éléments pour la recherche effectuer, néanmoins aucun ne correspond exactement à sa recherche
                            echo('<br><a onclick="'
                            . 'var table = {\'newTag\' : \'' . $search . '\'};'
                            . 'sendToAjax(\'tag\',\'ajouterTag\',null,table,\'#researchTag\',false,\'Ajout du tag en cours\')"'
                            . '>Sinon créez le tag : <span style="word-wrap: break-word;"><strong>' . $value . '</strong>?</span></a>');
                        }
                    } else {
                        // Un tag correspondant EXACTEMENT à la recherche à été trouvé, il n'y à donc pas lieux de permettre à l'utilisateur d'en créé un nouveau
                        echo('<br>Votre tag est dans la liste !');
                    }
                }
            }
        } else {
            $emptySearch = new Notification("Veuillez saisir quelque chose !", INFO);
            $_SESSION['tbNotifications'][] = $emptySearch;
        }
        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
        break;
    case 'ajouterTag':
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
            $dataArray['newTag'] = htmlspecialchars($dataArray['newTag']);
            if (!Tags::tagExiste($dataArray['newTag'])) {
                if (Tags::ajouterTag($dataArray['newTag'])) {
                    $succesTag = new Notification("La création du tag à été correctement effectué !", SUCCESS);
                    $_SESSION['tbNotifications'][] = $succesTag;
                } else {
                    $errAjouTag = new Notification("La création du tag à échoué !", ERROR);
                    $_SESSION['tbNotifications'][] = $errAjouTag;
                }
            } else {
                $errTagExist = new Notification("Le nom de tag est déjà utilisé!", ERROR);
                $_SESSION['tbNotifications'][] = $errTagExist;
            }
            $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
        } else {
            $errNotAllowed = new Notification("Vous n'avez pas les droits pour accéder à cette fonctionalité !", ERROR);
            $_SESSION['tbNotifications'][] = $errNotAllowed;
        }
        break;
}