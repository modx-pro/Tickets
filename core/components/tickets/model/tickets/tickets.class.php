<?php
/**
 * The base class for Tickets.
 *
 * @package tickets
 */
require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class Tickets {
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('tickets.core_path',$config,$this->modx->getOption('core_path').'components/tickets/');
		$assetsUrl = $this->modx->getOption('tickets.assets_url',$config,$this->modx->getOption('assets_url').'components/tickets/');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl.'css/',
			'jsUrl' => $assetsUrl.'js/',
			'imagesUrl' => $assetsUrl.'images/',

			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath.'model/',
			'chunksPath' => $corePath.'elements/chunks/',
			'templatesPath' => $corePath.'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath.'elements/snippets/',
			'processorsPath' => $corePath.'processors/',
		),$config);

		$this->modx->addPackage('tickets',$this->config['modelPath']);
		$this->modx->lexicon->load('tickets:default');
	}

	/**
	 * Initializes Tickets into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
	 */
	public function initialize($ctx = 'web') {
		switch ($ctx) {
			case 'mgr':
				if (!$this->modx->loadClass('tickets.request.TicketsControllerRequest',$this->config['modelPath'],true,true)) {
					return 'Could not load controller request handler.';
				}
				$this->request = new TicketsControllerRequest($this);
				return $this->request->handleRequest();
			break;
			case 'connector':
				if (!$this->modx->loadClass('tickets.request.TicketsConnectorRequest',$this->config['modelPath'],true,true)) {
					return 'Could not load connector request handler.';
				}
				$this->request = new TicketsConnectorRequest($this);
				return $this->request->handle();
			break;
			default:
				/* if you wanted to do any generic frontend stuff here.
				 * For example, if you have a lot of snippets but common code
				 * in them all at the beginning, you could put it here and just
				 * call $tickets->initialize($modx->context->get('key'));
				 * which would run this.
				 */
			break;
		}
	}

}

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */
class TicketsSectionCreateProcessor extends modResourceCreateProcessor {}

/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */
class TicketsSectionUpdateProcessor extends modResourceUpdateProcessor {}

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the Ticket type
 *
 * @package tickets
 */
class TicketCreateProcessor extends modResourceCreateProcessor {}

/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the Ticket type
 *
 * @package tickets
 */
class TicketUpdateProcessor extends modResourceUpdateProcessor {}