<?php
/**
 * Verschiedenste Einstellungen und Funktionen
 */

//Komplette URL zu Newshub (Hauptordner)
$newshubBaseUrl = "http://192.168.178.18/newshub/"; //Mit / am Ende

//URL zum font-awesome
$fontawesomeUrl = "http://192.168.178.18/cdn/fontawesome/font-awesome.min.css";

/**
 * Redirect auf die Hauptseite. (void - Funktion)
 */
function redirectHome() {
    echo("<h1>Redirect ...</h1>");
    header('Location: index.php');
}

/**
 * Ausgabe des meisten <head>-Tag-Inhalts an der Stelle, an der die Funktion aufgerufen wird. (void - Funktion)
 * @param string Das, was im <title> der Seite stehen soll
 */
function printHeadContent($title) {
    global $fontawesomeUrl;
    echo(
        "\n\t" . '<title>' . $title .'</title>' .
	    "\n\t" . '<meta charset="utf-8">' .
	    "\n\t" . '<link rel="icon" href="./res/favicon.ico">' .
        "\n\t" . '<link rel="stylesheet" type="text/css" href="./css/style.css">' .
        "\n\t" . '<link rel="stylesheet" type="text/css" href="./css/navbar.css">' .
        "\n\t" . '<link rel="stylesheet" type="text/css" href="' . $fontawesomeUrl . '">'
    );
}

/**
 * Ausgabe der Navbar an der Stelle, an der die Funktion aufgerufen wird. (void - Funktion)
 * --> Für korrekte Darstellung "navbar.css" in HTML-Head inkludieren
 * @param string Was in der Suchleiste stehen soll (OPTIONAL)
 */
function printNavbar($searchInput = "") {

    echo('
    <div id="navbar">
        <div id="logo">
            <a href="index.php"><img alt="radiotrackslogo" src="./res/newshub.png"></a>
        </div>
        
        <div id="searchbar">
            <form id="searchform" action="search.php" method="get" onsubmit="return checkSearchInput()">
                <input id="queryinput" type="textbox" name="query" placeholder="Suchen" value="' . $searchInput . '">
                <script type="text/javascript">
                    function checkSearchInput() {
                        if(document.getElementById("queryinput").value.length > 0) {
                            return true;
                        }
                        return false;
                    }
                </script>
                <div class="tooltip">
                    <button type="submit"><i class="fa fa-search"></i></button>
                    <span class="tooltiptext">Suchen</span>
                </div>
            </form>
        </div>
    </div>
    ');
}