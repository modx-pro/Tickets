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
			,'tplSectionRow' => 'tpl.Tickets.form.section.row'
			,'tplPreview' => 'tpl.Tickets.form.preview'
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
	 * @return mixed The result of the processor
	 */
	public function runProcessor($action = '', $data = array()) {
		if (empty($action)) {return false;}

		return $this->modx->runProcessor($action, $data, array('processors_path' => $this->config['processorsPath']));

	}

	/**
	 * Returns sections, available for user
	 *
	 * @access public
	 * @param integer @id Id of current section for "selected" placeholder
	 * @return mixed Templated sections for ticket form
	 */
	public function getSections($id = 0) {
		$response = $this->runProcessor('web/section/getlist');
		if ($response->isError()) {
			//return $response->getMessage();
			return false;
		}
		$response = json_decode($response->response, 1);
		$res = '';
		foreach ($response['results'] as $v) {
			if ($id == $v['id']) {$v['selected'] = 'selected';} else {$v['selected'] = '';}
			$res .= $this->modx->getChunk($this->config['tplSectionRow'], $v);
		}

		return $res;
	}


	/**
	 * Returns form for create/update Ticket
	 *
	 * @access public
	 * @param integer $id Id an existing Ticket
	 * @return mixed Rendered form
	 */
	public function getTicketForm($tid = 0) {
		$enable_editor = $this->modx->getOption('tickets.enable_editor');
		$htmlBlock = 'enable_editor:'.$enable_editor.'';
		if ($enable_editor) {
			$this->modx->regClientStartupScript($this->config['jsUrl'].'web/editor/jquery.markitup.js');
			$this->modx->regClientCSS($this->config['jsUrl'].'web/editor/editor.css');
			$htmlBlock .= ',editor:{ticket:'.$this->modx->getOption('tickets.editor_config.ticket').'}';
		}

		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
			Tickets = new Object();
			Tickets.config = {'.$htmlBlock.'};
		</script>');

		$arr = array(
			'assetsUrl' => $this->config['assetsUrl']
			,'sections' => $this->getSections()
		);

		if (!empty($tid)) {
			$response = $this->modx->runProcessor('resource/get', array('id' => $tid));
			if ($response->isError()) {
				return $response->getMessage();
			}
			$object = $response->response['object'];
			if ($object['createdby'] != $this->modx->user->id) {
				return $this->modx->lexicon('ticket_err_wrong_user');
			}
			$arr = array_merge($arr,$object);
			return $this->modx->getChunk($this->config['tplFormUpdate'], $arr);
		}
		else {
			return $this->modx->getChunk($this->config['tplFormCreate'], $arr);
		}
	}


	/**
	 * Save ticket through processor and redirect to it
	 *
	 * @TODO Написать проверку необходимых полей ресурса и возврат формы с данными и ошибкой
	 *
	 * @access public
	 * @param array $data section, pagetitle,text, etc
	 * @return
	 */
	public function saveTicket($data = array()) {
		$data['class_key'] = 'Ticket';

		// Здесь будет проверка присланных данных

		if (!empty($data['tid'])) {
			$data['id'] = $data['tid'];
			$data['context_key'] = $this->modx->context->key;
			$response = $this->modx->runProcessor('resource/update', $data);
		}
		else {
			$response = $this->modx->runProcessor('resource/create', $data);
		}

		if ($response->isError()) {
			return $response->getMessage();
		}
		$id = $response->response['object']['id'];
		$this->modx->sendRedirect($this->modx->makeUrl($id,'','','full'));
	}


	/**
	 * Returns sanitized preview of Ticket
	 *
	 * @access public
	 * @param array $data section, pagetitle,text, etc
	 * @return mixed rendered preview of Ticket for frontend
	 */
	public function previewTicket($data = array()) {
		$error = 0;
		$message = null;
		foreach ($data as $k => $v) {
			if ($k == 'content') {
				if (!$data[$k] = $this->Jevix($v, 'Ticket')) {
					$error = 1;
					$message = $this->modx->lexicon('err_no_jevix');
					$this->modx->log(modX::LOG_LEVEL_ERROR, $message);
				}
			}
			else {
				$data[$v] = str_replace(array('[[',']]'), array('&#091;&#091;','&#093;&#093;'), $this->modx->sanitizeString($v));
			}
		}
		return array(
			'error' => $error
			,'message' => $message
			,'data' => $this->modx->getChunk($this->config['tplPreview'], $data)
		);
	}


	/**
	 * Sanitize any text through Jevix snippet
	 *
	 * @access public
	 * @param string $text Text for sanitization
	 * @return array Array with status and sanitized text or error message
	 */
	public function Jevix($text = null, $setName = null) {
		if (empty($text)) {return ' ';}
		if (!$snippet = $this->modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			return false;
		}
		$params = array();
		if ($setName) {
			$params = $snippet->getPropertySet($setName);
		}
		$text = preg_replace('/\{\{\{\{\(*.?\)\}\}\}\}/','',$text);
		$params['input'] =  str_replace(array('[[',']]'), array('{{{{{','}}}}}'), $text);

		$filtered = $this->modx->runSnippet('Jevix', $params);

		$filtered = str_replace(array('{{{{{','}}}}}'), array('&#091;&#091;','&#093;&#093;'), $filtered);
		return $filtered;
	}

}