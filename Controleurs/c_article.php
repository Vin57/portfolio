<?php
/*
 * portfolio - portfolio
 * © Vincent, 2016
 * c_article | PHP - Fichiers de routage vers les différentes action ou option découlant de l'use case article
 *
 * 	@author :
 * 	@date : 25 déc. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Modele\Bll\Articles;
use Modele\Reference\Article;
use Modele\Bll\Tags;
use Modele\Reference\Notification;
use Modele\Reference\Interet;
use Application\WorkBench as WB;

$_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);

$action = (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) ? $_REQUEST['action'] : '';
$option = (isset($_REQUEST['option']) && !empty($_REQUEST['option'])) ? $_REQUEST['option'] : '';
$dataArray = (isset($_POST['array']) && !empty($_POST['array'])) ? $_POST['array'] : array();
switch ($action) {
    case 'ajouterArticle':// On ajoute un article
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_WRITER)) {
            switch ($option) {
                case 'insererArticle':
                    $selectTitre = (isset($dataArray['txt_titre']) && !empty($dataArray['txt_titre'])) ? $dataArray['txt_titre'] : '';
                    $selectDescription = (isset($dataArray['txt_texte']) && !empty($dataArray['txt_texte'])) ? $dataArray['txt_texte'] : '';
                    $selectDate = (isset($dataArray['date_creation']) && !empty($dataArray['date_creation'])) ? $dataArray['date_creation'] : '';
                    $selectInteret = (isset($dataArray['interet']) && !empty($dataArray['interet'])) ? $dataArray['interet'] : '';
                    $selectLien = (isset($dataArray['lien']) && !empty($dataArray['lien'])) ? $dataArray['lien'] : '';
                    $lesTags = (isset($dataArray['tags']) && !empty($dataArray['tags'])) ? $dataArray['tags'] : array();
                    if (!empty($selectTitre) && !empty($selectDescription) && (Utilities::isDate(date('d/m/Y', strtotime($selectDate))) && strtotime($selectDate) != 0)) {
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
                        $ajout = Articles::ajouterArticle($selectTitre, $selectDescription, $selectDate, $selectInteret, $selectLien, $tbObjetTag, $_SESSION['connected_user']->getId());
                        if ($ajout) {
                            $succArticle = new Notification("L'article à bien été ajouté !", SUCCESS);
                            $_SESSION['tbNotifications'][] = $succArticle;
                            ?>
                            <input id="date_creation" type="hidden" value="<?php echo($selectDate) ?>">
                            <script>
                                var date = new Date(document.getElementById('date_creation').value);
                                var table = {'mois': date.getMonth() + 1, // on rajoute 1 car les mois php commencent à 1 alors qu'en JS ils commencent à 0
                                    'annee': date.getFullYear()};
                                // On redirige l'utilisateur vers l'affichage du calendrier correspondat au mois de l'année de l'article venant d'être renseigné
                                sendToAjax('article', null, null, table, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php
                        } else {
                            $errorArticle = new Notification("Échec de l'ajout !", ERROR);
                            $_SESSION['tbNotifications'][] = $errorArticle;
                            $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                            // On récupére les données saisies pour les afficher à nouveaux dans la page d'ajout
                            $tbObjetTag = array();
                            foreach ($lesTags as $id) {
                                if (!empty($id)) {
                                    $result = Tags::chargerTagById($id);
                                    if ($result) {
                                        $tbObjetTag[] = $result;
                                    }
                                }
                            }
                            $selectTags = "";
                            foreach ($tbObjetTag as $tag) {
                                $selectTags .= \Render::DisplayClosableTag($tag);
                            }
                            include('../Vues/v_article__ajouter_article.html');
                        }
                    } else {// Des erreurs ont été trouvé 
                        if (empty($selectTitre)) {
                            $errTitre = new Notification("Veuillez saisir un <strong>titre</strong> s'il vous plait !", WARNING);
                            $_SESSION['tbNotifications'][] = $errTitre;
                        } if (empty($selectDescription)) {
                            $errTexte = new Notification("Veuillez saisir un <strong>texte</strong> s'il vous plait !", WARNING);
                            $_SESSION['tbNotifications'][] = $errTexte;
                        } if (!(Utilities::isDate(date('d/m/Y', strtotime($selectDate))) && strtotime($selectDate) != 0)) {
                            $errDate = new Notification("La <strong>Date</strong> saisie est invalide !", WARNING);
                            $_SESSION['tbNotifications'][] = $errDate;
                        }
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                        // On récupére les données saisies pour les afficher à nouveaux dans la page d'ajout
                        $tbObjetTag = array();
                        foreach ($lesTags as $id) {
                            if (!empty($id)) {
                                $result = Tags::chargerTagById($id);
                                if ($result) {
                                    $tbObjetTag[] = $result;
                                }
                            }
                        }
                        $selectTags = "";
                        foreach ($tbObjetTag as $tag) {
                            $selectTags .= \Render::DisplayClosableTag($tag);
                        }
                        include('../Vues/v_article__ajouter_article.html');
                    }
                    break;
                default: // On saisie l'article
                    // Les données peuvent avoir déjà été renseigné par l'utilisateur, mais si une erreur se produit, on doit pouvoir les récupérer
                    $selectTitre = (isset($dataArray['txt_titre']) && !empty($dataArray['txt_titre'])) ? $dataArray['txt_titre'] : "";

                    $selectDescription = (isset($dataArray['txt_texte']) && !empty($dataArray['txt_texte'])) ? $dataArray['txt_texte'] : "";

                    // Lorsque l'utilisateur souhaite rédiger un article, il doit cliquer sur le bouton ajouter du jour désirer, on transmet alors la date via ajax
                    $selectDate = (isset($dataArray['date_creation']) && !empty($dataArray['date_creation'])) ? date('Y-m-d', strtotime(Utilities::dateFrIntoDateEng($dataArray['date_creation']))) : date('Y-m-d');

                    $selectInteret = (isset($dataArray['interet']) && !empty($dataArray['interet'])) ? $dataArray['interet'] : STANDARD; // Par défaut l'intérêt standard est séléctionner

                    $selectLien = (isset($dataArray['lien']) && !empty($dataArray['lien'])) ? $dataArray['lien'] : "";

                    $selectTags = "";
                    if (isset($dataArray['tags']) && !empty($dataArray['tags'])) {
                        foreach ($dataArray['tags'] as $tagNom) {
                            if (!empty($tagNom)) {
                                $selectTags .= \Render::DisplayClosableTag(Tags::chargerTagByName(array($tagNom)));
                            }
                        }
                    }

                    include('../Vues/v_article__ajouter_article.html');
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
    case 'rss':
        $listFlux = ['https://techmeme.com/feed.xml', 'https://www.lemagit.fr/rss/ContentSyndication.xml' ,'https://korben.info/feed'];
        $lesFluxs = array();
        // Pour chaque nom de flux on va créer une liste d'articles temporaires
        foreach ($listFlux as $name_flux) {
            // Initialise une session cURL et retourne un identifiant de session
            $session_curl = curl_init();
            // Définition des options de transmissions de la page web
            // On supprime également la vérification du certificat SSL
            curl_setopt($session_curl, CURLOPT_SSL_VERIFYPEER, 0);
            // On appel la page via l'url
            curl_setopt($session_curl, CURLOPT_URL, $name_flux);
            // On retourne les données
            curl_setopt($session_curl, CURLOPT_RETURNTRANSFER, true);
            // La réponse est retournée au format XML afin d'être exploitable
            $responseFormatXML = curl_exec($session_curl);
            // On converti le XML sous forme de chaîne de caractères
            $rss = simplexml_load_string($responseFormatXML);

            $tbArticles = array();
            if ($rss && $responseFormatXML) {
                $items = $rss->channel->item;
                // On va créer un nouvel article pour chaque articles lus dans le flux courant
                for ($i = 0; $i < NOMBRE_ARTICLE_PAR_FLUX; $i++) {
                    $id = strtotime($items[$i]->pubDate);
                    $title = $items[$i]->title;
                    $description = preg_replace('%<a%', '<a target="_blank"', $items[$i]->description);
                    $date = date('Y-m-d', strtotime($items[$i]->pubDate));
                    switch ($date) {
                        case date('Y-m-d', time() - 3600 * 24):
                            $interet = new Interet(NEWS, "Hier");
                            break;
                        case date('Y-m-d'):
                            $interet = new Interet(IMPORTANT, "Aujourd'hui");
                            break;
                        default:
                            $interet = new Interet(STANDARD, "Cette semaine");
                            break;
                    }
                    $source = $items[$i]->link;
                    $tbArticles[] = new Article($id, $title, $description, $date, $interet, $source, array(), 0, null);
                }
            }
            if (!empty($tbArticles)) {
                $lesFluxs[$name_flux] = $tbArticles;
            }
        }
        // Variables pour l'affichage
        $nbFluxs = count($lesFluxs);
        if ($nbFluxs) {
            $res = "";
            foreach ($lesFluxs as $key => $tbArticle) {
                $titreFlux = $key;
                $titreFlux = (preg_replace('%' . REGEX_RSS_URL . '%', '', $key));
                $res .= '<div class="col-lg-4 col-md-6">';
                $res .= '<h1><a target="blank" class="link-blugre" href="' . (preg_replace('%' . REGEX_RSS_REPL . '%', '', $key)) . '">' . $titreFlux . '</a></h1>';
                foreach ($tbArticle as $article) {
                    $res .= Render::affichArticle($article, true);
                }
                $res .= '</div>';
            }
            include('../Vues/v_rss.html');
        } else {
            $errFlux = new Notification("La connexion au flux RSS à échouée. Vérifier votre connexion internet et rééssayer", ERROR);
            $_SESSION['tbNotifications'][] = $errFlux;
            ?>
            <script>
                sendToAjax('article', null, null, null, '#returnAjax', true, 'Redirection en cours');
            </script>
            <?php
        }
        break;
    case 'rechercherArticle':
        $results = Array();
        if (isset($dataArray['mode']) && !empty($dataArray['mode'])) {
            switch ($dataArray['mode']) {
                case 'titre':
                    $titreVal = (isset($dataArray['titre']) && !empty($dataArray['titre'])) ? Utilities::BanalizedString($dataArray['titre']) : "";
                    if (!empty(trim($titreVal))) {
                        $results = Articles::chargerArticlesParTitre($titreVal, 0);
                    }
                    break;
                case 'tag':
                    $lesIdTags = (isset($dataArray['tags']) && !empty($dataArray['tags'])) ? $dataArray['tags'] : array();
                    if (!empty($lesIdTags)) {
                        $realIdTags = array();
                        foreach ($lesIdTags as $id) {
                            if (!empty($id)) {
                                $realIdTags[] .= $id;
                            }
                        }
                        $results = Articles::chargerArticlesParTags($realIdTags);
                    }
                    break;
                default:
                    //not yet
                    break;
            }
            $nbResults = count($results);
            if ($nbResults) {
                $render = '';
                foreach ($results as $result) {
                    $render .= Render::affichArticle($result);
                }
                if (!empty($results)) {
                    $resultatS = ($nbResults > 1) ? 'Résultats correspondent' : 'Résultat correspond';
                    echo('<span class="lead">' . $nbResults . ' ' . $resultatS . ' à votre recherche</span>');
                    echo($render);
                }
            } else {
                $errNoResult = new Notification("Aucun article ne correspond à votre recherche !", INFO);
                $_SESSION['tbNotifications'][] = $errNoResult;
                $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
            }
        }
        break;
    case 'modifierArticle':
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_WRITER)) {
            switch ($option) {
                case 'insererArticle':
                    $idArticle = (isset($dataArray['id_article']) && !empty($dataArray['id_article'])) ? $dataArray['id_article'] : '';
                    $selectTitre = (isset($dataArray['txt_titre']) && !empty($dataArray['txt_titre'])) ? $dataArray['txt_titre'] : '';
                    $selectDescription = (isset($dataArray['txt_texte']) && !empty($dataArray['txt_texte'])) ? $dataArray['txt_texte'] : '';
                    $selectDate = (isset($dataArray['date_creation']) && !empty($dataArray['date_creation'])) ? $dataArray['date_creation'] : '';
                    $selectInteret = (isset($dataArray['interet']) && !empty($dataArray['interet'])) ? $dataArray['interet'] : '';
                    $selectLien = (isset($dataArray['lien']) && !empty($dataArray['lien'])) ? $dataArray['lien'] : '';
                    $lesTags = (isset($dataArray['tags']) && !empty($dataArray['tags'])) ? $dataArray['tags'] : array();
                    if (!empty($selectTitre) && !empty($selectDescription) && (Utilities::isDate(date('d/m/Y', strtotime($selectDate))) && strtotime($selectDate) != 0 && !empty($idArticle))) {
                        // A partir des id de tag obtenus, on va charger des objets tag
                        if (Articles::articleExiste($idArticle)) {
                            $tbObjetTag = array();
                            foreach ($lesTags as $id) {
                                if (!empty($id)) {
                                    $result = Tags::chargerTagById($id);
                                    if ($result) {
                                        $tbObjetTag[] = $result;
                                    }
                                }
                            }
                            $ajout = Articles::modifierArticle($idArticle, $selectTitre, $selectDescription, $selectDate, $selectInteret, $selectLien, $tbObjetTag);
                            if ($ajout) {
                                $succArticle = new Notification("L'article à bien été modifié !", SUCCESS);
                                $_SESSION['tbNotifications'][] = $succArticle;
                                ?>
                                <input id="date_creation" type="hidden" value="<?php echo($selectDate) ?>">
                                <script>
                                    var date = new Date(document.getElementById('date_creation').value);
                                    var table = {'mois': date.getMonth() + 1, // on rajoute 1 car les mois php commencent à 1 alors qu'en JS ils commencent à 0
                                        'annee': date.getFullYear()};
                                    // On redirige l'utilisateur vers l'affichage du calendrier correspondat au mois de l'année de l'article venant d'être renseigné
                                    sendToAjax('article', null, null, table, '#returnAjax', true, 'Redirection en cours');
                                </script>
                                <?php
                            } else {
                                $errorArticle = new Notification("Échec de la modification !", ERROR);
                                $_SESSION['tbNotifications'][] = $errorArticle;
                                $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                                // On récupére les données saisies pour les afficher à nouveaux dans la page d'ajout
                                $tbObjetTag = array();
                                foreach ($lesTags as $id) {
                                    if (!empty($id)) {
                                        $result = Tags::chargerTagById($id);
                                        if ($result) {
                                            $tbObjetTag[] = $result;
                                        }
                                    }
                                }
                                $selectTags = "";
                                foreach ($tbObjetTag as $tag) {
                                    $selectTags .= Render::DisplayClosableTag($tag);
                                }
                                include('../Vues/v_article__modifier_article.html');
                            }
                        } else {
                            $errArticle = new Notification("L'article que vous souhaitez modifier n'existe pas ou plus !", ERROR);
                            $_SESSION['tbNotifications'][] = $errArticle;
                            ?>
                            <script>
                                sendToAjax('article', null, null, null, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php
                        }
                    } else {// Des erreurs ont été trouvé 
                        if (empty($selectTitre)) {
                            $errTitre = new Notification("Veuillez saisir un <strong>titre</strong> s'il vous plait !", WARNING);
                            $_SESSION['tbNotifications'][] = $errTitre;
                        } if (empty($selectDescription)) {
                            $errTexte = new Notification("Veuillez saisir un <strong>texte</strong> s'il vous plait !", WARNING);
                            $_SESSION['tbNotifications'][] = $errTexte;
                        } if (!(Utilities::isDate(date('d/m/Y', strtotime($selectDate))) && strtotime($selectDate) != 0)) {
                            $errDate = new Notification("La <strong>Date</strong> saisie est invalide !", WARNING);
                            $_SESSION['tbNotifications'][] = $errDate;
                        }if (empty($idArticle)) {
                            $errId = new Notification("Impossible de modifier l'article sans identifiant", WARNING);
                            $_SESSION['tbNotifications'][] = $errId;
                            ?>
                            <script>
                                sendToAjax('article', null, null, null, '#returnAjax', true, 'Redirection en cours');
                            </script>
                            <?php
                        }
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                        // On récupére les données saisies pour les afficher à nouveaux dans la page d'ajout
                        $tbObjetTag = array();
                        foreach ($lesTags as $id) {
                            if (!empty($id)) {
                                $result = Tags::chargerTagById($id);
                                if ($result) {
                                    $tbObjetTag[] = $result;
                                }
                            }
                        }
                        $selectTags = "";
                        foreach ($tbObjetTag as $tag) {
                            $selectTags .= \Render::DisplayClosableTag($tag);
                        }
                        include('../Vues/v_article__modifier_article.html');
                    }
                    break;
                default:
                    $article = Articles::chargerArticleParId($dataArray['id_article']);
                    $idArticle = $dataArray['id_article'];
                    $selectTitre = $article->getTitre();
                    $selectDescription = $article->getTexte();
                    $selectDate = $article->getDateCreation();
                    $selectInteret = $article->getInteret()->getId();
                    $selectLien = $article->getLien();
                    $selectTags = "";
                    foreach ($article->getLesTags() as $tag) {
                        $selectTags .= Render::DisplayClosableTag($tag);
                    }
                    include('../Vues/v_article__modifier_article.html');
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
    case 'supprimerArticle':
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_ADMIN)) {
            $idArticle = (isset($dataArray['id_article']) && !empty($dataArray['id_article'])) ? $dataArray['id_article'] : null;
            if (!empty($idArticle)) {
                if (Articles::articleExiste($dataArray['id_article'])) {
                    if (Articles::supprimerArticle($dataArray['id_article'])) {
                        $succSuppr = new Notification("L'article à bien été supprimé !", SUCCESS);
                        $_SESSION['tbNotifications'][] = $succSuppr;
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                    } else {
                        $errSuppr = new Notification("L'article n'à pas été supprimé une erreur est survenue !", ERROR);
                        $_SESSION['tbNotifications'][] = $errSuppr;
                        $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
                    }
                } else {
                    $errExist = new Notification("L'article que vous désirez supprimer n'existe pas ou plus !", ERROR);
                    $_SESSION['tbNotifications'][] = $errExist;
                    ?>
                    <script>
                        sendToAjax('article', null, null, null, '#returnAjax', true, 'Redirection en cours');
                    </script>
                    <?php
                }
            } else {
                $errId = new Notification("Impossible de modifier l'article sans identifiant", WARNING);
                $_SESSION['tbNotifications'][] = $errId;
                ?>
                <script>
                    sendToAjax('article', null, null, null, '#returnAjax', true, 'Redirection en cours');
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
    case 'archiverArticle':
        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
            $idArticle = (isset($dataArray['id_article']) && !empty($dataArray['id_article'])) ? $dataArray['id_article'] : null;
            if (!empty($idArticle) && Articles::articleExiste($dataArray['id_article'])) {
                $opt = (!empty($option)) ? Articles::desarchiveArticle($dataArray['id_article']) : Articles::archiverArticle($dataArray['id_article']);
                if ($opt) {
                    echo(Render::affichArticle(Articles::chargerArticleParId($idArticle)));
                } else {
                    $errArchive = (!empty($option)) ? new Notification("Le désarchivage à échoué, une erreur est survenue !", ERROR) : new Notification("L'archivage de l'article à échoué, une erreur est survenue !", ERROR);
                    $_SESSION['tbNotifications'][] = $errArchive;
                }
            } else {
                $errExist = new Notification("L'article que vous désirez archiver n'existe pas ou plus !", ERROR);
                $_SESSION['tbNotifications'][] = $errExist;
            }
            $_SESSION['tbNotifications'] = WB::showNotification($_SESSION['tbNotifications']);
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
    case'consulterTousArticles':
        $limitBegin = (isset($dataArray['limit_begin']) && !empty($dataArray['limit_begin'])) ? $dataArray['limit_begin'] : 0;
        $limitEnd = (isset($dataArray['limit_end']) && !empty($dataArray['limit_end'])) ? $dataArray['limit_end'] : NB_ARTICLES_PAR_PAGE;
        $lesArticles = Articles::chargerLesArticles();
        $totalArticle = count($lesArticles);
        $renderingArticle = "";
        $articleRecent = Articles::listerArticle($limitBegin, $limitEnd);
        foreach ($articleRecent as $article) {
            $renderingArticle .= Render::affichArticle($article);
        }
        $pagination = (Render::displayPagination($lesArticles, NB_ARTICLES_PAR_PAGE, 'article', 'limitArticle', null, 'affichArticle', 0));
        include('../Vues/v_article__lister_tous_articles.html');
        break;

    case 'limitArticle':// Comparé au projet, on risque d'avoir bien plus d'article, c'est pourquoi on ne va pas recharger tous les articles à chaque changement de pagination
        $limitBegin = (isset($dataArray['limit_begin']) && !empty($dataArray['limit_begin'])) ? $dataArray['limit_begin'] : 0;
        $limitEnd = (isset($dataArray['limit_end']) && !empty($dataArray['limit_end'])) ? $dataArray['limit_end'] : NB_ARTICLES_PAR_PAGE;
        $lesArticles = Articles::listerArticle($limitBegin, $limitEnd);
        $renderingArticle = "";
        foreach ($lesArticles as $article) {
            $renderingArticle .= Render::affichArticle($article);
        }
        echo($renderingArticle);
        break;
    // Par défaut l'option est de consulter les articles
    default:
        $tbAnnee = Utilities::getPreviousYear(TIME_TO_LIVE_ARTICLE);
        $tbMois = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
        $annee = date('Y'); // par défaut l'année actuelle
        $mois = Utilities::getMonth(date('Y-m-d'), 1); // par défaut le mois actuel
        $mode = (isset($dataArray['mode']) && !empty($dataArray['mode'])) ? $dataArray['mode'] : null;

        if (isset($dataArray['mois']) && !empty($dataArray['mois'])) {
            if (Utilities::array_contains($tbMois, $dataArray['mois'], false, true)) {
                $mois = $dataArray['mois'];
            } elseif (is_numeric($dataArray['mois'])) {
                $mois = Utilities::getMonth($dataArray['mois'], 1);
            }
        }
        if (isset($dataArray['annee']) && !empty($dataArray['annee'])) {
            if (is_numeric($dataArray['annee']) && Utilities::array_contains($tbAnnee, $dataArray['annee'])) {
                $annee = $dataArray['annee'];
            }
        }

        // dates pour la gestion des boutons "mois precedent" et "mois suivant"
        $intMois = Utilities::convertMonthToInt32($mois);
        $dateCourante = $annee . "-" . $intMois . "-" . date('d'); //La date qui est actuellement consultée

        $datePrecedente = strtotime($dateCourante . " - 1 month");
        $moisPrecedent = date('m', $datePrecedente);
        $anneePrecedente = date('Y', $datePrecedente);

        $dateSuivante = strtotime($dateCourante . " + 1 month");
        $moisSuivant = date('m', $dateSuivante);
        $anneeSuivante = date('Y', $dateSuivante);

        // Chargement du calendrier
        $tbArticle = Articles::chargerArticlesParDate($intMois, $annee);
        $nbArticles = Utilities::compterArticles($tbArticle);

        // Variables pour l'affichage
        $strArticle = ($nbArticles > 1) ? "Articles" : "Article";
        $strNbArticles = $nbArticles . " ";
        $chaineArticle = $strArticle . " en " . $mois . " " . $annee;
        include('../Vues/v_article__lister_articles.html');
        break;
}