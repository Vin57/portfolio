<?php
/*
 * portfolio - portfolio
 * � Vincent, 2016
 * index | PHP - Fichier de routes principale (router en fonction des donn�es recu par le serveur)
 *
 * 	@author : PV
 * 	@date : 16 d�c. 2016
 *      @modify : 'in case of modification edit this and add an other modify section like this'
 */

namespace Main;

include('include/consts/_globals.php');
include('include/_util.inc.php');
include('include/_app.inc.php');
include('include/_render.php');

require_once('Modele/Autoloader.php');

Autoloader::register();

session_start();

?>
<!DOCTYPE HTML>
<html>
    <?php
    include('Vues/v_head.html');
    ?>
    <div id="page_header">
        <?php
        include('Vues/v_library.html');
        include('Vues/v_header.html');
        ?>
    </div>
    <body>
        <div id="fullAjax" class="container-fluid">
            <div id="returnAjax">
                
            </div>
        </div>
    </body>
    <script>
        sendToAjax('accueil', null, null, null, '#returnAjax', true, 'Chargement de l\'accueil en cours');
    </script>
    <noscript><div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Erreur JavaScript</h3>
                </div>
                <div class="panel-body">
                    Ce site requiert l'activation de JavaScript pour fonctionner
                </div>
            </div>
        </div>
    </div></noscript>
</html>
<?php
\Render::HideWaitMessage("patientMessage");
?>