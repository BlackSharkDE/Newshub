<?php
/**
 * Alles zur Ausgabe von Providern
 * !!! Skripte, die dieses Modul einbinden, müssen 'misc.php' einbinden !!!
 */

/**
 * Erstellt ein Provider <a>-Tag
 * @param array   Ein Array mit den Informationen des Providers
 * @return string Das Tag als String
 */
function getProviderFormat(array $providerArray) {
    global $newshubBaseUrl;
    return(
        '<a class="provider" href="' . $newshubBaseUrl . 'frontend/providerview.php?name=' . $providerArray["name"] . '">' .
        '<img class="providericon_link" src="' . $newshubBaseUrl . 'frontend/provider_icons/' . $providerArray["providerid"] . '.png" onerror="this.style.display=\'none\'">' .
        '<p class="providername">' . $providerArray["name"] . '</p>' .
        '<p class="providerslogan">' . $providerArray["slogan"] . '</p>' .
        '</a>'
    );
}

/**
 * Erstellt ein <div> der Klasse "providerbox" und gibt es aus. (void - Funktion)
 * --> Für korrekte Darstellung "provider.css" in HTML-Head inkludieren
 * @param array  Ein Array mit Provider-Arrays (assoziative Arrays)
 * @param string Was als Überschrift in der Box stehen soll (OPTIONAL, wenn leer, wird nichts angezeigt)
 */
function printProviderBox(array $arrayWithProvider, string $boxText = "") {
    echo(
        "\n\t\t" . '<div class="providerbox">' . 
        (strlen($boxText) > 0 ? "\n\t\t\t" . '<h3><i class="fa fa-inbox"></i>&nbsp;' . $boxText . ' (' . count($arrayWithProvider) . ')</h3>' : "") .
        "\n\t\t\t" . '<div class="providerslide">' .
        "\n\t\t\t\t" . '<ul>'
    );

    foreach($arrayWithProvider as $provider) {
        echo("\n\t\t\t\t\t" . '<li>' . getProviderFormat($provider) . '</li>');
    }

    echo(
        "\n\t\t\t\t" . '</ul>' .
        "\n\t\t\t" . '</div>' . 
        "\n\t\t" . '</div>' . "\n"
    );
}