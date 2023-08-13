<?php

/**
 * portfolio - portfolio
 * © vincp, 2017
 * Dal | Dal.lib.php - Librairies de classes d'accés aux données (Data Access Layer)
 *
 * 	@author : vincp
 * 	@date : 7 févr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

namespace Modele\Dal;

use Modele\Data\Database;

/**
 * Accés aux données des artciles
 * @author : vincp
 */
class ArticleDal {

    /**
     * loadArticle Est une fonction générique de chargement d'article(s).
     * Elle renvoie soit un tableau d'articles, soit un article en particulier
     * @param array $table
     * @return type
     */
    public static function loadArticle($table = array(), $last = false) {
        $bdd = Database::connect();
        $rq = "";
        $param = array();
        switch (count($table)) {
            case 1:#Cherche un article par son identifiant
                $rq = "SELECT * FROM article WHERE id_article = ?";
                $param = array($table[0]);
                break;
            case 2:#Cherche tous les articles sortie lors du mois de l'année renseigné
                $rq = "CALL sp_load_article_date(?,?)";
                $param = array($table[0], $table[1]);
                break;
            default:#Charge tous les articles
                if ($last) {
                    $rq = "SELECT MAX(id_article) FROM article";
                } else {
                    $rq = "SELECT * FROM article WHERE archive = 0 ORDER BY date_creation DESC";
                }
                $param = array();
                break;
        }
        try {
            $result = $bdd->getData($rq, $param);
            return $result;
        } catch (PDOException $ex) {
            return false;
        }
    }

