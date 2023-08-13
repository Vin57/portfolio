<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * Bll | Bll.lib.php - Librairie de classes logiques métiers (Business Logic Layer)
 *
 * 	@author :
 * 	@date : 7 févr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

namespace Modele\Bll;

/**
 * Les classes d'accés aux données
 */
use Modele\Dal\ArticleDal;
use Modele\Dal\TagDal;
use Modele\Dal\ProjetDal;
use Modele\Dal\SectionDal;
use Modele\Dal\MembreDal;
use Modele\Dal\RessourceDal;
use Modele\Dal\TypeProjetDal;
use Modele\Dal\InteretDal;
use Modele\Dal\GradeDal;
use Modele\Dal\CompetenceDal;
/*
 * Les classes de fonctions générique
 */
use Utilities;
/**
 * Les classes de référence
 */
use Modele\Reference\Article;
use Modele\Reference\Tag;
use Modele\Reference\Projet;
use Modele\Reference\Section;
use Modele\Reference\Ressource;
use Modele\Reference\Membre;
use Modele\Reference\TypeProjet;
use Modele\Reference\Interet;
use Modele\Reference\Grade;
use Modele\Reference\Competence;

/**
 * Fonctions métiers pour les données Articles
 * @author : vincp
 */
class Articles {

    /**
     * articleExiste Indique si le projet existe
     * @param int $idArticle : L'identifiant de l'article
     * @return boolean Retourne : true => L'article existe, false => L'article n'existe pas
     */
    public static function articleExiste($idArticle) {
        return self::chargerArticleParId($idArticle) != false;
    }

    /**
     * chargerLesArticles Charge tous les articles de la BDD et les renvoies dans un tableau d'objet
     * @return mixed Retourne un tableau d'objet Article si le chargement réussit, si la requête échoue, renvoie false
     */
    public static function chargerLesArticles() {
        $lesArticles = self::intoArrayObjArticles(ArticleDal::loadArticle());
        return $lesArticles;
    }

    /**
     * chargerArticlesParDate Charge tous les articles de la BDD d'une certaine date et les renvoies dans un tableau d'objet
     * @param int $mois : Le chiffre du mois 
     * @param int $annee : L'année en chiffre
     * @return mixed Retourne : Un tableau d'objet article => Si le chargement réussi, false => Le chargement échoue
     */
    public static function chargerArticlesParDate($mois, $annee) {
        $lesArticles = self::intoArrayObjArticles(ArticleDal::loadArticle(array($mois, $annee)));
        return $lesArticles;
    }
    
    /**
     * chargerArticlesParTitre Charge tous les articles de la BDD ayant un certain titre et les renvoies dans un tableau d'objet
     * @param string $titre : Un titre à rechercher
     * @param bool $exactMatch : true => On recherche le titre exact, false => Le titre doit contenir [@param1]
     * @param int $limit : Le nombre maximal de résultats proposé
     * @return mixed Retourne : Un tableau d'objet article => Si le chargement réussi, false => Le chargement échoue
     */
    public static function chargerArticlesParTitre($titre, $exactMatch, $limit = 5) {
        $lesArticles = self::intoArrayObjArticles(ArticleDal::loadArticlesByTitle($titre, $exactMatch, $limit));
        return $lesArticles;
    }

    /**
     * listerArticle Retourne une liste d'articles
     * @param int $limitBegin (facultatif, défaut = false): La limite de début
     * @param int $limitEnd (facultatif, défaut = false): La limite de fin
     * @param string $orderBy (facultatif, défaut = ""): La colone sur laquel ordonné le jeu de caractére, suivi de l'option DESC ou ASC
     * @return array(\)
     */
    public static function listerArticle($limitBegin = false, $limitEnd = false) {
        return self::intoArrayObjArticles(ArticleDal::listArticles($limitBegin, $limitEnd));
    }

