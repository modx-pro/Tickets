<?php
/**
 * Settings German Lexicon Entries
 */

$_lang['area_tickets.main'] = 'Allgemein';
$_lang['area_tickets.section'] = 'Ticket-Bereiche';
$_lang['area_tickets.ticket'] = 'Tickets';
$_lang['area_tickets.comment'] = 'Kommentare';
$_lang['area_tickets.mail'] = 'E-Mail-Benachrichtigungen';

$_lang['setting_tickets.frontend_css'] = 'Frontend-Stile';
$_lang['setting_tickets.frontend_css_desc'] = 'Der Pfad zur CSS-Datei. Wenn Sie eigene Stile verwenden möchten, geben Sie hier den Pfad dazu an oder löschen Sie den Parameter und laden Sie eigene Stile über das Seiten-Template.';
$_lang['setting_tickets.frontend_js'] = 'Frontend-Skripte';
$_lang['setting_tickets.frontend_js_desc'] = 'Der Pfad zu den Skripten. Wenn Sie eigene Skripte verwenden möchten, geben Sie hier den Pfad dazu an oder löschen Sie den Parameter und laden Sie eigene Skripte über das Seiten-Template.';

$_lang['setting_tickets.date_format'] = 'Datumsformat';
$_lang['setting_tickets.date_format_desc'] = 'Ausgabeformat des Datums bei der Erstellung von Tickets';
$_lang['setting_tickets.default_template'] = 'Ticket-Template';
$_lang['setting_tickets.default_template_desc'] = 'Standard-Template für neue Tickets. Wird im Manager und beim Erstellen eines Tickets im Frontend verwendet.';
$_lang['setting_tickets.ticket_isfolder_force'] = 'Tickets sind "Container"';
$_lang['setting_tickets.ticket_isfolder_force_desc'] = 'Voreinstellung des Parameters "Container" für Tickets.';
$_lang['setting_tickets.ticket_hidemenu_force'] = 'Tickets "Nicht in Menüs anzeigen"';
$_lang['setting_tickets.ticket_hidemenu_force_desc'] = 'Voreinstellung des Parameters "Nicht in Menüs anzeigen" für Tickets.';
$_lang['setting_tickets.ticket_show_in_tree_default'] = 'Im Ressourcenbaum anzeigen';
$_lang['setting_tickets.ticket_show_in_tree_default_desc'] = 'Aktivieren Sie diese Option, damit alle erstellten Tickets im Ressourcenbaum sichtbar sind.';
$_lang['setting_tickets.section_content_default'] = 'Standard-Inhalt für neue Ticket-Bereiche';
$_lang['setting_tickets.section_content_default_desc'] = 'Hier können Sie voreingestellten Inhalt für angelegte Ticket-Bereiche festlegen. Standardmäßig ist die Ausgabe von untergeordneten Tickets voreingestellt.';

$_lang['setting_tickets.enable_editor'] = 'Editor "markItUp" verwenden';
$_lang['setting_tickets.enable_editor_desc'] = 'Diese Einstellung aktiviert den Editor "markItUp" im Frontend zur komfortablen Bearbeitung von Tickets und Kommentaren.';
$_lang['setting_tickets.editor_config.ticket'] = 'Einstellungen des Ticket-Editors';
$_lang['setting_tickets.editor_config.ticket_desc'] = 'Ein in JSON-codiertes Array für die Einstellungen von "markItUp". Details hier - http://markitup.jaysalvat.com/documentation/';
$_lang['setting_tickets.editor_config.comment'] = 'Einstellung des Kommentar-Editors';
$_lang['setting_tickets.editor_config.comment_desc'] = 'Ein in JSON-codiertes Array für die Einstellungen von "markItUp". Details hier - http://markitup.jaysalvat.com/documentation/';

$_lang['setting_tickets.disable_jevix_default'] = 'Jevix standardmäßig deaktivieren';
$_lang['setting_tickets.disable_jevix_default_desc'] = 'Diese Einstellung aktiviert oder deaktiviert standardmäßig die Option "Jevix deaktivieren" für neue Tickets.';
$_lang['setting_tickets.process_tags_default'] = 'Tags standardmäßig ausführen';
$_lang['setting_tickets.process_tags_default_desc'] = 'Diese Einstellung aktiviert oder deaktiviert standardmäßig die Option "MODX-Tags ausführen" für neue Tickets.';
$_lang['setting_tickets.private_ticket_page'] = 'Redirect zu Ressource bei privaten Tickets';
$_lang['setting_tickets.private_ticket_page_desc'] = 'ID einer vorhandenen MODX-Ressource, an die der Benutzer weitergeleitet wird, wenn er nicht über ausreichende Rechte zum Anzeigen des privaten Tickets verfügt.';
$_lang['setting_tickets.unpublished_ticket_page'] = 'Ressource bei unveröffentlichten Tickets';
$_lang['setting_tickets.unpublished_ticket_page_desc'] = 'ID einer vorhandenen MODX-Ressource, die angezeigt wird, wenn ein nicht veröffentlichtes Ticket angefordert wird.';
$_lang['setting_tickets.ticket_max_cut'] = 'Maximale ungekürzte Textlänge';
$_lang['setting_tickets.ticket_max_cut_desc'] = 'Die maximale Anzahl von Zeichen ohne Tags, die ohne Kürzung gespeichert werden kann.';