    /**
     * loadArticlesByTags Charge tous les articles correspondant à l'identifiant du tag donné
     * @param Array(\int) $arrayIdTag : Un tableau d'identifiant de tag
     * @return array() Retourne : Le résultat de la requête sous forme d'un tableau associatif => Si le chargement réussi, false => Le chargement à échoué
     */
    public static function loadArticlesByTags($arrayIdTag) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM article
               INNER JOIN tag_article ON tag_article.id_art = article.id_article
               INNER JOIN tag ON tag_article.id_tag = tag.id_tag
               WHERE ";
        for ($i = 0; $i < count($arrayIdTag); $i++) {
            $rq .= "tag.id_tag = ?";
            if (!($i == count($arrayIdTag) - 1)) {
                $rq .= " AND ";
            }
        }
        $rq .= " ORDER BY article.date_creation DESC";
        $param = array($arrayIdTag);
        $result = $bdd->getData($rq, $param);
        return $result;
    }
    
    /**
     * loadArticlesByTitle Charge tous les articles de la BDD ayant un certain titre et les renvoies dans un tableau d'objet
     * @param string $title : Un titre à rechercher
     * @param bool $exactMatch : true => On recherche le titre exact, false => Le titre doit contenir [@param1]
     * @param int $limit : Le nombre maximal de résultats proposé
     * @return mixed Retourne : Un tableau d'objet article => Si le chargement réussi, false => Le chargement échoue
     */
    public static function loadArticlesByTitle($title, $exactMatch, $limit){
        $bdd = Database::connect();
        $rq = "CALL sp_search_titre(?,?,?)";
        $param = array($title, $exactMatch, $limit);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * deleteArticle Supprimer l'article dont l'identifiant est donné en paramétre
     * @param int $idArticle : L'identifiant de l'article
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function deleteArticle($idArticle) {
        $bdd = Database::connect();
        $bdd->beginTransaction();
        if (MembreDal::unJoinMembre($idArticle)) {
            try {
                $error = false;
                if (TagDal::unJoinTag($idArticle, TYPE_CONTENUE_ARTICLE)) { // On retire tous les tags de l'articles
                    $rq = "DELETE FROM calendrier_article WHERE id_article = ?";
                    $param = array($idArticle);
                    $unsetCalendar = $bdd->execSql($rq, $param);
                    if ($unsetCalendar) {
                        $rq = "DELETE FROM membre_article_fav WHERE id_article = ?";
                        $param = array($idArticle);
                        $unlinkFav = $bdd->execSql($rq, $param);
                        if ($unlinkFav) {
                            $rq = "DELETE FROM article WHERE id_article = ?";
                            $param = array($idArticle);
                            $result = $bdd->execSql($rq, $param);
                            if (!$result) {
                                $error = true;
                            }
                        } else {
                            
                        }
                    } else {
                        $error = true;
                    }
                    $bdd->commit();
                } else {
                    $error = true;
                }
            } catch (PDOException $e) {
                $error = true;
            }
        } else {
            $error = true;
        }
        if ($error) {
            $bdd->rollBack();
            return false;
        }
        return $result;
    }

    /**
     * archiveArticle Archive un article (celui-ci ne serat plus visible que par les administrateurs et le super administrateur)
     * @param int $idArticle : L'identifiant de l'article à archiver
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function archiveArticle($idArticle) {
        $bdd = Database::connect();
        $rq = "UPDATE article SET archive = 1 WHERE id_article = ?";
        $param = array($idArticle);
        $result = $bdd->execSql($rq, $param);
        return $result;
    }

    /**
     * unArchiveArticle Désarchive un article
     * @param int $idArticle : L'identifiant de l'article à archiver
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function unArchiveArticle($idArticle) {
        $bdd = Database::connect();
        $rq = "UPDATE article SET archive = 0 WHERE id_article = ?";
        $param = array($idArticle);
        $result = $bdd->execSql($rq, $param);
        return $result;
    }

    /**
     * addArticle Ajoute un article et éventuellemennt le lie au tags entrée dans le tableau en dernier paramétre
     * @param string $titre : Le titre de l'article
     * @param string $texte : Le texte de l'article
     * @param date $date_creation : La date de creation de l'article
     * @param int $interet : Le niveau d'intérêt de l'article
     * @param string $lien : Le lien de l'article
     * @param Array(\Tag) (facultatif, défaut = array()) $les_tags : Un tableau d'objet tag ou un tableau d'identifiant
     * @param int $id_auteur : L'identifiant de l'auteur de l'article
     * @return mixed Retourne : false => Si l'ajout échoue, l'identifiant de l'article venant d'être ajouté => Si l'ajout réussi
     */
    public static function addArticle($titre, $texte, $date_creation, $interet, $lien, $les_tags = array(), $id_auteur) {
        $error = false;
        $bdd = Database::connect();
        $bdd->beginTransaction();
        $rq = "INSERT INTO article(titre,texte,date_creation,niveau_interet,lien) VALUES(?,?,?,?,?)"; // l'id serat créé automatiquement
        $param = array($titre, $texte, $date_creation, $interet, $lien);
        try {
            $ajoutArticle = $bdd->execSQL($rq, $param);
            if ($ajoutArticle) {
                $idArticle = ArticleDal::loadArticle(array(), true)[0][0];
                foreach ($les_tags as $tag) {
                    if (!empty($tag)) {
                        if ($tag instanceof \Modele\Reference\Tag) {
                            if (!TagDal::joinTag($idArticle, $tag->getId())) {
                                $error = true;
                            }
                        } else {
                            if (!TagDal::joinTag($idArticle, $tag)) {
                                $error = true;
                            }
                        }
                    }
                }
            }
            if (!MembreDal::joinMembre($id_auteur, $idArticle)) {
                $error = true;
            }
            if (!$error) {
                $bdd->commit();
                return $idArticle;
            } else {
                $bdd->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $bdd->rollBack();
            return false;
        }
    }

    /**
     * updateArticle Modifie un article met à jour ses tags
     * @param string $titre : Le titre de l'article
     * @param string $texte : Le texte de l'article
     * @param date $date_creation : La date de creation de l'article
     * @param int $interet : Le niveau d'intérêt de l'article
     * @param string $lien : Le lien de l'article
     * @param Array(\Tag) (facultatif, défaut = array()) $tbObjetTag : Un tableau d'objet tag ou un tableau d'identifiant
     * @return mixed Retourne : false => Si la modification échoue, l'identifiant de l'article venant d'être ajouté => Si la modification réussi
     */
    public static function updateArticle($idArticle, $titre, $texte, $date_creation, $interet, $lien, $tbObjetTag = array()) {
        $bdd = Database::connect();
        $bdd->beginTransaction();
        $rq = "UPDATE article SET titre = ?,texte = ?,date_creation = ?,niveau_interet = ?,lien = ? WHERE id_article = ?";
        $param = array($titre, $texte, $date_creation, $interet, $lien, $idArticle);
        try {
            $ajoutArticle = $bdd->execSQL($rq, $param);
            if ($ajoutArticle) {
                if (TagDal::unJoinTag($idArticle, TYPE_CONTENUE_ARTICLE)) { // On retire tous les tags de l'articles
                    foreach ($tbObjetTag as $tag) {// pour les rattachers dans un nouvelle ordre
                        if (!empty($tag)) {
                            if ($tag instanceof \Modele\Reference\Tag) {
                                TagDal::joinTag($idArticle, $tag->getId(), TYPE_CONTENUE_ARTICLE);
                            }
                        }
                    }
                } else {
                    $bdd->rollBack();
                    return false;
                }
            }
            $bdd->commit();
            return $idArticle;
        } catch (PDOException $e) {
            $bdd->rollBack();
            return false;
        }
    }

    /**
     * loadArticleFavByMember Récupére les articles favoris du membre
     * @param int $id_membre : L'identifiant du membre
     * @return Array(\Article) Retourne : Un tableau d'article
     */
    public static function loadArticleFavByMember($id_member) {
        $bdd = Database::connect();
        $rq = "CALL sp_load_article_fav_by_member(?)";
        $param = array($id_member);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * articleIsFavOfMember Indique si l'article est l'un des articles favoris du membre
     * @param int $id_member : L'identifiant du membre
     * @param int $idArticle : L'identifiant de l'article
     * @return boolean Retourne : true => Si l'article est bien l'un des articles favoris du membre, false => sinon
     */
    public static function articleIsFavOfMember($id_member, $idArticle) {
        $bdd = Database::connect();
        $rq = "SELECT EXISTS(SELECT * FROM membre_article_fav WHERE id_membre = ? AND id_article = ?)";
        $param = array($id_member, $idArticle);
        $result = $bdd->getData($rq, $param);
        return $result;
    }
    
    public static function listArticles($limitBegin, $limitEnd){
        $bdd = Database::connect();
        $rq = "SELECT * FROM article WHERE archive = 0 ORDER BY date_creation DESC ";
        if (is_numeric($limitBegin)) {
            $rq .= "LIMIT " . $limitBegin . " ";
            if (is_numeric($limitBegin)) {
                $rq .= "," . $limitEnd;
            }
        }
        $result = $bdd->getData($rq, array());
        return $result;
    }

}

class InteretDal {

    public static function loadInteretById($id_interet) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM interet WHERE id_interet = ?";
        $param = array($id_interet);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

}

class CompetenceDal {

    public static function loadCompetence() {
        $bdd = Database::connect();
        $rq = "SELECT * FROM competence";
        $param = array();
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    public static function loadCompetenceByProjet($idProjet) {
        $bdd = Database::connect();
        $rq = "CALL sp_load_competence_by_projet(?)";
        $param = array($idProjet);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    public static function loadCompetenceByNom($nom_competence) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM competence WHERE nom_competence = ?";
        $param = array($nom_competence);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    public static function loadCompetenceById($id_competence) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM competence WHERE id_competence = ?";
        $param = array($id_competence);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * unJoinCompetence Retire toutes les compétences d'un projet
     * @param int $idProjet : l'identifiant du projet
     * @return boolean Retourne : true => si la suppression à réussit, false => sinon
     */
    public static function unJoinCompetence($idProjet) {
        $bdd = Database::connect();
        $rq = "DELETE FROM competence_projet WHERE id_projet = ?";
        $param = array($idProjet);
        $result = $bdd->execSql($rq, $param);
        return $result;
    }

    /**
     * joinCompetence Lie un projet à une compétence
     * @param int $idProjet : L'identifiant du projet
     * @param int $id_competence : L'identifiant de la compétence
     * @return Retourne : Le code erreur correspondant à l'éxecution de la requête, false => en cas d'échec de la jointure
     */
    public static function joinCompetence($idProjet, $id_competence) {
        $bdd = Database::connect();
        $rq = "CALL sp_join_competences_projet(?,?,@p_error)";
        $param = array($idProjet, $id_competence);
        $exec = $bdd->execSql($rq, $param);
        $result = false;
        if ($exec) {
            $result = $bdd->getData("SELECT @p_error");
        }
        return $result;
    }

}

/**
 * Accés aux données des Tag
 * @author : vincp
 */
class TagDal {

    /**
     * loadTag Charge un ou plusieurs tags en fonction des paramétres fournit par le tableau
     * @param Array() $table : un tableau de valeur qui contient -> Vide (mode 1) : le tableau est vide on renvoie tous les tags de la BDD
     *                                                         -> Une variable (mode 2) : -> Si c'est un string (mode 2.1) : On recherche le tag dont le nom correspond à la chaine fournit
     *                                                                                    -> Si c'est un entier (mode 2.2) : On recherche le tag dont l'identifiant correspond à l'entier fournit
     * @param boolean $last : Dans le cas du mode 1 uniquement, indique si l'on souhaite uniquement chargé le dernier tag de la BDD
     * @return type
     */
    public static function loadTag($table = array(), $last = false) {
        $bdd = Database::connect();
        $rq = "";
        $param = array();
        switch (count($table)) {
            case 1:#Charge un seul tag
                if (is_numeric($table[0])) {#Charge un tag par id
                    $rq = "SELECT * FROM tag WHERE id_tag = ?";
                    $param = array($table[0]);
                } else {#Charge un tag par nom
                    $rq = "SELECT * FROM tag WHERE nom_tag = ?";
                    $param = array($table[0]);
                }
                break;
            default:#Charge tous les tags
                if ($last) {
                    $rq = "SELECT * FROM tag WHERE id_tag IN(SELECT MAX(id_tag) FROM tag)";
                } else {
                    $rq = "SELECT * FROM tag";
                }
                break;
        }
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    public static function loadTagsByArticle($idArticle) {
        $bdd = Database::connect();
        $rq = "CALL sp_load_tags_by_article(?)";
        $param = array($idArticle);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * loadTagsByProjet Charge tous les tags d'un projet
     * @param int $idProjet : l'identifiant du projet
     * @return array(\) Renvoie un tableau d'objets anonyme contenant les propriétés des tags
     */
    public static function loadTagsByProjet($idProjet) {
        $bdd = Database::connect();
        $rq = "CALL sp_load_tags_by_projet(?)";
        $param = array($idProjet);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * unJoinTag Retire tous les tags d'une entitée
     * @param int $idProjet : l'identifiant de l'entitée
     * @param int $entity_type : Le type de l'entitée (include/consts/app/_config.inc.php-> 8 )
     * @return boolean Retourne true => si la suppression à réussit, false => sinon
     */
    public static function unJoinTag($id, $entity_type = TYPE_CONTENUE_ARTICLE) {
        $bdd = Database::connect();
        switch ($entity_type) {
            case TYPE_CONTENUE_ARTICLE:
                $rq = "DELETE FROM tag_article WHERE id_art = ?";
                break;
            case TYPE_CONTENUE_PROJET:
                $rq = "DELETE FROM tag_projet WHERE id_projet = ?";
                break;
        }
        $param = array($id);
        $result = $bdd->execSql($rq, $param);
        return $result;
    }

    /**
     * joinTag Lie un tag avec l'entitée
     * @param int $id : L'id de l'entitée
     * @param int $id_tag : L'id du tag
     * @param int $entity_type : Le type de l'entitée (include/consts/app/_config.inc.php-> 8 )
     * @return int Retourne le code erreur résultant de l'appel à la procédure stockée
     */
    public static function joinTag($id, $id_tag, $entity_type = TYPE_CONTENUE_ARTICLE) {
        $bdd = Database::connect();
        switch ($entity_type) {
            case TYPE_CONTENUE_ARTICLE:
                $rq = "CALL sp_join_tags_article(?,?,@p_result)";
                break;
            case TYPE_CONTENUE_PROJET:
                $rq = "CALL sp_join_tags_projet(?,?,@p_result)";
                break;
        }
        $param = array($id, $id_tag);
        $bdd->execSQL($rq, $param);
        $result = $bdd->getData("select @p_result", array());
        return $result;
    }

    public static function searchTag($search) {
        $bdd = Database::connect();
        $rq = "CALL sp_search_tag(?)";
        $param = array($search);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    public static function addTag($nom_tag) {
        $bdd = Database::connect();
        $rq = "INSERT INTO tag(nom_tag) VALUES(?)";
        $param = array($nom_tag);
        $result = $bdd->execSQL($rq, $param);
        return $result;
    }

    public static function tagExists($nom_tag) {
        $bdd = Database::connect();
        $rq = "SELECT EXISTS(SELECT * FROM tag WHERE nom_tag = ?)";
        $param = array($nom_tag);
        $result = $bdd->getData($rq, $param);
        return $result[0][0];
    }

}

class MembreDal {

    /**
     * addMember Ajoute un membre
     * @param string $pseudo : Le pseudo du membre
     * @param string $password : Le mot de passe du membre (crypter au préalable)
     * @param int $grade (facultatif, défaut = 2 ) : Le grade du membre (include/consts/metier/_metier.consts.php -> 9.1)
     * @return mixed Retourne : Le résultat de la requête => en cas de succés, false => En cas d'échec
     */
    public static function addMember($pseudo, $password, $grade = 2) {
        $pdo = Database::connect();
        $rq = 'INSERT INTO membre(pseudo,mdp,grade) VALUES (?,?,?)';
        $param = array($pseudo, $password, $grade);
        $result = $pdo->execSQL($rq, $param);
        return $result;
    }

    /**
     * joinMembre Lie un auteur à un article
     * @param int $id_auteur L'identifiant de l'article
     * @param int $idArticle L'identifiant d'un membre
     * @return mixed Retourne : Le résultat de la requête => en cas de succés, false => En cas d'échec
     */
    public static function joinMembre($id_auteur, $idArticle) {
        $pdo = Database::connect();
        $rq = 'INSERT INTO membre_article(id_membre,id_article) VALUES (?,?)';
        $param = array($id_auteur, $idArticle);
        $result = $pdo->execSQL($rq, $param);
        return $result;
    }

    /**
     * unJoinMembre Délie un article de son auteur, pour le supprimer
     * @param int $idArticle L'identifiant d'un membre
     * @return mixed Retourne : Le résultat de la requête => en cas de succés, false => En cas d'échec
     */
    public static function unJoinMembre($idArticle) {
        $pdo = Database::connect();
        $rq = 'DELETE FROM membre_article WHERE id_article = ?';
        $param = array($idArticle);
        $result = $pdo->execSQL($rq, $param);
        return $result;
    }

    /**
     * loadMemberById Charge un membre par identifiant
     * @param int $id_membre : L'identifiant d'un potentiel membre
     * @return mixed Retourne : Les données du membre => en cas de succés, false => En cas d'échec
     */
    public static function loadMemberById($id_membre) {
        $pdo = Database::connect();
        $rq = 'SELECT * FROM membre WHERE id_membre = ?';
        $param = array($id_membre);
        $result = $pdo->getData($rq, $param);
        return $result;
    }

    /**
     * loadMemberByArticle Charge un membre à l'aide d'un identifiant d'article
     * @param int $idArticle : L'identifiant de l'article
     * @return mixed Retourne : Les données de l'aticle écrit par le membre
     */
    public static function loadMemberByArticle($idArticle) {
        $pdo = Database::connect();
        $rq = 'SELECT id_membre FROM membre_article WHERE id_article = ?';
        $param = array($idArticle);
        $result = $pdo->getData($rq, $param);
        if (isset($result[0][0]) && is_numeric($result[0][0])) {
            return self::loadMemberById($result[0][0]);
        }
        return $result;
    }

    public static function loadMemberByPseudo($pseudo) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM membre WHERE pseudo = ?";
        $param = array($pseudo);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    public static function pseudoExists($pseudo) {
        $bdd = Database::connect();
        $rq = "SELECT pseudo FROM membre WHERE pseudo = ?";
        $param = array($pseudo);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * addArticleFav Ajoute un article favoris à un membre
     * @param int $id_membre : L'identifiant du membre
     * @param int $idArticle : L'identifiant de l'article
     * @return mixed Retourne : La requete => en cas de succes, false => en cas d'echec
     */
    public static function addArticleFav($id_membre, $idArticle) {
        $bdd = Database::connect();
        $rq = "CALL sp_add_fav_article_membre(?,?)";
        $param = array($id_membre, $idArticle);
        $result = $bdd->execSQL($rq, $param);
        return $result;
    }

    /**
     * removeArticleFav Retire un article favoris à un membre
     * @param int $id_membre : L'identifiant du membre
     * @param int $idArticle : L'identifiant de l'article
     * @return mixed Retourne : La requete => en cas de succes, false => en cas d'echec
     */
    public static function removeArticleFav($id_membre, $idArticle) {
        $bdd = Database::connect();
        $rq = "DELETE FROM membre_article_fav WHERE id_membre = ? AND id_article = ?";
        $param = array($id_membre, $idArticle);
        $result = $bdd->execSQL($rq, $param);
        return $result;
    }
    
    /**
     * updatePseudo Met à jour le pseudo d'un membre
     * 
     */
    public static function updatePseudo($id_membre, $pseudo){
        $bdd = Database::connect();
        $rq = "UPDATE membre SET pseudo = ? WHERE id_membre = ?";
        $param = array($pseudo, $id_membre);
        $result = $bdd->execSQL($rq, $param);
        return $result;
    }
    
    public static function updatePassword($id_membre, $password)
    {
        $bdd = Database::connect();
        $rq = "UPDATE membre SET mdp = ? WHERE id_membre = ?";
        $param = array($password, $id_membre);
        $result = $bdd->execSQL($rq, $param);
        return $result;
    }

}

class ProjetDal {

    /**
     * loadTag Charge un ou plusieurs projets en fonction des paramétres fournit par le tableau
     * @param Array() $table : un tableau de valeur qui contient -> Vide (mode 1) : le tableau est vide on renvoie tous les projets de la BDD
     *                                                           -> Une variable (mode 2) : -> Si c'est un string (mode 2.1) : On recherche le projet dont le nom correspond à la chaine fournit
     *                                                                                    -> Si c'est un entier (mode 2.2) : On recherche le projet dont l'identifiant correspond à l'entier fournit
     * @param boolean $last : Dans le cas du mode 1 uniquement, indique si l'on souhaite uniquement chargé le dernier projet de la BDD
     * @param int $limit : Limite les résultats de la requête (en mode 1 seulement.)
     * @param string $order : La colone sur laquel ordonné le jeu de caractére, suivi de l'option DESC ou ASC
     * @return mixed Retourne : un tableau contenant un jeu de résultats => en cas de succes, false => en cas d'echec
     */
    public static function loadProjet($table = array(), $last = false, $limit = null, $order = false) {
        $bdd = Database::connect();
        $rq = "";
        $param = array();
        switch (count($table)) {
            case 1:
                if (is_numeric($table[0])) {
                    $rq = "SELECT * FROM projet WHERE id_projet = ?";
                    $param = array($table[0]);
                } elseif (\Utilities::isDate($table[0])) {
                    $rq = "SELECT * FROM projet WHERE date_projet = ?";
                    $param = array($table[0]);
                } else {#Charge un projet par nom
                    $rq = "SELECT * FROM projet WHERE nom_projet = ?";
                    $param = array($table[0]);
                }
                break;
            default:
                if ($last) {
                    $rq = "SELECT * FROM projet WHERE id_projet IN(SELECT MAX(id_projet) FROM projet)";
                } else {
                    $rq = "SELECT * FROM projet";
                }
                if ($limit) {
                    $rq .= " LIMIT " . $limit;
                }
                break;
        }
        if ($order) {
            $rq .= " ORDER BY " . $order;
        }
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * deleteProjet Supprime le projet dont l'identifiant est donné en paramétre
     * @param int $idProjet : L'identifiant du projet
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function deleteProjet($idProjet) {
        $bdd = Database::connect();
        $bdd->beginTransaction();
        try {
            if (CompetenceDal::unJoinCompetence($idProjet)) {
                if (TagDal::unJoinTag($idProjet, TYPE_CONTENUE_PROJET)) { // On retire tous les tags du projets
                    $rq = "DELETE FROM projet WHERE id_projet = ?";
                    $param = array($idProjet);
                    $result = $bdd->execSql($rq, $param);
                    $bdd->commit();
                } else {
                    $bdd->rollBack();
                    return false;
                }
            } else {
                $bdd->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $bdd->rollBack();
            return false;
        }
        return $result;
    }

    /**
     * archiveProjet Archive le projet dont l'identifiant est donné en paramétre
     * @param int $idProjet : L'identifiant du projet
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function archiveProjet($idProjet) {
        $bdd = Database::connect();
        $rq = "UPDATE projet SET archive = 1 WHERE id_projet = ?";
        $param = array($idProjet);
        $result = $bdd->execSql($rq, $param);
        return $result;
    }

    /**
     * unArchiveProjet Désarchive le projet dont l'identifiant est donné en paramétre
     * @param int $idProjet : L'identifiant du projet
     * @return Array() Retourne : Le résultat d'une requête => En cas de réussite, false => En cas d'échec
     */
    public static function unArchiveProjet($idProjet) {
        $bdd = Database::connect();
        $rq = "UPDATE projet SET archive = 0 WHERE id_projet = ?";
        $param = array($idProjet);
        $result = $bdd->execSql($rq, $param);
        return $result;
    }

    /**
     * addProjet Ajoute un projet dans la BDD
     * @param string $nom Le nom du projet
     * @param date $date La date du projet
     * @param string $description La description du projet
     * @param int $type Le type du projet
     * @param Array(\Tag) $tbObjetTag Le tableau des tags devant être affecté au projet
     * @param Array(\Competence) $tbObjetTag Le tableau des compétences devant être affectée au projet
     * @return boolean
     */
    public static function addProjet($nom, $date, $description, $type, $tbObjetTag, $tbObjetCompetence) {
        $bdd = Database::connect();
        $bdd->beginTransaction();
        $rq = "INSERT INTO projet(nom_projet,date_projet,description,id_type_projet) VALUES(?,?,?,?)"; // l'id serat créé automatiquement
        $param = array($nom, $date, $description, $type);
        try {
            $ajoutProjet = $bdd->execSQL($rq, $param);
            if ($ajoutProjet) {
                $idProjet = self::loadProjet(array(), true)[0][0]; // on récupére l'id du projet
                if ($idProjet) {
                    if (!self::updateTagProjet($idProjet, $tbObjetTag)) {
                        $bdd->rollBack();
                        return false;
                    }
                    if (!self::updateCompetenceProjet($idProjet, $tbObjetCompetence)) {
                        $bdd->rollBack();
                        return false;
                    }
                } else {
                    $bdd->rollBack();
                    return false;
                }
            } else {
                $bdd->rollBack();
                return false;
            }
            $bdd->commit();
            return $idProjet;
        } catch (PDOException $e) {
            $bdd->rollBack();
            return false;
        }
    }

    /**
     * searchProjet Recherche un projet par son nom et par le contenu de sa déscription
     * @param string $search : La valeurs à rechercher
     * @return Array(\Projet) Retourne un tableau d'objet projet correspondant à la recherche, false = > en cas d'erreur
     */
    public static function searchProjet($search) {
        $bdd = Database::connect();
        $rq = "CALL sp_search_projet(?)";
        $param = array($search);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

    /**
     * updateProjet Met à jour un projet dans la BDD
     * @param int $idProjet : L'identifiant du projet
     * @param string $nom : Le nom du projet
     * @param date $date : La date du projet
     * @param string $description : La description du projet
     * @param int $type : Le type du projet
     * @param Array(\Tag) $tbObjetTag : Le tableau des tags devant être affecté au projet
     * @param Array(\Competence) $tbObjetCompetence : Le tableau des compétences devant être affectée au projet
     * @return mixed Retourne : L'id du projet => Si la MAJ réussit, false => si la suppression des tags à échoué, ou si une exception à été levé 
     */
    public static function updateProjet($idProjet, $nom, $date, $description, $type, $tbObjetTag, $tbObjetCompetence) {
        $bdd = Database::connect();
        $bdd->beginTransaction();
        $rq = "UPDATE projet SET nom_projet = ?,date_projet = ?,description = ?,id_type_projet = ? WHERE id_projet = ?";
        $param = array($nom, $date, $description, $type, $idProjet);
        try {
            $ajoutProjet = $bdd->execSQL($rq, $param);
            if ($ajoutProjet) {
                if (!self::updateTagProjet($idProjet, $tbObjetTag)) {
                    $bdd->rollBack();
                    return false;
                }
                if (!self::updateCompetenceProjet($idProjet, $tbObjetCompetence)) {
                    $bdd->rollBack();
                    return false;
                }
            } else {
                $bdd->rollBack();
                return false;
            }
            $bdd->commit();
            return $idProjet;
        } catch (PDOException $e) {
            $bdd->rollBack();
            return false;
        }
    }

    /**
     * updateTagProjet Met à jour les tag du projet donné en paramétre
     * @return boolean Retourne : true => Si la mise à jour à été effectué, false => si la mise à jour échoue
     */
    private static function updateTagProjet($idProjet, $tbObjetTag) {
        if (TagDal::unJoinTag($idProjet, TYPE_CONTENUE_PROJET)) { // On retire tous les tags du projets
            foreach ($tbObjetTag as $tag) {// pour les rattachers dans un nouvelle ordre
                if (!empty($tag)) {
                    if ($tag instanceof \Modele\Reference\Tag) {
                        TagDal::joinTag($idProjet, $tag->getId(), TYPE_CONTENUE_PROJET);
                    }
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * updateCompetenceProjet Met à jour les tag du projet donné en paramétre
     * @return boolean Retourne : true => Si la mise à jour à été effectué, false => si la mise à jour échoue
     */
    private static function updateCompetenceProjet($idProjet, $tbObjetCompetence) {
        if (CompetenceDal::unJoinCompetence($idProjet)) { // On retire toutes les compétences du projets
            foreach ($tbObjetCompetence as $competence) {// pour les rattachers dans un nouvelle ordre
                if (!empty($competence)) {
                    if ($competence instanceof \Modele\Reference\Competence) {
                        CompetenceDal::joinCompetence($idProjet, $competence->getId());
                    }
                }
            }
        } else {
            return false;
        }
        return true;
    }
    
    /* listProjet Renvoie une liste de projets
     * @param int $limitBegin (facultatif, défaut = false): La limite de début
     * @param int $limitEnd (facultatif, défaut = false): La limite de fin (ne peux être défini que si $limitBegin est défini et non nul)
     * @return Array(\) Retourne : Les résultats d'une requêtes
     */
    public static function listProjets ($limitBegin, $limitEnd) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM projet WHERE archive = 0 ORDER BY date_projet DESC ";
        if (is_numeric($limitBegin)) {
            $rq .= "LIMIT " . $limitBegin . " ";
            if (is_numeric($limitBegin)) {
                $rq .= "," . $limitEnd;
            }
        }
        $result = $bdd->getData($rq, array());
        return $result;
    }

}

class TypeProjetDal {

    public static function loadTypeProjet($id_type_projet) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM type_projet WHERE id_type_projet = ?";
        $param = array($id_type_projet);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

}

class GradeDal {

    public static function loadGradeById($id_grade) {
        $bdd = Database::connect();
        $rq = "SELECT * FROM grade WHERE id_grade = ?";
        $param = array($id_grade);
        $result = $bdd->getData($rq, $param);
        return $result;
    }

}
