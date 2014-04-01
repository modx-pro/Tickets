<?php
/**
 * Add chunks to build
 */

$chunks = array();

$tmp = array(
	'tpl.Tickets.form.create' => 'form_create',
	'tpl.Tickets.form.update' => 'form_update',
	'tpl.Tickets.form.preview' => 'form_preview',
	'tpl.Tickets.form.files' => 'form_files',
	'tpl.Tickets.form.file' => 'form_file',
	'tpl.Tickets.form.image' => 'form_image',

	'tpl.Tickets.ticket.latest' => 'ticket_latest',
	'tpl.Tickets.ticket.email.bcc' => 'ticket_email_bcc',

	'tpl.Tickets.comment.form' => 'comment_form',
	'tpl.Tickets.comment.form.guest' => 'comment_form_guest',
	'tpl.Tickets.comment.one.auth' => 'comment_one_auth',
	'tpl.Tickets.comment.one.guest' => 'comment_one_guest',
	'tpl.Tickets.comment.one.deleted' => 'comment_one_deleted',
	'tpl.Tickets.comment.wrapper' => 'comment_wrapper',
	'tpl.Tickets.comment.login' => 'comment_login',
	'tpl.Tickets.comment.latest' => 'comment_latest',
	'tpl.Tickets.comment.email.owner' => 'comment_email_owner',
	'tpl.Tickets.comment.email.reply' => 'comment_email_reply',
	'tpl.Tickets.comment.email.subscription' => 'comment_email_subscription',
	'tpl.Tickets.comment.email.bcc' => 'comment_email_bcc',
	'tpl.Tickets.comment.list.row' => 'comment_list_row',

	'tpl.Tickets.list.row' => 'ticket_list_row',
	'tpl.Tickets.sections.row' => 'ticket_sections_row',
	'tpl.Tickets.meta' => 'ticket_meta',
	'tpl.Tickets.meta.file' => 'ticket_meta_file',
);

// Save chunks for setup options
$BUILD_CHUNKS = array();

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'name' => $k,
		'description' => '',
		'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl'),
		'static' => BUILD_CHUNK_STATIC,
		'source' => 1,
		'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v.'.tpl',
		),'',true,true);

	$chunks[] = $chunk;

	$BUILD_CHUNKS[$k] = file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl');
}

ksort($BUILD_CHUNKS);
return $chunks;