<?php
/**
 * Settings English Lexicon Entries
 */

$_lang['area_tickets.main'] = 'Main';
$_lang['area_tickets.ticket'] = 'Ticket';
$_lang['area_tickets.comment'] = 'Comment';

$_lang['setting_tickets.frontend_css'] = 'Frontend styles';
$_lang['setting_tickets.frontend_css_desc'] = 'Path to file with styles of the shop. If you want to use your own styles - specify them here, or clean this parameter and load them in site template.';
$_lang['setting_tickets.frontend_js'] = 'Frontend scripts';
$_lang['setting_tickets.frontend_js_desc'] = 'Path to file with scripts of the shop. If you want to use your own sscripts - specify them here, or clean this parameter and load them in site template.';

$_lang['setting_tickets.default_template'] = 'Default template for new tickets';
$_lang['setting_tickets.default_template_desc'] = 'Default template for new tickets. Using in manager and when creating tickets on frontend.';

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