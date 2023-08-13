<?php

/*
 * portfolio - portfolio
 * © vincp, 2017
 * articles | articles.consts.php -
 *
 * 	@author :
 * 	@date : 2 févr. 2017
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

// 1 - Article //
define('MAX_LEN_TITRE_EVENT', 125); //La taille maximal d'un titre d'un article
define('MAX_LEN_TEXTE_EVENT', 65535); //La taille maximal d'un texte d'un article

define('NOMBRE_ARTICLE_PAR_FLUX',4);
define('NB_ARTICLES_PAR_PAGE',3);
// 2 - INTERET //
// 2.1 - Style CSS par niveau d'intérêt //
define('INTERET_STANDARD', 'primary');
define('INTERET_NEWS', 'success');
define('INTERET_IMPORTANT', 'danger');

// 2.2 - Niveaux d'intêret pour les articles //
define('STANDARD', 1);
define('NEWS', 2);
define('IMPORTANT', 3);

// 1.1 - Regex permettant de parser les titres des sites

define('REGEX_RSS_URL', "www\.|https:\/\/|http:|http:\/\/|\/feed|\/rss.*|\/|\.php|.xml");

// 1.1 - Regex permettant de parser les liens dans les flux rss

define('REGEX_RSS_REPL', "\/feed.*|\/rss.*");

// 3- Tags //
// La longueur maximum du nom d'un tag
define('MAX_LEN_NOM_TAG', 100);
// Lorsque l'on affichera des tags pour un projet le maximum serat de 5 tag affiché
define('MAX_DISPLAYING_TAG', 5);

// 5 - Ressources //
define('TYPE_RESSOURCE_IMAGE', 1);
define('TYPE_RESSOURCE_DOCUMENT', 2);
define('TYPE_RESSOURCE_URL', 3);

// 6 - CALENDAR //
define('FIRST_YEAR_ADMIT', 2000);
define('TIME_TO_LIVE_ARTICLE', 20); // durée de vie d'un article (avant que l'on ne puisse plus y accéder) en année
// 7 - Projet //
define('TYPE_PROJET_SITE', 1);
define('TYPE_PROJET_LOGICIEL', 2);
define('TYPE_PROJET_MODELISATION', 3);

// 7.1- Projet par page //
define('NB_PROJETS_PAR_PAGE',3);// sur la page d'accueil des projets par exemple, on affichera 5 projet

// 8 - Contenue //
define('TYPE_CONTENUE_ARTICLE', 1);
define('TYPE_CONTENUE_PROJET', 2);

// 9 - Membre //
// 9.1 - Membre status //
define('USER_STATUS_BANNED', 1);
define('USER_STATUS_MEMBRE', 2);
define('USER_STATUS_WRITER', 3);
define('USER_STATUS_MODO', 4);
define('USER_STATUS_ADMIN', 5);
define('USER_STATUS_SUPER_ADMIN', 6);
// 9.2 - Membre classe Bootstrap //
define('GRADE_BANNED','ban');
define('GRADE_MEMBRE','user');
define('GRADE_WRITER','pencil');
define('GRADE_MODO','users');
define('GRADE_ADMIN','user-secret');
define('GRADE_SUPER_ADMIN','hand-spock-o');
// 9.3 - Membre max articles favoris affiché //
define('MAX_ARTICLES_DISPLAY',5);
// 9.4 - Expression régulière validant le format du pseudo //
define('REGEX_PSEUDO', "^[\\wàáâãäåçèéêëìíîïðòóôõöùúûüýÿÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÒÓÔÕÖÙÚÛÜÝŸ]{5,30}$");
// 9.5 - Expression régulière validant le format du mot de passe //
define('REGEX_PASSWORD', "^([\\wàáâãäåçèéêëìíîïðòóôõöùúûüýÿÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÒÓÔÕÖÙÚÛÜÝŸ+\\-*\\/]){5,19}$");