    /**
     * supprimerArticle Supprimer le article dont l'identifiant est donné en paramétre
     * @param int $idArticle : L'identifiant du article
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function supprimerArticle($idArticle) {
        return ArticleDal::deleteArticle($idArticle);
    }

    /**
     * archiveArticle Archive un article (celui-ci ne serat plus visible que par les administrateurs et le super administrateur)
     * @param int $idArticle : L'identifiant de l'article à archiver
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function archiverArticle($idArticle) {
        return ArticleDal::archiveArticle($idArticle);
    }

    /**
     * desarchiveArticle Désarchive un article
     * @param int $idArticle : L'identifiant de l'article à archiver
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function desarchiveArticle($idArticle) {
        return ArticleDal::unArchiveArticle($idArticle);
    }

    /**
     * chargerArticlesParTags Charge les articles par tags
     * @param Array(\int) $arrayTag Un tableau d'identifiant de tag
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function chargerArticlesParTags($arrayIdTag) {
        $lesArticles = self::intoArrayObjArticles(ArticleDal::loadArticlesByTags($arrayIdTag));
        return $lesArticles;
    }

    public static function chargerArticleParId($id) {
        $lArticle = self::intoObjArticle(ArticleDal::loadArticle(array($id))[0]);
        return $lArticle;
    }

    /**
     * ajouterArticle Ajoute un article et retourne un objet de cette article si l'opération réussi
     * @param string $titre : Le titre de l'article
     * @param string $texte : Le texte de l'article
     * @param date $date_creation : La date de la création de l'article
     * @param int $interet : L'intérêt de l'article
     * @param string $lien : le Lien de l'article
     * @param array $les_tags : Les tags de cette article
     * @return bool Retourne : Un objet article => si l'ajout réussi, false => en cas d'échec
     */
    public static function ajouterArticle($titre, $texte, $date_creation, $interet, $lien, $les_tags, $id_auteur) {
        $result = ArticleDal::addArticle($titre, $texte, $date_creation, $interet, $lien, $les_tags, $id_auteur);
        if (is_numeric($result)) {
            return self::chargerArticleParId($result);
        } else {
            return false;
        }
    }

    /**
     * modifierArticle Modifie un article et retourne un objet de cette article si l'opération réussi
     * @param string $titre Le titre de l'article
     * @param string $texte Le texte de l'article
     * @param date $date_creation La date de la création de l'article
     * @param int $interet L'intérêt de l'article
     * @param string $lien le Lien de l'article
     * @param array $les_tags Les tags de cette article
     * @return bool Retourne : Un objet article => si la modification réussi, false => Si la modification échoue
     */
    public static function modifierArticle($idArticle, $titre, $texte, $date_creation, $interet, $lien, $tbObjetTag) {
        $result = ArticleDal::updateArticle($idArticle, $titre, $texte, $date_creation, $interet, $lien, $tbObjetTag);
        if (is_numeric($result)) {
            return self::chargerArticleParId($result);
        } else {
            return false;
        }
    }

    /**
     * articleEstFavorisDuMembre Indique si l'article est l'un des articles favoris du membre
     * @param int $id_member : L'identifiant du membre
     * @param int $idArticle : L'identifiant de l'article
     * @return boolean Retourne : true => Si l'article est bien l'un des articles favoris du membre, false => sinon
     */
    public static function articleEstFavorisDuMembre($id_member, $idArticle) {
        return ArticleDal::articleIsFavOfMember($id_member, $idArticle)[0][0];
    }

    /**
     * articleFavorisParMembre Récupére les articles favoris du membre
     * @param int $id_membre : L'identifiant du membre
     * @return Array(\Article) Retourne : Un tableau d'article
     */
    public static function articleFavorisParMembre($id_membre) {
        return self::intoArrayObjArticles(ArticleDal::loadArticleFavByMember($id_membre));
    }

    /**
     * Converti le résultat d'une requête en un objet Article et ajoute les tags à l'article
     * @param array $article le résultat d'une requête que l'on souhaite transformer en objet de type Article
     * @param int $mode 0 => <i>$article</i> est un tableau associatif, 1 => <i>$article</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $article ne peux pas être converti en Article, sinon retourne un objet de la classe Article
     */
    public static function intoObjArticle($article, $mode = 0) {
        if ($article) {
            // Si l'article provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($article[0]) && is_array($article[0])) {
                $article = $article[0];
            }
            $tags = ($mode == 0) ? Tags::chargerTagsByArticle($article[0]) : Tags::chargerTagsByArticle($article->id_article);
            $interet = ($mode == 0) ? Interets::chargerInteretParId($article[4]) : Interets::chargerInteretParId($article->niveau_interet);
            $auteur = ($mode == 0) ? Membres::chargerMembreParArticle($article[0]) : Membres::chargerMembreParArticle($article->id_article);
            if (Utilities::isCorrectReturnRequest($article)) {
                switch ($mode) {
                    case 0:
                        $article = new Article($article[0], $article[1], $article[2], $article[3], $interet, $article[5], $tags, $article[6], $auteur);
                        break;
                    default:
                        $article = new Article($article->id_article, $article->titre, $article->texte, $article->date_creation, $interet, $article->lien, $tags, $article->archive, $auteur);
                        break;
                }
            }
        }
        return $article;
    }

    /**
     * Converti le résultat d'une requête en un tableau d'objet Article 
     * @param array $tbArticle un tableau de résultat de requête
     * @param int $mode 0 => <i>$article</i> est un tableau associatif, 1 => <i>$article</i> est un tableau d'objet
     * @return mixed
     */
    public static function intoArrayObjArticles($tbArticle, $mode = 0) {
        if ($tbArticle) {
            if (Utilities::isCorrectReturnRequest($tbArticle)) {
                $resArticle = array();
                foreach ($tbArticle as $article) {
                    $resArticle[] = self::intoObjArticle($article, $mode);
                }
                $tbArticle = $resArticle;
            }
        }
        return $tbArticle;
    }

}

