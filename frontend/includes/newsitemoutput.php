<?php
/**
 * Alles zur Ausgabe von NewsItems
 */

/**
 * Filtert die URL bis zum Artikel heraus
 * @param string  Komplette URL zum Artikel
 * @return string URL ohne den Artikel
 */
function getPartUrl(string $articleUrl) {
    return substr($articleUrl,0,strripos($articleUrl,"/"));
}

/**
 * Gibt die NewsItems an Stelle des Funktionsaufrufs aus. (void - Funktion)
 * --> Für korrekte Darstellung "newsitem.css" in HTML-Head inkludieren
 * @param array Array mit "NewsItem"-Arrays (assoziative Arrays)
 */
function outputNewsItems(array $newsItemsArray) {
    foreach($newsItemsArray as $newsItem) {
        echo(
            "\n\t\t" . '<div class="newsitem">' .
            "\n\t\t\t" . '<p class="newsitemparturl">' . getPartUrl($newsItem["link"]) . '</p>' .
            "\n\t\t\t" . '<a class="newsitemheadline" href="' . $newsItem["link"] . '" target="_blank">' . $newsItem["title"] . '</a>' .
            "\n\t\t\t" . '<p><i class="newsitemdate">(' . $newsItem["published"] . ')</i> ' . $newsItem["description"] . '</p>' .
            "\n\t\t" . '</div>' . "\n"
        );
    }
}