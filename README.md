/**********Syntaxe de documentation a respecter**********/

1. Toutes documentation d'une fonction (ormis les constructeurs auquel on enl�ve le nom de la fonction) est etablie selon le schema suivant : 
/** NomDeLaFonction Cette fonction fait (remplacer par la description de la fonction en commencant la phrase par une majuscule)
* @param type $param (facultatif, défaut = "") : remplacer par la description du parametre en commencant la phrase par une majuscule
* @param Array(\ClasseContenueDansLeTableau) $param (facultatif, défaut = ""): (remplacer par la description du parametre en commencant la phrase par une majuscule)			    <------- parametre tableau
* @param type Retourne : (remplacer par le retour de la fonction en commencant la phrase par une majuscule)						    <------- Retour de la fonction
*/

2. pour donner une indication de l'endroit ou se trouve une constante cit� dans une documentation, il convient d'utiliser la convention suivante : 
(chemin/absolue/pour/la/constante/librairieDeLaConstante.php -> 2.2 <------- Le num�ro de la section dans laquel on trouve la definition de la constante)


3. Respect au maximum de la notation camel des fonctions sur l'ensemble du site

4. Laisser un commentaire //FFD-Flag For Deletion pour marquer un bout de code à revoir, ou à supprimer utlérieurement