/**
 * Fonctions métier pour les données Interet
 * @author vincp
 */
class Interets {

    /**
     * chargerInteretParId Charge un objet \Interet par son identifiants
     * @param int $id_interet : L'identifiant de l'intérêt
     * @return \Interet Retourne : Un objet \Interet
     */
    public static function chargerInteretParId($id_interet) {
        return self::intoObjInteret(InteretDal::loadInteretById($id_interet));
    }

    /**
     * intoObjInteret Converti le résultat d'une requête en un objet Interet
     * @param array $interet le résultat d'une requête que l'on souhaite transformer en objet de type Interet
     * @param int $mode 0 => <i>$interet</i> est un tableau associatif, 1 => <i>$interet</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $interet ne peux pas être converti en Interet, sinon retourne un objet de la classe Interet
     */
    private static function intoObjInteret($interet, $mode = 0) {
        if ($interet) {
            // Si l'interet provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($interet[0]) && is_array($interet[0])) {
                $interet = $interet[0];
            }
            if (Utilities::isCorrectReturnRequest($interet)) {
                switch ($mode) {
                    case 0:
                        $interet = new Interet($interet[0], $interet[1]);
                        break;
                    default:
                        $interet = new Interet($interet->id_interet, $interet->nom_interet);
                        break;
                }
            }
        }
        return $interet;
    }

    /**
     * intoArrayObjInterets Converti le résultat d'une requête en un tableau d'objet Interet
     * @param array $tbInteret un tableau de résultat de requête
     * @param int $mode 0 => <i>$interet</i> est un tableau associatif, 1 => <i>$interet</i> est un tableau d'objet
     * @return mixed
     */
    private static function intoArrayObjInteret($tbInteret, $mode = 0) {
        if ($tbInteret) {
            if (Utilities::isCorrectReturnRequest($tbInteret)) {
                $resInteret = array();
                foreach ($tbInteret as $interet) {
                    $resInteret[] = self::intoObjInteret($interet, $mode);
                }
                $tbInteret = $resInteret;
            }
        }
        return $tbInteret;
    }

}

class Competences {

    /**
     * chargerCompetence Charge toutes les compétences de la BDD
     * @return array(\Competence) Retourne : Un tableau d'objet Compétence
     */
    public static function chargerCompetences() {
        $tbCompetence = CompetenceDal::loadCompetence();
        if ($tbCompetence) {
            if (Utilities::isCorrectReturnRequest($tbCompetence)) {
                return $tbCompetence;
            }
        }
        return false;
    }

    /**
     * chargerCompetenceParProjet Charge les compétences d'un projet
     * @param int $idProjet : L'identifiant du projet
     * @return Array(\Competence) Retourne : Un tableau d'objets \Competence
     */
    public static function chargerCompetenceParProjet($idProjet) {
        return self::intoArrayObjCompetence(CompetenceDal::loadCompetenceByProjet($idProjet));
    }

    /**
     * chargerCompetenceParNom Charge une compétence par son nom
     * @param string $nom_competence : Le nom de la compétence
     * @return Array(\Competence) Retourne : Un tableau d'objets \Competence
     */
    public static function chargerCompetenceParNom($nom_competence) {
        return self::intoObjCompetence(CompetenceDal::loadCompetenceByNom($nom_competence));
    }

