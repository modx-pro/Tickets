<?php
/**
 * The base class for Tickets.
 *
 * @package tickets
 */
class Tickets {
	private $quipLoaded = false;

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
	public function initialize($ctx = 'mgr') {
		switch ($ctx) {
			case 'mgr':
				if (!$this->modx->loadClass('tickets.request.TicketsControllerRequest',$this->config['modelPath'],true,true)) {
					return 'Could not load controller request handler.';
				}
				$this->request = new TicketsControllerRequest($this);
				return $this->request->handleRequest();
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
	 * @param integer $id Id of an existing Ticket
	 * @param mixed $mode What form is need to load, for create or update?
	 * @return mixed Rendered form
	 */
	public function getTicketForm($data = array()) {
		$enable_editor = $this->modx->getOption('tickets.enable_editor');
		$htmlBlock = 'enable_editor:'.$enable_editor.'';
		if ($enable_editor) {
			$this->modx->regClientStartupScript($this->config['jsUrl'].'web/editor/jquery.markitup.js');
			$this->modx->regClientCSS($this->config['jsUrl'].'web/editor/editor.css');
			$this->modx->regClientCSS($this->config['cssUrl'].'web/tickets.css');
			$htmlBlock .= ',editor:{ticket:'.$this->modx->getOption('tickets.editor_config.ticket').'}';
		}

		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
			Tickets = new Object();
			Tickets.config = {'.$htmlBlock.'};
		</script>');

		$arr = array(
			'assetsUrl' => $this->config['assetsUrl']
		);
		$tpl = $this->config['tplFormCreate'];

		if (!empty($data)) {
			if (!empty($data['tid'])) {
				$response = $this->modx->runProcessor('resource/get', array('id' => $data['tid']));
				if ($response->isError()) {
					return $response->getMessage();
				}
				$object = $response->response['object'];
				if ($object['createdby'] != $this->modx->user->id) {
					return $this->modx->lexicon('ticket_err_wrong_user');
				}
				unset($data['parent']);
				$data = array_merge($object,$data);

				$tpl = $this->config['tplFormUpdate'];
			}
		}
		$parent = !empty($data['parent']) ? $data['parent'] : 0;
		$arr['sections'] = $this->getSections($parent);
		$arr = array_merge($arr,$data);
		return $this->modx->getChunk($tpl, $arr);
	}


	/**
	 * Save ticket through processor and redirect to it
	 *
	 * @access public
	 * @param array $data section, pagetitle,text, etc
	 * @return
	 */
	public function saveTicket($data = array()) {
		$data['class_key'] = 'Ticket';
		if (!empty($data['tid'])) {
			$data['id'] = $data['tid'];
			$data['context_key'] = $this->modx->context->key;
			$response = $this->modx->runProcessor('resource/update', $data);
		}
		else {
			$response = $this->modx->runProcessor('resource/create', $data);
		}
		if ($response->isError()) {
			if (count($response->errors)) {
				foreach ($response->errors as $v) {
					$data['error.'.$v->field] = $v->message;
				}
				$data['error'] = $this->modx->lexicon('ticket_err_form');
			}
			else {
				$data['error'] = $response->getMessage();
			}
			return $this->getTicketForm($data);
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
	public function Jevix($text = null, $setName = 'Ticket') {
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

		$snippet->setCacheable(false);
		$filtered = $snippet->process($params);

		$filtered = str_replace(array('{{{{{','}}}}}','`'), array('&#91;&#91;','&#93;&#93;','&#96;'), $filtered);
		return $filtered;
	}


	function getTicketComments($id = 0) {
		if (!$this->quipLoaded) {
			$path = $this->modx->getOption('quip.core_path','',$this->modx->getOption('core_path').'components/quip/model/');
			$this->modx->addPackage('quip',$path);
			$this->quipLoaded = true;
		}

		return $this->modx->getCount('quipComment', array('resource' => $id, 'deleted' => 0, 'approved' => 1));
	}

	function getLatestTickets($data) {
		if (!empty($data['parents'])) {$data['parents'] = explode(',', $data['parents']);}
		$response = $this->runProcessor('web/ticket/getlist', $data);
		$result = json_decode($response->response, true);
		$output = '';
		$sections = array();
		if ($result['total']) {
			foreach ($result['results'] as $v) {
				$v['comments'] = $this->getTicketComments($v['id']);
				if (!array_key_exists($v['parent'], $sections)) {
					$q = $this->modx->newQuery('TicketsSection', array('id' => $v['parent']));
					$q->select('pagetitle');
					if ($q->prepare() && $q->stmt->execute()) {
						$sections[$v['parent']] = $q->stmt->fetch(PDO::FETCH_COLUMN);
					}
				}
				$v['sectiontitle'] = $sections[$v['parent']];
				if (empty($data['tpl'])) {
					$output .= '<pre>'.print_r($v,true).'</pre>';
				}
				else {
					$output .= $this->modx->getChunk($data['tpl'], $v);
				}
			}
		}
		return $output;
	}

}