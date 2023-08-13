<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * c_membre | c_membre.php - Fichiers de routage vers les différentes action ou option découlant de l'use case membre
 *
 * 	@author :
 * 	@date : 22 avr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Modele\Bll\Membres;
use Modele\Bll\Articles;
use Modele\Reference\Membre;
use Modele\Reference\Notification;
use Application\WorkBench as WB;

$_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
$action = (isset($_POST['action']) && !empty($_POST['action'])) ? $_POST['action'] : '';
$option = (isset($_POST['option']) && !empty($_POST['option'])) ? $_POST['option'] : '';
$dataArray = (isset($_POST['array']) && !empty($_POST['array'])) ? $_POST['array'] : array();

switch ($action) {
    case 'favArticle':
        $idArticle = (isset($dataArray['id_article']) && !empty($dataArray['id_article'])) ? $dataArray['id_article'] : null;
        $membre = $_SESSION['connected_user'];
        if (isset($idArticle) && Articles::articleExiste($idArticle)) {
            if (empty($option)) {// Si l'option n'est pas renseigné, alors on effectue l'option par défaut qui est d'ajouter un article
                $exec = Membres::ajouterArticleFavoris($membre->getId(), $idArticle);
            } else {
                $exec = Membres::retirerArticleFavoris($membre->getId(), $idArticle);
            }
            if ($exec) {
                echo(Render::affichArticle(Articles::chargerArticleParId($idArticle)));
            } else {
                $errFav = (empty($option)) ? new Notification("L'ajout du favoris à échoué", ERROR) : new Notification("Le favoris n'à pas pu être retiré", ERROR);
                $_SESSION['tbNotifications'][] = $errFav;
            }
        }
        break;
    case 'modifierProfil':
        // Alimentation des valeurs par défaut des inputs
        $membre = $_SESSION['connected_user'];
        $updatePseudo = $membre->getPseudo();
        $passWordPrecedent = "";
        $nouveauPassword = "";
        $confirmPassword = "";
        switch ($option) {
            case 'affichage':
                // On vérifie qu'on à bien un membre
                if ($membre instanceof Membre) {
                    include('../Vues/v_profil_edition.html');
                }
                break;
            case 'modifierPass':
                // Par défaut on indique une erreur, si aucune erreur n'est détectée au cours du traitement, il convient de mettre cette variable à false
                $erreurPassword = true;

                $passWordPrecedent = (isset($dataArray['formerMdp']) && !empty($dataArray['formerMdp'])) ? $dataArray['formerMdp'] : '';
                $nouveauPassword = (isset($dataArray['new_mdp']) && !empty($dataArray['new_mdp'])) ? $dataArray['new_mdp'] : '';
                $confirmPassword = (isset($dataArray['confirmMdp']) && !empty($dataArray['confirmMdp'])) ? $dataArray['confirmMdp'] : '';


                // On chiffre le mot de passe pour le comparer au mot de passe actuel
                $encryptPasswordPrecedent = Application\Chiffrement::crypt($passWordPrecedent, $_SESSION['connected_user']->getPseudo() . md5($_SESSION['connected_user']->getPseudo()));

                // On va vérifier que ces données sont valide
                // L'utilisateur à correctement renseigné son mot de passe actuel
                $passwordIsOk = ($encryptPasswordPrecedent == $membre->getPassword());
                // L'utilisateur à saisi un mot de passe et la confirmation du mot de passe égal avec ce dernier
                $passwordIsConfirm = ($nouveauPassword == $confirmPassword);
                // Le format du mot de passe est valide
                $passwordFormatOk = preg_match('#' . REGEX_PASSWORD . '#', $nouveauPassword);

                if ($passwordIsOk &&
                        $passwordIsConfirm &&
                        $passwordFormatOk &&
                        !empty($passWordPrecedent) &&
                        !empty($nouveauPassword) &&
                        !empty($confirmPassword)) {
                    // On à toutes les données nécessaire au changement du mot de passe
                    $encryptNewPass = Application\Chiffrement::crypt($nouveauPassword, $membre->getPseudo() . md5($membre->getPseudo()));
                    if (Membres::modifierPassword($_SESSION['connected_user']->getId(), $encryptNewPass)) {
                        // Si la mise à jour à fonctionnée
                        $erreurPassword = false;
                        $succMajMdp = new Notification("Le mot de passe à bien été modifié", SUCCESS);
                        $_SESSION['tbNotifications'][] = $succMajMdp;
                        ?>
                        <script>
                            sendToAjax('membre', null, null, null, '#returnAjax', true, 'Redirection en cours');
                        </script>
                        <?php

                    } else {
                        // Si la mise à jour à échouée
                        $erreurMaj = new Notification("Une erreur innatendue est survenue, veuillez rééssayer", ERROR);
                        $_SESSION['tbNotifications'][] = $erreurMaj;
                    }
                } else {
                    if (!$passwordIsOk) {
                        // L'utilisateur à mal renseigné son mot de passe
                        $_SESSION['tbNotifications'][] = new Notification("Le mot de passe saisie est incorrect, veuillez saisir votre mot de passe actuel", ERROR);
                    }
                    if (!$passwordIsConfirm) {
                        // Le nouveau mot de passe ne correspond pas au mot de passe de confirmation
                        $_SESSION['tbNotifications'][] = new Notification("Les mots de passe ne correspondent pas", ERROR);
                    }
                    if (!$passwordFormatOk) {
                        // Le format du mot de passe est incorrect
                        $_SESSION['tbNotifications'][] = new Notification("Le mot de passe doit faire entre 5 et 20 caractères (les symboles +-*/ sont acceptés)", ERROR);
                    }
                    if (empty($passWordPrecedent) ||
                            empty($nouveauPassword) ||
                            empty($confirmPassword)) {
                        // L'un des champs obligatoire n'est pas renseigné
                        $_SESSION['tbNotifications'][] = new Notification("Veuillez renseigner tous les champs s'il vous plaît, réessayer", ERROR);
                    }
                }
                if ($erreurPassword) {
                    // Retour à l'édition du profil, et affichage des erreurs
                    $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                    include('../Vues/v_profil_edition.html');
                }
                break;
            case 'modifierPseudo':
                // Par défaut on indique une erreur, si aucune erreur n'est détectée au cours du traitement, il convient de mettre cette variable à false
                $erreurPseudo = true;
                // On récupère le pseudo saisi
                $updatePseudo = (isset($dataArray['pseudo']) && !empty($dataArray['pseudo'])) ? htmlspecialchars($dataArray['pseudo']) : '';

                // On vérifie que le format du pseudo est valide
                $pseudoFormatValide = preg_match('#' . REGEX_PSEUDO . '#', $updatePseudo);
                // On vérifie que le pseudo n'est pas déjà utilisé
                $pseudoExist = Membres::pseudoExiste($updatePseudo);

                if (!empty($updatePseudo) &&
                        $pseudoFormatValide &&
                        !$pseudoExist) {
                    if (Membres::modifierPseudo($_SESSION['connected_user']->getId(), $updatePseudo)) {
                        $erreurPseudo = false;
                        $succMAJPseudo = new Notification("Le pseudo à été correctement modifié", SUCCESS);
                        $_SESSION['tbNotifications'][] = $succMAJPseudo;
                        ?>
                        <script>
                            sendToAjax('membre', null, null, null, '#returnAjax', true, 'Redirection en cours');
                        </script>
                        <?php

                    } else {
                        $erreurMAJ = new Notification("Une erreur innatendue est survenue, veuillez rééssayer", ERROR);
                        $_SESSION['tbNotifications'][] = $erreurMAJ;
                    }
                } else {
                    if ($pseudoExist) {
                        $_SESSION['tbNotifications'][] = new Notification("Le pseudo renseigné est déjà utilisé", WARNING);
                    }
                    if (!$pseudoFormatValide) {
                        $_SESSION['tbNotifications'][] = new Notification("Le pseudo doit faire entre 5 et 30 caractères, ne contenir que des lettres, des chiffres ou  _", ERROR);
                    }
                    if (empty($updatePseudo)) {
                        $_SESSION['tbNotifications'][] = new Notification("Veuillez renseigner un pseudo", ERROR);
                    }
                }
                if ($erreurPseudo) {
                    // Retour à l'édition du profil, et affichage des erreurs
                    $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                    include('../Vues/v_profil_edition.html');
                }
        }
        break;
    default:// Consultation d'un profil
        $error = false;
        $idMembre = (isset($dataArray['id_membre']) && !empty($dataArray['id_membre'])) ? $dataArray['id_membre'] : $_SESSION['connected_user']->getId();
        if (!empty($idMembre) && Membres::membreExiste($idMembre)) {
            $membre = Membres::chargerMembreParId($idMembre);
            if ($membre) {
                $lesArticlesFavoris = Articles::articleFavorisParMembre($idMembre);
                $nbrArticlesTrouve = count($lesArticlesFavoris);
                $pseudo = $membre->getPseudo();
                $grade = $membre->getGrade();
                $articleFavoris = "";
                $limitArticleFavoris = ($nbrArticlesTrouve < MAX_ARTICLES_DISPLAY) ? ($nbrArticlesTrouve) : (MAX_ARTICLES_DISPLAY);
                for ($i = 0; $i < $limitArticleFavoris; $i++) {
                    $articleFavoris .= Render::affichArticle($lesArticlesFavoris[$i]);
                }
                if ($nbrArticlesTrouve > MAX_ARTICLES_DISPLAY) {
                    $notification = new Notification("Seul les " . MAX_ARTICLES_DISPLAY . " derniers articles favoris sont affichés", INFO);
                    WB::showNotification(array($notification));
                }
                include('../Vues/v_profil_membre.html');
            } else {
                $error = true;
                $errMembre = new Notification("Le chargement de la fiche de membre à échoué", ERROR);
                $_SESSION['tbNotifications'][] = $errMembre;
            }
        } else {
            $error = true;
            $errMembre = new Notification("La fiche de membre à laquelle vous souhaitez accéder n'existe pas", ERROR);
            $_SESSION['tbNotifications'][] = $errMembre;
        }
        if ($error) {
            ?>
            <script>
                sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php

        }
        break;
}