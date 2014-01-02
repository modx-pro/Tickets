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
		$this->addJavascript($this->Tickets->config['jsUrl'].'mgr/comment/comments.grid.js');
		$this->addJavascript($this->Tickets->config['jsUrl'].'mgr/thread/thread.grid.js');
		$this->addJavascript($this->Tickets->config['jsUrl'].'mgr/thread/thread.panel.js');
		$this->addLastJavascript($this->Tickets->config['jsUrl'].'mgr/misc/utils.js');
		$this->addLastJavascript($this->Tickets->config['jsUrl'].'mgr/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({xtype: "tickets-page-home"});
		});
		</script>');


	}

	public function getTemplateFile() {
		return $this->Tickets->config['templatesPath'].'home.tpl';
	}
}