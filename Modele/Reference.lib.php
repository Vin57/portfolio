<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * Reference | Reference.lib.php - Référence les objets 
 *
 * 	@author : vincp
 * 	@date : 7 févr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

namespace Modele\Reference;

//<editor-fold defaultstate="collapsed" desc="CLASSE METIER">

/**
 * Représente un Article
 * @author vincp
 */
class Article {

    private $_id;
    private $_titre;
    private $_texte;
    private $_date_creation;
    private $_interet;
    private $_lien;
    private $_les_tags;
    private $_archive;
    private $_auteur;

    /**
     * Représente un Article
     * @param int $id : L'identifiant de l'article
     * @param string $titre : Le titre de l'article
     * @param string $texte : Le texte de l'article
     * @param datetime $date_creation : La date de la création de l'article
     * @param Interet $interet : Le niveau d'intérêt(d'importance) de l'article
     * @param string $lien (facultatif, défaut = "") : Un lien menant à l'article d'origine
     * @param array $tags (facultatif, défaut = array()) : Le tableau contenant les tags de l'article
     * @param boolean $archive (facultatif, défaut = 0) : Indique si l'article est archivé
     * @param \Auteur $auteur (facultatif, défaut = null) : L'auteur de l'article
     */
    public function __construct($id, $titre, $texte, $date_creation, $interet, $lien = "", $tags = array(), $archive = 0, $auteur = null) {
        $this->setId($id);
        $this->setTitre($titre);
        $this->setTexte($texte);
        $this->setDateCreation($date_creation);
        $this->setInteret($interet);
        $this->setLien($lien);
        $this->setLesTags($tags);
        $this->setArchive($archive);
        $this->setAuteur($auteur);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id = $id;
    }

    public function setTitre($titre) {
        $this->_titre = $titre;
    }

    public function setTexte($texte) {
        $this->_texte = $texte;
    }

    public function setDateCreation($date_creation) {
        $this->_date_creation = $date_creation;
    }

    public function setInteret($interet) {
        $this->_interet = $interet;
    }

    public function setLien($lien) {
        $this->_lien = $lien;
    }

    public function setLesTags($lesTags) {
        $this->_les_tags = $lesTags;
    }

    public function setArchive($archive) {
        $this->_archive = $archive;
    }

    public function setAuteur($auteur) {
        $this->_auteur = $auteur;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id;
    }

    public function getTitre() {
        return $this->_titre;
    }

    public function getTexte() {
        return $this->_texte;
    }

    public function getDateCreation() {
        return $this->_date_creation;
    }

    public function getInteret() {
        return $this->_interet;
    }

    public function getLien() {
        return $this->_lien;
    }

    public function getLesTags() {
        return $this->_les_tags;
    }

    public function getArchive() {
        return $this->_archive;
    }
    
    public function getAuteur(){
        return $this->_auteur;
    }

}

/**
 * Représente un Interet
 * @author vincp
 */
class Interet {

    private $_id;
    private $_nom;

    /**
     * Représente un Interet
     * @param int $id : L'identifiant de l'interet
     * @param string $nom : Le nom de l'interet
     */
    public function __construct($id, $nom) {
        $this->setId($id);
        $this->setNom($nom);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id = $id;
    }

    public function setNom($nom) {
        $this->_nom = $nom;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id;
    }

    public function getNom() {
        return $this->_nom;
    }

}

/**
 * Représente une Competence
 * @author vincp
 */
class Competence {

    private $_id;
    private $_nom;

    /**
     * Représente une Competence
     * @param int $id : L'identifiant de la compétence
     * @param string $nom : Le nom de la compétence
     */
    public function __construct($id, $nom) {
        $this->setId($id);
        $this->setNom($nom);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id = $id;
    }

    public function setNom($nom) {
        $this->_nom = $nom;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id;
    }

    public function getNom() {
        return $this->_nom;
    }

}

/**
 * Représente un Tag
 * @author vincp
 */
class Tag {

    private $_id;
    private $_nom;

