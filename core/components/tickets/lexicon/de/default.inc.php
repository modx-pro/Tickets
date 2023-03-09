<?php
/**
 * Default German Lexicon Entries for Tickets
 */

include_once('setting.inc.php');

$_lang['tickets'] = 'Tickets';
$_lang['comments'] = 'Kommentare';
$_lang['threads'] = 'Threads';
$_lang['authors'] = 'Autoren';
$_lang['tickets_section'] = 'Ticket-Bereich';
$_lang['ticket'] = 'Ticket';
$_lang['ticket_all'] = 'Alle Tickets';
$_lang['ticket_menu_desc'] = 'Kommentar-Management und mehr';
$_lang['comments_all'] = 'Alle Kommentare';

$_lang['tickets_section_create_here'] = 'Ticket-Bereich';
$_lang['tickets_section_new'] = 'Neues Ticket';
$_lang['tickets_section_management'] = 'Ticket-Management';
$_lang['tickets_section_duplicate'] = 'Ticket-Bereich duplizieren';
$_lang['tickets_section_unpublish'] = 'Ticket-Bereich zurückziehen';
$_lang['tickets_section_publish'] = 'Ticket-Bereich veröffentlichen';
$_lang['tickets_section_undelete'] = 'Ticket-Bereich wiederherstellen';
$_lang['tickets_section_delete'] = 'Ticket-Bereich löschen';
$_lang['tickets_section_view'] = 'Anzeigen';

$_lang['tickets_section_settings'] = 'Einstellungen';
$_lang['tickets_section_tab_main'] = 'Hauptinhalt';

$_lang['tickets_section_tab_tickets'] = 'Untergeordnete Tickets';
$_lang['tickets_section_tab_tickets_intro'] = 'Alle Einstellungen hier gelten nur für neue Tickets.';
$_lang['tickets_section_settings_template'] = 'Template für untergeordnete Ressourcen';
$_lang['tickets_section_settings_template_desc'] = 'Seiten-Template wählen, welches allen neuen Tickets zugewiesen wird, die in diesem Ticket-Bereich erstellt werden. Wenn kein Template angegeben ist, wird <b>tickets.default_template</b> verwendet (festgelegt in den Systemeinstellungen).';
$_lang['tickets_section_settings_uri'] = 'URI-Schema';
$_lang['tickets_section_settings_uri_desc'] = 'Verwendbar sind <b>%y</b> - das Jahr mit zwei Stellen, <b>%m</b> der Monat, <b>%d</b> - der Tag, <b>%alias</b> - das Alias, <b>%id</b> - die ID und <b>%ext</b> - die Erweiterung des Dokuments.';
$_lang['tickets_section_settings_show_in_tree'] = 'Im Ressourcenbaum anzeigen';
$_lang['tickets_section_settings_show_in_tree_desc'] = 'Tickets werden standardmäßig nicht im Ressourcenbaum angezeigt (um das Admin-Panel nicht zu überfüllen), aber für neue Dokumente kann das eingestellt werden.';
$_lang['tickets_section_settings_hidemenu'] = 'Nicht im Menü anzeigen';
$_lang['tickets_section_settings_hidemenu_desc'] = 'Sie können die Einstellung für die Anzeige neuer Tickets im Menü festlegen.';
$_lang['tickets_section_settings_disable_jevix'] = 'Jevix deaktivieren';
$_lang['tickets_section_settings_disable_jevix_desc'] = 'Aus Sicherheitsgründen werden alle Tickets standardmäßig mit dem Jevix-Snippet verarbeitet. Sie können diese Verarbeitung für neue Tickets des aktuellen Ticket-Bereichs deaktivieren.';
$_lang['tickets_section_settings_process_tags'] = 'MODX-Tags verarbeiten';
$_lang['tickets_section_settings_process_tags_desc'] = 'Aus Sicherheitsgründen führen Tickets standardmäßig keine MODX-Tags aus. Sie können aber deren Ausführung für neue Tickets des aktuellen Ticket-Bereichs aktivieren.';

