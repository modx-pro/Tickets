<?php
/*
 * Properties German Lexicon Entries
 *
 * */

$_lang['tickets_prop_limit'] = 'Maximale Anzahl der Ergebnisse';
$_lang['tickets_prop_offset'] = 'Versatz vom Anfang der Ergebnisse';
$_lang['tickets_prop_depth'] = 'Tiefe der Suche nach Ressourcen (ausgehend von der übergeordneten Ressource)';
$_lang['tickets_prop_sortby'] = 'Das Feld, nach welchem sortiert wird';
$_lang['tickets_prop_sortdir'] = 'Reihenfolge der Sortierung';
$_lang['tickets_prop_parents'] = 'Liste von Ordnern, die verarbeitet werden sollen. Standardmäßig ist die aktuelle übergeordnete Ressource ausgewählt. Keine Begrenzung, wenn 0 gewählt ist.';
$_lang['tickets_prop_resources'] = 'Kommaseparierte Liste von IDs, die in den Ergebnissen enthalten sein sollen. Um Ressourcen auszuschließen, werden den IDs Bindestriche vorangestellt.';
$_lang['tickets_prop_threads'] = 'Kommaseparierte Liste von Threads, die in den Ergebnissen enthalten sein sollen. Um Ressourcen auszuschließen, werden den IDs Bindestriche vorangestellt.';
$_lang['tickets_prop_where'] = 'Ein JSON-codierter Codeschnipsel mit zusätzlichen Bedingungen.';
$_lang['tickets_prop_tvPrefix'] = 'Präfix für Template-Variablen, zum Beispiel "tv.". Standardmäßig ist der Parameter leer.';
$_lang['tickets_prop_includeContent'] = 'Das Feld "content" der Ressourcen einbeziehen.';
$_lang['tickets_prop_includeTVs'] = 'Eine optionale kommaseparierte Liste mit Namen von Template-Variablen, die mit verarbeitet werden sollen. Beispielsweise liefern "action,time" die Platzhalter [[+action]] und [[+time]].';
$_lang['tickets_prop_toPlaceholder'] = 'Falls nicht leer, zeigt das Snippet den Output in einem Platzhalter dieses Namens, statt ihn direkt anzuzeigen.';
$_lang['tickets_prop_outputSeparator'] = 'Eine optionale Zeichenkette zum Trennen der einzelnen Ergebnisse.';

$_lang['tickets_prop_showLog'] = 'Zeigt weitere Informationen zum Snippet an. Nur für autorisierte User im Kontext "mgr".';
$_lang['tickets_prop_showUnpublished'] = 'Unveröffentlichte Ressourcen anzeigen';
$_lang['tickets_prop_showDeleted'] = 'Gelöschte Ressourcen anzeigen';
$_lang['tickets_prop_showHidden'] = 'Ressourcen mit der Einstellung "Nicht in Menüs anzeigen" anzeigen';
$_lang['tickets_prop_fastMode'] = 'Falls aktiv, werden nur Werte direkt aus der Datenbank entnommen. MODX-Tags wie Filter oder Snippets werden nicht verarbeitet.';

$_lang['tickets_prop_action'] = 'Modus des Snippets';
$_lang['tickets_prop_cacheKey'] = 'Name des Snippet-Cache. Falls leer, ist das Caching deaktiviert.';
$_lang['tickets_prop_cacheTime'] = 'Dauer der Gültigkeit des Cache';
$_lang['tickets_prop_thread'] = 'Name des Kommentar-Threads. Standard ist "resource-[[*id]]".';
$_lang['tickets_prop_user'] = 'Nur Elemente wählen, die von diesem User erstellt wurden.';

