<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * c_authentification | c_authentification.php - Fichiers de routage vers les différentes action ou option découlant de l'use case authentification
 *
 * 	@author :
 * 	@date : 21 avr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Modele\Bll\Membres;
use Modele\Reference\Membre;
use Modele\Reference\Notification;
use Application\WorkBench as WB;

$_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
$action = (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';
$option = (isset($_REQUEST['option']) && !empty($_REQUEST['option'])) ? $_REQUEST['option'] : '';
$dataArray = (isset($_POST['array']) && !empty($_POST['array'])) ? $_POST['array'] : array();

switch ($action) {
    case 'inscription':
        switch ($option) {
            case 'valideInscription':
                // Récupération des variables saisies par l'utilisateur
                $selectPseudo = (isset($dataArray['pseudo']) && !empty($dataArray['pseudo'])) ? $dataArray['pseudo'] : '';
                $selectMdp = (isset($dataArray['mdp']) && !empty($dataArray['mdp'])) ? $dataArray['mdp'] : '';
                $selectConfirmMdp = (isset($dataArray['confirmMdp']) && !empty($dataArray['confirmMdp'])) ? $dataArray['confirmMdp'] : '';

                // Contrôles des valeurs
                $pseudoExist = Membres::pseudoExiste($selectPseudo);
                $pseudoFormatValide = preg_match('#'.REGEX_PSEUDO.'#', $selectPseudo);
                $mdpFormatValide = preg_match('#'. REGEX_PASSWORD .'#', $selectMdp);

                // Traitements et Affichage
                if (!empty($selectPseudo) &&
                        !empty($selectMdp) &&
                        !empty($selectConfirmMdp) &&
                        !$pseudoExist &&
                        $selectConfirmMdp == $selectMdp &&
                        $pseudoFormatValide &&
                        $mdpFormatValide) {
                    $encryptMdp = Application\Chiffrement::crypt($selectMdp, $selectPseudo . md5($selectPseudo));
                    $result = Membres::ajouterMembre($selectPseudo, $encryptMdp);
                    if ($result) {
                        $succInscription = new Notification("Inscription terminée ! Votre compte a bien été créé !", SUCCESS);
                        $_SESSION['tbNotifications'][] = $succInscription;
                        $infoInscription = new Notification("Vous pouvez dés à présent vous connecter !", INFO);
                        $_SESSION['tbNotifications'][] = $infoInscription;
                        ?>
                        <script>
                            sendToAjax('authentification', null, null, null, '#returnAjax', true, 'Redirection en cours');
                        </script>
                        <?php

                    } else {
                        $errInscription = new Notification("La création du compte à échouée !", ERROR);
                        $_SESSION['tbNotifications'][] = $errInscription;
                    }
                } else {// Des erreurs ont été trouvé 
                    if (!isset($selectPseudo) || empty($selectPseudo)) {
                        $errPseudo = new Notification("Veuillez saisir un pseudo !", ERROR);
                        $_SESSION['tbNotifications'][] = $errPseudo;
                    } else if (!$pseudoFormatValide) {
                        $errPseudo = new Notification("Le pseudo doit faire entre 5 et 30 caractères, ne contenir que des lettres, des chiffres ou  _", ERROR);
                        $_SESSION['tbNotifications'][] = $errPseudo;
                    } else if (!isset($selectMdp) || empty($selectMdp)) {
                        $errMdp = new Notification("Veuillez saisir un mot de passe !", ERROR);
                        $_SESSION['tbNotifications'][] = $errMdp;
                    } else if (!isset($selectConfirmMdp) || empty($selectConfirmMdp)) {
                        $errConfirm = new Notification("Veuillez confirmer le mot de passe !", WARNING);
                        $_SESSION['tbNotifications'][] = $errConfirm;
                    } else if (isset($selectMdp) && (isset($selectConfirmMdp)) && ($selectMdp != $selectConfirmMdp)) {
                        $errNotMatch = new Notification("Les mots de passe ne correspondent pas !", ERROR);
                        $_SESSION['tbNotifications'][] = $errNotMatch;
                    } else if (!$mdpFormatValide) {
                        $errMdp = new Notification("Le mot de passe doit faire entre 5 et 20 caractères (les symboles +-*/ sont acceptés)", ERROR);
                        $_SESSION['tbNotifications'][] = $errMdp;
                    } else if ($pseudoExist) {
                        $errPseudo = new Notification("Le pseudo choisit est déjà utilisé par quelqu'un d'autre !", WARNING);
                        $_SESSION['tbNotifications'][] = $errPseudo;
                    }
                    $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                    include('../Vues/v_inscription.html');
                }
                break;
            default:
                $selectPseudo = "";
                $selectMdp = "";
                $selectConfirmMdp = "";
                include("../Vues/v_inscription.html");
                break;
        }
        break;
    case 'deconnexion':
        $_SESSION['connected_user'] = null;
        $infoDeco = new Notification("Déconnexion effectué, à bientôt !", INFO);
        $_SESSION['tbNotifications'][] = $infoDeco;
        ?>
        <script>
            sendToAjax('header', null, null, null, '#page_header', true, 'Redirection en cours');
        </script>
        <script>
            sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Déconnexion en cours');
        </script>
        <?php

        break;
    default:// Par défaut on se connecte
        switch ($option) {
            case 'validConnexion':
                $selectMdp = (isset($dataArray['mdp']) && !empty($dataArray['mdp'])) ? $dataArray['mdp'] : '';
                $selectPseudo = (isset($dataArray['pseudo']) && !empty($dataArray['pseudo'])) ? $dataArray['pseudo'] : '';
                if (!empty($selectPseudo) && !empty($selectMdp)) {
                    // d'abbord on crypt le mot de passe affin d'avoir le même rendu que dans la BDD ou les MDP sont hashé
                    $encryptMdp = Application\Chiffrement::crypt($selectMdp, $selectPseudo . md5($selectPseudo));
                    $membre = Membres::chargerMembreParPseudo($selectPseudo);
                    if ($membre instanceof Membre) {
                        // On récupère le mdp crypté du membre correspondant au pseudo donné
                        $mdpMembre = $membre->getPassword();
                        if ($mdpMembre == $encryptMdp) {
                            $_SESSION['connected_user'] = $membre; // Le membre est authentifié et connecté
                            $infoDeco = new Notification("Bonjour " . $membre->getPseudo() . " heureux de vous revoir", INFO);
                            $_SESSION['tbNotifications'][] = $infoDeco;
                            ?>
                            <script>
                                sendToAjax('header', null, null, null, '#page_header', true, 'Redirection en cours');
                            </script>
                            <script>
                                sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php

                        } else {
                            $errPass = new Notification("Mot de passe incorect !", WARNING);
                            $_SESSION['tbNotifications'][] = $errPass;
                            $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                            include("../Vues/v_connexion.html");
                        }
                    } else {
                        $errMembre = new Notification("Ce membre n'existe pas !", WARNING);
                        $_SESSION['tbNotifications'][] = $errMembre;
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                        include("../Vues/v_connexion.html");
                    }
                } else {
                    if (!isset($selectPseudo) || empty($selectPseudo)) {
                        $errPseudo = new Notification("Veuillez saisir votre pseudo !", ERROR);
                        $_SESSION['tbNotifications'][] = $errPseudo;
                    }
                    if (!isset($selectMdp) || empty($selectMdp)) {
                        $errMdp = new Notification("Veuillez saisir votre mot de passe !", ERROR);
                        $_SESSION['tbNotifications'][] = $errMdp;
                    }
                    $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                    include("../Vues/v_connexion.html");
                }
                break;
            default:
                include("../Vues/v_connexion.html");
                break;
        }
        break;
}    