$_lang['tickets_section_tab_ratings'] = 'Ratings';
$_lang['tickets_section_tab_ratings_intro'] = 'Ratings für verschiedene Benutzeraktionen';
$_lang['tickets_section_rating_ticket'] = 'Tickets';
$_lang['tickets_section_rating_ticket_desc'] = 'Bewertungsfaktor für das Erstellen von Tickets in diesem Bereich.';
$_lang['tickets_section_rating_comment'] = 'Kommentare';
$_lang['tickets_section_rating_comment_desc'] = 'Bewertungsfaktor für Kommentare zu Tickets in diesem Bereich.';
$_lang['tickets_section_rating_view'] = 'Views';
$_lang['tickets_section_rating_view_desc'] = 'Bewertungsfaktor der Views für Tickets in diesem Bereich.';
$_lang['tickets_section_rating_vote_ticket'] = 'Autor des Tickets';
$_lang['tickets_section_rating_vote_ticket_desc'] = 'Bewertungsfaktor "Ticket" für den Autor. Negative Stimmabgaben nehmen eine Bewertung weg.';
$_lang['tickets_section_rating_vote_comment'] = 'Autor des Kommentars';
$_lang['tickets_section_rating_vote_comment_desc'] = 'Bewertungsfaktor "Kommentar" für den Autor. Negative Stimmabgaben nehmen eine Bewertung weg.';
$_lang['tickets_section_rating_star_ticket'] = 'Ticket zu Favoriten';
$_lang['tickets_section_rating_star_ticket_desc'] = 'Bewertungsfaktor "Ticket hinzufügen zu den Favoriten" für den Autor.';
$_lang['tickets_section_rating_star_comment'] = 'Kommentar zu Favoriten';
$_lang['tickets_section_rating_star_comment_desc'] = 'Bewertungsfaktor "Kommentar hinzufügen zu den Favoriten" für den Autor.';
$_lang['tickets_section_rating_min_ticket_create'] = 'Ticket-Rating';
$_lang['tickets_section_rating_min_ticket_create_desc'] = 'Minimales Rating, das zum Erstellen eines Tickets in diesem Ticket-Bereich erforderlich ist.';
$_lang['tickets_section_rating_days_ticket_vote'] = 'Rating-Zeitraum Ticket';
$_lang['tickets_section_rating_days_ticket_vote_desc'] = 'Maximale Zahl der Tage nach Veröffentlichung des Tickets, an welchen Besucher Bewertungen dazu abgeben können.';
$_lang['tickets_section_rating_min_comment_create'] = 'Kommentar-Rating';
$_lang['tickets_section_rating_min_comment_create_desc'] = 'Minimales Rating, das zum Erstellen eines Kommentars in diesem Ticket-Bereich erforderlich ist.';
$_lang['tickets_section_rating_days_comment_vote'] = 'Rating-Zeitraum Kommentar';
$_lang['tickets_section_rating_days_comment_vote_desc'] = 'Maximale Zahl der Tage nach Veröffentlichung des Kommentars, an welchen Besucher Bewertungen dazu abgeben können.';

$_lang['tickets_section_notify'] = 'Über neue Tickets benachrichtigen';
$_lang['tickets_section_subscribed'] = 'Sie haben die Benachrichtigungen über neue Tickets in diesem Ticket-Bereich abonniert.';
$_lang['tickets_section_unsubscribed'] = 'Sie werden keine weiteren Benachrichtigungen über neue Tickets in diesem Ticket-Bereich erhalten.';
$_lang['tickets_section_email_subscription'] = 'Neues Ticket im Ticket-Bereich "[[+section.pagetitle]]"';

$_lang['ticket_create_here'] = 'Ticket erstellen';

