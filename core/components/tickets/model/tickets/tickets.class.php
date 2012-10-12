<?php
/**
 * The base class for Tickets.
 *
 * @package tickets
 */
class Tickets {
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('tickets.core_path',$config,$this->modx->getOption('core_path').'components/tickets/');
		$assetsUrl = $this->modx->getOption('tickets.assets_url',$config,$this->modx->getOption('assets_url').'components/tickets/');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'

			,'connectorUrl' => $connectorUrl

			,'corePath' => $corePath
			,'modelPath' => $corePath.'model/'
			,'chunksPath' => $corePath.'elements/chunks/'
			,'templatesPath' => $corePath.'elements/templates/'
			,'chunkSuffix' => '.chunk.tpl'
			,'snippetsPath' => $corePath.'elements/snippets/'
			,'processorsPath' => $corePath.'processors/'

			,'tplFormCreate' => 'tpl.Tickets.form.create'
			,'tplFormUpdate' => 'tpl.Tickets.form.update'
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


	/**
	 * Shorthand for the call of processor
	 *
	 * @access public
	 * @param string $action Path to processor
	 * @param array $data Data to be transmitted to the processor
	 */
	public function runProcessor($action = '', $data = array()) {
		if (empty($action)) {return false;}

		return $this->modx->runProcessor($action, $data, array('processors_path' => $this->config['processorsPath']));

	}


	public function getTicketForm($id = 0) {
		if (!empty($id)) {
			$response = $this->modx->runProcessor('ticket/get', array('id' => 1));
			if ($response->isError()) {
				return $response->getMessage();
			}
			$tmp = json_decode($response->response, 1);
			$arr = $tmp['ticket'];
			//$arr['sections'] = $this->getSections();
			return $this->modx->getChunk($this->config['tplFormUpdate'], $arr);
		}
		else {
			$arr = array(
				//'sections' => $this->getSections()
			);
			return $this->modx->getChunk($this->config['tplFormCreate'], $arr);
		}
	}


	public function saveTicket($data = array()) {
		return true;
	}

	public function previewTicket($data = array()) {
		return true;
	}

}