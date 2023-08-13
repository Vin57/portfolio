<?php

/*
 * portfolio - portfolio
 * © Vincent, 2017
 * _app | _app.inc.php - Classes métier de l'application
 *
 * 	@author :
 * 	@date : 8 janv. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

namespace Application;

class WorkBench {

    /**
     * getBadgeInteret : Affiche un badge en fonction de l'intérêts donné en paramétre
     * @param \Interet $interet : Un objet de type \Interet
     * @return string Retourne : Un icone Bootstrap
     */
    public static function getBadgeInteret(\Modele\Reference\Interet $interet) {
        if ($interet instanceof \Modele\Reference\Interet) {
            $badge = "<i class='uk-badge uk-badge-";
            $badge .= self::getBootstrapClassInteret($interet->getId());
            $badge .= "'>" . $interet->getNom();
            return $badge .= "</i>";
        }
    }

    /**
     * getIcoGrade Affiche un icon en fonction du grade donné en paramétre
     * @param \Interet $interet le niveau d'intérêts d'un badge (include/consts/metier/_metier.consts.php -> 9.1)
     * @return string Retourne : Un icone Font Awesome
     */
    public static function getIcoGrade(\Modele\Reference\Grade $grade) {
        if ($grade instanceof \Modele\Reference\Grade) {
            $badge = "<i class='fa fa-";
            $badge .= self::getIcoClassGrade($grade->getId());
            $badge .= "'>&nbsp;&nbsp;" . $grade->getNom();
            return $badge .= "</i>";
        }
    }

    /**
     * getBootstrapClassInteret Obtient la classe bootstrap associé au niveau d'intéret donné en paramétre
     * @param int $niv_interet : Un niveau d'intérêt (include/consts/metier/_metier.consts.php -> 2.2)
     * @return string Retourne : Une classe Bootstrap (include/consts/metier/_metier.consts.php -> 2.1)
     */
    public static function getBootstrapClassInteret($niv_interet) {
        $result = "";
        switch ($niv_interet) {
            case STANDARD:
                $result .= INTERET_STANDARD;
                break;
            case NEWS:
                $result .= INTERET_NEWS;
                break;
            case IMPORTANT:
                $result .= INTERET_IMPORTANT;
                break;
        }
        return $result;
    }

    /**
     * getIcoClassGrade Obtient la classe bootstrap associé à l'identifiant du grade donné en paramétre
     * @param int $id_grade : Un niveau d'intérêt (include/consts/metier/_metier.consts.php -> 9.2)
     * @return string Retourne : Une classe Font Awesome (include/consts/metier/_metier.consts.php -> 2.1)
     */
    public static function getIcoClassGrade($id_grade) {
        $result = "";
        switch ($id_grade) {
            case USER_STATUS_BANNED:
                $result .= GRADE_BANNED;
                break;
            case USER_STATUS_MEMBRE:
                $result .= GRADE_MEMBRE;
                break;
            case USER_STATUS_WRITER:
                $result .= GRADE_WRITER;
                break;
            case USER_STATUS_MODO:
                $result .= GRADE_MODO;
                break;
            case USER_STATUS_ADMIN:
                $result .= GRADE_ADMIN;
                break;
            case USER_STATUS_SUPER_ADMIN:
                $result .= GRADE_SUPER_ADMIN;
                break;
        }
        return $result;
    }

    /**
     * showNotification Affiche les différentes notifications contenue dans le tableau fournit
     * @param array $tbNotification : Un tableau d'objets notifications
     * @param string $class : Un ensemble de class à rajouté au notification (séparé chacune d'entre elle par un espace)
     * @return string Retourne : Un script HTML constituant un ensemble d'éléments du DOM
     */
    public static function showNotification($tbNotification, $class = "", $formcontrol="form-control") {
        if (!empty($tbNotification)) {
            $return = "<div class='row'>";
            foreach ($tbNotification as $notification) {
                $type_msg = "";
                switch ($notification->getType()) {
                    case ERROR:
                        $type_msg = "danger";
                        $icon = "ban";
                        break;
                    case WARNING:
                        $type_msg = "warning";
                        $icon = "exclamation-triangle";
                        break;
                    case INFO:
                        $type_msg = "info";
                        $icon = "info-circle";
                        break;
                    case SUCCESS:
                        $type_msg = "success";
                        $icon = "check";
                        break;
                    default:
                        $type_msg = "warning";
                        $icon = "exclamation-triangle";
                        break;
                }
                $return .= "<span style='z-index:10' class='alert alert-" . $type_msg . " alert-dismissible  ".$formcontrol." ". $class . "'>"
                        . "<i class='fa fa-" . $icon . "' aria-hidden='true'></i>&nbsp;&nbsp;"
                        . $notification->getMessage()
                        . "<span class='btn close' data-dismiss='alert'>×</span>"
                        . "</span>";
            }
            echo $return . "</div>";
            return null;
        }
    }

    /**
     * isAllowed renvoie vrai si le grade est autorisé, sinon renvoie faux
     * @param \Membre $grade_membre : Le membre à vérifier
     * @param int $required_grade : Le niveau de grade requis
     * @return boolean Retourne : true => Le membre est autorisé, false => Le membre n'est pas autorisé
     */
    public static function isAllowed($membre, $required_grade) {
        if ($membre instanceof \Modele\Reference\Membre) {
            return $membre->getGrade()->getId() >= $required_grade;
        } else {
            return false;
        }
    }

    /**
     * is_multiple_value_input Indique si un \Input accepte plusieurs valeurs (comme les select par exemple)
     * @param int $value : Une constante (include/consts/app/_const_input.php -> 1) permettant de reconnaitre le type du champs de saisie
     * @return boolean Retourne : true => Si le nombre donnée en paramètre correpsond à un champs de saisie acceptant plusieurs valeurs, 
     *                            false => Sinon false
     */
    public static function is_multiple_value_input($value) {
        // On stocke les valeurs de constante qui sont des champs de saisie à valeurs multiple
        $multiple_input_value = array(SELECT);
        // On parcours ensuite le tableau ci-dessus et on cherche si le paramètres donnée est dans le tableau
        return \Utilities::array_contains($multiple_input_value, $value);
    }

    public static function purifier($value) {
        $result = array();
        if (is_array($value)) {
            foreach ($value as $var) {
                if (is_string($var)) {
                    $var = htmlspecialchars($var);
                }
                $result[] .= $var;
            }
        }
        return $result;
    }

}

