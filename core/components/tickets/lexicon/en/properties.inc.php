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
$_lang['tickets_prop_resources'] = 'Comma-delimited list of ids to include in the results. Prefix an id with a dash to exclude the resource from the result.';
$_lang['tickets_prop_where'] = 'A JSON-style expression of criteria to build any additional where clauses from';
$_lang['tickets_prop_tvPrefix'] = 'The prefix for TemplateVar properties, "tv." for ex`ample. By default it is empty.';
$_lang['tickets_prop_includeContent'] = 'Retrieve field "content" from resources.';
$_lang['tickets_prop_includeTVs'] = 'An optional comma-delimited list of TemplateVar names to include in selection. For example "action,time" give you placeholders [[+action]] and [[+time]].';
$_lang['tickets_prop_toPlaceholder'] = 'If not empty, the snippet will save output to placeholder with that name, instead of return it to screen.';
$_lang['tickets_prop_outputSeparator'] = 'An optional string to separate each tpl instance.';

$_lang['tickets_prop_showLog'] = 'Display additional information about snippet work. Only for authenticated in context "mgr".';
$_lang['tickets_prop_showUnpublished'] = 'Show unpublished resources.';
$_lang['tickets_prop_showDeleted'] = 'Show deleted goods.';
$_lang['tickets_prop_showHidden'] = 'Show goods, that hidden in menu.';
$_lang['tickets_prop_fastMode'] = 'If enabled, then in chunk will be only received values ​​from the database. All raw tags of MODX, such as filters, snippets calls will be cut.';

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
$_lang['tickets_prop_tplCommentAuth'] = 'Chunk for displaying comment for authorized user.';
$_lang['tickets_prop_tplCommentGuest'] = 'Chunk for displaying comment for guests.';
$_lang['tickets_prop_tplCommentDeleted'] = 'Chunk for displaying deleted comment.';
$_lang['tickets_prop_tplComments'] = 'Chunk for the outer wrapper for comments.';
$_lang['tickets_prop_tplLoginToComment'] = 'Chunk  for guests with requirement of authorization.';
$_lang['tickets_prop_tplCommentEmailOwner'] = 'Chunk for notification of new comments of ticket owner.';
$_lang['tickets_prop_tplCommentEmailReply'] = 'Chunk for notification of user who replied to a comment.';
$_lang['tickets_prop_tplCommentEmailSubscription'] = 'Chunk for notification of a subscriber about new comment.';
$_lang['tickets_prop_tplCommentEmailBcc'] = 'Chunk for bcc notification about new ticket.';
$_lang['tickets_prop_tplTicketEmailBcc'] = 'Chunk for bcc notification about new comment.';

$_lang['tickets_prop_commentsDepth'] = 'Integer value indicating maximum depth of comments thread.';
$_lang['tickets_prop_autoPublish'] = 'If true, all comments in thread will be published without moderation.';
$_lang['tickets_prop_formBefore'] = 'If true, commenting form will be placed before comments.';
//$_lang['tickets_prop_dateFormat'] = 'The format of the dates displayed for a comment, with function date().';
$_lang['tickets_prop_gravatarIcon'] = 'The default Gravatar icon to load if none is found for a user.';
$_lang['tickets_prop_gravatarSize'] = 'The size, in pixels, of the Gravatar.';
$_lang['tickets_prop_gravatarUrl'] = 'The URL of Gravatar';

$_lang['tickets_prop_allowedFields'] = 'Fields of the ticket, which allowed to fill the user. You can specify the names of the TVs.';
$_lang['tickets_prop_requiredFields'] = 'The required fields of the ticket, which the user must fill in to send the form.';
$_lang['tickets_prop_redirectUnpublished'] = 'Вы можете указать, на какой документ отправлять пользователя при создании неопубликованного тикета.';