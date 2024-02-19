<?php
/**
 * Gibt den Feed eines Providers aus
 */

require './includes/misc.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <?php printHeadContent("Newshub | Providerview"); ?>
    
    <link rel="stylesheet" type="text/css" href="./css/items/newsitem.css">
    <link rel="stylesheet" type="text/css" href="./css/pages/providerview.css">
</head>
<body>

    <?php printNavbar(); ?>

    <div id="content">
        <?php
            //GET-Parameter "name" muss gesetzt sein
            if(isset($_GET["name"])) {
                $providerName = strval($_GET["name"]);
                $providerName = urlencode($providerName);

                //Welche Seite des Providers angezeigt wird
                $currentPage = 1; //Standardmäßig Seite 1
                if(isset($_GET["page"])) {
                    $currentPage = intval($_GET["page"]);
                }

                //Informationen zum Provider
                $providerInfos = file_get_contents($newshubBaseUrl . "api/rest.php?providerinfo=" . $providerName);
                if(!is_bool($providerInfos) && is_string($providerInfos)) {
                    $providerInfos = json_decode($providerInfos,True);
                    //var_dump($providerInfos);

                    //Valides Ergebnis
                    if(is_array($providerInfos)) {
                        
                        echo(
                            "\n\t\t" . '<div class="provider">' .
                            "\n\t\t\t" . '<h1><img class="providericon_view" src="' . $newshubBaseUrl . 'frontend/provider_icons/' . $providerInfos["providerid"] . '.png" onerror="this.style.display=\'none\'"><a class="providername" href="' . $providerInfos["websiteurl"] . '" target="_blank">' . $providerInfos["name"] . '</a></h1>' .
                            "\n\t\t\t" . '<p><i>' . $providerInfos["slogan"] . '</i></p>' .
                            "\n\t\t\t" . '<p><b>' . $providerInfos["newscount"] . '</b> gespeicherte News | <a class="providerliveview" href="' . $newshubBaseUrl . 'api/liveview.php?providername=' . $providerInfos["name"] . '" target="_blank">' . "Liveview (Feed)" . '</a></p>' .
                            "\n\t\t\t" . '<p>Letztes Update: ' . $providerInfos["lastupdate"] . '</p>' .
                            "\n\t\t\t" . '<p>Nächstes Update: ' . $providerInfos["nextupdate"] . '</p>' .
                            "\n\t\t" . '</div>'
                        );

                        //Hole NewsItems
                        $providerNews = file_get_contents($newshubBaseUrl . "api/rest.php?providernews=" . $providerName . "&group&page=" . $currentPage);
                        if(!is_bool($providerNews) && is_string($providerNews)) {
                            $providerNews = json_decode($providerNews,True);
                            //var_dump($providerNews);
                            
                            require './includes/newsitemoutput.php';

                            $dateNames = array_keys($providerNews);
                            //var_dump($dateNames);

                            for($i = 0; $i < count($providerNews); $i += 1) {
                                $dateName = $dateNames[$i];
                                echo('<h3 class="newsitemdaydate">' . date("l - d F Y",strtotime($dateName)) . "</h3>");
                                outputNewsItems($providerNews[$dateName]);
                                echo('<hr class="newsitemdayseperator">');
                            }

                            //-- Buttons für das Vor- und Zurückblättern --
                            echo("\n" . '<br><div id="buttonsdiv">');

                            //Vorherige und Nächste Seite
                            $previousPage = $currentPage - 1;
                            $nextPage     = $currentPage + 1;

                            //Wenn nicht mehr auf Seite 1, den Zurück-Button einblenden
                            if($currentPage > 1) {
                                echo('<a class="BackAndForthButtons" href="' . $newshubBaseUrl . 'frontend/providerview.php?name=' . $providerName . '&page=' . $previousPage . '">Zurück auf Seite ' . $previousPage . '</a>');
                            }

                            //Wenn nächste Seite noch Newsitems haben wird, den Weiter-Button einblenden
                            if((($nextPage * 100) - 100) < intval($providerInfos["newscount"])) {
                                echo('<a class="BackAndForthButtons" href="' . $newshubBaseUrl . 'frontend/providerview.php?name=' . $providerName . '&page=' . $nextPage . '">Weiter zu Seite ' . $nextPage . '</a>');
                            }

                            echo("</div>");

                        } else {
                            redirectHome();
                        }

                    } else {
                        redirectHome();
                    }

                } else {
                    redirectHome();
                }

            } else {
                redirectHome();
            }
        ?>
    </div>

</body>
</html>