$_lang['setting_tickets.snippet_prepare_comment'] = 'Snippet für Kommentarverarbeitung';
$_lang['setting_tickets.snippet_prepare_comment_desc'] = 'Ein spezielles Snippet, das Kommentare verarbeitet. Es überschreibt die Standardverarbeitung und wird direkt in der Klasse "Tickets" aufgerufen, dementsprechend sind alle Methoden und Variablen dieser Klasse verwendbar.';
$_lang['setting_tickets.comment_edit_time'] = 'Bearbeitungszeit';
$_lang['setting_tickets.comment_edit_time_desc'] = 'Zeit in Sekunden, in welcher Kommentare noch bearbeitet werden können.';
$_lang['setting_tickets.clear_cache_on_comment_save'] = 'Cache leeren beim Kommentieren';
$_lang['setting_tickets.clear_cache_on_comment_save_desc'] = 'Mit dieser Einstellung können Sie den Ticket-Cache löschen, wenn Sie mit Kommentaren arbeiten (Erstellen/Bearbeiten/Löschen). Das ist nur notwendig, wenn Sie das Snippet "TicketComments" ungecached aufrufen.';

$_lang['setting_tickets.mail_from'] = 'Ausgehende Mailadresse';
$_lang['setting_tickets.mail_from_desc'] = 'Die Adresse zum Senden von E-Mail-Benachrichtigungen. Wenn diese Einstellung nicht ausgefüllt ist, wird die Systemeinstellung "emailsender" verwendet.';
$_lang['setting_tickets.mail_from_name'] = 'Name des Senders';
$_lang['setting_tickets.mail_from_name_desc'] = 'Name des Senders aller Benachrichtigungen. Wenn diese Einstellung nicht ausgefüllt ist, wird die Systemeinstellung "site_name" verwendet.';
$_lang['setting_tickets.mail_queue'] = 'Warteschlange für Benachrichtigungen';
$_lang['setting_tickets.mail_queue_desc'] = 'Warteschlange für Benachrichtigungen verwenden, oder alle E-Mails gleichzeitig senden? Wenn Sie diese Option aktivieren, muß eine Cron-Datei hinzugefügt werden – "/core/components/tickets/cron/mail_queue.php"';
$_lang['setting_tickets.mail_bcc'] = 'Benachrichtigungen für Administratoren';
$_lang['setting_tickets.mail_bcc_desc'] = 'Geben Sie eine kommaseparierte Liste von Administratoren an (<b>ID</b>), die Benachrichtigungen über neue Tickets und Kommentare erhalten sollen.';
$_lang['setting_tickets.mail_bcc_level'] = 'Level der Benachrichtigung für Administratoren';
$_lang['setting_tickets.mail_bcc_level_desc'] = 'Es gibt 3 Levels der Administratorenbenachrichtigung: 0 - deaktiviert, 1 - sendet nur Benachrichtigungen über neue Tickets, 2 - Tickets + Kommentare. Empfohlenes Level ist 1.';

$_lang['setting_tickets.count_guests'] = 'Seitenansichten von Gästen zählen';
$_lang['setting_tickets.count_guests_desc'] = 'Wenn diese Option aktiviert ist, werden Seitenaufrufe von allen Website-Besuchern berücksichtigt, nicht nur von autorisierten Benutzern. Beachten Sie, dass die Anzahl der Aufrufe bei dieser Einstellung relativ einfach zu fälschen ist.';

//$_lang['setting_tickets.section_id_as_alias'] = 'ID des Bereichs als Alias verwenden';
//$_lang['setting_tickets.section_id_as_alias_desc'] = 'Wenn diese Option aktiviert ist, werden für Ticket-Bereiche keine Aliase für benutzerfreundliche URLs generiert. Stattdessen wird ihre ID als Alias verwendet.';
//$_lang['setting_tickets.ticket_id_as_alias'] = 'ID des Tickets als Alias verwenden';
//$_lang['setting_tickets.ticket_id_as_alias_desc'] = 'Wenn diese Option aktiviert ist, werden für Tickets keine Aliase für benutzerfreundliche URLs generiert. Stattdessen wird ihre ID als Alias verwendet.';

$_lang['setting_mgr_tree_icon_ticket'] = 'Icon für Tickets';
$_lang['setting_mgr_tree_icon_ticket_desc'] = 'Icon für Tickets im Ressourcenbaum';
$_lang['setting_mgr_tree_icon_ticketssection'] = 'Icon für Ticket-Bereiche';
$_lang['setting_mgr_tree_icon_ticketssection_desc'] = 'Icon für Ticket-Bereiche im Ressourcenbaum';

$_lang['setting_tickets.source_default'] = 'Medienquelle für Tickets';
$_lang['setting_tickets.source_default_desc'] = 'Medienquelle festlegen, welche bei Tickets für den Upload von Dateien verwendet wird.';

$_lang['tickets.source_thumbnails_desc'] = 'JSON-codiertes Array von Optionen für die Erstellung von Vorschaubildern';
$_lang['tickets.source_maxUploadWidth_desc'] = 'Maximale Breite von Bildern für den Upload. Alle Bilder die größer sind, werden passend skaliert.';
$_lang['tickets.source_maxUploadHeight_desc'] = 'Maximale Höhe von Bildern für den Upload. Alle Bilder die größer sind, werden passend skaliert.';
$_lang['tickets.source_maxUploadSize_desc'] = 'Maximale Dateigöße für den Upload (in Bytes)';
$_lang['tickets.source_imageNameType_desc'] = 'Dieser Parameter gibt an, wie eine Datei nach dem Upload umbenannt wird. Hash – Erzeugung eines eindeutigen Namens abhängig vom Inhalt der Datei. Friendly - Erzeugung des Namens durch den Algorithmus der friendly URLs der Webseite (wird von Systemeinstellungen gesteuert).';

