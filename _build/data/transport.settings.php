<?php
/**
 * Loads system settings into build
 *
 * */

$settings = array();

$tmp = array(
	'enable_editor' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'tickets.main',
	),
	'frontend_css' => array(
		'value' => '[[+cssUrl]]web/default.css',
		'xtype' => 'textfield',
		'area' => 'tickets.main',
	),
	'frontend_js' => array(
		'value' => '[[+jsUrl]]web/default.js',
		'xtype' => 'textfield',
		'area' => 'tickets.main',
	),
	'editor_config.ticket' => array(
		'xtype' => 'textarea',
		'value' => '{onTab: {keepDefault:false, replaceWith:"	"}
		,markupSet: [
			{name:"Bold", className: "btn-bold", key:"B", openWith:"<b>", closeWith:"</b>" }
			,{name:"Italic", className: "btn-italic", key:"I", openWith:"<i>", closeWith:"</i>"  }
			,{name:"Underline", className: "btn-underline", key:"U", openWith:"<u>", closeWith:"</u>" }
			,{name:"Stroke through", className: "btn-stroke", key:"S", openWith:"<s>", closeWith:"</s>" }
			,{separator:"---------------" }
			,{name:"Bulleted List", className: "btn-bulleted", openWith:"	<li>", closeWith:"</li>", multiline:true, openBlockWith:"<ul>\n", closeBlockWith:"\n</ul>"}
			,{name:"Numeric List", className: "btn-numeric", openWith:"	<li>", closeWith:"</li>", multiline:true, openBlockWith:"<ol>\n", closeBlockWith:"\n</ol>"}
			,{separator:"---------------" }
			,{name:"Quote", className: "btn-quote", openWith:"<blockquote>", closeWith:"</blockquote>"}
			,{name:"Code", className: "btn-code", openWith:"<code>", closeWith:"</code>"}
			,{name:"Link", className: "btn-link", openWith:"<a href=\"[![Link:!:http://]!]\">", closeWith:"</a>" }
			,{name:"Picture", className: "btn-picture", replaceWith:"<img src=\"[![Source:!:http://]!]\" />" }
			,{separator:"---------------" }
			,{name:"Cut", className: "btn-cut", openWith:"<cut/>" }
		]}',
		'area' => 'tickets.ticket',
	),
	'editor_config.comment' => array(
		'xtype' => 'textarea',
		'value' => '{onTab: {keepDefault:false, replaceWith:"	"}
		,markupSet: [
			{name:"Bold", className: "btn-bold", key:"B", openWith:"<b>", closeWith:"</b>" }
			,{name:"Italic", className: "btn-italic", key:"I", openWith:"<i>", closeWith:"</i>"  }
			,{name:"Underline", className: "btn-underline", key:"U", openWith:"<u>", closeWith:"</u>" }
			,{name:"Stroke through", className: "btn-stroke", key:"S", openWith:"<s>", closeWith:"</s>" }
			,{separator:"---------------" }
			,{name:"Quote", className: "btn-quote", openWith:"<blockquote>", closeWith:"</blockquote>"}
			,{name:"Code", className: "btn-code", openWith:"<code>", closeWith:"</code>"}
			,{name:"Link", className: "btn-link", openWith:"<a href=\"[![Link:!:http://]!]\">", closeWith:"</a>" }
			,{name:"Picture", className: "btn-picture", replaceWith:"<img src=\"[![Source:!:http://]!]\" />" }
		]}',
		'area' => 'tickets.comment',
	),
	'default_template' => array(
		'xtype' => 'modx-combo-template',
		'value' => '',
		'area' => 'tickets.ticket',
	),
	'disable_jevix_default' => array(
		'xtype' => 'combo-boolean',
		'value' => false,
		'area' => 'tickets.ticket',
	),
	'process_tags_default' => array(
		'xtype' => 'combo-boolean',
		'value' => false,
		'area' => 'tickets.ticket',
	),
	'snippet_prepare_comment' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'tickets.comment',
	),
	'comment_edit_time' => array(
		'xtype' => 'numberfield',
		'value' => 180,
		'area' => 'tickets.comment',
	),
	'clear_cache_on_comment_save' => array(
		'xtype' => 'combo-boolean',
		'value' => false,
		'area' => 'tickets.comment',
	),
	'private_ticket_page' => array(
		'xtype' => 'numberfield',
		'value' => 0,
		'area' => 'tickets.ticket',
	),

	'mail_from' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'tickets.mail',
	),
	'mail_from_name' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'tickets.mail',
	),
	'mail_queue' => array(
		'xtype' => 'combo-boolean',
		'value' => false,
		'area' => 'tickets.mail',
	),
	'mail_bcc' => array(
		'xtype' => 'textfield',
		'value' => '',
		'area' => 'tickets.mail',
	),
	'mail_bcc_level' => array(
		'xtype' => 'numberfield',
		'value' => 1,
		'area' => 'tickets.mail',
	),
	'section_content_default' => array(
		'value' => "[[!pdoPage?\n\t&element=`getTickets`\n]]\n\n[[!+page.nav]]",
		'xtype' => 'textarea',
		'area' => 'tickets.section',
	),
	'ticket_hidemenu_force' => array(
		'value' => false,
		'xtype' => 'combo-boolean',
		'area' => 'tickets.ticket',
	),
	'ticket_isfolder_force' => array(
		'value' => false,
		'xtype' => 'combo-boolean',
		'area' => 'tickets.ticket',
	),
	'ticket_show_in_tree_default' => array(
		'value' => false,
		'xtype' => 'combo-boolean',
		'area' => 'tickets.ticket',
	),
	'section_id_as_alias' => array(
		'value' => false,
		'xtype' => 'combo-boolean',
		'area' => 'tickets.section',
	),
	'ticket_id_as_alias' => array(
		'value' => false,
		'xtype' => 'combo-boolean',
		'area' => 'tickets.ticket',
	)

);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => PKG_NAME_LOWER.'.'.$k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	),'',true,true);

	$settings[] = $setting;
}

return $settings;