$_lang['ticket_no_comments'] = 'Es gibt noch keine Kommentare auf dieser Seite. Sie können den ersten schreiben.';
$_lang['tickets_message_close_all'] = 'Alle schließen';
$_lang['err_no_jevix'] = 'Für eine reibungslose Funktion wird das Jevix-Snippet benötigt. Sie müssen es aus dem MODX-Repository installieren.';
$_lang['tickets_err_unknown'] = 'Ein unbekannter Fehler ist aufgetreten.';
$_lang['ticket_err_id'] = 'Das Ticket mit der ID = [[+id]] wurde nicht gefunden.';
$_lang['ticket_err_wrong_user'] = 'Sie versuchen ein Ticket zu aktualisieren, welches nicht von Ihnen stammt.';
$_lang['ticket_err_no_auth'] = 'Sie müssen eingeloggt sein, um ein Ticket zu erstellen.';
$_lang['ticket_err_wrong_parent'] = 'Für das Ticket wurde ein falscher Ticket-Bereich angegeben.';
$_lang['ticket_err_wrong_resource'] = 'Ungültiges Ticket angegeben.';
$_lang['ticket_err_wrong_thread'] = 'Ungültiger Thread angegeben.';
$_lang['ticket_err_wrong_section'] = 'Ungültiger Ticket-Bereich angegeben.';
$_lang['ticket_err_access_denied'] = 'Zugriff abgelehnt';
$_lang['ticket_err_form'] = 'Das Formular enthält Fehler. Bitte beheben Sie diese.';
$_lang['ticket_err_deleted_comment'] = 'Sie versuchen, einen gelöschten Kommentar zu bearbeiten.';
$_lang['ticket_err_unpublished_comment'] = 'Dieser Kommentar ist nicht veröffentlicht.';
$_lang['ticket_err_ticket'] = 'Das gewählte Ticket existiert nicht.';
$_lang['ticket_err_vote_own'] = 'Sie können keine Bewertungen für Ihr eigenes Ticket abgeben.';
$_lang['ticket_err_vote_already'] = 'Sie haben dieses Ticket bereits bewertet.';
$_lang['ticket_err_empty'] = 'Sie haben noch keinen Text für dieses Ticket eingegeben.';
$_lang['ticket_err_publish'] = 'Sie haben keine Berechtigung, Tickets zu veröffentlichen.';
$_lang['ticket_err_cut'] = 'Die Länge dieses Texts beträgt [[+length]] Zeichen. Sie müssen das Tag &lt;cut/&gt angeben, wenn der Text länger als [[+max_cut]] Zeichen ist.';
$_lang['ticket_err_rating_ticket'] = 'Um ein Ticket in diesem Ticket-Bereich veröffentlichen zu können, brauchen Sie mehr als [[+rating]] Bewertungen.';
$_lang['ticket_err_rating_comment'] = 'Um einen Kommentar in diesem Ticket-Bereich schreiben zu können, brauchen Sie mehr als [[+rating]] Bewertungen.';
$_lang['ticket_err_vote_ticket_days'] = 'Der Zeitraum für die Bewertungen dieses Tickets ist abgelaufen.';
$_lang['ticket_err_vote_comment_days'] = 'Der Zeitraum für die Bewertungen dieses Kommentars ist abgelaufen.';
$_lang['ticket_unpublished_comment'] = 'Ihr Kommentar wird nach der Moderation veröffentlicht.';
$_lang['permission_denied'] = 'Für diese Aktion haben Sie keine Berechtigung.';
$_lang['field_required'] = 'Dieses Feld wird benötigt.';
$_lang['ticket_clear'] = 'Löschen';

$_lang['ticket_comments_intro'] = 'Hier finden Sie die Kommentare der gesamten Webseite.';
$_lang['ticket_comment_deleted_text'] = 'Dieser Kommentar wurde gelöscht.';
$_lang['ticket_comment_remove_confirm'] = 'Sind Sie sicher, dass Sie den <b>Thread endgültig löschen</b> möchten? Dieser Vorgang ist irreversibel!';

