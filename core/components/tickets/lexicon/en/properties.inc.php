<?php
/*
 * Properties English Lexicon Entries
 *
 * */

$_lang['tickets_prop_limit'] = 'The number of results to limit.';
$_lang['tickets_prop_offset'] = 'An offset of resources returned by the criteria to skip';
$_lang['tickets_prop_depth'] = 'Integer value indicating depth to search for resources from each parent.';
$_lang['tickets_prop_sortby'] = 'The field to sort by.';
$_lang['tickets_prop_sortdir'] = 'The direction to sort by';
$_lang['tickets_prop_parents'] = 'Container list, separated by commas, to search results. By default, the query is limited to the current parent. If set to 0, query not limited.';
$_lang['tickets_prop_resources'] = 'Comma-separated list of resource ids to include. Prefix an id with a dash to exclude it.';
$_lang['tickets_prop_threads'] = 'Comma-separated list of comment thread ids to include. Prefix an id with a dash to exclude it.';
$_lang['tickets_prop_where'] = 'A JSON-style expression of criteria to build any additional where clauses from';
$_lang['tickets_prop_tvPrefix'] = 'Prefix for TV placeholders, for example "tv.". Empty by default.';
$_lang['tickets_prop_includeContent'] = 'Retrieve field "content" from resources.';
$_lang['tickets_prop_includeTVs'] = 'An optional comma-delimited list of TemplateVar names to include in selection. For example "action,time" give you placeholders [[+action]] and [[+time]].';
$_lang['tickets_prop_toPlaceholder'] = 'If set, the snippet stores output in this placeholder instead of printing it.';
$_lang['tickets_prop_outputSeparator'] = 'An optional string to separate each tpl instance.';

$_lang['tickets_prop_showLog'] = 'Display additional information about snippet work. Only for authenticated in context "mgr".';
$_lang['tickets_prop_showUnpublished'] = 'Show unpublished resources.';
$_lang['tickets_prop_showDeleted'] = 'Show deleted resources.';
$_lang['tickets_prop_showHidden'] = 'Show resources hidden from the menu.';
$_lang['tickets_prop_fastMode'] = 'If enabled, chunks receive only DB values. Unprocessed MODX tags (filters, snippet calls) are stripped.';

$_lang['tickets_prop_action'] = 'Mode of snippet';
$_lang['tickets_prop_cacheKey'] = 'Name of snippet cache. If empty - caching will be disabled.';
$_lang['tickets_prop_cacheTime'] = 'Time of cache.';
$_lang['tickets_prop_thread'] = 'Name of comments thread. By default, "resource-[[*id]]".';
$_lang['tickets_prop_user'] = 'Select only elements created by this user.';

$_lang['tickets_prop_tpl'] = 'The chunk tpl to use for each row.';
$_lang['tickets_prop_tplFormCreate'] = 'Chunk for creation of new ticket';
$_lang['tickets_prop_tplFormUpdate'] = 'Chunk for update existing ticket';
$_lang['tickets_prop_tplSectionRow'] = 'Chunk for template one section of form select';
$_lang['tickets_prop_tplPreview'] = 'Chunk for preview ticket before publish';
$_lang['tickets_prop_tplCommentForm'] = 'The add comment form. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['tickets_prop_tplCommentFormGuest'] = 'The add comment form for anonymous users.';
$_lang['tickets_prop_tplCommentAuth'] = 'Chunk for displaying comment for authorized user.';
$_lang['tickets_prop_tplCommentGuest'] = 'Chunk for displaying comment for guests.';
$_lang['tickets_prop_tplCommentDeleted'] = 'Chunk for displaying deleted comment.';
$_lang['tickets_prop_tplComments'] = 'Chunk for the outer wrapper for comments.';
$_lang['tickets_prop_tplLoginToComment'] = 'Chunk that asks guests to sign in before commenting.';
$_lang['tickets_prop_tplCommentEmailOwner'] = 'Chunk for notification of new comments of ticket owner.';
$_lang['tickets_prop_tplCommentEmailReply'] = 'Chunk for notification of user who replied to a comment.';
$_lang['tickets_prop_tplCommentEmailSubscription'] = 'Chunk for notification of a subscriber about new comment.';
$_lang['tickets_prop_tplCommentEmailBcc'] = 'Chunk for BCC notification about a new comment.';
$_lang['tickets_prop_tplTicketEmailBcc'] = 'Chunk for BCC notification about a new ticket.';
$_lang['tickets_prop_tplTicketEmailSubscription'] = 'Chunk for notification of a section subscriber about a new ticket.';
$_lang['tickets_prop_tplCommentEmailUnpublished'] = 'Chunk for notification about unpublished new comment.';
$_lang['tickets_prop_tplWrapper'] = 'Name of a chunk serving as a wrapper template for the output. This does not work with toSeparatePlaceholders.';
$_lang['tickets_prop_threadUrl'] = 'Absolute URL used in the manager to open a comment. Use when commenting on non-resource objects.';

