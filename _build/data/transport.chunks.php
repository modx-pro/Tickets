<?php
/**
 * Add chunks to build
 */

$chunks = array();

$tmp = array(
	'tpl.Tickets.form.create' => 'form_create'
	,'tpl.Tickets.form.update' => 'form_update'
	,'tpl.Tickets.form.preview' => 'form_preview'
	,'tpl.Tickets.form.section.row' => 'form_section_row'

	,'tpl.Tickets.ticket.latest' => 'ticket_latest'
	,'tpl.Tickets.ticket.email.bcc' => 'ticket_email_bcc'

	,'tpl.Tickets.comment.form' => 'comment_form'
	,'tpl.Tickets.comment.one.auth' => 'comment_one_auth'
	,'tpl.Tickets.comment.one.guest' => 'comment_one_guest'
	,'tpl.Tickets.comment.one.deleted' => 'comment_one_deleted'
	,'tpl.Tickets.comment.wrapper' => 'comment_wrapper'
	,'tpl.Tickets.comment.login' => 'comment_login'
	,'tpl.Tickets.comment.latest' => 'comment_latest'
	,'tpl.Tickets.comment.email.owner' => 'comment_email_owner'
	,'tpl.Tickets.comment.email.reply' => 'comment_email_reply'
	,'tpl.Tickets.comment.email.subscription' => 'comment_email_subscription'
	,'tpl.Tickets.comment.email.bcc' => 'comment_email_bcc'

	,'tpl.Tickets.list.row' => 'ticket_list_row'
	,'tpl.Tickets.sections.row' => 'ticket_sections_row'
);

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'name' => $k
		,'description' => ''
		,'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl')
		,'static' => BUILD_CHUNK_STATIC
		,'source' => 1
		,'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v.'.tpl'
		),'',true,true);

	$chunks[] = $chunk;
}

return $chunks;