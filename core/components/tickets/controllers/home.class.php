<?php

if (!class_exists('TicketsMainController')) {
	require_once dirname(dirname(__FILE__)) . '/index.class.php';
}

class TicketsHomeManagerController extends TicketsMainController {


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('tickets');
	}


	/**
	 *
	 */
	public function loadCustomCssJs() {
		$this->Tickets->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			'css' => true,
			'threads' => true,
			'comments' => true,
		));
		$this->addLastJavascript($this->Tickets->config['jsUrl'] . 'mgr/home.js');
		$this->addHtml('
		<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({xtype: "tickets-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->Tickets->config['templatesPath'] . 'home.tpl';
	}

}