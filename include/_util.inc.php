<?php

/**
 * portfolio - portfolio
 * © Vincent, 2017
 * _util | _util.inc.php - Fonctions generique
 *
 * 	@author :
 * 	@date : 1 janv. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

use Application\WorkBench;

class Utilities {

    /**
     * array_contains Parcours un tableau en retournant vrai, faux, ou le nombre d'occurence trouvé en fonction du paramétre $search
     * @param array $array Le tableau dans lequel on souhaite faire la recherche
     * @param mixed $search La variable à rechercher dans le tableau donnée en premier paramétre
     * @param bool $withKeySearch : true=> La recherche est également étendu aux clés du tableau, 
     *                              false => On ne cherche la donnée que parmis les valeurs du tableau
     * @param bool $exactMatch : true => On souhaite savoir si la données trouvés est identique à celle cherchez,
     *                           false => on souhaite savoir si la donnés trouvé est égal à celle chercher
     * @param bool $runThrough : true => On souhaite parcourir tous le tableau et retourné le nombre d'occurence trouvé,
     *                          false => On s'arrête dés que l'on trouve la première occurence
     * @return boolean Retourne : false => La valeur n'à pas été trouvé dans le tableau ppour les paramètres donné
     *                            true => La valeur à été trouvé dans le tableau
     *                            (si runThroug est activé) => Le nombre de valeur trouvée
     */
    public static function array_contains($array, $search, $withKeySearch = false, $exactMatch = false, $runThrough = false) {
        $totalFound = 0; // Utile en mode runThrough
        foreach ($array as $key => $data) {
            if (($data == $search || $key == $search)) {// Si l'une des deux valeurs correspond à la recherche
                if ($withKeySearch && ($key == $search)) {// Si l'on est en mode withKeySearch et que la valeur recherché est égal à la clé
                    if (self::compareValue($key, $search, $exactMatch)) {//Si les valeurs correspondent
                        if ($runThrough)
                            $totalFound += 1;
                        else
                            return true;
                    }
                }
                if (self::compareValue($data, $search, $exactMatch)) {
                    if ($runThrough)
                        $totalFound += 1;
                    else
                        return true;
                }
            }
        }
        return $totalFound;
    }

    /**
     * compareValue Compare deux valeurs entre elle
     * @param mixed $valueOne : La première valeur
     * @param mixed $valueTwo : La seconde valeur
     * @param bool $exactMatch : true => On souhaite savoir si la données trouvés est identique à celle cherchez, false, on souhaite savoir si la donnés trouvé est égal à celle chercher
     * @return boolean true si les valeurs correspondent, sinon retourne false
     */
    public static function compareValue($valueOne, $valueTwo, $exactMatch = false) {
        if ($exactMatch && $valueOne === $valueTwo) {// Si on est en mode exactMatch et que la valeur de la clé est identique à celle recherché
            return true;
        } else if (!$exactMatch && $valueOne == $valueTwo) {// Si l'on est pas en mode exactMatch
            return true;
        }
        return false;
    }
    
    /**
     * isEmpty Indique si un tableau est vide (ne contient pas de valeur ou ne contient que des tableaux eux mêmes vide)
     * @param \Array $array : Un tableau (uni-multidimenssionel)
     * @param int $deep : Indique la profondeur jusqu'à laquelle la profondeur de la recherche récurssive doit s'effectuer
     * @param int $deepLevel : Indique le niveau de profondeur courrant durant le parcours (ne pas instancier)
     * @return int true si le tableau est vide, sinon false
     */
    public static function isEmpty($array, $deep,$deepLevel = 0)
    {
        $isEmpty = true;
        if(is_array($array)){
            foreach($array as $value){
                if(is_array($value) && $deep < $deepLevel){
                    $deepLevel ++;
                    $isEmpty = self::isEmpty($value, $deep,$deepLevel);
                }elseif($deep == $deepLevel){
                    return true;
                }else{
                    return false;
                }
                if(!$isEmpty){
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * Vérifie si le résultat d'une requéte est valide (n'est pas un message d'erreur, correspond bien à un tableau...)
     * @param mixed $result le résultat d'une requête
     * @return boolean retourne true si le résultat est correct et apte à être utiliser, sinon retourne false
     */
    public static function isCorrectReturnRequest($result) {
        return($result != PDO_EXCEPTION_VALUE and is_array($result));
    }

    /**
     * displayList Affiche une liste de choix à partir d'un jeu de résultat 
     * de la forme {identifiant, libellé}
     * @param string $tab : un tableau de deux colonnes
     * @param string $classe : la classe CSS à appliquer à l'élément
     * @param string $id : l'id (et nom) de la liste de choix
     * @param int $size : l'attribut size de la liste de choix
     * @param string $idSelect : l'élément à présélectionner dans la liste
     * @param string $onchange : le nom de la fonction à appeler 
     * @param boolean $valueIsId : Indique si l'identifiant et la valeur 
     * en cas d'événement onchange()
     */
    public static function displayList($tab, $classe, $id, $size, $idSelect, $onchange, $valueIsId = false) {
        // affichage de la liste de choix
        $return = '<select class="' . $classe . '" id="' . $id . '" name="' . $id . '" size="'
                . $size . '" onchange="' . $onchange . '">';
        foreach ($tab as $value) {
            $return .= ($value == $idSelect) ? '<option selected value="' : '<option value="';
            if (!is_array($value)) {
                $return .= $value . '">' . $value . '</option>';
            } else {
                if ($valueIsId) {
                    $return .= $value[1] . '">' . $value[1] . '</option>';
                } else {
                    $return .= $value[0] . '">' . $value[1] . '</option>';
                }
            }
        }
        $return .= '</select>';
        return $return;
    }

    /**
     * genereTbMois retourne un tableau de mois
     * @param $intMoisDebut : Le mois de début en chiffre (en partant de 0)
     * @param $intMoisFin : Le mois de fin en chiffre (en s'arrétant à 12)
     * @param $modeTB : 0 == retourne un tableau de chiffre, 1 == retourne un tableau de mois en lettres
     * @param $modeMois : 1 == le mois en entier, 2 == le mois abrégé. Dans le cas du tableau en chiffre entrez 0
     * @return mixed Retourne : Le tableau des mois si la génération réussie, sinon -1
     */
    public static function genereTbMois($intMoisDebut, $intMoisFin, $modeTB, $modeMois) {
        $tbMois = array();
        for ($i = $intMoisDebut; $i < $intMoisFin; $i++) {
            if ($modeTB == 0) {
                $tbMois[$i] = $i;
            } elseif ($modeTB == 1) {
                $tbMois[$i] = self::getMonth($i + 1, $modeMois);
            } else {
                return -1;
            }
        }
        return $tbMois;
    }

    /**
     * Indique si une valeur est une date au format dd/mm/YYYY
     * @param $valeur une valeur à tester
     * @return boolean vrai ou faux
     */
    public static function isDate($valeur) {
        if (substr($valeur, 0, 1) < 3) {
            if (preg_match("#^[0-2][0-9]{1}/#", substr($valeur, 0, 3))) {
                if (substr($valeur, 3, 1) == 0) {
                    if (preg_match("#[0-9]{1}/#", substr($valeur, 4, 2))) {
                        if (preg_match("#[0-9]{4}$#", substr($valeur, 6, 4))) {
                            return true;
                        }
                    }
                } else {
                    if (preg_match("#1[0-2]{1}/#", substr($valeur, 3, 3))) {

                        if (preg_match("#[0-9]{4}$#", substr($valeur, 6, 4))) {
                            return true;
                        }
                    }
                }
            }
        } else {
            if (preg_match("#^3[0-1]{1}/#", substr($valeur, 0, 3))) {
                if (substr($valeur, 3, 1) == 0) {
                    if (preg_match("#[0-9]{1}/#", substr($valeur, 4, 2))) {
                        if (preg_match("#[0-9]{4}$#", substr($valeur, 6, 4))) {
                            return true;
                        }
                    }
                } else {
                    if (preg_match("#1[0-2]{1}/#", substr($valeur, 3, 3))) {
                        if (preg_match("#[0-9]{4}$#", substr($valeur, 6, 4))) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Retourne le jour d'une date
     * @param $date : un objet DateTime
     * @return le jour
     */
    public static function getDay($date) {
        if (gettype($date) == 'string') {
            $laDate = new DateTime($date);
        } else {
            $laDate = $date;
        }
        return $laDate->format('d');
    }

    /**
     * Retourne l'année d'une date
     * @$date : un objet DateTime
     * @return l'année
     */
    public static function getYear($date) {
        if (gettype($date) == 'string') {
            $laDate = new DateTime($date);
        } else {
            $laDate = $date;
        }
        $laDate = new DateTime($date);
        return $laDate->format('Y');
    }

    /**
     * Retourne le mois d'une date exprimé en français
     * @date : une chaîne représentant une date ou un numéro de mois
     * @mode : 1 == le mois en entier, 2 == le mois abrégé
     * @return le mois ou false si la valeur passé ne correspond à aucun mois
     */
    public static function getMonth($date, $mode) {
        if (!is_numeric($date) && is_string($date)) {// Si ce n'est pas un nombre et que c'est une chaîne
            $laDate = new DateTime($date); // On convertie la chaine au format date
            $month = intval($laDate->format('m')); // Et on récupère le mois
        } else {
            $month = $date; // sinon on à transmis un entier qui équivaut à un mois
        }
        if (!($month > 0 and $month < 13 )) {
            $month = -1;
        }
        switch ($month) {
            case 1 : {
                    if ($mode == 1) {
                        $result = 'Janvier';
                    } else {
                        $result = 'Jan';
                    }
                } break;
            case 2 : {
                    if ($mode == 1) {
                        $result = 'Février';
                    } else {
                        $result = 'Fév';
                    }
                } break;
            case 3 : {
                    if ($mode == 1) {
                        $result = 'Mars';
                    } else {
                        $result = 'Mar';
                    }
                } break;
            case 4 : {
                    if ($mode == 1) {
                        $result = 'Avril';
                    } else {
                        $result = 'Avr';
                    }
                } break;
            case 5 : {
                    $result = 'Mai';
                } break;
            case 6 : {
                    if ($mode == 1) {
                        $result = 'Juin';
                    } else {
                        $result = 'Jun';
                    }
                } break;
            case 7 : {
                    if ($mode == 1) {
                        $result = 'Juillet';
                    } else {
                        $result = 'Jul';
                    }
                } break;
            case 8 : {
                    if ($mode == 1) {
                        $result = 'Août';
                    } else {
                        $result = 'Aug';
                    }
                } break;
            case 9 : {
                    if ($mode == 1) {
                        $result = 'Septembre';
                    } else {
                        $result = 'Sep';
                    }
                } break;
            case 10 : {
                    if ($mode == 1) {
                        $result = 'Octobre';
                    } else {
                        $result = 'Oct';
                    }
                } break;
            case 11 : {
                    if ($mode == 1) {
                        $result = 'Novembre';
                    } else {
                        $result = 'Nov';
                    }
                } break;
            case 12 : {
                    if ($mode == 1) {
                        $result = 'Décembre';
                    } else {
                        $result = 'Dec';
                    }
                } break;
            default : $result = false;
                break;
        }
        return $result;
    }

    /*
     * retourne le jour (en français) correspondant à celui entré en paramétre (en nombre)
     * @param un jour correspondant à sa position dans une semaine (0=>Lundi,1=>Mardi...)
     * @return Retourne un jour correspondant au nombre passé en paramétre ou OOPS en cas d'erreur
     */

    public static function getStrDay($day) {
        if ($day >= 0 and $day < 7) {
            switch ($day) {
                case 0: {
                        $result = "Lundi";
                    }break;
                case 1: {
                        $result = "Mardi";
                    }break;
                case 2: {
                        $result = "Mercredi";
                    }break;
                case 3: {
                        $result = "Jeudi";
                    }break;
                case 4: {
                        $result = "Vendredi";
                    }break;
                case 5: {
                        $result = "Samedi";
                    }break;
                case 6: {
                        $result = "Dimanche";
                    }break;
            }
        } else {
            return "OOPS";
        }
        return $result;
    }

    public static function DayEngToFrDay($Day) {
        switch ($Day) {
            case "Mon" : {
                    return "Lundi";
                }break;
            case "Tue" : {
                    return "Mardi";
                }break;
            case "Wed" : {
                    return "Mercredi";
                }break;
            case "Thu" : {
                    return "Jeudi";
                }break;
            case "Fri" : {
                    return "Vendredi";
                }break;
            case "Sat" : {
                    return "Samedi";
                }break;
            case "Sun" : {
                    return "Dimanche";
                }break;
            default : {
                    return "OOPS";
                }
        }
    }

    public static function DayFrToEngDay($Day) {
        switch ($Day) {
            case "Lundi" : {
                    return "Mon";
                }break;
            case "Mardi" : {
                    return "Tue";
                }break;
            case "Mercredi" : {
                    return "Wed";
                }break;
            case "Jeudi" : {
                    return "Thu";
                }break;
            case "Vendredi" : {
                    return "Fri";
                }break;
            case "Samedi" : {
                    return "Sat";
                }break;
            case "Dimanche" : {
                    return "Sun";
                }break;
            default : {
                    return "OOPS";
                }
        }
    }

    public static function MonthFrToEngMonth($Month) {
        switch ($Month) {
            case "Janvier" : {
                    return "January";
                }break;
            case "Fevrier" : {
                    return "February";
                }break;
            case "Mars" : {
                    return "March";
                }break;
            case "Avril" : {
                    return "April";
                }break;
            case "Mai" : {
                    return "May";
                }break;
            case "Juin" : {
                    return "June";
                }break;
            case "Juillet" : {
                    return "July";
                }break;
            case "Août" : {
                    return "August";
                }break;
            case "Septembre" : {
                    return "September";
                }break;
            case "Octobre" : {
                    return "October";
                }break;
            case "Décembre" : {
                    return "December";
                }break;
            default : {
                    return "OOPS";
                }
        }
    }

    /*
     * retourne le jour (en nombre) correspondant à celui entré en paramétre (en nombre)
     * @param un jour correspondant à sa position dans une semaine (0=>Lundi,1=>Mardi...)
     * @return Retourne un nombre correspondant au jour passé en paramétre ou OOPS en cas d'erreur
     */

    public static function getIntDay($day) {
        if (is_string($day)) {
            switch ($day) {
                case "Lundi": {
                        $result = 0;
                    }break;
                case "Mardi": {
                        $result = 1;
                    }break;
                case "Mercredi": {
                        $result = 2;
                    }break;
                case "Jeudi": {
                        $result = 3;
                    }break;
                case "Vendredi": {
                        $result = 4;
                    }break;
                case "Samedi": {
                        $result = 5;
                    }break;
                case "Dimanche": {
                        $result = 6;
                    }break;
                default : {
                        $result = "OOPS";
                    }
            }
        } else {
            return "OOPS";
        }
        return $result;
    }

    /*
     * Retourne en français la sequence de la journée
     * @param une sequence de la journée (1=> midi,2=>soir)
     * @return retourne Midi, soir ou -1 en cas d'erreur de paramétre
     */

    public static function getStrSequence($sequence) {
        switch ($sequence) {
            case 1: {
                    $result = "Midi";
                }break;
            case 2: {
                    $result = "Soir";
                }break;
            default: {
                    $result = -1;
                }
        }
        return $result;
    }

    public static function getDateFrancais($date) {
        return self::getDay($date) . ' ' . self::getMonth($date, 1) . ' ' . self::getYear($date);
    }

    public static function getDateFrancaisMoisAnnee($date) {
        return self::getMonth($date) . ' ' . self::getYear($date);
    }

    /**
     * getHeureFr génére un tableau avec toutes les heures d'une journée au format Français
     * @param int $spacement l'espacement entre chaque minute
     * @return tab retourne un tableau des heures (de 00:00 à 24:00)
     */
    public static function getHeureFr($spacement) {
        $tbHeure = array();
        if ($spacement > 0) {
            $i = 0;
            for ($i; $i < 24; $i++) {
                if ($i < 10) {
                    $i = "0" . $i;
                }
                $a = 0;
                while ($a < 60) {
                    if ($a < 10) {
                        $a = "0" . $a;
                    }
                    $tbHeure[] .= $i . ":" . $a;
                    $a = $a + $spacement;
                }
            }
            $tbHeure[] .= "24:00";
        }
        return $tbHeure;
    }

    /**
     * @month un mois en français
     * @return un mois en chiffre
     */
    public static function convertMonthToInt32($month) {
        if (gettype($month) == "string") {
            switch ($month) {
                case "Janvier": {
                        $result = 1;
                    }
                    break;
                case "Février": {
                        $result = 2;
                    }
                    break;
                case "Mars": {
                        $result = 3;
                    }
                    break;
                case "Avril": {
                        $result = 4;
                    }
                    break;
                case "Mai": {
                        $result = 5;
                    }
                    break;
                case "Juin": {
                        $result = 6;
                    }
                    break;
                case "Juillet": {
                        $result = 7;
                    }
                    break;
                case "Août": {
                        $result = 8;
                    }
                    break;
                case "Septembre": {
                        $result = 9;
                    }
                    break;
                case "Octobre": {
                        $result = 10;
                    }
                    break;
                case "Novembre": {
                        $result = 11;
                    }
                    break;
                case "Décembre": {
                        $result = 12;
                    }
                    break;
                default: {
                        $result = -1;
                    }
            }
        } else {
            $result = -1;
        }
        return $result;
    }

    /**
     * Test si une chaîne est vide et renvoie un espace dans ce cas, sinon renvoie la chaîne
     * @param $chaine : la chaîne à tester
     */
    public static function testChaineVide($chaine) {
        if (!empty(trim($chaine))) {
            $result = $chaine;
        } else {
            $result = '&nbsp;';
        }
        return $result;
    }

    /**
     * operationDate : retourne en nombre de jour le résultat d'une opération entre deux dates
     * @param : $dateOne
     * @param : $dateTwo
     * @param : $operateur
     * @return : un nombre de jour sous forme d'entier
     */
    public static function operationDate($dateOne, $DateTwo, $operateur) {
        if (
                checkdate(
                        (date('m', strtotime($dateOne))), (date('d', strtotime($dateOne))), (date('Y', strtotime($dateOne)))
                )
                and
                checkdate(
                        (date('m', strtotime($DateTwo))), (date('d', strtotime($DateTwo))), (date('Y', strtotime($DateTwo)))
                )
        ) {
            switch ($operateur) {
                case "-": {
                        $result = strtotime($dateOne) - strtotime($DateTwo);
                        $result = $result / 60 / 60 / 24;
                    }break;
                case "+": {
                        $result = strtotime($dateOne) + strtotime($DateTwo);
                        $result = $result / 60 / 60 / 24;
                    }break;
                case "*": {
                        $result = strtotime($dateOne) * strtotime($DateTwo);
                        $result = $result / 60 / 60 / 24;
                    }break;
            }
        } else {
            $result = -1;
        }
        return $result;
    }

    /**
     * SQL retire des informations aux heures par exemple 00:00 devient 0 
     * editHour sert donc à rééditer les heures
     * @param string $uneHeure une Heure venant de la BDD
     */
    public static function EditHour($uneHeure) {
        if (strlen($uneHeure) == 1) {//SQL transforme 00:00 en 0
            $return = '0' . $uneHeure . ':00';
        } elseif (strlen($uneHeure) == 2) {//SQL transforme 24:00 en 24
            $return = $uneHeure . ':00';
        } else {
            $return = $uneHeure;
        }
        return $return;
    }

    /**
     * Permet d'ajouter un nombre d'heures à un horraire
     * @param string $temps un horraire au format H:is ou au format H:i ou au format H
     * @param int $addHeure un nombre d'heure à ajouter
     */
    public static function ajoutHeure($temps, $addHeure = 1) {
        try {
            $heure = date('H', strtotime($temps));
            $heure += $addHeure;
            $hPlus = date('H', strtotime(self::EditHour($heure)));
            $result = date('H:i', strtotime($hPlus . ':' . date('i', strtotime($temps))));
            return($result);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Convertie une date française au format d/m/Y vers le format anglais m/d/Y
     * @param type $value
     * @return type
     */
    public static function dateFrIntoDateEng($value) {
        $day = substr($value, 0, 2);
        $month = substr($value, 3, 2);
        $year = substr($value, 6);
        $date = ($month . '/' . $day . '/' . $year);
        return $date;
    }

    /**
     * getPreviousYear Obtient toutes les années avant l'année actuel (année actuel incluse)
     * @param int $nbYears : Indique le nombre d'années avant l'année courante, que l'on souhaite récupérer
     * @return Array(\) Retourne un tableau d'année
     */
    public static function getPreviousYear($nbYears = 0) {
        $tbYears = array();
        $initAnnee = date('Y');
        if ($nbYears == 0) {
            $tbYears [] .= $initAnnee;
        }
        for ($i = 0; $i < $nbYears; $i++) {
            $tbYears [] .= ($initAnnee - $i);
        }
        return $tbYears;
    }

    /**
     * BanalizedString Banalise une chaîne de caractères, en vue de la passer dans une requête SQL
     * @param string $search : Une chaîne de caractères à banaliser
     * @return string une chaîne de caractère banaliser
     */
    public static function BanalizedString($search){
        $search = htmlspecialchars($search);
        $search = preg_replace("%'|\"%", '', $search);
        return $search;
    }
    
    /**
     * compterArticles Compte le nombre d'articles contenu dans un tableau d'articles
     * en omettant les articles archivés
     * @param array(/Article) $tbArticles Un tableau contenant des objets de type \Article
     */
    public static function compterArticles($tbArticles)
    {
        $total = 0;
        foreach ($tbArticles as $article){
            if(!$article->getArchive() || (isset($_SESSION['connected_user']) && WorkBench::isAllowed($_SESSION['connected_user'], USER_STATUS_MODO))){
                $total++;
            }
        }
        return $total;
    }
}
