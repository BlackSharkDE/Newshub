<?php
/**
 * Leitet auf die Hauptseite eines Providers weiter.
 * 
 * Erwartet den URL-Parameter (GET) "providername" mit dem Namen (String) eines Providers
 * 
 * Beispiele:
 * - http://localhost/newshub/api/redirect.php?providername=Ein Name eines Providers
 * - http://localhost/newshub/api/redirect.php?providername=Ein%20Name%20eines%20Providers
 */

require 'db/dbo.php';

if(isset($_GET["providername"])) {
    
    //GET-Parameter einlesen
    $providerName = strval($_GET["providername"]);
    
    //Muss ein String sein
    if(is_string($providerName)) {
        
        //Informationen des Providers besorgen
        $pI = getProviderInfo($providerName);
        //var_dump($pI); //DEBUG

        //Ergebnis muss 7 Einträge enthalten
        if(count($pI) === 7) {
            //Attribut für die Website-URL auswählen
            $websiteUrl = $pI["websiteurl"];

            //Weiterleitung
            header('Location: ' . $websiteUrl);
        
        } else{
            echo('providername "' . $providerName . '" unbekannt');
        }

    } else {
        echo("providername ist kein String");
    }

} else {
    echo("providername Parameter fehlt");
}