    /**
     * chargerCompetenceParId Charge un objet \Competence par son identifiants
     * @param int $id_competence : L'identifiant de l'intérêt
     * @return \Competence Retourne : Un objet \Competence
     */
    public static function chargerCompetenceParId($id_competence) {
        return self::intoObjCompetence(CompetenceDal::loadCompetenceById($id_competence));
    }

    /**
     * intoObjCompetence Converti le résultat d'une requête en un objet Competence
     * @param array $competence le résultat d'une requête que l'on souhaite transformer en objet de type Competence
     * @param int $mode 0 => <i>$competence</i> est un tableau associatif, 1 => <i>$competence</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $competence ne peux pas être converti en Competence, sinon retourne un objet de la classe Competence
     */
    private static function intoObjCompetence($competence, $mode = 0) {
        if ($competence) {
            // Si la competence provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($competence[0]) && is_array($competence[0])) {
                $competence = $competence[0];
            }
            if (Utilities::isCorrectReturnRequest($competence)) {
                switch ($mode) {
                    case 0:
                        $competence = new Competence($competence[0], $competence[1]);
                        break;
                    default:
                        $competence = new Competence($competence->id_competence, $competence->nom_competence);
                        break;
                }
            }
        }
        return $competence;
    }

    /**
     * intoArrayObjCompetences Converti le résultat d'une requête en un tableau d'objet Competence
     * @param array $tbCompetence un tableau de résultat de requête
     * @param int $mode 0 => <i>$competence</i> est un tableau associatif, 1 => <i>$competence</i> est un tableau d'objet
     * @return mixed
     */
    private static function intoArrayObjCompetence($tbCompetence, $mode = 0) {
        if ($tbCompetence) {
            if (Utilities::isCorrectReturnRequest($tbCompetence)) {
                $resCompetence = array();
                foreach ($tbCompetence as $competence) {
                    $resCompetence[] = self::intoObjCompetence($competence, $mode);
                }
                $tbCompetence = $resCompetence;
            }
        }
        return $tbCompetence;
    }

}

/**
 * Fonctions métier pour les données Tags
 * @author vincp
 */
class Tags {

    public static function chargerLesTags() {
        return self::intoArrayObjTags(TagDal::loadTag());
    }

    public static function chargerTagsByArticle($idArticle) {
        return self::intoArrayObjTags(TagDal::loadTagsByArticle($idArticle));
    }

    public static function chargerTagsParProjet($idProjet) {
        return self::intoArrayObjTags(TagDal::loadTagsByProjet($idProjet));
    }

    public static function chargerTagById($id_tag) {
        return self::intoObjTag(TagDal::loadTag(array($id_tag))[0]);
    }

    public static function chargerTag($array = array()) {
        $result = TagDal::loadTag($array);
        if ($result) {
            if (is_array($result)) {
                return self::intoArrayObjTags($result);
            } else {
                return self::intoObjTag($result);
            }
        }
        return false;
    }

    /**
     * chargerTagByName charge les tags par nom
     * @param string $name le nom du tag
     * @return un objet tag si son chargement à réussit, sinon retourne false
     */
    public static function chargerTagByName($name) {
        $result = TagDal::loadTag(array($name));
        if ($result) {
            return self::intoObjTag($result[0]);
        }
        return false;
    }

    public static function lierTagArticle($idArticle, $id_tag) {
        return self::intoObjTag(TagDal::joinTagArticle($idArticle, $id_tag));
    }

    public static function chercherTag($search) {
        return self::intoArrayObjTags(TagDal::searchTag($search));
    }

    public static function ajouterTag($nom_tag, $return_tag = false) {
        $result = false;
        if (!self::tagExiste($nom_tag)) {
            $result = TagDal::addTag($nom_tag);
            if ($result) {
                if ($return_tag) {
                    return self::intoObjTag(TagDal::loadTag(array(), true));
                }
            }
        }
        return $result;
    }

    /**
     * tagExiste Indique si un tag existe ou non
     * @param string $nom_tag : Le nom du tag
     * @param boolean $return_tag : true -> Retourne l'objet tag trouver, si celui-ci existe,
     * @return mixed Retourne : true (ou bien le tag si l'on à choisit de retourner le tag) si le tag est trouver, sinon false
     */
    public static function tagExiste($nom_tag, $return_tag = false) {
        $result = TagDal::tagExists($nom_tag);
        if ($result) {
            if ($return_tag) {
                return self::intoObjTag(TagDal::loadTag(array($nom_tag)));
            }
            return true;
        }
        return false;
    }

