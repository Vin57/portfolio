<?php

/**
 * portfolio - portfolio
 * © Vincent, 2017
 * _render | _render.php - Affichage de partie generique
 *
 * 	@author :
 * 	@date : 1 janv. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */
use Modele\Reference\Modal;
use Modele\Reference\Input;
use Modele\Reference\InputMultipleValue;
use Modele\Reference\Projet;
use Modele\Reference\Article;
use Application\WorkBench;

class Render {

    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE CALENDRIER">
    /**
     * Affiche sous forme de calendrier, le mois (paramétre 1) de l'année (paramétre 2)
     * @param string $unMois un mois en français
     * @param string $uneAnnee une année au format YYYY
     * @param array(/Articles) un tableau d'objets article
     */
    public static function DisplayCalendar($unMois, $uneAnnee, $tbArticle) {
        // Le nombre de jour dans le mois que l'on souhaite affiché
        $DayInMonth = (cal_days_in_month(CAL_GREGORIAN, Utilities::convertMonthToInt32($unMois), $uneAnnee) + 1);

        //Le premier jour du mois séléctionné
        $firstDay = (date('r', strtotime('first day of ' . Utilities::MonthFrToEngMonth($unMois) . ' ' . $uneAnnee . '')));
        $premierJour = Utilities::DayEngToFrDay(substr($firstDay, 0, 3));

        $j = Utilities::getIntDay($premierJour);
        $totalJ = 1;
        $semaine = 1;
        ?>
        <div class="row">
            <?php
            /**
             * A chaque tour de cette boucle on à une semaine
             * On effectue d'abbord les traitements tel que la recherche d'articles
             * Puis on effectue l'affichage du jour contenant aucun, un, ou plusieurs article(s)
             */
            while ($totalJ < $DayInMonth) {
                $tabDetailsArticle = "";
                $tourneur = 0;
                $cptTour = 0; //On doit utiliser un compteur de tour car bien que les jours pourrait faire office de compteur,
                // Ceux-ci ne commence pas toujours à 0
                ?>
                <div id="Semaine<?= ($semaine) ?>" class="col-lg-12">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 uk-badge badge-blugre">Semaine <?= ($semaine) ?></div><br>
                    <?php
                    while ($j <= 6 and $totalJ < $DayInMonth) {//A chaque tour de cette boucle on à un nouveau jour
                        ?>
                        <?php
                        // On va construire un tableau dans lequel on va entrer les dates de :
                        // La journée,
                        if ($totalJ < 10) {// Pour rendre l'affichage plus agréable, si le nombre n'à pas de 0 (est < à 10) on lui en ajoute un
                            $currentLoopDay = "0" . $totalJ;
                        } else {
                            $currentLoopDay = $totalJ;
                        }

                        // Du mois,
                        if (Utilities::convertMonthToInt32($unMois) < 10) {// Pour rendre l'affichage plus agréable, si le nombre n'à pas de 0 (est < à 10) on lui en ajoute un
                            $currentLoopMonth = "0" . Utilities::convertMonthToInt32($unMois);
                        } else {
                            $currentLoopMonth = Utilities::convertMonthToInt32($unMois);
                        }

                        // De l'année.
                        $currentLoopYears = $uneAnnee;

                        // Et on rentre ces données (jour/mois/annee) dans tbJour
                        $tbJour[$semaine][$cptTour] = $currentLoopDay . "/" . $currentLoopMonth . "/" . $currentLoopYears;
                        // On va maintenant affiché s'il y en à les articles de chaques journées
                        $articleForDay = self::SearchEventForDay($tbArticle, $tbJour[$semaine][$cptTour]);

                        // On construit l'afichage de cette journée
                        $displayDay = ('<div class="uk-panel uk-panel-box col-lg-3 col-md-3 col-sm-3 col-xs-4">');
                        if (count($articleForDay) > 0) {
                            $explodeDate = explode('/', $tbJour[$semaine][$cptTour]); // Pour l'identifiant de la modale, on ne peux pas utiliser de /
                            $idForModal = implode('', $explodeDate);
                            // S'il y à au moins un article, on affiche un badge indiquant la présence d'article
                            $displayDay .= ('<div'
                                    . ' id="Semaine' . $semaine . 'Notif' . $j . '" '
                                    . ' class="uk-badge uk-badge-notification badge-blugre notif-left"'
                                    . ' data-target="#' . $idForModal . 'Modal"'
                                    . ' data-toggle="modal"'
                                    . ' style="cursor:pointer">'
                                    . count($articleForDay)
                                    . '</div></a>');
                            // Si il y à des articles, on les mets en forme pour l'affichage
                            $tabDetailsArticle .= '<div  id="Semaine' . $semaine . 'Article' . $j . '">';
                            $tabDetailsArticle .= self::DisplayArticle($articleForDay, $tbJour[$semaine][$cptTour], $idForModal);
                            $tabDetailsArticle .= '</div>';
                        }
                        if (isset($_SESSION['connected_user']) && Application\Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_WRITER)) {
                            $displayDay .= '<div class="uk-panel-badge">'
                                    . '<a class="ico-plus-grey" onclick="'
                                    . 'var table = {\'date_creation\' : \'' . $tbJour[$semaine][$cptTour] . '\'};'
                                    . 'sendToAjax(\'article\',\'ajouterArticle\',null,table,\'#returnAjax\',true,\'Chargement du formulaire\')"'
                                    . '>'
                                    . '<span class="fa-stack fa-lg">'
                                    . '<i class="fa fa-plus-circle fa-stack-2x"></i>'
                                    . '</span>'
                                    . '</a>'
                                    . '</div>';
                        }
                        $displayDay .= "<center>"
                                . "<h4>"
                                . $currentLoopDay
                                . "</h4>"
                                . "<small>"
                                . Utilities::getStrDay($j)
                                . "</small>"
                                . "</center>";
                        echo($displayDay);
                        echo($tabDetailsArticle);
                        echo('</div>');

                        $cptTour ++; //La boucle à fait un tour
                        $j += 1; //Le nombre de jour entré dans la semaine
                        $totalJ += 1; //Le nombre total de jour déjà affiché                
                    }
                    ?>
                </div>
                <?php
                $j = 0;
                $semaine += 1; // On passe à la semaine suivante              
            }
            ?>
        </div>
        <?php
    }

    /**
     * Retourne un tableau d'article pour une journée
     * @param tab $tbArticle un tableau d'article dont on souhaite récupérer ceux qui ont été créé le $dateDay
     * @param string $dateDay une date au format JJ/MM/YYYY
     * @return array Un tableau d'articles pour la journée à la date $dateDay
     */
    private static function SearchEventForDay($tbArticle, $dateDay) {
        $articleForDay = array();
        foreach ($tbArticle as $unArticle) {
            if (!$unArticle->getArchive() || (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO))) {
                $dateCreation = date('d/m/Y', strtotime($unArticle->getDateCreation()));
                if (date($dateDay) == $dateCreation) {
                    $articleForDay[] = $unArticle;
                }
            }
        }
        return $articleForDay;
    }

    /**
     * Mise en forme pour l'affichage d'un tableau d'article donné en paramétre
     * @param tab $lesArticles les articles d'une journée
     * @param string $idModal
     * @return string une chaîne de code HTML affichant les evénements pour la journée
     */
    private static function DisplayArticle($lesArticles, $date, $idModal) {
        $return = "";
        $text = "";
        foreach ($lesArticles as $unArticle) {
            if (!$unArticle->getArchive() || (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO))) {
                $text .= self::affichArticle($unArticle);
            }
        }
        $return .= self::DisplayModal(new Modal('Les articles du ' . $date, $idModal . 'Modal', $text));
        return $return;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE MESSAGE">
    /**
     *  Affiche un message
     * @param type $message le message à affiché
     */
    public static function DisplayWaitMessage($msg, $idMessage) {
        echo('<center><div id="' . $idMessage . '" style="display:none;">'
        . $msg . '</br>'
        . '<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>'
        . '<span class="sr-only">Loading...</span>'
        . '</div></center>');
    }

    /**
     * Cache le message d'attente
     * @param type $idMessage l'id du message à caché (celle donnée en paramétre à la fonction DisplayWaitMessage)
     */
    public static function HideWaitMessage($idMessage) {
        echo "<script>";
        echo "document.getElementById('" . $idMessage . "').style.display = 'none'";
        echo "</script>";
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE MODAL">
    /**
     * DisplayModal Affiche une modal
     * @param \Modal $modal
     * @return string
     */
    public static function DisplayModal(Modal $modal) {
        $return = '<div class="modal" id="' . $modal->getId() . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel">' . $modal->getTitre() . '</h4>
                                </div>
                                <div class="modal-body">';
        $return .= $modal->getText();
        $return .= "<br></br>";
        if ($modal->haveForm()) {// On va ajouter un formulaire à la modale
            $return .= '<hr>';
            foreach ($modal->getForms() as $form) {
                $return .= self::DisplayForm($form);
            }
        }
        $return .= "</div>"
                . "</div>"
                . "</div>"
                . "</div>";
        return $return;
    }

    public static function DisplayForm($form) {
        $return = ' <form action="#' . $form->getAction() . '" method="' . $form->getMethod() . '" enctype="' . $form->getEnctype() . '">';
        foreach ($forms as $input) {
            $return .= '<ul class="rechercha">';
            $return .= self::DisplayInput($input);
            $return .= '</ul>';
        }
        $return .= "</form>";
        return $return;
    }

    public static function DisplayInput($input) {
        $return = "";
        if ($input instanceof Input) {
            $label = $input->getLabel();
            if (!empty($label)) {
                $return .= '<label>' . $label . '</label>';
            }
            $is_multiple_type = WorkBench::is_multiple_value_input($input->getType());
            $return .= ($is_multiple_type) ? '<' : '<input';
            $return .= ' class="' . $input->getClass() . '"';
            if ($is_multiple_type) {// Un select ou tout autre éléments pouvant prendre différente valeur
                $return .= $input->getType() . ' name=' . $input->getName() . '>';
                foreach ($input->getOption() as $key => $option) {
                    $return .= '<option value ="' . $option . '" ';
                    if ($input->getSelected() == $key) {
                        $return .= 'selected';
                    }
                    $return .= '>';
                    $return .= '</option>';
                }
                $return .= '<' . $input->getType() . '/>';
            } else {
                $return .= (!empty($input->getType())) ? ' type="' . $input->getType() . '" ' : '';
                $return .= (!empty($input->getValue())) ? ' value="' . $input->getValue() . '" ' : '';
                $return .= '>';
            }
        }
        return $return;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE TAG">
    /**
     * DisplayAddingTag Affiche un tag et permet l'ajout de celui-ci dans un élément du DOM
     * @param \Tag $tag : Un objet de type Tag
     * @param string $zoneDisplay : Un identifiant d'un élément du DOM dans lequel on souhaite créé le tag, lors du clique sur celui-ci
     * @param array $class (facultatif, défaut = Array('badge')) : Tableau définissant les classes de l'élément HTML
     * @return string Retourne : Un tag sur lequel on peux cliqué et qui génére un clone éponyme dans l'élément du DOM donnée en second paramètre
     */
    public static function DisplayAddingTag($tag, $zoneDisplay, $class = Array('badge')) {
        // Construction de la chaîne des classes d'un tag
        $stringClass = "new Array(";
        for ($i = 0; $i < Count($class); $i++) {
            if ($i >= 1) {
                $stringClass .= ',';
            }
            $stringClass .= "'$class[$i]'";
        }
        $stringClass .= ")";
        
        $return = '<a class="badge badge-square"';
        $return .= 'onmousedown="return false"';
        $return .= 'onselectstart="return false"';
        $return .= 'onclick="creatElement(\'span\',\'' . $tag->getNom() . '\',\'' . $tag->getId() . '\',' . $stringClass . ',\'' . htmlspecialchars($tag->getNom()) . '\',\'' . $zoneDisplay . '\',true);">';
        $return .= $tag->getNom();
        $return .= '</a>&nbsp;';
        
        return $return;
    }

    /**
     * Affiche un tag en permettant aussi de le retirer de son noeud parent
     * @param Tag $tag : Un objet de type Tag
     * @return string Retourne : Un tag que l'on peux supprimer du noeud courrant en cliquant sur une petite croix porté par ce dernier
     */
    public static function DisplayClosableTag($tag) {
        return '<span id="' . $tag->getId() . '" class="badge" >' . htmlspecialchars($tag->getNom()) . '<span class="btn btn-xs close" onclick="dropParent(this.parentNode)">×</span></span>';
    }

    /**
     * 
     * @param type $tag
     */
    public static function DisplayTag($tag) {
        return '<span id="' . $tag->getId() . '" class="badge badge-square">' . htmlspecialchars($tag->getNom()) . '</span>';
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE COMPETENCE">
    /**
     * DisplayClosableCompetence Affiche une compétence en permettant aussi de le retirer de son noeud parent
     * @param \Competence $tag : Un objet de type Compétence
     * @return string Retourne : Une compétence que l'on peux supprimer du noeud courrant en cliquant sur une petite croix
     */
    public static function DisplayClosableCompetence($competence) {
        return '<span id="' . $competence->getNom() . '" class="panel-blugre col-lg-6 col-md-6 col-sm-12 col-xs-12">' . htmlspecialchars($competence->getNom()) . '<span class="btn btn-xs close" onclick="dropParent(this.parentNode)">×</span></span>';
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE PROJET">

    /**
     * 
     * @param Projet $projet
     * @return type
     */
    public static function affichProject(Projet $projet) {
        $return = false;
        if (!$projet->getArchive() || (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO))) {

            if ($projet instanceof Projet) {
                $return = "<div id='projet-" . $projet->getId() . "'>";
                $return .= "<span class='label label-grey pull-left'>" . $projet->getType()->getLibelle() . "</span>";
                $return .= "<span class='pull-right'>";
                $return .= self::DisplayAdminBtn($projet, 'projet', 'projet');
                $return .= "</span>";

                $return .= "<div class='panel panel-blugre'>";
                $return .= "<div class='panel-heading'>";
                $return .= "<div class='panel-title breakword'>" . $projet->getNom() . " | ";
                $return .= "<small><i>" . $projet->getDate() . "</i></small>";
                $return .= "</div>";
                $return .= "</div>";
                $return .= "<div class='panel-body'>";

                foreach ($projet->getLesTags() as $tag) {
                    $return .= self::DisplayTag($tag) . " ";
                }
                $return .= '<hr><button type="button" class="btn btn-blugre" data-toggle="collapse" data-target="#projet' . $projet->getId() . '">Description</button>';
                $return .= "<div id='projet" . $projet->getId() . "' class='collapse'>";
                $return .= "<span class='breakword'>" . preg_replace('@<script[^>]*?>.*?</script>@si', '&nbsp;', $projet->getDescription()) . "</span>";
                $return .= "</div>";
                $return .= "</div>";
                $return .= "</div>";
                $btnYes = new Input("button", null, null, "btn btn-danger btn-block", null, null, "Oui", "");
                $contentModal = "<span class='col-lg-12'>" . self::DisplayInput($btnYes) . "</span>";
                $confModal = new Modal('Supprimer ' . $projet->getNom(), 'confirmDeletProjet' . $projet->getId(), $contentModal);
                $return .= self::DisplayModal($confModal);
                $return .= "</div>";
            }
            return $return;
        }
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE ARTICLE">

    /**
     * displayProject Affiche un article
     * @param Article $article : L'objet \Article à affiché
     * @param boolean $notOwn : Indique si le site posséde cette article ou non (si l'article à été généré via un flux rss alors il n'est pas dans la bdd donc le site ne le posséde pas)
     */
    public static function affichArticle(Article $article, $notOwn = false) {
        $return = false;
        if (!$article->getArchive() || (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO))) {
            if ($article instanceof Article) {
                $return = "<div id='article-" . $article->getId() . "'>";
                $interet = WorkBench::getBadgeInteret($article->getInteret());
                $return .= "<span class='pull-left'>" . $interet . "</span>";
                $return .= "<span class='pull-right'>";
                if (!$notOwn) {// Si l'on posséde l'article dans la BDD (l'article est écrit par un utilisateur)
                    if (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MEMBRE)) {
                        if (\Modele\Bll\Articles::articleEstFavorisDuMembre($_SESSION['connected_user']->getId(), $article->getId())) {
                            $return .= self::DisplayLinkIcon($article->getId(), 'article', 'link-yellow', '<i class="fa fa-2x fa-star" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;', 'membre', 'favArticle', 'delete', 'article-' . $article->getId());
                        } else {
                            $return .= self::DisplayLinkIcon($article->getId(), 'article', 'link-yellow', '<i class="fa fa-2x fa-star-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;', 'membre', 'favArticle', null, 'article-' . $article->getId());
                        }
                    }
                    $return .= self::DisplayAdminBtn($article, 'article', 'article');
                }
                $return .= "</span>";
                $return .= "<div class='panel panel-blugre'>";
                $return .= "<div class='panel-heading'>";
                $return .= "<div class='panel-title breakword'>" . $article->getTitre();
                if (!$notOwn) {// Si l'article est écrit par un utilisateur, on affiche son pseudo
                    $return .= '| <a onclick=\'var table = {"id_membre": ' . $article->getAuteur()->getId() . '};
                                sendToAjax("membre", null, null, table, "#returnAjax", true, "Chargement de l\"espace membre en cours");\'>';
                    $return .= $article->getAuteur()->getPseudo() . " <i class='fa fa-id-card-o' aria-hidden='true'></i></a>";
                }
                $return .= "<small><i> (" . $article->getDateCreation() . ") </i></small>";
                $return .= "</div>";
                $return .= "</div>";
                $return .= "<div class='panel-body'>";
                foreach ($article->getLesTags() as $tag) {
                    $return .= self::DisplayTag($tag) . " ";
                }
                $return .= '<hr><button type="button" class="btn btn-blugre" data-toggle="collapse" data-target="#article' . $article->getId() . '">Description</button>';
                $return .= "<div id='article" . $article->getId() . "' class='collapse'>";
                $return .= "<span class='breakword'>" . preg_replace('@<script[^>]*?>.*?</script>@si', '&nbsp;', $article->getTexte()) . "</span>";
                $return .= "</div>";
                if (!empty($article->getLien())) {
                    $lien = $article->getLien();
                    $return .= "<div class='panel-footer'>";
                    if ($lien != null and ! empty(trim($lien))) {
                        $return .= "<div class='blue-rectangle'>"
                                . "Source : &nbsp;&nbsp;&nbsp;<a target='_blank' href='" . $lien . "'><i class='fa fa-external-link' aria-hidden='true'></i>&nbsp;" . substr($lien, 0, 30) . "...</a>"
                                . "</div>";
                    }
                    $return .= "</div>";
                }
                $return .= "</div>";
                $return .= "</div>";
                $btnYes = new Input("button", null, null, "btn btn-danger btn-block", null, null, "Oui", "");
                $contentModal = "<span class='col-lg-12'>" . self::DisplayInput($btnYes) . "</span>";
                $confModal = new Modal('Supprimer ' . $article->getTitre(), 'confirmDeletArticle' . $article->getId(), $contentModal);
                $return .= self::DisplayModal($confModal);
                $return .= "</div>";
            }
        }
        return $return;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE BOUTTON">

    /**
     * DisplayLinkIcon Affiche un icone, effectuant une action dans la limite de Read, Update, Delete
     * @param int $id_entity : L'identifiant de l'entitée
     * @param string $name_entity : Le nom de l'entitée
     * @param string $class : La ou les classes CSS aplliqué au bouton (séparé par des espaces)
     * @param string $icon : Un icone bootstrap ou font-awesome (balise comprise)
     * @param string $uc : L'use case de l'icon
     * @param string $action (facultatif, défaut = "") : L'action de l'icon
     * @param string $option (facultatif, défaut = "") : L'option de l'icon
     * @param string $callBack (facultatif, défaut = "returnAjax") : L'identifiant de l'élément dans lequel renvoyer la réponse
     * @return string Retourne : Un icone cliquable
     */
    public static function DisplayLinkIcon($id_entity, $name_entity, $class, $icon, $uc, $action = "", $option = "", $callBack = "returnAjax") {
        $return = "<a class='" . $class . "' onclick=\""
                . "var table = {'id_" . $name_entity . "' :" . $id_entity . "};"
                . "sendToAjax('" . $uc . "','" . $action . "','" . $option . "',table,'#" . $callBack . "',false,'Chargement en cours')"
                . "\">"
                . $icon
                . '</a>';
        return $return;
    }

    /**
     * DisplayAdminBtn Affiche les boutons d'administration d'une entité 
     * @param Object $entity : Une entité de type \Article ou \Projet
     * @param string $name_entity : Le nom de l'entité en minuscule
     * @param string $uc : Le nom de l'use case menant au controleur
     * @return string Retourne : Du code HTML
     */
    public static function DisplayAdminBtn($entity, $name_entity, $uc) {
        $return = "";
        if (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_WRITER)) {
            $return .= self::DisplayLinkIcon($entity->getId(), $name_entity, '', '<i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;', $uc, 'modifier' . ucfirst($name_entity));
        }
        if (!$entity->getArchive()) {
            if (isset($_SESSION['connected_user']) && Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
                $return .= self::DisplayLinkIcon($entity->getId(), $name_entity, 'link-orange', '<i class="fa fa-2x fa-archive" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;', $uc, 'archiver' . ucfirst($name_entity), null, $name_entity . '-' . $entity->getId());
            }
        } else {
            if (isset($_SESSION['connected_user']) && Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO)) {
                $return .= self::DisplayLinkIcon($entity->getId(), $name_entity, 'link-orange', '<span class="fa-stack fa-lg">
                                                         <i class="fa fa-circle fa-stack-2x"></i>
                                                         <i class="fa fa-archive fa-stack-1x fa-inverse"></i>
                                                       </span>', $uc, 'archiver' . ucfirst($name_entity), 'desarchiver', $name_entity . '-' . $entity->getId());
            }
        }
        if (isset($_SESSION['connected_user']) && Workbench::isAllowed($_SESSION['connected_user'], USER_STATUS_ADMIN)) {
            $return .= self::DisplayLinkIcon($entity->getId(), $name_entity, 'link-red', '<i class="fa fa-2x fa-trash" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;', $uc, 'supprimer' . ucfirst($name_entity), null, $name_entity . '-' . $entity->getId());
        }
        return $return;
    }

    //</editor-fold>
    //<editor-fold defaultstate="collapsed" desc="REGION AFFICHAGE PAGINATION">

    /**
     * displayPagination Affiche des boutons de paginations en fonction du tableau donné en paramètres
     * @param array() $array : Le tableau des éléments à paginer
     * @param int $cut : Le nombre d'élément affiché par page
     * @param string $uc : L'use case vers lequel on doit rediriger l'utilisateur lors du clique
     * @param string $action
     * @param string $option
     * @param string $callback
     */
    public static function displayPagination($array, $cut, $uc, $action, $option, $callback, $selected) {
        $render = "<ul class='pager'>";
        $nbPage = count($array) / $cut;
        $limitBegin = 0;
        $key = 'pagination-group-' . $cut . $uc . $action . $option . $callback; // la clé identifiant le groupe de pagination;
        for ($i = 0; $i < $nbPage; $i++) {
            $class = ($i == $selected) ? '' : '';
            $render .= "<li id='" . $key . "' class='" . $class . "'>"
                    . "<a onclick=\"var table={'limit_begin':" . $limitBegin . ",'limit_end':" . $cut . ", 'selected':" . $i . "};"
                    . "sendToAjax('" . $uc . "', '" . $action . "', '" . $option . "', table, '#" . $callback . "', true, 'Affichage en cours')"
                    . "\">" . ($i + 1) . "</a>"
                    . "</li>";
            $limitBegin += $cut;
        }
        $render .= "</ul>";
        return $render;
    }

    //</editor-fold>
}
