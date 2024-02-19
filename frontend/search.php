<?php
/**
 * Seite zum Suchen von News oder Providern
 */

require './includes/misc.php';

//Der Suchstring
$searchQuery = "";

//Die Resultats-Arrays
$newsItems = array();
$provider  = array();

if(isset($_GET["query"])) {
    $searchQuery = strval($_GET["query"]);

    //Hole Ergebnisse
    $searchResult = file_get_contents($newshubBaseUrl . "api/rest.php?search=" . urlencode($searchQuery));
    if (!is_bool($searchResult) && is_string($searchResult)) {
        $searchResult = json_decode($searchResult,True);
        //var_dump($searchResult);

        //Die jeweiligen Resultats-Arrays zuweisen
        $newsItems = $searchResult["newsitems"];
        $provider  = $searchResult["provider"];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php printHeadContent("Newshub | Search"); ?>
    
    <link rel="stylesheet" type="text/css" href="./css/items/newsitem.css">
    <link rel="stylesheet" type="text/css" href="./css/items/provider.css">
</head>
<body>

    <?php printNavbar($searchQuery); ?>

    <div id="content">
        <?php
            //Ob NewsItems und/oder Provider gefunden wurden
            $newsItemCount = count($newsItems);
            $providerCount = count($provider);
            if($newsItemCount > 0 || $providerCount > 0) {
                //Gefundene Suchergebnisse formatiern (in HTML ausgeben)
                echo("<p>" . ((int) $newsItemCount) . " Suchergebnisse gefunden</p>");
                
                //Ausgabe der Provider
                require './includes/provideroutput.php';
                printProviderBox($provider,"Provider");

                //Ausgabe der NewsItems
                require './includes/newsitemoutput.php';
                outputNewsItems($newsItems);

            } else {
                //Keine Suchergebnisse gefunden
                echo("<h1>Keine Suchergebnisse gefunden</h1>");
            }            
        ?>
    </div>

</body>
</html>