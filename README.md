# Newshub

Ein Service, der Nachrichten sammelt und diese durchsuchbar macht.

## Übersicht

* Die API ist in PHP implementiert und dient zur Datenabfrage über die gesammelten News.
* Im `storage`-Ordner befindet sich der RSS/Atom-Feed-Getter, der die Nachrichten sammelt und in der Datenbank speichert.
  * Das Python-Package `atomicfeed` (nicht enthalten) dient zur Verarbeitung der Feeds.
  * Die Python-Packages `requests`, `dateutil`, `emoji` und `mariadb` sind Fremdmodule, die von anderen Entwicklern stammen.
* Das Verzeichnis `storage-provider` dient nur zur Aufbewarung des Datenbankschemas und der News-Provider.
* Das Widget ist eine simple JS-Anwendung, welche die Newshub-Inhalte auf anderen Websites anzeigbar macht.