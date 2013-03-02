<?php
/**
 * Loads system settings into build
 *
 * @package tickets
 * @subpackage build
 */
$settings = array();

$settings['enable_editor']= $modx->newObject('modSystemSetting');
$settings['enable_editor']->fromArray(array(
	'key' => 'tickets.enable_editor'
	,'value' => 'true'
	,'xtype' => 'combo-boolean'
	,'namespace' => 'tickets'
	,'area' => 'Editor'
),'',true,true);

$settings['editor_config.ticket']= $modx->newObject('modSystemSetting');
$settings['editor_config.ticket']->fromArray(array(
	'key' => 'tickets.editor_config.ticket'
	,'value' => '{onTab: {keepDefault:false, replaceWith:"	"}
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
	]}'
	,'xtype' => 'textarea'
	,'namespace' => 'tickets'
	,'area' => 'Editor'
),'',true,true);

$settings['editor_config.comment']= $modx->newObject('modSystemSetting');
$settings['editor_config.comment']->fromArray(array(
	'key' => 'tickets.editor_config.comment'
	,'value' => '{onTab: {keepDefault:false, replaceWith:"	"}
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
	]}'
	,'xtype' => 'textarea'
	,'namespace' => 'tickets'
	,'area' => 'Editor'
),'',true,true);

$settings['default_template']= $modx->newObject('modSystemSetting');
$settings['default_template']->fromArray(array(
	'key' => 'tickets.default_template'
	,'value' => ''
	,'xtype' => 'numberfield'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

$settings['disable_jevix_default']= $modx->newObject('modSystemSetting');
$settings['disable_jevix_default']->fromArray(array(
	'key' => 'tickets.disable_jevix_default'
	,'value' => 'false'
	,'xtype' => 'combo-boolean'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

$settings['process_tags_default']= $modx->newObject('modSystemSetting');
$settings['process_tags_default']->fromArray(array(
	'key' => 'tickets.process_tags_default'
	,'value' => 'false'
	,'xtype' => 'combo-boolean'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

$settings['snippet_prepare_comment']= $modx->newObject('modSystemSetting');
$settings['snippet_prepare_comment']->fromArray(array(
	'key' => 'tickets.snippet_prepare_comment'
	,'value' => ''
	,'xtype' => 'textfield'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

$settings['comment_edit_time']= $modx->newObject('modSystemSetting');
$settings['comment_edit_time']->fromArray(array(
	'key' => 'tickets.comment_edit_time'
	,'value' => 180
	,'xtype' => 'textfield'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

$settings['clear_cache_on_comment_save']= $modx->newObject('modSystemSetting');
$settings['clear_cache_on_comment_save']->fromArray(array(
	'key' => 'tickets.clear_cache_on_comment_save'
	,'value' => 'false'
	,'xtype' => 'combo-boolean'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

$settings['private_ticket_page']= $modx->newObject('modSystemSetting');
$settings['private_ticket_page']->fromArray(array(
	'key' => 'tickets.private_ticket_page'
	,'value' => 0
	,'xtype' => 'textfield'
	,'namespace' => 'tickets'
	,'area' => 'Ticket'
),'',true,true);

return $settings;