$_lang['ticket_comment_name'] = 'Autor';
$_lang['ticket_comment_text'] = 'Kommentar';
$_lang['ticket_comment_createdon'] = 'Erstellt am';
$_lang['ticket_comment_editedon'] = 'Bearbeitet am';
$_lang['ticket_comment_deletedon'] = 'Gelöscht am';
$_lang['ticket_comment_parent'] = 'Eltern';
$_lang['ticket_comment_thread'] = 'Thread';
$_lang['ticket_comment_email'] = 'E-Mail';
$_lang['ticket_comment_view'] = 'Kommentar auf der Webseite anzeigen';
$_lang['ticket_comment_reply'] = 'Antworten';
$_lang['ticket_comment_edit'] = 'Bearbeiten';
$_lang['ticket_comment_create'] = 'Kommentar schreiben';
$_lang['ticket_comment_preview'] = 'Vorschau';
$_lang['ticket_comment_save'] = 'Senden';
$_lang['ticket_comment_was_edited'] = 'Der Kommentar wurde bearbeitet';
$_lang['ticket_comment_guest'] = 'Gast';
$_lang['ticket_comment_deleted'] = 'Gelöscht';
$_lang['ticket_comment_captcha'] = 'Geben Sie die Summe ein: [[+a]] + [[+b]]';
$_lang['ticket_comment_notify'] = 'Über neue Kommentare benachrichtigen';
$_lang['ticket_comment_err_id'] = 'Der Kommentar mit der angegebenen ID = [[+id]] wurde nicht gefunden.';
$_lang['ticket_comment_err_no_auth'] = 'Sie müssen eingeloggt sein, um Kommentare erstellen zu können.';
$_lang['ticket_comment_err_wrong_user'] = 'Sie versuchen einen Kommentar zu aktualisieren, welcher nicht von Ihnen stammt.';
$_lang['ticket_comment_err_no_time'] = 'Der Zeitraum, um den Kommentar zu bearbeiten, ist abgelaufen.';
$_lang['ticket_comment_err_has_replies'] = 'Dieser Kommentar hat bereits Antworten erhalten, daher können Sie ihn nicht mehr ändern.';
$_lang['ticket_comment_err_parent'] = 'Sie versuchen, auf einen Kommentar zu antworten, der nicht existiert.';
$_lang['ticket_comment_err_comment'] = 'Dieser Kommentar existiert nicht.';
$_lang['ticket_comment_err_vote_own'] = 'Sie können Ihren eigenen Kommentar nicht bewerten.';
$_lang['ticket_comment_err_vote_already'] = 'Sie haben diesen Kommentar bereits bewertet.';
$_lang['ticket_comment_err_wrong_guest_ip'] = 'Sie haben keine Berechtigung: Ihre IP ist nicht dieselbe wie die IP des Autors.';
$_lang['ticket_comment_err_empty'] = 'Sie haben offenbar vergessen, einen Kommentar einzugeben.';
$_lang['ticket_comment_err_email'] = 'Die angegebene E-Mail-Adresse ist ungültig.';
$_lang['ticket_comment_err_guest_edit'] = 'Sie haben keine Berechtigung, Kommentare zu bearbeiten.';
$_lang['ticket_comment_err_captcha'] = 'Ungültiger Spamschutz-Code';
$_lang['ticket_comment_err_no_email'] = 'Sie müssen die E-Mail in Ihren Kontoeinstellungen angeben.';

