<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * c_projet | c_projet.php - Fichiers de routage vers les différentes action ou option découlant de l'use case projet
 *
 * 	@author :
 * 	@date : 15 avr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Modele\Bll\Projets;
use Modele\Bll\Tags;
use Modele\Bll\TypeProjets;
use Modele\Bll\Competences;
use Modele\Reference\Notification;
use Application\WorkBench as WB;

$_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);

$action = (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';
$option = (isset($_REQUEST['option']) && !empty($_REQUEST['option'])) ? $_REQUEST['option'] : '';
$dataArray = (isset($_POST['array']) && !empty($_POST['array'])) ? $_POST['array'] : array();

switch ($action) {
    case 'ajouterProjet':
        $tbCompetence = Competences::chargerCompetences();
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_WRITER)) {
            $validAjout = (isset($dataArray['ajout']) && !empty($dataArray['ajout'])) ? $dataArray['ajout'] : '';
            switch ($option) {
                case 'insererProjet':
                    $selectNom = (isset($dataArray['txt_nom_projet']) && !empty($dataArray['txt_nom_projet'])) ? $dataArray['txt_nom_projet'] : '';
                    $selectDate = (isset($dataArray['date_projet']) && !empty($dataArray['date_projet'])) ? $dataArray['date_projet'] : '';
                    $selectDescription = (isset($dataArray['description']) && !empty($dataArray['description'])) ? trim($dataArray['description']) : '';
                    $selectType = (isset($dataArray['type_projet']) || !empty($dataArray['type_projet'])) ? $dataArray['type_projet'] : 0;
                    $lesTags = (isset($dataArray['tags']) && !empty($dataArray['tags'])) ? $dataArray['tags'] : array();
                    $lesCompetences = (isset($dataArray['competences']) && !empty($dataArray['competences'])) ? $dataArray['competences'] : array();
                    $tbObjetTag = array();
                    foreach ($lesTags as $id) {
                        if (!empty($id)) {
                            $result = Tags::chargerTagById($id);
                            if ($result) {
                                $tbObjetTag[] = $result;
                            }
                        }
                    }
                    $tbObjetCompetence = array();
                    foreach ($lesCompetences as $nom) {// On reçoit des noms de compétences
                        if (!empty($nom)) {
                            $result = Competences::chargerCompetenceParNom($nom); // Que l'on transforme en objet \Competence
                            if ($result) {
                                $tbObjetCompetence[] = $result;
                            }
                        }
                    }
                    if (!empty($selectNom) && !empty($selectDate) && !empty($selectDescription) && !empty($selectType)) {
                        $ajout = Projets::ajouterProjet($selectNom, $selectDate, $selectDescription, $selectType, $tbObjetTag, $tbObjetCompetence);
                        if ($ajout) {
                            $succProjet = new Notification("Le projet à bien été créé !", SUCCESS);
                            $_SESSION['tbNotifications'][] = $succProjet;
                            ?>
                            <script>
                                sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Création projet en cours');
                            </script>
                            <?php

                        } else {
                            $errorProjet = new Notification("Échec de la création du projet !", ERROR);
                            $_SESSION['tbNotifications'][] = $errorProjet;
                            $selectTags = "";
                            foreach ($tbObjetTag as $tag) {
                                $selectTags .= Render::DisplayClosableTag($tag);
                            }
                            $selectCompetences = "";
                            foreach ($tbObjetCompetence as $competence) {
                                $selectCompetences .= Render::DisplayClosableCompetence($competence);
                            }
                            include('../Vues/v_projet__ajouter_projet.html');
                        }
                    } else {// Des erreurs ont été trouvé 
                        if (empty($selectNom)) {
                            $errTitre = new Notification("Veuillez saisir un <strong>nom</strong> de projet", WARNING);
                            $_SESSION['tbNotifications'][] = $errTitre;
                        } if (!(Utilities::isDate(date('d/m/Y', strtotime($selectDate))) && strtotime($selectDate) != 0)) {
                            $errDate = new Notification("Veuillez saisir une <strong>date</strong> de projet valide", WARNING);
                            $_SESSION['tbNotifications'][] = $errDate;
                        } if (empty($selectType)) {
                            $errType = new Notification("Veuillez saisir un <strong>type</strong> de projet", WARNING);
                            $_SESSION['tbNotifications'][] = $errType;
                        }if (empty($selectDescription)) {
                            $errDescription = new Notification("Veuillez saisir une <strong>description</strong> pour ce projet", WARNING);
                            $_SESSION['tbNotifications'][] = $errDescription;
                        }
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                        // On récupére les données saisies pour les afficher à nouveaux dans la page d'ajout

                        $selectTags = "";
                        foreach ($tbObjetTag as $tag) {
                            $selectTags .= Render::DisplayClosableTag($tag);
                        }

                        $selectCompetences = "";
                        foreach ($tbObjetCompetence as $competence) {
                            $selectCompetences .= Render::DisplayClosableCompetence($competence);
                        }
                        include('../Vues/v_projet__ajouter_projet.html');
                    }
                    break;
                default:
                    $selectCompetences = "";
                    $selectNom = "";
                    $selectDate = date('Y-m-d');
                    $selectType = 0;
                    include('../Vues/v_projet__ajouter_projet.html');
                    break;
            }
        } else {
            $errNotAllowed = new Notification("Vous n'avez pas les droits pour accéder à cette fonctionalité !", ERROR);
            $_SESSION['tbNotifications'][] = $errNotAllowed;
            ?>
            <script>
                sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php

        }
        break;
    case 'modifierProjet':
        $tbCompetence = Competences::chargerCompetences();
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_WRITER)) {
            switch ($option) {
                case 'insererProjet':
                    ?>
                    <script>
                        sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
                    </script>
                    <?php

                    $idProjet = (isset($dataArray['id_projet']) && !empty($dataArray['id_projet'])) ? $dataArray['id_projet'] : '';
                    $selectNom = (isset($dataArray['txt_nom_projet']) && !empty($dataArray['txt_nom_projet'])) ? $dataArray['txt_nom_projet'] : '';
                    $selectDate = (isset($dataArray['date_projet']) && !empty($dataArray['date_projet'])) ? $dataArray['date_projet'] : '';
                    $selectDescription = (isset($dataArray['description']) && !empty($dataArray['description'])) ? trim($dataArray['description']) : '';
                    $selectType = (isset($dataArray['type_projet']) || !empty($dataArray['type_projet'])) ? $dataArray['type_projet'] : 0;
                    $lesTags = (isset($dataArray['tags']) && !empty($dataArray['tags'])) ? $dataArray['tags'] : array();
                    $lesCompetences = (isset($dataArray['competences']) && !empty($dataArray['competences'])) ? $dataArray['competences'] : array();

                    // A partir des id de tag obtenus, on va charger des objets tag
                    $tbObjetTag = array();
                    foreach ($lesTags as $id) {
                        if (!empty($id)) {
                            $result = Tags::chargerTagById($id);
                            if ($result) {
                                $tbObjetTag[] = $result;
                            }
                        }
                    }
                    $tbObjetCompetence = array();
                    foreach ($lesCompetences as $nom) {// On reçoit des noms de compétences
                        if (!empty($nom)) {
                            $result = Competences::chargerCompetenceParNom($nom); // Que l'on transforme en objet \Competence
                            if ($result) {
                                $tbObjetCompetence[] = $result;
                            }
                        }
                    }
                    if (!empty($selectNom) && !empty($selectDate) && !empty($selectDescription) && !empty($selectType) && !empty($idProjet)) {
                        if (Projets::projetExiste($idProjet)) {
                            $modif = Projets::modifierProjet($idProjet, $selectNom, $selectDate, $selectDescription, $selectType, $tbObjetTag, $tbObjetCompetence);
                            if ($modif) {

                                $succProjet = new Notification("Le projet à bien été modifié !", SUCCESS);
                                $_SESSION['tbNotifications'][] = $succProjet;
                            } else {
                                $errorProjet = new Notification("Échec de la modification du projet !", ERROR);
                                $_SESSION['tbNotifications'][] = $errorProjet;
                                $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                                $selectTags = "";
                                foreach ($tbObjetTag as $tag) {
                                    $selectTags .= Render::DisplayClosableTag($tag);
                                }
                                $selectCompetences = "";
                                foreach ($tbObjetCompetence as $competence) {
                                    $selectCompetences .= Render::DisplayClosableCompetence($competence);
                                }
                                include('../Vues/v_projet__modifier_projet.html');
                            }
                        } else {
                            $errorProjet = new Notification("Le projet que vous souhaitez modifier n'existe pas ou plus !", ERROR);
                            $_SESSION['tbNotifications'][] = $errorProjet;
                            ?>
                            <script>
                                sendToAjax('portfeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php

                        }
                    } else {// Des erreurs ont été trouvé
                        if (empty($selectNom)) {
                            $errTitre = new Notification("Veuillez saisir un <strong>nom</strong> de projet", WARNING);
                            $_SESSION['tbNotifications'][] = $errTitre;
                        } if (!(Utilities::isDate(date('d/m/Y', strtotime($selectDate))) && strtotime($selectDate) != 0)) {
                            $errDate = new Notification("Veuillez saisir une <strong>date</strong> de projet valide", WARNING);
                            $_SESSION['tbNotifications'][] = $errDate;
                        } if (empty($selectType)) {
                            $errType = new Notification("Veuillez saisir un <strong>type</strong> de projet", WARNING);
                            $_SESSION['tbNotifications'][] = $errType;
                        }if (empty($selectDescription)) {
                            $errDescription = new Notification("Veuillez saisir une <strong>description</strong> pour ce projet", WARNING);
                            $_SESSION['tbNotifications'][] = $errDescription;
                        }if (empty($idProjet)) {
                            $errId = new Notification("Impossible de modifier le projet sans identifiant", WARNING);
                            $_SESSION['tbNotifications'][] = $errId;
                            ?>
                            <script>
                                sendToAjax('article', null, null, null, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php

                        }
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                        $selectTags = "";
                        foreach ($tbObjetTag as $tag) {
                            $selectTags .= Render::DisplayClosableTag($tag);
                        }

                        $selectCompetences = "";
                        foreach ($tbObjetCompetence as $competence) {
                            $selectCompetences .= Render::DisplayClosableCompetence($competence);
                        }
                        include('../Vues/v_projet__modifier_projet.html');
                    }
                    break;
                default:
                    $idProjet = (isset($dataArray['id_projet']) && !empty($dataArray['id_projet'])) ? $dataArray['id_projet'] : null;
                    if (!empty($idProjet)) {
                        if (Projets::projetExiste($idProjet)) {
                            $projet = Projets::chargerProjetParId($dataArray['id_projet']);
                            $projetId = $projet->getId();
                            $selectNom = $projet->getNom();
                            $selectDate = $projet->getDate();
                            $selectType = $projet->getType()->getId();
                            $selectDescription = $projet->getDescription();
                            $selectTags = "";
                            foreach ($projet->getLesTags() as $tag) {
                                $selectTags .= Render::DisplayClosableTag($tag);
                            }
                            $selectCompetences = "";
                            foreach ($projet->getLesCompetences() as $competence) {
                                $selectCompetences .= Render::DisplayClosableCompetence($competence);
                            }
                            include('../Vues/v_projet__modifier_projet.html');
                        } else {
                            $errExist = new Notification("Le projet choisit pour modification n'existe pas", ERROR);
                            $_SESSION['tbNotifications'][] = $errExist;
                            ?>
                            <script>
                                sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php

                        }
                    } else {
                        $errId = new Notification("Impossible de modifier le projet sans identifiant", WARNING);
                        $_SESSION['tbNotifications'][] = $errId;
                        ?>
                        <script>
                            sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
                        </script>
                        <?php

                    }
                    break;
            }
        } else {
            $errNotAllowed = new Notification("Vous n'avez pas les droits pour accéder à cette fonctionalité !", ERROR);
            $_SESSION['tbNotifications'][] = $errNotAllowed;
            ?>
            <script>
                sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php

        }
        break;
    case 'supprimerProjet':
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_ADMIN)) {
            $idProjet = (isset($dataArray['id_projet']) && !empty($dataArray['id_projet'])) ? $dataArray['id_projet'] : null;
            if (!empty($idProjet)) {
                if (Projets::projetExiste($dataArray['id_projet'])) {
                    if (Projets::supprimerProjet($dataArray['id_projet'])) {
                        $sucSuppr = new Notification("Le projet à bien été supprimé !", SUCCESS);
                        $_SESSION['tbNotifications'][] = $sucSuppr;
                    } else {
                        $errSuppr = new Notification("Le projet n'à pas été supprimé une erreur est survenue !", ERROR);
                        $_SESSION['tbNotifications'][] = $errSuppr;
                    }
                    $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                } else {
                    $errExist = new Notification("Le projet que vous désirez supprimer n'existe pas ou plus !", ERROR);
                    $_SESSION['tbNotifications'][] = $errExist;
                    ?>
                    <script>
                        sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
                    </script>
                    <?php

                }
            } else {
                $errId = new Notification("Impossible de supprimer le projet sans identifiant", WARNING);
                $_SESSION['tbNotifications'][] = $errId;
                ?>
                <script>
                    sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
                </script>
                <?php

            }
        } else {
            $errNotAllowed = new Notification("Vous n'avez pas les droits pour accéder à cette fonctionalité !", ERROR);
            $_SESSION['tbNotifications'][] = $errNotAllowed;
            ?>
            <script>
                sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php

        }
        break;
    case 'archiverProjet':
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
            $idProjet = (isset($dataArray['id_projet']) && !empty($dataArray['id_projet'])) ? $dataArray['id_projet'] : null;
            if (!empty($idProjet) && Projets::projetExiste($dataArray['id_projet'])) {
                $opt = (!empty($option)) ? Projets::desarchiverProjet($dataArray['id_projet']) : Projets::archiverProjet($dataArray['id_projet']);
                if ($opt) {
                    if (!empty($option)) {
                        $succArchive = new Notification("Le projet à été sorti des archives !", SUCCESS);
                    } else {
                        $succArchive = new Notification("L'archivage du projet à réussie !", SUCCESS);
                    }
                    $_SESSION['tbNotifications'][] = $succArchive;
                } else {
                    if (!empty($option)) {
                        $succArchive = new Notification("Le désarchivage à échoué, une erreur est survenue !", ERROR);
                    } else {
                        $errArchive = new Notification("L'archivage du projet à échoué, une erreur est survenue !", ERROR);
                    }
                    $_SESSION['tbNotifications'][] = $errArchive;
                }
            } else {
                $errExist = new Notification("Le projet que vous désirez archiver n'existe pas ou plus !", ERROR);
                $_SESSION['tbNotifications'][] = $errExist;
            }
            ?>
            <script>
                sendToAjax('portefeuille', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php

        } else {
            $errNotAllowed = new Notification("Vous n'avez pas les droits pour accéder à cette fonctionalité !", ERROR);
            $_SESSION['tbNotifications'][] = $errNotAllowed;
            ?>
            <script>
                sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php

        }
        break;
    // Par défaut on charge tous les projet
    default:
        $limitBegin = (isset($dataArray['limit_begin']) && !empty($dataArray['limit_begin'])) ? $dataArray['limit_begin'] : 0;
        $limitEnd = (isset($dataArray['limit_end']) && !empty($dataArray['limit_end'])) ? $dataArray['limit_end'] : NB_PROJETS_PAR_PAGE;
        $search = (isset($dataArray['search']) && !empty($dataArray['search'])) ? Utilities::BanalizedString($dataArray['search']) : null;
        $redirect = false;
        if (!empty(trim($search))) {
            $lesResultats = Projets::rechercherProjet($search);
            if (!empty($lesResultats)) {
                $nbResultat = count($lesResultats);
                $renderingProjets = "";
                foreach ($lesResultats as $projet) {
                    $renderingProjets .= Render::affichProject($projet);
                }
                $resultatS = ($nbResultat > 1) ? "résultats" : "résultat";
                $infoResult = new Notification("<strong>" . $nbResultat . "</strong> " . $resultatS . "  pour \"" . htmlspecialchars($search) . "\"!", INFO);
                $_SESSION['tbNotifications'][] = $infoResult;
                $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                echo($renderingProjets);
            } else {
                $infoNoResult = new Notification("Aucun résultat pour \"" . htmlspecialchars($search) . "\"!", INFO);
                $_SESSION['tbNotifications'][] = $infoNoResult;
                $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
            }
        } else {// L'utilisateur n'à rien saisie on réaffiche tous les projets
            $allProjet = Projets::chargerLesProjets();
            $pagination = (Render::displayPagination($allProjet, NB_PROJETS_PAR_PAGE, 'portefeuille', '', '', 'returnAjax', 0));
            $renderingProjets = "";
            $projetRecent = Projets::listerProjets($limitBegin, $limitEnd);
            foreach ($projetRecent as $projet) {
                $renderingProjets .= Render::affichProject($projet);
            }
            echo($renderingProjets);
            echo($pagination);
        }
        break;
}