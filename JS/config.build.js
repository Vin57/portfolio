/*
 * JS - Constante javascript
 * @author : Vincent
 * @modify : 'in case of modification edit this and add an other modify section like this'
 * @date : 10 déc. 2016
 */

/**
 * CONSTANTES DU FICHIER shaker.js
 */
const ERROR = 1;
const WARNING = 2;
const CSS_ALERT_DANGER_CLASS = 'alert-danger';
const CSS_ALERT_WARNING_CLASS = 'alert-warning';
const CSS_ALERT_SUCCESS_CLASS = 'alert-success';
/**
 * CONSTANTES DU FICHIER redirect.js
 */
const DEBUG_MODE = 1;

// REGEX
// Expression régulière validant le format du pseudo //
const REGEX_PSEUDO = "^[\\wàáâãäåçèéêëìíîïðòóôõöùúûüýÿÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÒÓÔÕÖÙÚÛÜÝŸ]{5,30}$";
// Expression régulière validant le format du mot de passe //
const REGEX_PASSWORD = "^([\\wàáâãäåçèéêëìíîïðòóôõöùúûüýÿÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÒÓÔÕÖÙÚÛÜÝŸ+\\-*\\/]){5,19}$";