$_lang['ticket_authors_intro'] = 'Hier werden Profile von Autoren mit Ratings gesammelt. Die Einstellungen für die Berechnung werden für jeden Ticket-Bereich separat festgelegt. <br/>Sie sehen, wie viele Tickets, Kommentare und Views der Autor aufzuweisen hat, sowie wie viele andere Nutzer ihn zu den Favoriten hinzugefügt und für ihn abgestimmt haben.';
$_lang['ticket_authors_rebuild'] = 'Autoren-Ratings aktualisieren';
$_lang['ticket_authors_rebuild_confirm'] = 'Möchten Sie wirklich die Bewertungen aller Autoren der Website neu berechnen lassen? Dieser Vorgang kann sehr lange dauern.';
$_lang['ticket_authors_rebuild_wait'] = 'Profile der Autoren verarbeiten ...';
$_lang['ticket_authors_rebuild_wait_ext'] = 'Verarbeitet: [[+processed]] von [[+total]]';
$_lang['ticket_author_createdon'] = 'Erstellt am';
$_lang['ticket_author_visitedon'] = 'Besucht am';
$_lang['ticket_author_rating'] = 'Ratings';
$_lang['ticket_author_tickets'] = 'Tickets';
$_lang['ticket_author_comments'] = 'Kommentare';
$_lang['ticket_author_views'] = 'Views';
$_lang['ticket_author_stars'] = 'Favoriten';
$_lang['ticket_author_stars_tickets'] = 'Tickets in Favoriten';
$_lang['ticket_author_stars_comments'] = 'Kommentare in Favoriten';
$_lang['ticket_author_votes_tickets'] = 'Ticket-Ratings';
$_lang['ticket_author_votes_comments'] = 'Kommentar-Ratings';
$_lang['ticket_author_votes_tickets_up'] = 'Stimmabgaben für Tickets';
$_lang['ticket_author_votes_tickets_down'] = 'Stimmabgaben gegen Tickets';
$_lang['ticket_author_votes_comments_up'] = 'Stimmabgaben für Kommentare';
$_lang['ticket_author_votes_comments_down'] = 'Stimmabgaben gegen Kommentare';
$_lang['ticket_author_rating_desc'] = 'Für / Gegen';
$_lang['ticket_author_stars_desc'] = 'Tickets / Kommentare';

$_lang['ticket_tickets_intro'] = 'Hier finden Sie die Tickets der gesamten Webseite.';
$_lang['ticket_publishedon'] = 'Veröffentlicht am';
$_lang['ticket_pagetitle'] = 'Titel';
$_lang['ticket_parent'] = 'Ticket-Bereich';
$_lang['ticket_author'] = 'Autor';
$_lang['ticket_delete'] = 'Ticket löschen';
$_lang['ticket_delete_text'] = 'Sind sie sicher, dass Sie dieses Ticket löschen wollen?';
$_lang['ticket_create'] = 'Ticket erstellen';
$_lang['ticket_disable_jevix'] = 'Jevix deaktivieren';
$_lang['ticket_disable_jevix_help'] = 'Inhalt der Webseite ohne Filterung mit dem Jevix-Snippet ausgeben. <b>Sehr gefährlich</b>, da jeder Benutzer, welcher die Webseite bearbeitet, diese auch angreifen kann (XSS, LFI usw.).';
$_lang['ticket_process_tags'] = 'MODX-Tags verarbeiten';
$_lang['ticket_process_tags_help'] = 'Standardmäßig werden die Tags in eckigen Klammern unverändert ohne Parser-Verarbeitung ausgegeben. Wenn aktiviert, werden Snippets, Chunks usw. auf dieser Webseite bearbeitet.';
$_lang['ticket_private'] = 'Privates Ticket';
$_lang['ticket_private_help'] = 'Wenn diese Option aktiviert ist, benötigt der Benutzer die Berechtigung "ticket_view_private", um dieses Ticket anzuzeigen.';
$_lang['ticket_show_in_tree'] = 'Im Ressourcenbaum anzeigen';
$_lang['ticket_show_in_tree_help'] = 'Standardmäßig werden Tickets im MODX-Ressourcenbaum nicht angezeigt, um diesen nicht zu überladen.';
$_lang['ticket_createdon'] = 'Erstellt am';
$_lang['ticket_publishedon'] = 'Veröffentlicht am';
$_lang['ticket_content'] = 'Inhalt';
$_lang['ticket_publish'] = 'Veröffentlichen';
$_lang['ticket_preview'] = 'Anzeigen';
$_lang['ticket_comments'] = 'Kommentare';
$_lang['ticket_actions'] = 'Aktionen';
$_lang['ticket_save'] = 'Speichern';
$_lang['ticket_draft'] = 'Zu Entwürfen';
$_lang['ticket_open'] = 'Öffnen';
$_lang['ticket_read_more'] = 'Weiterlesen';
$_lang['ticket_saved'] = 'Gespeichert!';

