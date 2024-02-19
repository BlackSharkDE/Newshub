//Komplette URL zu Newshub (Hauptordner)
var newshubUrl = "http://192.168.178.18/Newshub/"; //Mit / am Ende

/**
 * Konfiguriert das Widget. (void - Funktion)
 * @param string ID des Div, das benutzt wird
 * @param string Welche Farbe für Text und Umrandung benutzt werden soll (HEX-Code)
 * @param string Name des Providers
 * @param int    Wie viele NewsItems angezeigt werden sollen (immer die aktuellsten News ; neue Items werden zuerst angezeigt) (OPTIONAL)
 */
function setupNewshubWidget(divId,colorCode,providerName,itemCount = -1) {

    //Widget-Div finden
    var widgetDiv = document.getElementById(divId);
    widgetDiv.style.borderColor = colorCode; //Umrandung festlegen
    
    //Head erstellen und hinuzufügen
    var headDiv = document.createElement("div");
    headDiv.classList.add("newshubwidgethead");
    headDiv.style.borderColor = colorCode;
    headDiv.innerHTML = '<h2><a style="color: ' + colorCode + ';" href="'+ newshubUrl + "api/redirect.php?providername=" + providerName + '" target="_blank">' + providerName + '</a></h2>\n'
    + '<p style="color: ' + colorCode + ';">'
    + '<a style="color: ' + colorCode + ';" href="' + newshubUrl + "frontend/providerview.php?name=" + providerName + '" target="_blank">Newshub</a>'
    + ' | '
    + '<a style="color: ' + colorCode + ';" href="' + newshubUrl + "api/liveview.php?providername=" + providerName + '" target="_blank">Feed (live)</a>'
    + '</p>';
    widgetDiv.appendChild(headDiv);

    /**
     * Füllt das Widget-Div mit News-Items. (void - Funktion)
     * @param string Rückgabe der Newshub-Api (JSON)
     */
    function createNewsItems(apiJson) {
        
        //JSON-String parsen
        apiJson = JSON.parse(apiJson);
        
        //Die newsItems im Widget ausgeben
        //--> API-Antwort enthält ein Objekt, welches Datum-Attribute enthält, welche wiederum jeweils ein Array enthalten
        Object.keys(apiJson).forEach(function(newsDay) {

            //Tages-Div
            dayDiv = document.createElement("div");
            dayDiv.classList.add("newshubwidgetday");
            dayDiv.style.borderTop = "2px solid " + colorCode;

            //Das Tages-Div füllen
            apiJson[newsDay].forEach(function(newsItem) {

                //newsItem in ein HTML-Node packen
                var p = document.createElement("p");
                var a = document.createElement("a");
                a.target = "_blank";
                a.href = newsItem["link"];
                a.innerHTML = "[" + newsItem["published"] + "]<br>" + newsItem["title"];
                a.style.color = colorCode;
                p.appendChild(a);

                //Dem dayDiv den HTML-Node hinzufügen
                dayDiv.appendChild(p);
            });

            //Das dayDiv dem Widget hinzufügen
            widgetDiv.appendChild(dayDiv);
        });
    }

    //-- API-Abfragen --

    //Abfragen von den News (werden async hinzugefügt)
    let providerNewsRequest = new XMLHttpRequest();
    providerNewsRequest.open('GET', newshubUrl + "api/rest.php?providernews=" + providerName + "&itemcount=" + itemCount + "&group", true);
    providerNewsRequest.send();
    providerNewsRequest.onreadystatechange = function() {
        if(providerNewsRequest.readyState == 4) {
            let apiResponse = providerNewsRequest.response;
            //console.log(apiResponse); //DEBUG
            createNewsItems(apiResponse);
        }
    };

    //Abfragen von den Provider-Infos (werden async hinzugefügt)
    let providerInfosRequest = new XMLHttpRequest();
    providerInfosRequest.open('GET', newshubUrl + "api/rest.php?providerinfo=" + providerName, true);
    providerInfosRequest.send();
    providerInfosRequest.onreadystatechange = function() {
        if(providerInfosRequest.readyState == 4) {
            let apiResponse = providerInfosRequest.response;
            //console.log(apiResponse); //DEBUG

            //JSON-String parsen
            apiJson = JSON.parse(apiResponse);

            //Enthält das Provider-Icon
            let imgTag = '<img class="providericon_widget" src="' + newshubUrl +'frontend/provider_icons/' + apiJson["providerid"] + '.png" onerror="this.style.display=\'none\'"><br>';

            //Provider-Icon zum Head-Div hinzufügen
            headDiv.innerHTML = headDiv.innerHTML.replace("<h2>","<h2>" + imgTag);

            //News-Anzahl zum Head-Div hinzufügen
            headDiv.innerHTML = headDiv.innerHTML.replace(" | "," | " + apiJson["newscount"] + " | ");
        }
    };
}