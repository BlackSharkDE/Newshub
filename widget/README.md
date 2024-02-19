# widget

Ein Widget für Websites, die Newshub-Feeds einbinden

# In eine Website einbinden

Zuerst folgendes `<div>` in die Website einfügen:
```html
<div id="idofthisdiv" class="newshubwidget">
    <link rel="stylesheet" type="text/css" href="http://server_address_or_domain/newshub/widget/style.css">
    <script type="text/javascript" src="http://server_address_or_domain/newshub/widget/setup.js"></script>
    <script type="text/javascript">setupNewshubWidget("idofthisdiv","#000000","Name of the Provider",20);</script>
</div>
```
Danach das `<div>` anpassen:
*  `id`: Das ID-Attribut des `<div>` kann frei gewählt werden
*  `<link>`: Die URL zur `style.css` anpassen
*  `<script>`: Die URL zur `setup.js` (erstes `<script>`-Tag) anpassen
*  Skriptparameter (zweites `<script>`-Tag) anpassen. Für Parameter, siehe `setup.js`.

# Serverseitige Einstellung

Da dieses Repository auf dem Webserver liegt, auf dem der Rest von Newshub auch hinterlegt ist, muss die Variable `newshubUrl` in `setup.js` ggf. angepasst werden,
je nachdem, wie die Server-URL lautet.