    /**
     * Représente un Tag
     * @param int $id : L'identifiant du tag
     * @param string $nom : Le nom du tag
     */
    public function __construct($id, $nom) {
        $this->setId($id);
        $this->setNom($nom);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id = $id;
    }

    public function setNom($nom) {
        $this->_nom = $nom;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id;
    }

    public function getNom() {
        return $this->_nom;
    }

}

/**
 * Représente un projet (le projeeeeeeeeeeeeeeet !)
 * @author vincp
 */
class Projet {

    private $_id_projet;
    private $_nom_projet;
    private $_date_projet;
    private $_description;
    private $_type;
    private $_les_tags;
    private $_les_competences;
    private $_archive;

    /**
     * Représente un projet
     * @param string $id : L'identifiant du projet
     * @param string $nom : Le nom du projet
     * @param Date $date : La date du projet (de début ou de fin, sert à placer le projet dans le temps)
     * @param String $description : La description du projet
     * @param TypeProjet $type : Un objet \TypeProjet
     * @param Array(\Tag) $tags : Un tableau d'objet \Tag
     * @param Array(\Competence) $competences : Un tableau d'objet \Competence
     * @param boolean $archive (facultatif, défaut = 0) : Indique si le projet est archivé
     */
    public function __construct($id, $nom, $date, $description, TypeProjet $type, $tags = array(), $competences, $archive = 0) {
        $this->setId($id);
        $this->setNom($nom);
        $this->setDate($date);
        $this->setDescription($description);
        $this->setType($type);
        $this->setLesTags($tags);
        $this->setLesCompetences($competences);
        $this->setArchive($archive);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    /**
     * setId Affecte l'id du projet
     * @param int $id : L'id du projet
     */
    public function setId($id) {
        $this->_id_projet = $id;
    }

    /**
     * setNom Affecte le nom du projet
     * @param string $nom : Le nom du projet
     */
    public function setNom($nom) {
        $this->_nom_projet = $nom;
    }

    /**
     * setDate Affecte la date du projet
     * @param Date $date : La date du projet
     */
    public function setDate($date) {
        $this->_date_projet = $date;
    }

    /**
     * setDescription Affecte la description du projet
     * @param String $desc : La description du projet
     */
    public function setDescription($desc) {
        $this->_description = $desc;
    }

    /**
     * setType Affecte le type du projet
     * @param int $type : Le type du projet
     */
    public function setType($type) {
        $this->_type = $type;
    }

    /**
     * setLesTags Affecte les tags
     * @param Array(\Tag) $tags : Un tableau de \Tag
     */
    public function setLesTags($tags) {
        $this->_les_tags = $tags;
    }
    
    /**
     * setLesCompetences Affecte les compétences
     * @param Array(\Competence) $competences : Un tableau de \Competence
     */
    public function setLesCompetences($competences){
        $this->_les_competences = $competences;
    }

    public function setArchive($archive) {
        $this->_archive = $archive;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id_projet;
    }

    public function getNom() {
        return $this->_nom_projet;
    }

    public function getDate() {
        return $this->_date_projet;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function getType() {
        return $this->_type;
    }

    public function getLesTags() {
        return $this->_les_tags;
    }
    
    public function getLesCompetences(){
        return $this->_les_competences;
    }

    public function getArchive() {
        return $this->_archive;
    }

}

/**
 * Représente une section composant un projet
 * Une section peut elle meme etre composer de sous section
 */
class Section {

    private $_id_section;
    private $_titre_section;
    private $_texte_section;
    private $_les_ressources;
    private $_les_sections;

    /**
     * Représente une section composant un projet
     * @param int $id : L'identifiant de la section
     * @param string $titre : Le titre de la section
     * @param string $texte : Le texte de la section
     * @param Array(\Ressource) $ressources : Les ressources utilisée par la section
     */
    public function __construct($id, $titre, $texte, $ressources = array()) {
        $this->setId($id);
        $this->setTitre($titre);
        $this->setTexte($texte);
        $this->setLesRessources($ressources);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id_section = $id;
    }

    public function SetTitre($titre) {
        $this->_titre_section = $titre;
    }

    public function setTexte($texte) {
        $this->_texte_section = $texte;
    }

    public function setRessources($ressources) {
        $this->_les_ressources = $ressources;
    }

    /**
     * setLesSections Affecte un tableau de sections au projet
     * @param Array(\Section) $sections : La ou les sections du projet
     */
    public function setLesSections($sections) {
        $this->_les_sections = $sections;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id_section;
    }

    public function getTitre() {
        return $this->_titre_section;
    }

    public function getTexte() {
        return $this->_texte_section;
    }

    public function getRessources() {
        return $this->_les_ressources;
    }

    public function getLesSections() {
        return $this->_les_sections;
    }

}

// Représente une ressource, à savoir une image, un document, une url
// Le lien va donc déterminer le type de la ressource (url pour un site, chemin pour un document et une image)
class Ressource {

    private $_id_ressource;
    private $_nom_ressource;
    private $_lien_ressource;
    private $_type_ressource;

    /**
     * Représente une ressource
     * @param type $id : L'identifiant de la ressource
     * @param type $nom : Le nom de la ressource
     * @param type $lien : Le lien de la ressource (le chemin/url menant à celle-ci)
     * @param type $type : Le type de la ressource (include/consts/metier/_metier.consts.php -> 5s)
     */
    public function __construct($id, $nom, $lien, $type) {
        $this->setId($id);
        $this->setNom($nom);
        $this->setLien($lien);
        $this->setType($type);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id_ressource = $id;
    }

    public function setNom($nom) {
        $this->_nom_ressource = $nom;
    }

    public function setLien($lien) {
        $this->_lien_ressource = $lien;
    }

    public function setType($type) {
        $this->_type_ressource = $type;
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function getId() {
        return $this->_id_ressource;
    }

    public function getNom() {
        return $this->_nom_ressource;
    }

    public function getLien() {
        return $this->_lien_ressource;
    }

    public function getType() {
        return $this->_type_ressource;
    }

}

// Type du projet
class TypeProjet {

    private $_id_type_projet;
    private $_lib_type_projet;

    /**
     * Représente un type de projet
     * @param int $id_type_projet : L'identifiant du projet
     * @param string $lib_type_projet : Le libellé du projet
     */
    public function __construct($id_type_projet, $lib_type_projet) {
        $this->setId($id_type_projet);
        $this->setLibelle($lib_type_projet);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id_type_projet = $id;
    }

    public function setLibelle($lib) {
        $this->_lib_type_projet = $lib;
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function getId() {
        return $this->_id_type_projet;
    }

    public function getLibelle() {
        return $this->_lib_type_projet;
    }

}

//</editor-fold>
//<editor-fold defaultstate="collapsed" desc="CLASSE APPLICATION">

/**
 * Représente une notification
 * @author vincp
 */
class Notification {

    private $_message;
    private $_type;

    /**
     * Représente une notification
     * @param string $message : Le message à afficher
     * @param int $type : Le type du message (ERROR,WARNING,INFO,SUCCESS) voir _config.inc.php
     */
    public function __construct($message, $type) {
        $this->setMessage($message);
        $this->setType($type);
    }

    public function setMessage($message) {
        $this->_message = $message;
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function getMessage() {
        return $this->_message;
    }

    public function getType() {
        return $this->_type;
    }

}

/**
 * Représente un Form
 * @author vincp
 */
class Form {

    private $_method;
    private $_action;
    private $_enctype;
    private $_inputs;

    /**
     * Représente un Form
     * @param string $method : Le type de la méthode à éxecuter (POST,GET...)
     * @param string $action : L'action du formulaire
     * @param string $enctype : L'attribut enctype du formulaire (multipart/form-data par exemple)
     * @param array(\Input) $tb_input : Un tableau de champs de saisie 
     * @author vincp
     */
    public function __construct($method, $action, $enctype, $tb_input) {
        $this->setMethod($method);
        $this->setAction($action);
        $this->setEnctype($enctype);
        $this->setInputs($tb_input);
    }

    public function setMethod($method) {
        $this->_method = $method;
    }

    public function setAction($action) {
        $this->_action = $action;
    }

    public function setEnctype($enctype) {
        $this->_enctype = $enctype;
    }

    public function setInputs($inputs) {
        $this->_inputs = $inputs;
    }

    public function getMethod() {
        return $this->_method;
    }

    public function getAction() {
        return $this->_action;
    }

    public function getEnctype() {
        return $this->_enctype;
    }

    public function getInputs() {
        return $this->_inputs;
    }

}

/**
 * Représente un champ de saisie
 * @author vincp
 */
class Input {

    protected $_type;
    protected $_name;
    protected $_id;
    protected $_class;
    protected $_required;
    protected $_label;
    protected $_value;
    protected $_onClickEvent;

    /**
     * Représente un champs de saisie
     * @param string $type : L'attribut 'type' du champ de saisie
     * @param string $name (facultatif, défaut = "") : L'attribut 'name' du champ de saisie
     * @param string $id (facultatif, défaut = "") : L'attribut 'id' du champ de saisie
     * @param string $class (facultatif, défaut = "") : L'attribut 'class' du champ de saisie
     * @param bool $required (facultatif, défaut = true) : L'attribut required du champ de saisie
     * @param string $label (facultatif, défaut = "") : L'attribut 'label' du champ de saisie
     * @param string $value (facultatif, défaut = "") : L'attribut 'value' du champ de saisie
     * @param string $onclickEvent (facultatif, défaut = "") : L'événement 'onclick' du champs de saisie
     */
    public function __construct($type, $name = "", $id = "", $class = "", $required = true, $label = "", $value = "", $onclickEvent = "") {
        $this->setType($type);
        $this->setName($name);
        $this->setId($id);
        $this->setClass($class);
        $this->setRequired($required);
        $this->setLabel($label);
        $this->setValue($value);
        $this->setOnClick($onclickEvent);
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function setClass($class) {
        $this->_class = $class;
    }

    public function setRequired($required) {
        $this->_required = $required;
    }

    public function setLabel($label) {
        $this->_label = $label;
    }

    public function setValue($value) {
        $this->_value = $value;
    }

    public function setOnClick($onClickEvent) {
        $this->_onclickEvent = $onClickEvent;
    }

    public function getType() {
        return $this->_type;
    }

    public function getName() {
        return $this->_name;
    }

    public function getId() {
        return $this->_id;
    }

    public function getClass() {
        return $this->_class;
    }

    public function getLabel() {
        return $this->_label;
    }

    public function getValue() {
        return $this->_value;
    }

}

/**
 * Représente un champs de saisie avec plusieurs options,
 * par exemple un 'select' est un input dont la première valeur est value (classe input)
 * et la valeur de chacune de ses options est contenues dans le tableaux de valeur values
 * @author vincp
 */
class InputMultipleValue extends Input {

    private $_tb_option;
    private $_selected;

    /**
     * Représente un champs de saisie avec plusieurs options
     * @param string $type : L'attribut 'type' du champ de saisie
     * @param string $name (facultatif, défaut = "") : L'attribut 'name' du champ de saisie
     * @param string $id (facultatif, défaut = "") : L'attribut 'id' du champ de saisie
     * @param string $class (facultatif, défaut = "") : L'attribut 'class' du champ de saisie
     * @param boolean $required (facultatif, défaut = true) : L'attribut required du champ de saisie
     * @param array(\String) $tb_option (facultatif, défaut = array()) : Un tableau d'option
     * @param mixed $selected (facultatif, défaut = "") : La valeur de la cle du tableau qui doit etre preselectionne 
     * @param string $label (facultatif, défaut = "") : L'attribut 'label' du champ de saisie
     * @param string $onclickEvent (facultatif, défaut = "") : L'événement 'onclick' du champs de saisie
     */
    public function __construct($type, $name = "", $id = "", $class = "", $required = true, $tb_option = array(), $selected = "", $label = "", $onclickEvent = "") {
        parent::__construct($type, $name, $id, $class, $required, $label, "", $onclickEvent); // on hérite de Input, mais les InputMultipleValue n'ont justement pas de value propre
        $this->setOption($tb_option);
        $this->setSelected($selected);
    }

    public function setOption($tb_option) {
        $this->_tb_option = $tb_option;
    }

    public function setSelected($selected) {
        $this->_selected = $selected;
    }

    public function getOption() {
        return $this->_tb_option;
    }

    public function getSelected() {
        return $this->_selected;
    }

}

/**
 * Représente une Modal
 * @author vincp
 */
class Modal {

    private $_titre;
    private $_id;
    private $_text;
    private $_forms;

    /**
     * Représente une Modal
     * @param string $titre : Le titre de la modal
     * @param string $id : L'identifiant dans le DOM de la modal
     * @param string $text : Le contenu textuelle de la modal
     * @param array(\Form) $tb_form (faculatif): Un tableau de formulaire
     * @author vincp
     */
    public function __construct($titre, $id, $text, $tb_form = array()) {
        $this->setTitre($titre);
        $this->setId($id);
        $this->setText($text);
        $this->setForms($tb_form);
    }

    public function setTitre($titre) {
        $this->_titre = $titre;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function setText($text) {
        $this->_text = $text;
    }

    public function setForms($forms) {
        $this->_forms = $forms;
    }

    public function getTitre() {
        return $this->_titre;
    }

    public function getId() {
        return $this->_id;
    }

    public function getText() {
        return $this->_text;
    }

    public function getForms() {
        return $this->_forms;
    }

    /**
     * haveForm Indique si la modal posséde au moins un formulaire
     * @return bool Retourne : true => la modal posséde un ou plusieurs formulaire,
     *                         false => la modale ne posédde aucun formulaire
     */
    public function haveForm() {
        return !empty($this->_forms);
    }

}

/**
 * Représente un Grade
 * @author vincp
 */
class Grade {

    private $_id;
    private $_nom;

    /**
     * Représente un Grade
     * @param int $id : L'identifiant du grade
     * @param string $nom : Le nom du grade
     */
    public function __construct($id, $nom) {
        $this->setId($id);
        $this->setNom($nom);
    }

    /*
     * Setteur (accesseur en écriture)
     */

    public function setId($id) {
        $this->_id = $id;
    }

    public function setNom($nom) {
        $this->_nom = $nom;
    }

    /*
     * Getteur (accesseur en lecture)
     */

    public function getId() {
        return $this->_id;
    }

    public function getNom() {
        return $this->_nom;
    }

}

/**
 * Représente un Membre
 * @author vincp
 */
class Membre {

    private $_id;
    private $_pseudo;
    private $_password;
    private $_grade;

    /**
     * Représente un membre du site
     * @param int $pId : L'id du membre
     * @param string $pPseudo : Le pseudo du membre
     * @param string $pPassword le mot de passe du membre
     * @param \Grade $pGrade le grade du membre (un objet de la classe grade)
     */
    public function __construct($pId, $pPseudo, $pPassword, $pGrade) {
        $this->setId($pId);
        $this->setPseudo($pPseudo);
        $this->setPassword($pPassword);
        $this->setGrade($pGrade);
    }

    public function getId() {
        return $this->_id;
    }

    public function getPseudo() {
        return htmlspecialchars($this->_pseudo);
    }

    public function getPassword() {
        return $this->_password;
    }

    public function getGrade() {
        return $this->_grade;
    }

    public function setId($pID) {
        $this->_id = $pID;
    }

    public function setPseudo($pPseudo) {
        $this->_pseudo = $pPseudo;
    }

    public function setPassword($pPassword) {
        $this->_password = $pPassword;
    }

    public function setGrade($pGrade) {
        $this->_grade = $pGrade;
    }

}

//</editor-fold>