$_lang['tickets_prop_tpl'] = 'Das Template (Chunk), das für eine Reihe verwendet wird.';
$_lang['tickets_prop_tplFormCreate'] = 'Chunk für die Erstellung eines neuen Tickets';
$_lang['tickets_prop_tplFormUpdate'] = 'Chunk für die Aktualisierung eines bestehenden Tickets';
$_lang['tickets_prop_tplSectionRow'] = 'Chunk für eine Reihe des Ticket-Bereichs';
$_lang['tickets_prop_tplPreview'] = 'Chunk für die Darstellung der Ticket-Vorschau vor der Veröffentlichung';
$_lang['tickets_prop_tplCommentForm'] = 'Chunk für das Formular zum Hinzufügen eines neuen Kommentars';
$_lang['tickets_prop_tplCommentAuth'] = 'Chunk für die Darstellung eines Kommentars für authorisierte Benutzer';
$_lang['tickets_prop_tplCommentGuest'] = 'Chunk für die Darstellung eines Kommentars für Gast-Benutzer';
$_lang['tickets_prop_tplCommentDeleted'] = 'Chunk für die Darstellung von gelöschten Kommentaren';
$_lang['tickets_prop_tplComments'] = 'Chunk für den äußeren Wrapper für Kommentare.';
$_lang['tickets_prop_tplLoginToComment'] = 'Chunk für Gast-Benutzer mit Autorisierungspflicht.';
$_lang['tickets_prop_tplCommentEmailOwner'] = 'Chunk für die Benachrichtigung über neue Kommentare des Ticket-Besitzers';
$_lang['tickets_prop_tplCommentEmailReply'] = 'Chunk für die Benachrichtigung eines Benutzers, der auf einen Kommentar geantwortet hat';
$_lang['tickets_prop_tplCommentEmailSubscription'] = 'Chunk für die Benachrichtigung eines Abonnenten über einen neuen Kommentar';
$_lang['tickets_prop_tplCommentEmailBcc'] = 'Chunk für die BCC-Benachrichtigung über ein neues Ticket';
$_lang['tickets_prop_tplTicketEmailBcc'] = 'Chunk für die BCC-Benachrichtigung über einen neuen Kommentar';
$_lang['tickets_prop_tplCommentEmailUnpublished'] = 'Chunk für die BCC-Benachrichtigung über einen unveröffentlichten neuen Kommentar';
$_lang['tickets_prop_tplWrapper'] = 'Name eines Chunks, der als Wrapper für den Output fungiert. Funktioniert nicht zusammen mit "toSeparatePlaceholders".';
$_lang['tickets_prop_threadUrl'] = 'Absolute URL, um vom Manager zu einem Kommentar zu gelangen. Wird verwendet, wenn etwas anderes als Ressourcen kommentiert wird.';

$_lang['tickets_prop_commentsDepth'] = 'Ganzzahliger Wert für die maximale Tiefe des Kommentar-Threads';
$_lang['tickets_prop_autoPublish'] = 'Falls "true" werden alle Kommentare authorisierter Benutzer des Threads ohne vorangehende Moderation veröffentlicht.';
$_lang['tickets_prop_autoPublishGuest'] = 'Falls "true" werden alle Kommentare anonymer Benutzer des Threads ohne vorangehende Moderation veröffentlicht.';
$_lang['tickets_prop_formBefore'] = 'Falls "true" wird das Kommentar-Formular vor den Kommentaren platziert.';
//$_lang['tickets_prop_dateFormat'] = 'Das Datumformat für einen Kommentar, mit der Funktion date()';
$_lang['tickets_prop_gravatarIcon'] = 'Das Standard-Gravatar-Icon, falls keines für den Benutzer gefunden wurde.';
$_lang['tickets_prop_gravatarSize'] = 'Die Größe des Gravatar-Icons in Pixeln';
$_lang['tickets_prop_gravatarUrl'] = 'Die URL von Gravatar';

