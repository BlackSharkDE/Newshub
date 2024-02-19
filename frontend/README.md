# frontend

Das Webinterface

## Voraussetzungen / Setup

An sich benötigt das Front-End keine externen Dependencies. Lediglich eine **Font Awesome 4**-Bezugsquelle.
Diese ist aktuell auf einen selbstgehosteten Server eingestellt. Sollte man die Quelle ändern wollen, muss man in der `misc.php` die `$fontawesomeUrl`-Variable, die
die CSS-Datei-URL von `font-awesome.min.css` beinhaltet, manuell abändern.

Zudem muss in der `misc.php` ggf. die `$newshubBaseUrl`-Variable geändert werden.

## Icons für die Provider

Dies ist optional und dient nur der Optik. Die Icons können in den Ordner `provider_icons` gelegt werden.

Voraussetzungen:

* Sie müssen vom Datentyp `.png` sein und sollten nicht allzu große Auflösungen haben (da sie eh klein angezeigt werden).
* Sie müssen als Dateiname `Provider-ID.png` heißen (z.B. `6.png`). Die ID des Providers kann in der Datenbank eingesehen oder über die API abgefragt werden.