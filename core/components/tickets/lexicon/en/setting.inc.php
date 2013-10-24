<?php
/**
 * Settings English Lexicon Entries
 */

$_lang['area_tickets.main'] = 'Main';
$_lang['area_tickets.ticket'] = 'Ticket';
$_lang['area_tickets.comment'] = 'Comment';
$_lang['area_tickets.mail'] = 'Email notices';

$_lang['setting_tickets.frontend_css'] = 'Frontend styles';
$_lang['setting_tickets.frontend_css_desc'] = 'Path to file with styles of the shop. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_tickets.frontend_js'] = 'Frontend scripts';
$_lang['setting_tickets.frontend_js_desc'] = 'Path to file with scripts of the shop. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';

$_lang['setting_tickets.default_template'] = 'Default template for new tickets';
$_lang['setting_tickets.default_template_desc'] = 'Default template for new tickets. Using in manager and when creating tickets on frontend.';
$_lang['setting_tickets.ticket_isfolder_force'] = 'Force "isfolder"';
$_lang['setting_tickets.ticket_isfolder_force_desc'] = 'Force parameter "isfolder" for tickets.';
$_lang['setting_tickets.ticket_hidemenu_force'] = 'Force "hidemenu"';
$_lang['setting_tickets.ticket_hidemenu_force_desc'] = 'Force parameter "hidemenu" for tickets.';
$_lang['setting_tickets.section_content_default'] = 'Default content for new tickets section';
$_lang['setting_tickets.section_content_default_desc'] = ' Here you can specify the default content of new tickets section. By default it lists children tickets.';

$_lang['setting_tickets.enable_editor'] = 'Editor "markItUp"';
$_lang['setting_tickets.enable_editor_desc'] = 'If true, enables "markItUp" on frontend, for handy work with tickets and comments.';
$_lang['setting_tickets.editor_config.ticket'] = 'Settings of tickets editor';
$_lang['setting_tickets.editor_config.ticket_desc'] = 'JSON encoded array of settings for "markItUp". See more details - http://markitup.jaysalvat.com/documentation/';
$_lang['setting_tickets.editor_config.comment'] = 'Settings of comments editor';
$_lang['setting_tickets.editor_config.comment_desc'] = 'JSON encoded array of settings for "markItUp". See more details - http://markitup.jaysalvat.com/documentation/';

$_lang['setting_tickets.disable_jevix_default'] = 'Disable Jevix by default';
$_lang['setting_tickets.disable_jevix_default_desc'] = 'If true, setting "Disable Jevix" will be disabled for all new tickets by default.';
$_lang['setting_tickets.process_tags_default'] = 'Process tags by default';
$_lang['setting_tickets.process_tags_default_desc'] = 'If true, setting "Process MODX tags" will be disabled for all new tickets by default.';
$_lang['setting_tickets.private_ticket_page'] = 'Redirect from private ticket';
$_lang['setting_tickets.private_ticket_page_desc'] = 'Id of existing MODX resource for redirect user without needed permissions for viewing private tickets to.';

$_lang['setting_tickets.snippet_prepare_comment'] = 'Snippet for comment prepare';
$_lang['setting_tickets.snippet_prepare_comment_desc'] = 'Special snippet, that will prepare all comments before returning to frontend. It will be called in class "Tickets" and will be able to use all it methods and variables.';
$_lang['setting_tickets.comment_edit_time'] = 'Time to edit';
$_lang['setting_tickets.comment_edit_time_desc'] = 'Time in seconds for editing own comment.';
$_lang['setting_tickets.clear_cache_on_comment_save'] = 'Clear cache on commenting';
$_lang['setting_tickets.clear_cache_on_comment_save_desc'] = 'If true, cache of ticket will be cleared on any action with comment: create\update\remove. It needed only if you call snippet "TicketComments" uncached.';

$_lang['setting_tickets.mail_from'] = 'Mailbox outgoing mail';
$_lang['setting_tickets.mail_from_desc'] = 'Address to send the notifications. If not full - will be used system setting "emailsender".';
$_lang['setting_tickets.mail_from_name'] = 'The name of the sender';
$_lang['setting_tickets.mail_from_name_desc'] = 'Name of sender of all notifications. If empty - will be used systen setting "site_name".';
$_lang['setting_tickets.mail_queue'] = 'Messages queue';
$_lang['setting_tickets.mail_queue_desc'] = 'Whether to use a message queue or send letters immediately. If you activate this option, you need to add to the cron file "/core/components/tickets/cron/mail_queue.php"';
$_lang['setting_tickets.mail_bcc'] = 'Admin notifications';
$_lang['setting_tickets.mail_bcc_desc'] = 'Specify a comma-separated list of <b>id</b> of administrators you want to send messages about new ticket and comments.';
$_lang['setting_tickets.mail_bcc_level'] = 'Level of admin notifications';
$_lang['setting_tickets.mail_bcc_level_desc'] = 'There are 3 possible levels of admin notifications: 0 - disabled, 1 - send only messages about new tickets, 2 - tickets + comments. Recommended level is 1.';