$_lang['tickets_prop_allowedFields'] = 'Felder des Tickets, die dem Benutzer ausgefüllt werden dürfen. Hier die Namen der entsprechenden TVs angeben.';
$_lang['tickets_prop_requiredFields'] = 'Die Pflichtfelder, die vom Benutzer ausgefüllt werden müssen, bevor das Formular gesendet werden kann.';
$_lang['tickets_prop_bypassFields'] = 'Felder des Tickets, die Sie beim Sichern nicht filtern wollen.';
$_lang['tickets_prop_redirectUnpublished'] = 'Sie können angeben, welches Dokument dem Benutzer beim Erstellen eines nicht veröffentlichten Tickets gesendet werden soll.';
$_lang['tickets_prop_sections_parents'] = 'Standardmäßig werden alle verfügbaren Ticket-Bereiche angezeigt. Sie können das einschränken, indem Sie IDs der Eltern-Ressourcen kommasepariert angeben.';
$_lang['tickets_prop_sections_resources'] = 'Standardmäßig werden alle verfügbaren Tickets der Ticket-Bereiche angezeigt. Sie können das einschränken, indem Sie eine komma-separierte Liste von IDs der Ticket-Bereiche angeben.';
$_lang['tickets_prop_sections_sortby'] = 'Das Feld, nach welchem die Liste der Ticket-Bereiche sortiert wird';
$_lang['tickets_prop_sections_sortdir'] = 'Reihenfolge der Sortierung der Ticket-Bereiche';
$_lang['tickets_prop_sections_context'] = 'Komma-separierte Liste von Kontexten, in denen nach Ticket-Bereichen gesucht werden soll.';

$_lang['tickets_prop_meta_tpl'] = 'Chunk mit Template für Ticket-Informationen';
$_lang['tickets_prop_getSection'] = 'Zusätzliche Abfrage in der Datenbank, um den übergeordneten Ticket-Bereich abzurufen?';
$_lang['tickets_prop_getUser'] = 'Zusätzliche Abfrage in der Datenbank, um das Profil des Autors abzurufen?';

$_lang['tickets_prop_allowGuest'] = 'Kommentar-Funktion für nicht authorisierte Benutzer aktivieren?';
$_lang['tickets_prop_allowGuestEdit'] = 'Dürfen nicht authorisierte Benutzer ihre Kommentare bearbeiten?';
$_lang['tickets_prop_allowGuestEmails'] = 'Gast-Benutzern E-Mail-Benachrichtigungen über ihre Antworten schicken?';
$_lang['tickets_prop_enableCaptcha'] = 'Spam-Schutz für nicht authorisierte Benutzer aktivieren?';
$_lang['tickets_prop_minCaptcha'] = 'Die kleinste Zahl im Spam-Schutz';
$_lang['tickets_prop_maxCaptcha'] = 'Die größte Zahl im Spam-Schutz';

$_lang['tickets_prop_allowFiles'] = 'Benutzern erlauben, Dateien auf den Server hochzuladen';
$_lang['tickets_prop_source'] = 'ID der Medienquelle für den Datei-Upload. Standardmäßig ist die Medienquelle ausgewählt, die in der Systemeinstellung "tickets.source_default" angegeben ist.';
$_lang['tickets_prop_tplFiles'] = 'Container für die Darstellung des Uploaders und die Liste der bereits hochgeladenen Dateien.';
$_lang['tickets_prop_tplFile'] = 'Chunk für hochgeladene Datei, die kein Bild ist.';
$_lang['tickets_prop_tplImage'] = 'Chunk für hochgeladene Bild-Datei.';

$_lang['tickets_prop_getFiles'] = 'Liste hochgeladener Dateien zeigen?';
$_lang['tickets_prop_unusedFiles'] = 'Nur die Dateien zeigen, deren Links nicht im Inhalt des Tickets enthalten sind.';
$_lang['tickets_prop_meta_tplFile'] = 'Chunk für Datei in der Liste';

$_lang['tickets_prop_class'] = 'Klasse für die Auswahl der Ergebnisse angeben';
