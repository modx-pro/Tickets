<?php
/**
 * The comments manager controller for Tickets.
 *
 * @package tickets
 */
class TicketsHomeManagerController extends TicketsMainController {
	public function process(array $scriptProperties = array()) {}

	public function getPageTitle() { return $this->modx->lexicon('tickets'); }

	public function loadCustomCssJs() {
		$this->modx->regClientStartupScript($this->Tickets->config['jsUrl'].'mgr/comment/comments.grid.js');
		$this->modx->regClientStartupScript($this->Tickets->config['jsUrl'].'mgr/thread/thread.grid.js');
		$this->modx->regClientStartupScript($this->Tickets->config['jsUrl'].'mgr/thread/thread.panel.js');
		$this->modx->regClientStartupScript($this->Tickets->config['jsUrl'].'mgr/home.js');
	}

	public function getTemplateFile() {
		return $this->Tickets->config['templatesPath'].'home.tpl';
	}
}