    /**
     * intoObjTag Converti le résultat d'une requête en un objet Tag
     * @param array $tag le résultat d'une requête que l'on souhaite transformer en objet de type Tag
     * @param int $mode 0 => <i>$tag</i> est un tableau associatif, 1 => <i>$tag</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $tag ne peux pas être converti en Tag, sinon retourne un objet de la classe Tag
     */
    private static function intoObjTag($tag, $mode = 0) {
        if ($tag) {
            // Si le tag provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($tag[0][0]) && is_array($tag[0])) {
                $tag = $tag[0];
            }
            if (Utilities::isCorrectReturnRequest($tag)) {
                switch ($mode) {
                    case 0:
                        $tag = new Tag($tag[0], $tag[1]);
                        break;
                    default:
                        $tag = new Tag($tag->id_tag, $tag->nom_tag);
                        break;
                }
            }
        }
        return $tag;
    }

    /**
     * intoArrayObjTags Converti le résultat d'une requête en un tableau d'objet Tag
     * @param array $tbTag un tableau de résultat de requête
     * @param int $mode 0 => <i>$tag</i> est un tableau associatif, 1 => <i>$tag</i> est un tableau d'objet
     * @return mixed
     */
    private static function intoArrayObjTags($tbTag, $mode = 0) {
        if ($tbTag) {
            if (Utilities::isCorrectReturnRequest($tbTag)) {
                $resTag = array();
                foreach ($tbTag as $tag) {
                    $resTag[] = self::intoObjTag($tag, $mode);
                }
                $tbTag = $resTag;
            }
        }
        return $tbTag;
    }

}

class Projets {

    /**
     * projetExiste Indique si le projet existe
     * @param int $idProjet : L'identifiant du projet
     * @return boolean Retourne : true => Le projet existe, false => sinon
     */
    public static function projetExiste($idProjet) {
        return ProjetDal::loadProjet(array($idProjet)) != false;
    }

    /**
     * loadTag Charge un ou plusieurs projets en fonction des paramétres fournit par le tableau
     */
    public static function chargerLesProjets() {
        return self::intoArrayObjProjet(ProjetDal::loadProjet());
    }

    public static function chargerProjetParId($id) {
        return self::intoObjProjet(ProjetDal::loadProjet(array($id)));
    }

    /**
     * supprimerProjet Supprime le projet dont l'identifiant est donné en paramétre
     * @param int $idProjet : L'identifiant du projet
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function supprimerProjet($idProjet) {
        return ProjetDal::deleteProjet($idProjet);
    }

    /**
     * archiverProjet Archive le projet dont l'identifiant est donné en paramétre
     * @param int $idProjet : L'identifiant du projet
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function archiverProjet($idProjet) {
        return ProjetDal::archiveProjet($idProjet);
    }

    /**
     * desarchiverProjet Désarchive le projet dont l'identifiant est donné en paramétre
     * @param int $idProjet : L'identifiant du projet
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function desarchiverProjet($idProjet) {
        return ProjetDal::unArchiveProjet($idProjet);
    }

    /**
     * rechercherProjet Recherche un projet par son nom et par le contenu de sa déscription
     * @param string $search : La valeurs à rechercher
     * @return Array(\Projet) Retourne un tableau d'objet projet correspondant à la recherche, false = > en cas d'erreur
     */
    public static function rechercherProjet($search) {
        return self::intoArrayObjProjet(ProjetDal::searchProjet($search));
    }

    /**
     * modifierProjet Met à jour un projet dans la BDD
     * @param int $idProjet : L'identifiant du projet
     * @param string $nom : Le nom du projet
     * @param date $date : La date du projet
     * @param string $description : La description du projet
     * @param int $type : Le type du projet
     * @param Array(\Tag) $tbObjetTag : Le tableau des tags devant être affecté au projet
     * @param Array(\Competence) $tbObjetCompetence : Le tableau des compétences devant être affecté au projet
     * @return mixed Retourne : Le projet => Si la modification réussie, false => En cas d'échec de la modification
     */
    public static function modifierProjet($idProjet, $nom, $date, $description, $type, $tbObjetTag, $tbObjetCompetence) {
        $result = ProjetDal::updateProjet($idProjet, $nom, $date, $description, $type, $tbObjetTag, $tbObjetCompetence);
        if (is_numeric($result) && $result > 0) {
            return self::chargerProjetParId($result);
        } else {
            return false;
        }
    }

