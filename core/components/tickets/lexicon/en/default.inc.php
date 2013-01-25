<?php
/**
 * Default English Lexicon Entries for Tickets
 *
 * @package tickets
 * @subpackage lexicon
 */
$_lang['tickets'] = 'Tickets';
$_lang['comments'] = 'Comments';
$_lang['threads'] = 'Comments threads';
$_lang['tickets_section'] = 'Tickets ticket';
$_lang['ticket'] = 'Ticket';
$_lang['ticket_all'] = 'Все';
$_lang['ticket_menu_desc'] = 'Comments management and more.';
$_lang['comments_all'] = 'All comments';

$_lang['tickets_section_create_here'] = 'Section with tickets';
$_lang['tickets_section_new'] = 'New tickes ticket';

$_lang['ticket_create_here'] = 'Create ticket';

$_lang['ticket_no_comments'] = 'This page has no comments. You can write the first.';
$_lang['err_no_jevix'] = 'Snippet Jevix is required for proper work. You need to install it from MODX repository.';
$_lang['ticket_err_wrong_user'] = 'You trying to update ticket that is not yours.';
$_lang['ticket_err_no_auth'] = 'You need to authenticate for create of ticket.';
$_lang['ticket_err_wrong_parent'] = 'Invalid section for this ticket was specified.';
$_lang['ticket_err_wrong_resource'] = 'Wrong ticket specified.';
$_lang['ticket_err_wrong_thread'] = 'Wrong comments thread specified.';
$_lang['ticket_err_access_denied'] = 'Access denied';
$_lang['ticket_err_form'] = 'Form contains errors. Please, fix it.';
$_lang['ticket_err_empty_comment'] = 'Comment can not be empty.';
$_lang['permission_denied'] = 'You do not have permissions for this action.';
$_lang['field_required'] = 'This field is required.';
$_lang['ticket_clear'] = 'Clear';


$_lang['ticket_comment_intro'] = '';
$_lang['ticket_comment_all_intro'] = 'Here are comments from all of the site.';
$_lang['ticket_comment_email_owner'] = 'New comment for your ticket "[[+pagetitle]]"';
$_lang['ticket_comment_email_reply'] = 'Reply to your comment for ticket "[[+pagetitle]]"';
$_lang['ticket_comment_deleted_text'] = 'This comment was deleted.';
$_lang['ticket_comment_update'] = 'Update this comment';
$_lang['ticket_comment_remove'] = 'Remove with children';
$_lang['ticket_comment_remove_confirm'] = 'Are you sure you want to permanently remove <b>comments thread</b>, starting with this? This operation is irreversible!';
$_lang['ticket_comment_delete'] = 'Delete this comment';
$_lang['ticket_comment_undelete'] = 'Undelete this comment';
$_lang['ticket_comment_viewauthor'] = 'Open authors page';

$_lang['ticket_comment_name'] = 'Author';
$_lang['ticket_comment_text'] = 'Comment';
$_lang['ticket_comment_createdon'] = 'Created on';
$_lang['ticket_comment_editedon'] = 'Edited on';
$_lang['ticket_comment_deletedon'] = 'Deleted on';
$_lang['ticket_comment_parent'] = 'Parent';
$_lang['ticket_comment_email'] = 'Email';
$_lang['ticket_comment_view'] = 'View comment on site';
$_lang['ticket_comment_reply'] = 'reply';
$_lang['ticket_comment_edit'] = 'edit';
$_lang['ticket_comment_create'] = 'Write comment';
$_lang['ticket_comment_preview'] = 'Preview';
$_lang['ticket_comment_save'] = 'Write';
$_lang['ticket_comment_was_edited'] = 'Comment was edited';
$_lang['ticket_comment_err_no_auth'] = 'You need to authenticate for create of comments.';
$_lang['ticket_comment_err_wrong_user'] = 'You trying to update comment that is not yours.';
$_lang['ticket_comment_err_no_time'] = 'Time for editing comment is ended.';
$_lang['ticket_comment_err_has_replies'] = 'This comment already has replies, therefore you cannot change it.';


$_lang['ticket_publishedon'] = 'Published On';
$_lang['ticket_pagetitle'] = 'Title';
$_lang['ticket_author'] = 'Author';
$_lang['ticket_delete'] = 'Delete ticket';
$_lang['ticket_delete_text'] = 'Are you sure you want to delete this ticket?';
$_lang['ticket_create'] = 'Create ticket?';
$_lang['ticket_disable_jevix'] = 'Disable Jevix';
$_lang['ticket_disable_jevix_help'] = 'Display content of this page without Jevix sanitization. It is dangerous, any user, that creates the page can attack your site (XSS, LFI etc.).';
$_lang['ticket_process_tags'] = 'Process MODX tags';
$_lang['ticket_process_tags_help'] = 'By default tags in bracket displaying as is, without processing by parser. If you enable it - on this page can be run various snippets, chunks, etc.';
$_lang['ticket_private'] = 'Private ticket';
$_lang['ticket_private_help'] = 'If true, users will must be have permission "ticket_view_private" for reading this ticket.';
$_lang['ticket_pagetitle'] = 'Title';
$_lang['ticket_content'] = 'Describe your problem';
$_lang['ticket_publish'] = 'Publish';
$_lang['ticket_preview'] = 'Preview';
$_lang['ticket_save'] = 'Submit';
$_lang['ticket_read_more'] = 'Read more';

$_lang['ticket_thread_intro'] = 'Comments that are grouped on threads. Usually, one thread is the all comments of the one page.';
$_lang['ticket_thread_createdon'] = 'Created on';
$_lang['ticket_thread_editedon'] = 'Edited on';
$_lang['ticket_thread_deletedon'] = 'Deleted on';
$_lang['ticket_thread_comments'] = 'Comments';
$_lang['ticket_thread_resource'] = 'Ticket id';
$_lang['ticket_thread_delete'] = 'Disable thread';
$_lang['ticket_thread_undelete'] = 'Enable thread';
$_lang['ticket_thread_remove'] = 'Remove with comments';
$_lang['ticket_thread_remove_confirm'] = 'Are you sure you want to totally remove <b>all</b> this thread? This operation is irreversible!';
$_lang['ticket_thread_view'] = 'View on site';
$_lang['ticket_thread_err_deleted'] = 'Comments to this ticket was disabled';
$_lang['ticket_thread_manage_comments'] = 'Manage comments';


$_lang['ticket_date_now'] = 'Just now';
$_lang['ticket_date_today'] = 'Today at';
$_lang['ticket_date_yesterday'] = 'Yesterday at';
$_lang['ticket_date_tomorrow'] = 'Tomorrow at';
$_lang['ticket_date_minutes_back'] = '["[[+minutes]] minutes ago","[[+minutes]] minutes ago","[[+minutes]] minutes ago"]';
$_lang['ticket_date_minutes_back_less'] = 'Less than a minute ago';
$_lang['ticket_date_hours_back'] = '["[[+hours]] hours ago","[[+hours]] hours ago","[[+hours]] hours ago"]';
$_lang['ticket_date_hours_back_less'] = 'Less than an hour ago';
$_lang['ticket_date_months'] = '["january","february","march","april","may","june","july","august","september","october","november","december"]';