/*
 * portfolio - portfolio
 * © vincp, 2017
 * _dataManage | _dataManage.js - En charge de la gestion de données
 *
 * 	@author :
 * 	@date : 18 janv. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

/**
 * Extrait les valeurs à partir d'un tableau d'objet
 * @param {Array} table tableau d'objet du DOM
 * @returns {Array} Retourne le tableau contenant les valeurs extraite du tableau d'objet
 */
function extractValueOf(table) {
    var resultTable = new Array();
    for (var i = 0; i < table.length; i++) {
        resultTable[table[i].name] = table[i].value;
    }
    return resultTable;
}

/**
 * Récupére les valeurs des éléments contenus dans le parent donné en premier paramètre
 * @param {object} elementObject L'élément du DOM contenant des valeurs à obtenir
 * @param {string} targetAttribute L'attribut dans lequel les valeurs sont sauvegardés
 * @returns {Array} Retourne un tableau de valeur
 */
function getValueOfElement(elementObject, targetAttribute)
{
    var resultTable = new Array();
    var childOfElement = elementObject.children;
    for (var i = 0; i < childOfElement.length; i++) {
        var value = childOfElement[i].getAttribute(targetAttribute);
        if (value != null) {
            resultTable[i] = value;
        }
    }
    return resultTable;
}

/**
 * Convertit les valeurs d'un tableau dans un 
 * @param {Array} table Un tableau
 * @returns {String} Une chaine de charactére
 */
function convertArayToStringValue(table, delimiter) {
    var str = "";
    for (var i = 0; i < table.length; i++) {
        str += table[i] + delimiter;
    }
    return str;
}

/*
 * Renvoie le fil d'ariane
 */
function returnArianne(array) {
    console.log(array);
}