    /**
     * listerProjet Retourne une liste de projets
     * @param int $limitBegin (facultatif, défaut = false): La limite de début
     * @param int $limitEnd (facultatif, défaut = false): La limite de fin
     * @param string $orderBy (facultatif, défaut = ""): La colone sur laquel ordonné le jeu de caractére, suivi de l'option DESC ou ASC
     * @return array(\)
     */
    public static function listerProjets($limitBegin = false, $limitEnd = false) {
        return self::intoArrayObjProjet(ProjetDal::listProjets($limitBegin, $limitEnd));
    }

    /**
     * ajouterProjet Ajoute un projet dans la BDD
     * @param int $idProjet : L'identifiant du projet
     * @param string $nom : Le nom du projet
     * @param date $date : La date du projet
     * @param string $description : La description du projet
     * @param int $type : Le type du projet
     * @param Array(\Tag) $tbObjetTag : Le tableau des tags devant être affecté au projet
     * @param Array(\Competence) $tbObjetCompetence : Le tableau des compétences devant être affecté au projet
     * @return mixed Retourne : Le projet => Si l'ajout réussie, false => En cas d'échec de l'ajout
     */
    public static function ajouterProjet($nom, $date, $description, $type, $tbObjetTag, $tbObjetCompetence) {
        $result = ProjetDal::addProjet($nom, $date, $description, $type, $tbObjetTag, $tbObjetCompetence);
        if (is_numeric($result) && $result > 0) {
            return self::chargerProjetParId($result);
        } else {
            return false;
        }
    }

    /**
     * intoObjProjet Converti le résultat d'une requête en un objet Projet
     * @param Array(\Tag) $projet : Le résultat d'une requête que l'on souhaite transformer en objet de type $projet
     * @param int $mode : 0 => <i>$projet</i> est un tableau associatif,
     *                    1 => <i>$projet</i> est un tableau d'objet anonyme dont chacune des propriétées est contenue dans une colone
     * @return mixed Retourne : false si le tableaux $projet ne peux pas être converti en Projet, sinon retourne un objet de la classe $projet
     */
    private static function intoObjProjet($projet, $mode = 0) {
        if ($projet) {
            // Si le projet provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($projet[0][0]) && is_array($projet[0])) {
                $projet = $projet[0];
            }
            $tags = ($mode == 0) ? Tags::chargerTagsParProjet($projet[0]) : Tags::chargerTagsParProjet($projet->id_projet);
            $competences = ($mode == 0) ? Competences::chargerCompetenceParProjet($projet[0]) : Competences::chargerCompetenceParProjet($projet->id_projet);
            $typeProjet = ($mode == 0) ? TypeProjets::chargerTypeParId($projet[4]) : TypeProjets::chargerTypeParId($projet->id_type_projet);
            if (Utilities::isCorrectReturnRequest($projet)) {
                switch ($mode) {
                    case 0:
                        $projet = new Projet($projet[0], $projet[1], $projet[2], $projet[3], $typeProjet[0], $tags, $competences, $projet[5]);
                        break;
                    default:
                        $projet = new Projet($projet->id_projet, $projet->nom_projet, $projet->date_projet, $projet->description, $typeProjet[0], $tags, $competences, $projet->archive);
                        break;
                }
            }
        }
        return $projet;
    }

    /**
     * intoArrayObjProjet Converti le résultat d'une requête en un tableau d'objet Projet
     * @param array $tbProjet : Un tableau de résultat de requête
     * @param int $mode : 0 => <i>$tbProjet</i> est un tableau associatif,
     *                    1 => <i>$tbProjet</i> est un tableau d'objet
     * @return mixed Retourne : false si le tableau ne peux être converti
     */
    private static function intoArrayObjProjet($tbProjet, $mode = 0) {
        if ($tbProjet) {
            if (Utilities::isCorrectReturnRequest($tbProjet)) {
                $resProjet = array();
                foreach ($tbProjet as $projet) {
                    $resProjet[] = self::intoObjProjet($projet, $mode);
                }
                $tbProjet = $resProjet;
            }
        }
        return $tbProjet;
    }

}

class Ressources {

    public static function chargerRessource() {
        return self::intoArrayObjRessource(RessourceDal::loadRessource());
    }

}

