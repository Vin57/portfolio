<?php

namespace Modele\Data;

use PDO;
use Modele\Data\Database;

/**
 * portfolio - portfolio
 * © vincp, 2017
 * Data | Data.lib.php - Class technique d'accés aux données
 *
 * 	@author : vincp
 *  	@date : 7 févr. 2017
 *      @modify : design patern in use : SINGLETON
 */
class Database {

    private static $monPdo;
    private static $bdd = null;

    private function __construct() {
        Database::$monPdo = $this->connexionBdd();
    }

    /**
     * connect Invoque l'objet PDO permettant la connexion à la BDD
     * @return PDO l'objet PDO
     */
    public static function connect() {
        if (null == Database::$bdd) {
            Database::$bdd = new Database();
        }
        return Database::$bdd;
    }

    /**
     * connexionBdd Instancie un nouvelle objet PDO et le retourne
     * @return PDO un objet PDO
     */
    public function connexionBdd() {
        try {
            $bdd = new PDO(DSN, DB_USER, DB_PWD, array(PDO::ATTR_PERSISTENT => true));
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
        return $bdd;
    }

    /**
     * execSQL Execute une requete standard ou une requete preparee
     * @param string $sql : Requete SQL a executer
     * @param array $params : Tableau de parametres contenant les valeurs a remplacer dans la requete
     * @return boolean Retourne : La requete => en cas de succes, false => en cas d'echec
     */
    public function execSQL($sql, $params = array()) {
        if ($params) {
            $contains_array = false; //Indique si parmis les paramétre  l'un d'eux est un tableau
            $requete = Database::$monPdo->prepare($sql);
            $pos = 0;
            for ($i = 0; $i < count($params); $i++) {
                $pos += $i; // Le premier paramétre de bindParam impose de donner la position de l'élément dans la requéte
                if (is_array($params[$i])) {// Si la valeur est un tableau on l'ajouteras à la requête
                    $contains_array = true;
                    for ($a = 0; $a < count($params[$i]); $a++) {
                        $pos += 1; // On ajoute une nouvelle valeur qui se placera à une nouvelle position dans la requête
                        $val = $params[$i][$a];
                        $requete->bindParam($pos, $val);
                    }
                } else {
                    if($pos == 0){
                        $pos = 1;
                    }
                    $requete->bindParam($pos, $params[$i]);
                }
            }
            if ($contains_array) {
                $requete->execute();
            } else {
                $requete->execute($params);
            }
        } else {
            $requete = Database::$monPdo->query($sql);
        }
        return ($requete) ? $requete : false;
    }
    
    /**
     * getData Execute une requete standard ou une requete preparee et retourne un jeu d'enregistrements sous la forme d'un tableau associatif.
     * @param string $sql : La requete SQL à exécuter
     * @param array $params : Un tableau de parametres contenant les valeurs à substituer dans la requete SQL
     * @param boolean $pdo_fetch_object : true => Lorsqu'un objet est trouve dans le tableau, appelle son constructeur et assigne les proprietees de 
     *                                             l'objets aux valeurs des colonnes respectives,
     *                                    false => Retrouve et renvoie toutes les données transmises par la requete dans un objet anonyme avec les noms 
     *                                             de propriétés qui correspondent aux noms des colonnes retournés dans le jeu de résultats
     * @return mixed Retourne : un tableau contenant un jeu de résultats => en cas de succes, false => en cas d'echec
     */
    public function getData($sql, $params = array(), $pdo_fetch_object = false) {
        $requete = $this->execSQL($sql, $params);
        if ($requete) {
            $data = (!$pdo_fetch_object) ? $requete->fetchAll() : $requete->fetchAll(PDO::FETCH_OBJ);
            $requete->CloseCursor();
            return $data;
        } else {
            return false;
        }
    }

    /**
     *  _/PASSERELLE\_ Vers la méthode beginTransaction de la classe PDO
     * beginTransaction Commence une transaction
     */
    public function beginTransaction() {
        return self::$monPdo->beginTransaction();
    }

    /**
     *  _/PASSERELLE\_ Vers la méthode commit de la classe PDO
     * commit Consigne les données dans la BDD et valide la transaction
     */
    public function commit() {
        return self::$monPdo->commit();
    }

    /**
     * _/PASSERELLE\_ Vers la méthode rollBack de la classe PDO
     * rollBack Permet de remettre la BDD dans l'état dans lequelle elle était lors du début de la transaction
     */
    public function rollBack() {
        return self::$monPdo->commit();
    }

}
