/*
 * portfolio - portfolio
 * © Vincent, 2017
 * _redir | JS - redirection dynamique vers page php
 *
 * 	@author :
 * 	@date : 12 janv. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

/**
 * @param {string} uc : Le use case à indiqué (dans le cadre d'ue architecture MVC)
 * @param {string} action : l'action à effectuer dans un controleur (dans le cadre d'une architecture MVC)
 * @param {string} option : l'option à effectuer dans l'action sélectionner du controleur
 * @param {array} array : tableau de données envoyé au script
 * @param {string} callback : Un selecteur javascript pour cibler le ou les objets du DOM dans lesquelles on souhaites retourner les données obtenues
 * @param {boolean} beamMeUp : Indique si l'on souhaite renvoyer l'utilisateur en haut de page 
 * @param {string} waitMessage : Si l'on désire afficher un message d'attente lors du chargement de la page, alors il suffit
 *                               de saisir un message, sinon rien ne serat affiché
 * @returns {string} le code html renvoyer par l'éxecution du script
 */
function sendToAjax(uc, action, option, array, callback, beamMeUp, waitMessage){
    if(DEBUG_MODE === 1){
        console.log(uc, action, option, array);
    }
    clearPage();
    if (typeof waitMessage == "string") {
        displayMessage('patientMessage', waitMessage);
    }
    if (beamMeUp != "undefined" && beamMeUp !== false) {
        window.scrollTo(0, 0);
    }
    $(document).ready(function () {
        $.post(
                'Ajax/index.ajx.php', // URL du script à executer
                {
                    // Données envoyées
                    uc: uc,
                    action: action,
                    option: option,
                    array: array
                },
                function (data) {
                    $(callback).html(data);
                },
                'text' // Format de retour : texte
                );
    });
}

//edit : vincp -> N'est plus utilisé depuis que l'affichage des modals à été fixé
//edit : vincp -> Est à nouveau utilisé depuis qu'on accéde au formulaire via une modal
/**
* clearPage supprime la classe modal-backdrop de la page pour ne pas caché l'ensemble de cette dernière
*/
function clearPage() {
    var div = document.getElementsByTagName('DIV');// Le corps de la page web
    for (i = 0; i < div.length; i++)
    {
        div[i].classList.contains('modal-backdrop');
        if (div[i].classList.contains('modal-backdrop')) {
            div[i].classList.remove('modal-backdrop');
            var parent = div[i].parentElement;
            parent.removeChild(div[i]); 
        }
    }
}


/**
 * displayMessage : Affiche un message dans la zone sélectionné
 * @param {string} id : l'id de la zone à affiché
 * @param {string} waitMessage
 * @param {string} annyPic : ajout l'image de chargement par défaut
 */
function displayMessage(id, waitMessage) {
    waitZone = document.getElementById(id);
    waitZone.style.display = "block";
    msg = waitMessage;
    msg = '<br><i class="fa fa-cog fa-spin fa-3x fa-fw"></i>' + '<span class="sr-only">Loading...</span>' + waitMessage;
    var loadMSG = document.createElement('div');
    loadMSG.innerHTML = msg;
    if (waitZone.hasChildNodes()) {
        waitZone.innerHTML = "";
    }
    waitZone.appendChild(loadMSG);
}

function eraseWait(id) {
    document.getElementById(id).style.display = "none";
}