class TypeProjets {

    public static function chargerTypeParId($id) {
        return self::intoArrayObjType(TypeProjetDal::loadTypeProjet($id));
    }

    /**
     * intoObjType Converti le résultat d'une requête en un objet Type
     * @param array $tag le résultat d'une requête que l'on souhaite transformer en objet de type TypeProjet
     * @param int $mode 0 => <i>$tag</i> est un tableau associatif, 1 => <i>$type</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $type ne peux pas être converti en Tag, sinon retourne un objet de la classe TypeProjet
     */
    private static function intoObjType($type, $mode = 0) {
        if ($type) {
            if (isset($type[0][0]) && is_array($type[0])) {
                $type = $type[0];
            }
            if (Utilities::isCorrectReturnRequest($type)) {
                switch ($mode) {
                    case 0:
                        $type = new TypeProjet($type[0], $type[1]);
                        break;
                    default:
                        $type = new TypeProjet($type->id_type_projet, $type->lib_type_projet);
                        break;
                }
            }
        }
        return $type;
    }

    /**
     * intoArrayObjProjet Converti le résultat d'une requête en un tableau d'objet Projet
     * @param array $tbProjet : Un tableau de résultat de requête
     * @param int $mode : 0 => <i>$tbProjet</i> est un tableau associatif,
     *                    1 => <i>$tbProjet</i> est un tableau d'objet
     * @return mixed Retourne : false si le tableau ne peux être converti
     */
    private static function intoArrayObjType($tbType, $mode = 0) {
        if ($tbType) {
            if (Utilities::isCorrectReturnRequest($tbType)) {
                $resType = array();
                foreach ($tbType as $type) {
                    $resType[] = self::intoObjType($type, $mode);
                }
                $tbType = $resType;
            }
        }
        return $tbType;
    }

}

class Membres {

    public static function ajouterMembre($pseudo, $password, $grade = 2) {
        $result = MembreDal::addMember($pseudo, $password, $grade);
        return $result;
    }

    public static function chargerMembreParPseudo($pseudo) {
        $result = self::intoObjMembre(MembreDal::loadMemberByPseudo($pseudo));
        return $result;
    }

    public static function chargerMembreParArticle($idArticle) {
        $result = self::intoObjMembre(MembreDal::loadMemberByArticle($idArticle));
        return $result;
    }

    /**
     * loadMemberById Charge un membre par identifiant
     * @param int $id_membre : L'identifiant d'un potentiel membre
     * @return mixed mixed Retourne : Un objet membre => en cas de succés, false => En cas d'échec
     */
    public static function chargerMembreParId($id_membre) {
        sleep(0);
        return self::intoObjMembre(MembreDal::loadMemberById($id_membre));
    }

    public static function pseudoExiste($pseudo) {
        $result = false;
        if (!empty($pseudo)) {
            $result = MembreDal::pseudoExists($pseudo);
        }
        return $result;
    }

    /**
     * projetExiste Indique si le projet existe
     * @param int $idProjet : L'identifiant du projet
     * @return boolean Retourne : true => Le projet existe, false => sinon
     */
    public static function membreExiste($id_membre) {
        return self::chargerMembreParId($id_membre) != false;
    }

    /**
     * ajouterArticleFavoris Ajoute un article favoris à un membre
     * @param int $id_membre : L'identifiant du membre
     * @param int $idArticle : L'identifiant de l'article
     * @return mixed Retourne : Le résultat de la requête => La requête à réussie, false => La requête à échoué
     */
    public static function ajouterArticleFavoris($id_membre, $idArticle) {
        return MembreDal::addArticleFav($id_membre, $idArticle);
    }

    /**
     * retirerArticleFavoris Retirer un article favoris à un membre
     * @param int $id_membre : L'identifiant du membre
     * @param int $idArticle : L'identifiant de l'article
     * @return mixed Retourne : Le résultat de la requête => La requête à réussie, false => La requête à échoué
     */
    public static function retirerArticleFavoris($id_membre, $idArticle) {
        return MembreDal::removeArticleFav($id_membre, $idArticle);
    }
    
    /**
     * modifierPseudo Met à jour le pseudo d'un membre
     * @param int $id_membre : L'identifiant du membre
     * @param string $pseudo : pseudo du membre
     * @return boolean Retourne : true => La modification à bien été effectuée, false => La modification à échouée
     */
    public static function modifierPseudo($id_membre, $pseudo){
        return MembreDal::updatePseudo($id_membre, $pseudo);
    }
    
