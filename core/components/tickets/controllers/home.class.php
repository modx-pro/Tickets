<?php

class TicketsHomeManagerController extends modExtraManagerController {

	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('tickets:default');
	}


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
		/** @var Tickets $Tickets */
		$Tickets = $this->modx->getService('Tickets');

		$Tickets->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			'css' => true,
			'threads' => true,
			'comments' => true,
			'tickets' => true,
			'authors' => true,
		));
		$this->addLastJavascript($Tickets->config['jsUrl'] . 'mgr/home.js');
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
		/** @var Tickets $Tickets */
		$Tickets = $this->modx->getService('Tickets');

		return $Tickets->config['templatesPath'] . 'home.tpl';
	}

}