$_lang['tickets_prop_commentsDepth'] = 'Integer value indicating maximum depth of comments thread.';
$_lang['tickets_prop_autoPublish'] = 'If true, all comments of authenticated users in thread will be published without moderation.';
$_lang['tickets_prop_autoPublishGuest'] = 'If true, all comments of anonymous users in thread will be published without moderation.';
$_lang['tickets_prop_formBefore'] = 'If true, commenting form will be placed before comments.';
//$_lang['tickets_prop_dateFormat'] = 'The format of the dates displayed for a comment, with function date().';
$_lang['tickets_prop_gravatarIcon'] = 'The default Gravatar icon to load if none is found for a user.';
$_lang['tickets_prop_gravatarSize'] = 'The size, in pixels, of the Gravatar.';
$_lang['tickets_prop_gravatarUrl'] = 'The URL of Gravatar';

$_lang['tickets_prop_allowedFields'] = 'Ticket fields the user may fill in. TV names are allowed.';
$_lang['tickets_prop_requiredFields'] = 'The required fields, which the user must fill in to send the form.';
$_lang['tickets_prop_bypassFields'] = 'Ticket fields that should not be filtered on save.';
$_lang['tickets_prop_redirectUnpublished'] = 'You can specify what document to send the user when creating unpublished ticket.';
$_lang['tickets_prop_sections_parents'] = 'By default all ticket sections are listed. Limit them with a comma-separated list of parent ids.';
$_lang['tickets_prop_sections_resources'] = 'By default all ticket sections are listed. Limit them with a comma-separated list of section ids.';
$_lang['tickets_prop_sections_sortby'] = 'Field to sort the list of sections.';
$_lang['tickets_prop_sections_sortdir'] = 'Sort direction for the list of sections.';
$_lang['tickets_prop_sections_context'] = 'Comma separated list of contexts to search sections.';

$_lang['tickets_prop_meta_tpl'] = 'Chunk for templating information about a ticket.';
$_lang['tickets_prop_getSection'] = 'Make additional query in the DB to obtain the parent section?';
$_lang['tickets_prop_getUser'] = 'Make additional query in the DB to obtain a profile of the author?';

$_lang['tickets_prop_allowGuest'] = 'Enable commenting for unauthorized users?';
$_lang['tickets_prop_allowGuestEdit'] = 'Allow unauthorized users to edit their comments?';
$_lang['tickets_prop_allowGuestEmails'] = 'Send to the guests email notifications about the replies?';
$_lang['tickets_prop_enableCaptcha'] = 'Enable protection against spam for unauthorized users?';
$_lang['tickets_prop_minCaptcha'] = 'Minimum number used to generate the captcha.';
$_lang['tickets_prop_maxCaptcha'] = 'Maximum number used to generate the captcha.';

$_lang['tickets_prop_allowFiles'] = 'Allow user to upload files to the server.';
$_lang['tickets_prop_source'] = 'Media source id for uploads. Defaults to the "tickets.source_default" system setting.';
$_lang['tickets_prop_tplFiles'] = 'Container to display the uploader and the list of already uploaded files.';
$_lang['tickets_prop_tplFile'] = 'Chunk for an uploaded file that is not an image.';
$_lang['tickets_prop_tplImage'] = 'The chunk for uploaded image.';

$_lang['tickets_prop_getFiles'] = 'Display a list of uploaded files?';
$_lang['tickets_prop_unusedFiles'] = 'Show only uploaded files that are not linked in the ticket content.';
$_lang['tickets_prop_meta_tplFile'] = 'The chunk for file in the list.';

$_lang['tickets_prop_class'] = 'Specify class for selecting results.';
$_lang['tickets_prop_tree'] = 'Show comments as a tree. Set to No for a flat list (limit/offset pagination).';
$_lang['tickets_prop_separatePlaceholder'] = 'If true, each comment is placed into a separate placeholder instead of a single output.';