$_lang['ticket_threads_intro'] = 'In der Regel entspricht ein Thread den Kommentaren auf einer Seite.';
$_lang['ticket_thread'] = 'Thread';
$_lang['ticket_thread_name'] = 'Name des Threads';
$_lang['ticket_thread_createdon'] = 'Erstellt am';
$_lang['ticket_thread_editedon'] = 'Bearbeitet am';
$_lang['ticket_thread_deletedon'] = 'Gelöscht am';
$_lang['ticket_thread_comments'] = 'Kommentare';
$_lang['ticket_thread_resource'] = 'Ticket ID';
$_lang['ticket_thread_delete'] = 'Thread deaktivieren';
$_lang['ticket_thread_undelete'] = 'Thread aktivieren';
$_lang['ticket_thread_close'] = 'Thread schließen';
$_lang['ticket_thread_open'] = 'Thread öffnen';
$_lang['ticket_thread_remove'] = 'Löschen mit Kommentaren';
$_lang['ticket_thread_remove_confirm'] = 'Sind Sie sicher, dass Sie diesen <b>Thread endgültig löschen</b> wollen? Dieser Vorgang ist irreversibel!';
$_lang['ticket_thread_view'] = 'Auf der Webseite anzeigen';
$_lang['ticket_thread_err_deleted'] = 'Kommentieren ist deaktiviert.';
$_lang['ticket_thread_err_closed'] = 'Kommentare hinzufügen ist deaktiviert.';
$_lang['ticket_thread_manage_comments'] = 'Kommentare verwalten';
$_lang['ticket_thread_subscribed'] = 'Sie haben Benachrichtigungen über neue Kommentare dieses Threads abonniert.';
$_lang['ticket_thread_unsubscribed'] = 'Sie werden keine weiteren Benachrichtigungen über neue Kommentare dieses Threads erhalten.';

$_lang['ticket_date_now'] = 'Jetzt';
$_lang['ticket_date_today'] = 'Heute um';
$_lang['ticket_date_yesterday'] = 'Gestern um';
$_lang['ticket_date_tomorrow'] = 'Morgen um';
$_lang['ticket_date_minutes_back'] = '["Vor [[+minutes]] Minuten","Vor [[+minutes]] Minuten","Vor [[+minutes]] Minuten"]';
$_lang['ticket_date_minutes_back_less'] = 'Vor weniger als einer Minute';
$_lang['ticket_date_hours_back'] = '["Vor [[+hours]] Stunden","Vor [[+hours]] Stunden","Vor [[+hours]] Stunden"]';
$_lang['ticket_date_hours_back_less'] = 'Vor weniger als einer Stunde';
$_lang['ticket_date_months'] = '["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"]';

$_lang['ticket_like'] = 'Like';
$_lang['ticket_dislike'] = 'Dislike';
$_lang['ticket_refrain'] = 'Ratings anschauen';
$_lang['ticket_rating_total'] = 'Alle';
$_lang['ticket_rating_and'] = 'und';

$_lang['ticket_file_select'] = 'Dateien auswählen';
$_lang['ticket_file_delete'] = 'Löschen';
$_lang['ticket_file_restore'] = 'Wiederherstellen';
$_lang['ticket_file_insert'] = 'Link einfügen';
$_lang['ticket_err_source_initialize'] = 'Die Medienquelle kann nicht initialisiert werden';
$_lang['ticket_err_file_ns'] = 'Die angegebene Datei konnte nicht verarbeitet werden';
$_lang['ticket_err_file_ext'] = 'Falsche Dateiendung';
$_lang['ticket_err_file_save'] = 'Die Datei konnte nicht hochgeladen werden';
$_lang['ticket_err_file_owner'] = 'Diese Datei gehört nicht Ihnen';
$_lang['ticket_err_file_exists'] = 'Es existiert bereits eine Datei gleichen Namens oder Inhalts: "[[+file]]"';
$_lang['ticket_uploaded_files'] = 'Hochgeladene Dateien';