class Ariane {

    public static $_fil; // Le fil d'ariane

    private static function setFil($noeud) {
        Ariane::$_fil[] = $noeud;
    }

    private static function getFil() {
        return Ariane::$_fil;
    }

    private static function clearFil() {
        Ariane::$_fil = null;
    }

    public static function construireFil($noeud) {
        Ariane::setFil($noeud);
    }

    public static function afficherFil() {
        $return = Ariane::getFil();
        Ariane::clearFil(); // à chaque affichage on vide le fil d'arriane puisque l'on veut uniquement afficher le chemin ayant permis d'arriver jusqu'à là
        return $return;
    }

}

class Noeud {

    private $_display_name;
    private $_array_parameter;

    public function __construct($display_name, $array_of_parameter) {
        $this->setDisplayName($display_name);
        $this->setArrayOfParameter($array_of_parameter);
    }

    // GETTEUR

    /**
     * getDisplayName affiche le nom du noeud
     * @return string le nom du noeud
     */
    public function getDisplayName() {
        return $this->_display_name;
    }

    /**
     * getArrayOfParameter renvoie le tableau de paramètres d'accés à la page ciblée
     * @return array un tableau de paramètre(s)
     */
    public function getArrayOfParameter() {
        return $this->_array_parameter;
    }

    // SETTEUR

    /**
     * setDisplayName affecte le nom donné en paramètre au nom du noeud
     * @paral string le nom du noeud
     */
    private function setDisplayName($displayName) {
        $this->_display_name = $displayName;
    }

    /**
     * setArrayOfParameter affecte le tableau donné en paramétre au tableau de paramètres du noeuds
     * @param array $array Un tableau de un ou plusieurs paramètres permettant de joindre la page concerné
     */
    private function setArrayOfParameter($array) {
        $this->_array_parameter = $array;
    }

}

/**
 * @author : OpenClassroom
 */
class Chiffrement {

    // Algorithme utilisé pour le cryptage des blocs
    private static $cipher = MCRYPT_RIJNDAEL_128;
    // Clé de cryptage         
    // Mode opératoire (traitement des blocs)
    private static $mode = 'cbc';

    public static function crypt($data, $key) {
        $keyHash = md5($key);
        $key = substr($keyHash, 0, mcrypt_get_key_size(self::$cipher, self::$mode));
        $iv = substr($keyHash, 0, mcrypt_get_block_size(self::$cipher, self::$mode));

        $data = mcrypt_encrypt(self::$cipher, $key, $data, self::$mode, $iv);

        return base64_encode($data);
    }

    public static function decrypt($data, $key) {
        $keyHash = md5($key);
        $key = substr($keyHash, 0, mcrypt_get_key_size(self::$cipher, self::$mode));
        $iv = substr($keyHash, 0, mcrypt_get_block_size(self::$cipher, self::$mode));

        $data = base64_decode($data);

        $data = mcrypt_decrypt(self::$cipher, $key, $data, self::$mode, $iv);
        return rtrim($data);
    }

}
