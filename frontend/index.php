<?php
/**
 * Hauptseite
 */

require './includes/misc.php';

//Daten / Statistiken besorgen
$frontendStats = file_get_contents($newshubBaseUrl . "api/rest.php?frontend&newscount&newestnews&providercount&mostnews&randomprovider&allprovider");
if(!is_bool($frontendStats) && is_string($frontendStats)) {
    $frontendStats = json_decode($frontendStats,True);
    //var_dump($frontendStats);
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php printHeadContent("Newshub | Home"); ?>

    <link rel="stylesheet" type="text/css" href="./css/items/newsitem.css">
    <link rel="stylesheet" type="text/css" href="./css/pages/providerview.css">
    <link rel="stylesheet" type="text/css" href="./css/items/provider.css">
    <link rel="stylesheet" type="text/css" href="./css/pages/index.css">
</head>
<body>
    
    <?php printNavbar(""); ?>

    <div id="content">
        <div id="newsstats">
            <h3>Aktuell sind insgesamt <?php echo($frontendStats["newscount"]); ?> News in der Datenbank gespeichert</h3>

            <h4>Die aktuellsten News:</h4>
            <?php
                require './includes/newsitemoutput.php';
                outputNewsItems($frontendStats["newestnews"]);
            ?>
        </div>

        <?php require './includes/provideroutput.php'; ?>

        <div id="providerstats">
            <div id="providerwrapper">
                <div class="selectedprovider">
                    <h3>Provider mit den meisten News</h3>
                    <?php printProviderBox(array($frontendStats["mostnews"])); ?>
                </div>

                <div class="selectedprovider">
                    <h3>Ausgesuchter Provider</h3>
                    <?php printProviderBox(array($frontendStats["randomprovider"])); ?>
                </div>
            </div>
        </div>

        <div id="allprovider">
            <h3>Insgesamt sind <?php echo($frontendStats["providercount"]); ?> Provider in der Datenbank hinterlegt</h3>
            <?php
                $allProvider = array($frontendStats["allprovider"]);
                foreach($allProvider as $provider) {
                    printProviderBox($provider);
                }
            ?>
        </div>
    </div>
    
</body>
</html>