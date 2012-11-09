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

$_lang['tickets.fastMode'] = 'If true - chunk of the comment will not be fully processed, the script only replace placeholders for values. All filters, chunks, snippets and etc will be cut.';
$_lang['tickets.useCss'] = 'If true, Quip will provide a basic CSS template for the presentation.';
$_lang['tickets.useJs'] = 'If true, Quip will provide a basic JS template for the presentation.';
$_lang['tickets.dateFormat'] = 'The format of the dates displayed for a comment.';
$_lang['tickets.gravatarIcon'] = 'The default Gravatar icon to load if none is found for a user.';
$_lang['tickets.gravatarSize'] = 'The size, in pixels, of the Gravatar.';
$_lang['tickets.gravatarUrl'] = 'The URL of Gravatar';
$_lang['tickets.tplCommentForm'] = 'The add comment form. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['tickets.tplComment'] = 'A Chunk for the comment itself.';
$_lang['tickets.tplComments'] = 'A Chunk for the outer wrapper for comments.';
$_lang['tickets.tplLoginToComment'] = 'The portion to show when the user is not logged in. Can either be a chunk name or value. If set to a value, will override the chunk.';
$_lang['tickets.tplCommentEmailOwner'] = 'The chunk for notification of new comments of ticket owner.';
$_lang['tickets.tplCommentEmailReply'] = 'The chunk for notification of user who replied to a comment.';