$_lang['tickets_action_view'] = 'Anzeigen';
$_lang['tickets_action_edit'] = 'Bearbeiten';
$_lang['tickets_action_publish'] = 'Veröffentlichen';
$_lang['tickets_action_unpublish'] = 'Zurückziehen';
$_lang['tickets_action_delete'] = 'Löschen';
$_lang['tickets_action_undelete'] = 'Wiederherstellen';
$_lang['tickets_action_remove'] = 'Entfernen';
$_lang['tickets_action_duplicate'] = 'Duplizieren';
$_lang['tickets_action_open'] = 'Öffnen';
$_lang['tickets_action_close'] = 'Schließen';

$_lang['ticket_comment_email_owner'] = 'Neuer Kommentar für Ihr Ticket "[[+pagetitle]]"';
$_lang['ticket_comment_email_reply'] = 'Antwort auf Ihren Kommentar zum Ticket "[[+pagetitle]]"';
$_lang['ticket_comment_email_subscription'] = 'Neuer Kommentar zum Ticket "[[+pagetitle]]"';
$_lang['ticket_comment_email_bcc'] = 'Neuer Kommentar zum Ticket "[[+pagetitle]]"';
$_lang['ticket_comment_email_unpublished_bcc'] = 'Kommentar zum Ticket "[[+pagetitle]]" zurückziehen';
$_lang['ticket_comment_email_unpublished_intro'] = 'User <b>[[+name]]</b> hat einen Kommentar zum Ticket "<a href="[[~[[+resource]]?scheme=`full`]]">[[+pagetitle]]</a>" hinterlassen. <br/>Bitte im MODX-Manager überprüfen:';
$_lang['ticket_comment_email_subscription_intro'] = 'Benutzer <b>[[+name]]</b> hat einen Kommentar zu einem Ticket hinterlassen, für das Sie angemeldet sind - "<a href="[[~[[+resource]]?scheme=`full`]]">[[+pagetitle]]</a>":';
$_lang['ticket_comment_email_reply_intro'] = 'Benutzer <b>[[+name]]</b> hat auf Ihren Kommentar zum Ticket "<a href="[[~[[+resource]]?scheme=`full`]]">[[+pagetitle]]</a>" geantwortet:';
$_lang['ticket_comment_email_reply_text'] = 'Text des Kommentars:';
$_lang['ticket_comment_email_owner_intro'] = 'Benutzer <b>[[+name]]</b> hat einen Kommentar zu Ihrem Ticket "<a href="[[~[[+resource]]?scheme=`full`]]">[[+pagetitle]]</a>" erstellt:';
$_lang['ticket_comment_email_bcc_intro'] = 'Benutzer <b>[[+name]]</b> hat einen Kommentar im Ticket "<a href="[[~[[+resource]]?scheme=`full`]]">[[+pagetitle]]</a>" erstellt:';

$_lang['ticket_email_bcc'] = 'Ein neues Ticket befindet sich auf der Website - "[[+pagetitle]]"';
$_lang['ticket_email_bcc_intro'] = 'Benutzer <b>[[+fullname]]</b> ([[+email]]) hat auf Ihrer Webseite ein neues Ticket erstellt: <a href="[[~[[+id]]?scheme=`full`]]">[[+pagetitle]]</a>';
$_lang['ticket_email_subscribed_intro'] = 'Benutzer <b>[[+fullname]]</b> hat ein neues Ticket erstellt: "<a href="[[~[[+id]]?scheme=`full`]]">[[+pagetitle]]</a>" für den Ticket-Bereich "<a href="[[~[[+section]]?scheme=`full`]]">[[+section_title]]</a>", welchen Sie abonniert haben.';
$_lang['ticket_email_all_comments'] = 'Alle Kommentare';
$_lang['ticket_email_view'] = 'Anzeigen';
$_lang['ticket_email_reply'] = 'Antworten';