    /**
     * modifierPassword Met à jour le mot de passe d'un membre
     * @param int $id_membre : L'identifiant du membre
     * @param string $password : mot de passe du membre
     * @return boolean Retourne : true => La modification à bien été effectuée, false => La modification à échouée
     */
    public static function modifierPassword($id_membre, $password){
        return MembreDal::updatePassword($id_membre, $password);
    }

    /**
     * intoObjMembre Converti le résultat d'une requête en un objet Membre
     * @param array $membre le résultat d'une requête que l'on souhaite transformer en objet de type Membre
     * @param int $mode 0 => <i>$membre</i> est un tableau associatif, 1 => <i>$membre</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $membre ne peux pas être converti en Membre, sinon retourne un objet de la classe Membre
     */
    private static function intoObjMembre($membre, $mode = 0) {
        if ($membre && !empty($membre)) {
            // Si le membre provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($membre[0][0]) && is_array($membre[0])) {
                $membre = $membre[0];
            }
            $grade = ($mode == 0) ? Grades::chargerGradeParId($membre[3]) : Grades::chargerGradeParId($membre->grade);
            if (Utilities::isCorrectReturnRequest($membre)) {
                switch ($mode) {
                    case 0:
                        $membre = new Membre($membre[0], $membre[1], $membre[2], $grade);
                        break;
                    default:
                        $membre = new Membre($membre->id_membre, $membre->pseudo, $membre->mdp, $grade);
                        break;
                }
            }
        }
        return $membre;
    }

    /**
     * intoArrayObjMembre Converti le résultat d'une requête en un tableau d'objet Membre
     * @param array $tbMembre : Un tableau de résultat de requête
     * @param int $mode : 0 => <i>$tbMembre</i> est un tableau associatif,
     *                    1 => <i>$tbMembre</i> est un tableau d'objet
     * @return mixed Retourne : false si le tableau ne peux être converti
     */
    private static function intoArrayObjMembre($tbMembre, $mode = 0) {
        if ($tbMembre) {
            if (Utilities::isCorrectReturnRequest($tbMembre)) {
                $resMembre = array();
                foreach ($tbMembre as $membre) {
                    $resMembre[] = self::intoObjMembre($tbMembre, $mode);
                }
                $tbMembre = $resMembre;
            }
        }
        return $tbMembre;
    }

}

class Grades {

    public static function chargerGradeParId($id_grade) {
        $result = self::intoObjGrade(GradeDal::loadGradeById($id_grade));
        return $result;
    }

    /**
     * intoObjGrade Converti le résultat d'une requête en un objet Grade
     * @param array $grade le résultat d'une requête que l'on souhaite transformer en objet de type Grade
     * @param int $mode 0 => <i>$grade</i> est un tableau associatif, 1 => <i>$grade</i> est un tableau d'objet
     * @return mixed retourne false si le tableaux $grade ne peux pas être converti en Grade, sinon retourne un objet de la classe Grade
     */
    private static function intoObjGrade($grade, $mode = 0) {
        if ($grade) {
            // Si le grade provient directement d'une requête retournant son résultat dans un tableau multidimensionel
            if (isset($grade[0][0]) && is_array($grade[0])) {
                $grade = $grade[0];
            }
            if (Utilities::isCorrectReturnRequest($grade)) {
                switch ($mode) {
                    case 0:
                        $grade = new Grade($grade[0], $grade[1]);
                        break;
                    default:
                        $grade = new Grade($grade->id_grade, $grade->nom_grade);
                        break;
                }
            }
        }
        return $grade;
    }

    /**
     * intoArrayObjGrades Converti le résultat d'une requête en un tableau d'objet Grade
     * @param array $tbGrade un tableau de résultat de requête
     * @param int $mode 0 => <i>$grade</i> est un tableau associatif, 1 => <i>$grade</i> est un tableau d'objet
     * @return mixed
     */
    private static function intoArrayObjGrades($tbGrade, $mode = 0) {
        if ($tbGrade) {
            if (Utilities::isCorrectReturnRequest($tbGrade)) {
                $resGrade = array();
                foreach ($tbGrade as $grade) {
                    $resGrade[] = self::intoObjGrade($grade, $mode);
                }
                $tbGrade = $resGrade;
            }
        }
        return $tbGrade;
    }

}
