/*
 * portfolio - portfolio
 * © Vincent, 2016
 * _animation | JS - script charger d'animer les pages
 *
 * 	@author : PV
 * 	@date : 16 déc. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

/**
 * Add the selected class to a specifie element
 * @param {type} id The id of the target element
 * @param {type} className An class name
 */
function addClass(id, className)
{
    var element = document.getElementById(id);
    if (element != null) {
        if (!element.classList.contains(className)) {
            element.classList.add(className);
        } else {
            element.classList.remove(className);
        }
    }
}

// INUTILISÉ DEPUIS LA VERSION 8 (garder pour historique)
///**
// * Advance a progress bar
// * @param {type} id the id of the progress bar
// * @param {type} n the current width of the progress bar
// * @param {type} maxValue the maximum width of the progress bar
// */
//function barProgress(id, n, maxValue) {
//    var element = document.getElementById(id);
//    if (element != null) {
//        element.style.width = n + "%";
//        if (n < maxValue) {
//            setTimeout(function () {
//                barProgress(id, n + 1, maxValue);
//            }, 10);
//        }
//    }
//}
//
///**
// * Call function to anim the profil of the user
// */
//function animProfil() {
//    barProgress('progressC', 0, 60);
//    barProgress('progressPhp', 0, 70);
//    barProgress('progressSql', 0, 60);
//    barProgress('progressHtml', 0, 65);
//    barProgress('progressJavaScript', 0, 45);
//    barProgress('progessSymfony', 0, 40);
//    barProgress('progressBootstrap', 0, 40);
//}


/**
 * Hide navigation bar when the user click anywhere on the body
 */
$('body').click(function (e) {
    //Add event to navbar element//
    if (e.target.id != 'reduce-navbar') {
        var navbar = document.getElementById('navbar');
        if (navbar.classList.contains('in')) {
            navbar.classList.remove('in')
        }
    }
})

/**
 * Hide the previous displayed element and call function appearSlow on the element with the specifie id
 * @param {string} id The id of the element to display
 * @param {int} maxWidth The maximum width of the display content
 * @param {int} maxHeight The maximum height of the display content
 */
function displayContent(id, maxWidth, maxHeight) {
    //First we select the previous element wich is pass in this function
    var previous = document.getElementsByClassName('onCurrentViewWithDisplayContentFunction')[0];//It can only be one element at the time with this class name 
    console.log(previous);
    var e = document.getElementById(id);
    if (!e.classList.contains('onCurrentViewWithDisplayContentFunction')) {
        e.style.position = "absolute"
        e.style.zIndex = 999998;//Maximu value of z-index
        e.style.display = 'block'
        e.classList.add('onCurrentViewWithDisplayContentFunction');
    }
    if (previous != null) {
        previous.classList.remove('onCurrentViewWithDisplayContentFunction');
        previous.style.display = 'none';
    }
    appearSlow(id, null, null, maxWidth, maxHeight);
}

/**
 * Reveal an specific elements with an animation wich made the content look grower
 * @param {string} id the id of the element
 * @param {int} nWidth the current width of the element
 * @param {int} nHeight the current height of the element
 * @param {int} maxWidth the max width of the element
 * @param {int} maxHeight the max height of the element
 */
function appearSlow(id, nWidth, nHeight, maxWidth, maxHeight) {
    var element = document.getElementById(id);
    nWidth += 5;
    nHeight += 5;
    element.style.width = nWidth + "%";
    element.style.height = nHeight + "%";
    if (nHeight < maxHeight || nWidth < maxWidth) {
        setTimeout(function () {
            appearSlow(id, nWidth, nHeight, maxWidth, maxHeight);
        }, 5)
    }
}

/**
 * AttribId affecte une valeur à la balise identifiant par l'id idBaliseToSet
 * @param {string} valueToSet la valeur à affecter
 * @param {string} idBaliseToSet l'identifiant de la balise à laquel on doit affecter la valeur valueToSet
 */
function attribValue(valueToSet, idBaliseToSet)
{
    document.getElementById(idBaliseToSet).value = valueToSet;
}

/**
 * Un élément à mettre en évidence
 * @param {string} balise la balise à mettre en évidence
 * @param {string} le nom du groupe d'éléments à mettre en évidence (comme un groupe de bouttons radio)
 */
function setEvidence(balise, group_name) {
    var groupKey = 'setEvidence' + group_name;//La clé permettant d'identifier le groupe de balise
    var previousSetEvidence = document.getElementsByClassName(groupKey)[0];
    //Si un élément à déjà été mis en évidence dans le groupe
    if (previousSetEvidence != undefined) {
        previousSetEvidence.classList.remove(groupKey);
        previousSetEvidence.style.border = "";
    }
    balise.classList.add(groupKey);//On marque la balise comme ayant été mise en évidence
    balise.style.border = "thick solid black";
}

/**
 * Créé et ajoute un élément dans l'élément parent choisit
 * @param {string} tagElement Le tag de l'élément créé
 * @param {string} nameElement Le nom de l'élément créé
 * @param {string} idElement L'identifiant de l'élément créé
 * @param {array} classElement Un ensemble de classe à ajouté à cet élément
 * @param {string} contentElement le contenue de l'élément
 * @param {string} idParentElement L'identifiant de l'élément parent auquel on va ajouter l'élément créé
 * @param {boolean} withClose : Indique si l'on doit ajouter une croix permettant de supprimer l'élément du DOM
 */
function creatElement(tagElement, nameElement, idElement, classElement, contentElement, idParentElement, withClose) {
    var newElement = document.createElement(tagElement);
    var parent = document.getElementById(idParentElement);
    var foundDuplicata = false;

    // Construction de l'élément
    newElement.name = nameElement;
    newElement.id = idElement;
    classElement.forEach(function (element) {
        newElement.classList.add(element)
    });
    newElement.innerHTML = contentElement;

    if (withClose) {
        newElement.innerHTML += "<span class='btn btn-xs close' onclick='$("+idParentElement+").children("+idElement+").hide()'>×</span>";
    }
    for (var i = 0; i < parent.childNodes.length; i++) {
        if (parent.childNodes[i].id == newElement.id &&
                parent.childNodes[i].innerHTML == newElement.innerHTML) {
            foundDuplicata = parent.childNodes[i];
        }
    }
    if (!foundDuplicata) {
        // Si c'est un nouvel élément, on l'ajoute au parent
        parent.appendChild(newElement);
    } else {
        // Si le noeud parent contient déjà l'élément, on le retire
        if($(foundDuplicata).is(":visible")){
            // Si l'élément est visible, on le supprime
            parent.removeChild(foundDuplicata);
        } else {
            // Si c'est un élément déjà existant, mais qu'il avait été masqué, on l'affiche de nouveau
            $(foundDuplicata).show();
        }
    }
}

/**
 * Supprime e de l'élément parent
 * @param {object} e Élément du DOM
 */
function dropParent(e) {
    e.parentNode.removeChild(e);
}

/**
 * Cache l'élément si celui-ci est affiché, sinon l'affiche
 * @param {mixed} element L'objet, ou l'identifiant de l'objet
 */
function hideOrDisplay(element) {
    if (typeof $("#returnTagArticle") == "object") {
        var object = element;
    } else if (typeof $("#returnTagArticle") == "string") {
        var object = document.getElementById(idElement);
    }
    if (object.style.visibility == "hidden") {
        object.style.visibility = "visible";
    } else {
        object.style.visibility = "hidden";
    }
}

