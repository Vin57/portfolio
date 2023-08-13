/*
 * portfolio - portfolio
 * © vincp, 2017
 * _shaker | _shaker.js - fichier charger de vérifier des données
 *
 * 	@author : vincp
 * 	@date : 4 déc. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

/**
 * ckValue
 * @param {string} id_Data l'identifiant de la donnée à vérifier
 * @param {string} pregex une expression régulière
 * @param {number} (facultatif)len_maxi la longueur maximale de la donnée à vérifier (si c'est un nombre sa valeur maximal)
 * @param {number} (facultatif)len_mini la longueur minimale de la donnée à vérifier (si c'est un nombre sa valeur minimal)
 * @returns {Boolean} false si la donnée est incorect
 */
function ckValue(id_Data, pregex, len_mini, len_maxi) {
    // Test de l'existence de l'identifiant de la donnée à analyser
    if (typeof id_Data != "undefined" && id_Data != null)
    {
        var toCheck = document.getElementById(id_Data); //L'objet à analyser
        if (toCheck != null) {
            var toCheck = toCheck.value;
            // Test de l'existence de l'expression régulière obligatoire
            if (pregex != null) {
                if (typeof pregex == "object")
                {
                    var result_pregexp = pregex.test(toCheck);
                } else {
                    var result_pregexp = (toCheck == pregex);
                }
            } else {
                throw new Error('ckValue() require one parameter !');
                return false;
            }
            if (!result_pregexp) {
                return false;// Si la valeur est inconforme à celle attendue
            }


            // Test de l'existence du paramètre facultatif len_maxi
            if (typeof len_maxi != "undefined" && len_maxi != null)
            {
                if (typeof toCheck == "string") {
                    if (toCheck.length > len_maxi)
                    {
                        return false;
                    }
                }
                if (typeof toCheck == "number") {
                    if (toCheck > len_maxi)
                    {
                        return false;
                    }
                }
            }


            // Test de l'existence du paramètre facultatif len_mini
            if (typeof len_mini != "undefined" && len_mini != null)
            {
                if (typeof toCheck == "string") {
                    if (toCheck.length < len_mini)
                    {
                        return false;
                    }
                }
                if (typeof toCheck == "number") {
                    if (toCheck < len_mini)
                    {
                        return false;
                    }
                }
            }
        } else {
            throw new Error("object : " + id_Data + " does not exists");
            return false;
        }
    } else {
        throw new Error("ckValue() require id");
        return false;
    }
    // Si l'execution de la fonction parvient jusqu'ici,
    // alors tous les tests on réussit, on conssidére donc la valeur comme valide
    return true;
}

/**
 * Affiche un message pour informer sur une erreur à propos d'une saisie
 * @param {string} id l'identifiant de l'élément concernant le message
 * @param {type} id_zone_message l'identifiant de l'endroit où le message doit apparaître
 * @param {type} message le message à afficher
 */
function messageDisplay(id, id_zone_message, message, type)
{
    messageReinit(id, id_zone_message);// On réinitialise le message
    var element_concern = document.getElementById(id);
    var zoneDisplay = document.getElementById(id_zone_message);
    switch (type) {
        case (ERROR):
            {
                element_concern.classList.add(CSS_ALERT_DANGER_CLASS);
            }
            break;
        case (WARNING):
            {
                element_concern.classList.add(CSS_ALERT_WARNING_CLASS);
            }
            break;
    }
    zoneDisplay.innerHTML = message;
}

/**
 * Le message, ainsi que les classes ayant pu être ajouter sont supprimé
 * @param {string} idMessage l'identifiant du message à supprimer
 * @@param {string} idObjet l'identifiant de l'objet dont on doit supprimer les classes 
 */
function messageReinit(idObjet, idMessage)
{
    var element = document.getElementById(idObjet);
    if (element.classList.contains(CSS_ALERT_DANGER_CLASS)) {
        element.classList.remove(CSS_ALERT_DANGER_CLASS);
    }
    if (element.classList.contains(CSS_ALERT_WARNING_CLASS)) {
        element.classList.remove(CSS_ALERT_WARNING_CLASS);
    }
    if (element.classList.contains(CSS_ALERT_SUCCESS_CLASS)) {
        element.classList.remove(CSS_ALERT_SUCCESS_CLASS);
    }
    var message = document.getElementById(idMessage);
    message.innerHTML = null;
}

/**
 * La saisie à valider
 * @param {string} id l'identifiant de l'objet à valider
 */
function validSection(id)
{
    var element = document.getElementById(id);
    if (element.classList.contains(CSS_ALERT_DANGER_CLASS)) {
        element.classList.remove(CSS_ALERT_DANGER_CLASS);
    }
    if (element.classList.contains(CSS_ALERT_WARNING_CLASS)) {
        element.classList.remove(CSS_ALERT_WARNING_CLASS);
    }
    element.classList.add(CSS_ALERT_SUCCESS_CLASS);
}