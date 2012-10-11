<?php
/**
 * The main manager controller for Tickets.
 *
 * @package tickets
 */

require_once dirname(__FILE__) . '/model/tickets/tickets.class.php';

abstract class TicketsMainController extends modExtraManagerController {
	/** @var Tickets $tickets */
	public $tickets;

	public function initialize() {
		$this->Tickets = new Tickets($this->modx);

		$this->modx->regClientCSS($this->Tickets->config['cssUrl'].'mgr.css');
		$this->modx->regClientStartupScript($this->Tickets->config['jsUrl'].'mgr/tickets.js');
		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
		Ext.onReady(function() {
			Tickets.config = '.$this->modx->toJSON($this->Tickets->config).';
			Tickets.config.connector_url = "'.$this->Tickets->config['connectorUrl'].'";
		});
		</script>');
		
		return parent::initialize();
	}

	public function getLanguageTopics() {
		return array('tickets:default');
	}

	public function checkPermissions() { return true;}
}

class IndexManagerController extends TicketsMainController {
	public static function getDefaultController() { return 'home'; }
}