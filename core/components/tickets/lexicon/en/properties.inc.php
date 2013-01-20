<?php
/**
 * Properties English Lexicon Entries for Tickets
 *
 * @package tickets
 * @subpackage lexicon
 */
$_lang['tickets.action'] = 'Mode of snippet';
$_lang['tickets.limit'] = 'The number of results to limit.';
$_lang['tickets.start'] = 'The start index of results to pull from.';
$_lang['tickets.sortBy'] = 'The field to sort by.';
$_lang['tickets.sortDir'] = 'The direction to sort by';
$_lang['tickets.tpl'] = 'The chunk tpl to use for each row.';
$_lang['tickets.tplFormCreate'] = 'Chunk for creation of new ticket';
$_lang['tickets.tplFormUpdate'] = 'Chunk for update existing ticket';
$_lang['tickets.tplSectionRow'] = 'Chunk for template one section of form select';
$_lang['tickets.tplPreview'] = 'Chunk for preview ticket before publish';
$_lang['tickets.cacheKey'] = 'Name of snippet cache. If empty - caching will be disabled.';
$_lang['tickets.toPlaceholder'] = 'If not empty, the snippet will save output to placeholder with that name, instead of return it to screen.';
$_lang['tickets.showLog'] = 'Display additional information about snippet work. Only for authenticated in context "mgr".';
$_lang['tickets.parents'] = 'Container list, separated by commas, to search results. By default, the query is limited to the current parent. If set to 0, query not limited.';

$_lang['tickets.thread'] = 'Name of comments thread. By default, "resource-[[*id]]".';
$_lang['tickets.fastMode'] = 'If enabled, then in chunk will be only received values ​​from the database. All raw tags of MODX, such as filters, snippets calls will be cut.';
$_lang['tickets.dateFormat'] = 'The format of the dates displayed for a comment, with function date().';
$_lang['tickets.gravatarIcon'] = 'The default Gravatar icon to load if none is found for a user.';
$_lang['tickets.gravatarSize'] = 'The size, in pixels, of the Gravatar.';
$_lang['tickets.gravatarUrl'] = 'The URL of Gravatar';
$_lang['tickets.tplCommentForm'] = 'The add comment form. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['tickets.tplComment'] = 'A Chunk for the comment itself.';
$_lang['tickets.tplComments'] = 'A Chunk for the outer wrapper for comments.';
$_lang['tickets.tplLoginToComment'] = 'The portion to show when the user is not logged in. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['tickets.tplCommentEmailOwner'] = 'The chunk for notification of new comments of ticket owner.';
$_lang['tickets.tplCommentEmailReply'] = 'The chunk for notification of user who replied to a comment.';