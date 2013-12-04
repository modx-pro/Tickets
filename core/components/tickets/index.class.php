<?php
/**
 * The main manager controller for Tickets.
 *
 * @package tickets
 */

require_once dirname(__FILE__) . '/model/tickets/tickets.class.php';

abstract class TicketsMainController extends modExtraManagerController {
	/** @var Tickets $tickets */
	public $Tickets;

	public function initialize() {
		$this->Tickets = new Tickets($this->modx);

		$this->addCSS($this->Tickets->config['cssUrl'].'mgr/tickets.css');
		$this->addJavascript($this->Tickets->config['jsUrl'].'mgr/tickets.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			Tickets.config = '.$this->modx->toJSON($this->Tickets->config).';
			Tickets.config.connector_url = "'.$this->Tickets->config['connectorUrl'].'";
		});
		</script>');
		
		parent::initialize();
	}

	public function getLanguageTopics() {
		return array('tickets:default');
	}

	public function checkPermissions() { return true;}
}

class IndexManagerController extends TicketsMainController {
	public static function getDefaultController() { return 'home'; }
}