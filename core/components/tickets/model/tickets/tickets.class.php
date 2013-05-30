<?php
/**
 * The base class for Tickets.
 *
 * @package tickets
 */
class Tickets {
	/* @var modX $modx */
	public $modx;
	/* @var pdoTools $pdoTools */
	public $pdoTools;
	public $initialized = array();
	private $prepareCommentCustom = null;
	private $last_view = 0;

	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('tickets.core_path',$config,$this->modx->getOption('core_path').'components/tickets/');
		$assetsUrl = $this->modx->getOption('tickets.assets_url',$config,$this->modx->getOption('assets_url').'components/tickets/');
		$actionUrl = $this->modx->getOption('tickets.action_url', $config, $assetsUrl.'action.php');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'imagesUrl' => $assetsUrl.'images/'

			,'connectorUrl' => $connectorUrl
			,'actionUrl' => $actionUrl

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
			,'tplCommentAuth' => 'tpl.Tickets.comment.one.auth'
			,'tplCommentGuest' => 'tpl.Tickets.comment.one.guest'
			,'tplComments' => 'tpl.Tickets.comment.wrapper'
			,'tplLoginToComment' => 'tpl.Tickets.comment.login'
			,'tplPreview' => 'tpl.Tickets.form.preview'
			,'tplCommentEmailOwner' => 'tpl.Tickets.comment.email.owner'
			,'tplCommentEmailReply' => 'tpl.Tickets.comment.email.reply'

			,'fastMode' => true
			,'dateFormat' => 'd F Y, H:i'
			,'dateNow' => 10
			,'dateDay' => 'day H:i'
			,'dateMinutes' => 59
			,'dateHours' => 10
			,'charset' => $this->modx->getOption('modx_charset')
			,'snippetPrepareComment' => $this->modx->getOption('tickets.snippet_prepare_comment')
			,'commentEditTime' => $this->modx->getOption('tickets.comment_edit_time', null, 180)
		),$config);

		$this->modx->addPackage('tickets',$this->config['modelPath']);
		$this->modx->lexicon->load('tickets:default');

		if ($name = $this->config['snippetPrepareComment']) {
			if ($snippet = $this->modx->getObject('modSnippet', array('name' => $name))) {
				$this->prepareCommentCustom = $snippet->get('content');
			}
		}
	}


	/**
	 * Initializes component into different contexts.
	 *
	 * @access public
	 * @param string $ctx The context to load. Defaults to web.
	 */
	public function initialize($ctx = 'web', $scriptProperties = array()) {
		$this->config = array_merge($this->config, $scriptProperties);
		$this->config['ctx'] = $ctx;
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		switch ($ctx) {
			case 'mgr': break;
			default:
				if (!MODX_API_MODE) {
					$config = $this->makePlaceholders($this->config);

					if ($css = $this->modx->getOption('tickets.frontend_css')) {
						$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
					}
					if ($js = $this->modx->getOption('tickets.frontend_js')) {
						$enable_editor = $this->modx->getOption('tickets.enable_editor');
						$formBefore = !empty($this->config['formBefore']) ? 1 : 0;
						$editorConfig = 'enable_editor: '.$enable_editor.'';
						if ($enable_editor) {
							$this->modx->regClientScript($this->config['jsUrl'].'web/editor/jquery.markitup.js');
							$this->modx->regClientCSS($this->config['jsUrl'].'web/editor/editor.css');
							$editorConfig .= '
							,editor: {
								ticket: '.$this->modx->getOption('tickets.editor_config.ticket').'
								,comment: '.$this->modx->getOption('tickets.editor_config.comment').'
							}';
						}

						$this->modx->regClientStartupScript(str_replace('					', '', '
						<script type="text/javascript">
						TicketsConfig = {
							jsUrl: "'.$this->config['jsUrl'].'web/"
							,cssUrl: "'.$this->config['cssUrl'].'web/"
							,actionUrl: "'.$this->config['actionUrl'].'"
							,formBefore: '.$formBefore.'
							,close_all_message: "'.$this->modx->lexicon('tickets_message_close_all').'"
							,tpanel: '.($this->modx->user->isAuthenticated() ? 1 : 0).'
							,'.$editorConfig.'
						};
						if(typeof jQuery == "undefined") {
							document.write("<script src=\""+TicketsConfig.jsUrl+"lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
						}
						</script>
						'), true);
						$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
					}
				}

				$this->initialized[$ctx] = true;
				break;
		}
		return true;
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
		$this->initialize($this->modx->context->key);

		$tpl = $this->config['tplFormCreate'];

		if (!empty($data)) {
			if (!empty($data['tid'])) {
				$response = $this->modx->runProcessor('resource/get', array('id' => $data['tid']));
				if ($response->isError()) {
					return $response->getMessage();
				}
				$object = $response->response['object'];
				if ($object['class_key'] != 'Ticket' || ($object['createdby'] != $this->modx->user->id  && !$this->modx->hasPermission('edit_document'))) {
					return $this->modx->lexicon('ticket_err_wrong_user');
				}
				unset($data['parent']);
				$data = array_merge($object,$data);
				foreach ($data as $k => $v) {
					if (is_string($v)) {
						$data[$k] = html_entity_decode($v, ENT_QUOTES, $this->config['charset']);
						$data[$k] = str_replace(array('[^','^]','[',']'), array('&#91;^','^&#93;','{{{{{','}}}}}'), $data[$k]);
						$data[$k] = htmlentities($data[$k], ENT_QUOTES, $this->config['charset']);
					}
				}
				$tpl = $this->config['tplFormUpdate'];
			}
		}
		$parent = !empty($data['parent']) ? $data['parent'] : 0;
		$arr = array_merge(array(
				'assetsUrl' => $this->config['assetsUrl']
				,'sections' => $this->getSections($parent)
			)
			,$data
		);

		return $this->modx->getChunk($tpl, $arr);
	}


	/**
	 * Returns form for create/update Comment
	 *
	 * @access public
	 * @return mixed Rendered form
	 */
	public function getCommentForm() {
		$this->initialize($this->modx->context->key);

		if (!$this->modx->user->isAuthenticated()) {
			return $this->modx->getChunk($this->config['tplLoginToComment']);
		}
		else {
			$arr = array(
				'assetsUrl' => $this->config['assetsUrl']
				,'thread' => $this->config['thread']
			);
			return $this->modx->getChunk($this->config['tplCommentForm'], $arr);
		}
	}


	/**
	 * Save ticket through processor and redirect to it
	 *
	 * @access public
	 * @param array $data section, pagetitle,text, etc
	 * @return
	 */
	public function saveTicket($data = array()) {
		foreach ($data as $k => $v) {
			if ($k !== 'content') {
				$data[$k] = $this->sanitizeString($v);
			}
		}
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
			$message = $response->getMessage();
			if (is_array($message) && !empty($message['message'])) {
				$data['error'] = $message['message'];
			}
			else if (count($response->errors)) {
				foreach ($response->errors as $v) {
					$data['error.'.$v->field] = $v->message;
				}
				$data['error'] = $this->modx->lexicon('ticket_err_form');
			}
			else {
				$data['error'] = $message;
			}
			return $this->getTicketForm($data);
		}
		$id = $response->response['object']['id'];

		if ($data['published'] != 1) {$id = $data['parent'];}
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
				$data[$k] = $this->sanitizeString($v);
			}
		}
		return array(
			'error' => $error
			,'message' => $message
			,'data' => $this->modx->getChunk($this->config['tplPreview'], $data)
		);
	}


	/**
	 * Returns sanitized preview of Comment
	 *
	 * @access public
	 * @param array $data section, pagetitle,comment, etc
	 * @return mixed rendered preview of Comment for frontend
	 */
	public function previewComment($data = array()) {
		$comment = $this->modx->newObject('TicketComment', array(
			'text' => $this->Jevix($data['text'], 'Comment')
			,'fullname' => $this->modx->user->Profile->get('fullname')
			,'email' => $this->modx->user->Profile->get('email')
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->get('id')
			,'resource' => $this->config['resource']
		));
		$comment->set('id', '0');
		$comment=$comment->toArray();
		return $this->templateNode(array_merge(array('mode'=>'preview'),$comment), $this->config['tplCommentGuest']);
	}


	/**
	 * Returns sanitized preview of Comment
	 *
	 * @access public
	 * @param array $data section, pagetitle,comment, etc
	 * @return mixed rendered preview of Comment for frontend
	 */
	public function saveComment($data = array()) {
		$data['raw'] = $data['text'];
		$data['text'] = $this->Jevix($data['text'], 'Comment');

		$data['published'] = !empty($this->config['autoPublish']);
		if (!empty($data['id'])) {
			$response = $this->runProcessor('web/comment/update', $data);
		}
		else {
			$response = $this->runProcessor('web/comment/create', $data);
		}
		if ($response->isError()) {
			$arr = array(
				'message' => $response->getMessage()
				,'error' => 1
			);
		}
		else {
			$comment = $response->response['object'];
			$comment['mode'] = 'save';
			$comment['new_parent'] = $data['parent'];
			$comment['resource'] = $this->config['resource'];
			if ($profile = $this->modx->getObject('modUserProfile', array('internalKey' => $comment['createdby']))) {
				$profile = $profile->toArray();
				$comment = array_merge($profile, $comment);
			}

			if ($comment['published']) {
				$arr = array(
					'error' => 0
					,'message' => ''
					,'data' => $this->templateNode($comment, $this->config['tplCommentAuth'])
					//,'count' => $this->getTicketComments($this->config['thread'])
				);
				$this->modx->cacheManager->delete('tickets/latest.comments');
				$this->modx->cacheManager->delete('tickets/latest.tickets');
			}
			else {
				$arr = array(
					'error' => 0
					,'message' => $this->modx->lexicon('ticket_unpublished_comment')
					,'data' => ''
					//,'count' => $this->getTicketComments($this->config['thread'])
				);
			}

			if (empty($data['id'])) {
				$this->sendCommentMails($this->prepareComment($comment));
			}
		}
		return $arr;
	}


	/**
	 * Returns Comment for edit by its author
	 *
	 * @access public
	 * @param integer $id Id of an comment
	 * @return array|boolean Json encoded array with comment text and time to edit or false
	 */
	public function getComment($id) {
		$response = $this->runProcessor('web/comment/get', array('id' => $id));
		if ($response->isError()) {
			$arr = array(
				'message' => $response->getMessage()
				,'error' => 1
			);
		}
		else {
			$comment = $response->response['object'];
			$time = time() - strtotime($comment['createdon']);
			$time_limit = $this->config['commentEditTime'];
			if ($comment['createdby'] != $this->modx->user->id) {
				$arr = array(
					'message' => $this->modx->lexicon('ticket_comment_err_wrong_user')
					,'error' => 1
				);
			}
			else if ($this->modx->getCount('TicketComment', array('parent' => $comment['id']))) {
				$arr = array(
					'message' => $this->modx->lexicon('ticket_comment_err_has_replies')
					,'error' => 1
				);
			}
			else if ($time >= $time_limit) {
				$arr = array(
					'message' => $this->modx->lexicon('ticket_comment_err_no_time')
					,'error' => 1
				);
			}
			else {
				$arr = array(
					'error' => 0
					,'data' => $comment['raw']
					,'time' => $time_limit - $time
				);
			}
		}

		return $arr;
	}


	public function getNewComments($name) {
		$arr = array(
			'error' => 0
			,'data' => array()
		);
		if (!$this->modx->user->isAuthenticated()) {
			$arr['error'] = 1;
			$arr['message'] = $this->modx->lexicon('access_denied');
		}
		else if ($thread = $this->modx->getObject('TicketThread', array('name' => $name))) {
			if ($view = $this->modx->getObject('TicketView', array('uid' => $this->modx->user->id, 'parent' => $thread->get('resource')))) {

				$date = $view->get('timestamp');
				$q = $this->modx->newQuery('TicketComment');
				$q->leftJoin('modUser', 'User', '`User`.`id` = `TicketComment`.`createdby`');
				$q->leftJoin('modUserProfile', 'Profile', '`Profile`.`internalKey` = `TicketComment`.`createdby`');
				$q->where(array(
					'`TicketComment`.`published`' => 1
					,'`TicketComment`.`thread`' => $thread->id
					,'`TicketComment`.`createdby`:!=' => $this->modx->user->id
				));
				$q->andCondition(array(
					'`TicketComment`.`createdon`:>' => $date
					,'OR:`TicketComment`.`editedon`:>' => $date
				));

				$q->sortby('`TicketComment`.`id`', 'ASC');
				$q->select($this->modx->getSelectColumns('TicketComment', 'TicketComment'));
				$q->select($this->modx->getSelectColumns('modUser', 'User', '', array('username')));
				$q->select($this->modx->getSelectColumns('modUserProfile', 'Profile', '', array('id'), true));

				$comments = array();
				if ($q->prepare() && $q->stmt->execute()) {
					while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
						$row['resource'] = $thread->resource;
						$row['new_parent'] = $row['parent'];
						$comments[$row['id']] = $this->templateNode($row);
					}
					$arr['data'] = $comments;
					$this->logView($thread->resource);
				}
			}
		}

		return $arr;
	}

	/**
	 * Sanitize any text through Jevix snippet
	 *
	 * @access public
	 * @param string $text Text for sanitization
	 * @return array Array with status and sanitized text or error message
	 */
	public function Jevix($text = null, $setName = 'Ticket', $replaceTags = true) {
		if (empty($text)) {return ' ';}
		if (!$snippet = $this->modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			return false;
		}
		// Loading parser if needed - it is for mgr context
		if (!is_object($this->modx->parser)) {
			$this->modx->getParser();
		}

		$params = array();
		if ($setName) {
			$params = $snippet->getPropertySet($setName);
		}
		$text = preg_replace('/\{\{\{\{\(*.?\)\}\}\}\}/','',$text);
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		$params['input'] =  str_replace(array('[',']'), array('{{{{{','}}}}}'), $text);

		$snippet->setCacheable(false);
		$filtered = $snippet->process($params);

		if ($replaceTags) {
			$filtered = str_replace(array('{{{{{','}}}}}','`'), array('&#91;','&#93;','&#96;'), $filtered);
		}
		else {
			$filtered = str_replace(array('{{{{{','}}}}}'), array('[',']'), $filtered);
		}

		return $filtered;
	}


	/**
	 * Returns comments count for ticket
	 *
	 * @access public
	 * @param int|string $id Id of ticket
	 * @return integer Number of comments
	 */
	/*
	public function getTicketComments($thread = '') {
		$count = 0;
		if (is_numeric($thread)) {$thread = 'resource-'.$thread;}

		$q = $this->modx->newQuery('TicketComment');
		$q->leftJoin('TicketThread', 'TicketThread','TicketThread.id = TicketComment.thread');
		$q->where(array('TicketThread.name' => $thread, 'published' => 1));
		$q->select('COUNT(`id`)');
		if ($q->prepare() && $q->stmt->execute()) {
			$count = $q->stmt->fetch(PDO::FETCH_COLUMN);
		}
		return $count;
	}
	*/


	/**
	 * Sanitize MODX tags
	 *
	 * @access public
	 * @param string $string Any string with MODX tags
	 * @return string String with html entities
	 */
	public function sanitizeString($string = '') {
		$string = htmlentities(trim($string), ENT_QUOTES, "UTF-8");
		$arr1 = array('[',']','`');
		$arr2 = array('&#091;','&#093;','&#096;');

		return str_replace($arr1, $arr2, $string);
	}


	/*
	 * Returns all comments of the resource with given id
	 * */
	/*
	public function getCommentThread($thread = '') {
		$thread = trim((string) $thread);
		if (empty($thread)) {
			$thread = 'resource-' . $this->modx->resource->id;
		}
		else if (is_numeric($thread)) {
			$thread = 'resource-'.$thread;
		}

		$data = array_merge($this->config, array(
			'thread' => $thread
		));
		$response = $this->runProcessor('web/thread/get', $data);
		if (is_array($response->response) && isset($response->response['message'])) {
			return $response->response['message'];
		}
		$data = json_decode($response->response,true);
		$comments = '';
		if ($data['total'] > 0) {
			$tpl = $this->modx->user->isAuthenticated() ? $this->config['tplCommentAuth'] : $this->config['tplCommentGuest'];
			foreach ($data['results'] as $node) {
				$comments .= $this->templateNode($node, $tpl);
			}
		}

		$arr = array(
			'total' => $data['total']
			,'comments' => $comments
		);

		$commentsThread = $this->modx->getChunk($this->config['tplComments'], $arr);
		$commentForm = $this->getCommentForm();

		return $commentsThread . $commentForm;
	}


	/*
	 * Recursive template of the comment node
	 * */
	public function templateNode($node = array(), $tpl = null) {
		$children = null;
		if (!empty($node['children'])) {
			foreach ($node['children'] as $v) {
				$children .= $this->templateNode($v, $tpl);
			}
		}

		// Checking comment novelty
		if (isset($node['resource']) && $this->last_view === 0) {
			if ($view = $this->modx->getObject('TicketView', array('parent' => $node['resource'], 'uid' => $this->modx->user->id))) {
				$this->last_view = strtotime($view->get('timestamp'));
			}
			else {
				$this->last_view = -1;
			}
		}

		// Processing comment and selecting needed template
		$node = $this->prepareComment($node);
		if (empty($tpl)) {
			$tpl = $this->modx->user->isAuthenticated() ? $this->config['tplCommentAuth'] : $this->config['tplCommentGuest'];
		}
		if ($node['deleted']) {
			$tpl = $this->config['tplCommentDeleted'];
		}

		if (!empty($children)) {
			$node['children'] = $children;
			$node['comment_edit_link'] = false;
		}
		else if ($node['createdby'] == $this->modx->user->id && (time() - strtotime($node['createdon']) <= $this->config['commentEditTime'])) {
			$node['comment_edit_link'] = true;
		}
		$node['comment_was_edited'] = $node['editedby'] && $node['editedon'];
		$node['comment_new'] = $node['createdby'] != $this->modx->user->id && $this->last_view > 0 && strtotime($node['createdon']) > $this->last_view;

		return $this->getChunk($tpl, $node, $this->config['fastMode']);
	}


	/*
	 * Render of the comment
	 * */
	public function prepareComment($data = array()) {
		if (!empty($this->prepareCommentCustom)) {
			return eval($this->prepareCommentCustom);
		}
		else {
			$data['avatar'] = $this->config['gravatarUrl'] . md5(strtolower($data['email'])) .'?s=' . $this->config['gravatarSize'] . '&d=' . $this->config['gravatarIcon'];
			if (!empty($data['resource'])) {
				$data['url'] = $this->modx->makeUrl($data['resource'], '', '', 'full');
			}

			$data['date_ago'] = $this->dateFormat($data['createdon']);
			return $data;
		}
	}


	/* Method for transform array to placeholders
	 *
	 * @var array $array With keys and values
	 * @return array $array Two nested arrays With placeholders and values
	 * */
	public function makePlaceholders(array $array = array(), $prefix = '') {
		$result = array(
			'pl' => array()
			,'vl' => array()
		);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$result = array_merge_recursive($result, $this->makePlaceholders($v, $k.'.'));
			}
			else {
				$result['pl'][$prefix.$k] = '[[+'.$prefix.$k.']]';
				$result['vl'][$prefix.$k] = $v;
			}
		}
		return $result;
	}


	/*
	 *Email notifications about new comment
	 * */
	public function sendCommentMails($comment = array()) {
		$owner = $reply = null;
		$resource = $parent = array();
		$q = $this->modx->newQuery('TicketThread');
		$q->leftJoin('modResource', 'modResource','TicketThread.resource = modResource.id');
		$q->leftJoin('modUserProfile', 'modUserProfile','modResource.createdby = modUserProfile.internalKey');
		$q->select('modUserProfile.email,modResource.id as resource,modResource.pagetitle,modResource.createdby as author');
		$q->where(array('TicketThread.id' => $comment['thread']));
		if ($q->prepare() && $q->stmt->execute()) {
			$res = $q->stmt->fetch(PDO::FETCH_ASSOC);

			if (empty($res)) {return;}

			$resource = array(
				'resource' => $res['resource']
				,'pagetitle' => $res['pagetitle']
				,'author' => $res['author']
			);
			$owner = $res['email'];
		}

		if (!empty($resource)) {
			$comment = array_merge($comment, $resource);
		}

		if ($comment['parent']) {
			$q = $this->modx->newQuery('TicketComment');
			$q->leftJoin('modUserProfile', 'modUserProfile','TicketComment.createdby = modUserProfile.internalKey');
			$q->select('modUserProfile.email,TicketComment.text');
			$q->where(array('TicketComment.id' => $comment['parent'], 'TicketComment.createdby:!=' => $comment['createdby']));
			if ($q->prepare() && $q->stmt->execute()) {
				$res = $q->stmt->fetch(PDO::FETCH_ASSOC);
				$reply = $res['email'];
				$parent = array(
					'parent_text' => $res['text']
				);
			}
		}

		if (!empty($parent)) {
			$comment = array_merge($comment, $parent);
		}

		$owner_email = array(
			'subject' => $this->modx->lexicon('ticket_comment_email_owner', $resource)
			,'message' => $this->modx->getChunk($this->config['tplCommentEmailOwner'], $this->prepareComment($comment))
			,'to' => $owner
		);

		$reply_email = array(
			'subject' => $this->modx->lexicon('ticket_comment_email_reply', $resource)
			,'message' => $this->modx->getChunk($this->config['tplCommentEmailReply'], $this->prepareComment($comment))
			,'to' => $reply
		);

		if (!empty($reply)) {
			$this->sendMail($reply_email);
		}
		if (!empty($owner) && $owner != $reply && $comment['createdby'] != $resource['author']) {
			$this->sendMail($owner_email);
		}
	}


	/*
	 * Just sends emails
	 * */
	public function sendMail($data = array()) {
		if (empty($data['subject']) || empty($data['to']) || empty($data['message'])) {
			return false;
		}
		$this->modx->getService('mail', 'mail.modPHPMailer');
		$this->modx->mail->set(modMail::MAIL_SUBJECT, $data['subject']);
		$this->modx->mail->set(modMail::MAIL_BODY, $data['message']);
		$this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender', $this->config, $this->modx->getOption('emailsender')));
		$this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('mailfrom', $this->config, $this->modx->getOption('site_name')));
		$this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('mailfrom', $this->config, $this->modx->getOption('site_name')));
		$this->modx->mail->address('to', $data['to']);
		$this->modx->mail->setHTML(true);
		if (!$this->modx->mail->send()) {
			$this->modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$this->modx->mail->mailer->ErrorInfo);
		}
		$this->modx->mail->reset();
		return true;
	}

	/* Loads an instance of pdoTools for chunks processing
	 *
	 * */
	public function loadPdoTools() {
		if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
			$this->pdoTools = $this->modx->getService('pdofetch','pdoFetch', MODX_CORE_PATH.'components/pdotools/model/pdotools/', array('nestedChunkPrefix' => 'tickets_'));
		}
	}


	/**
	 * Process and return the output from a Chunk by name.
	 *
	 * @param string $chunkName The name of the chunk.
	 * @param array $properties An associative array of properties to process
	 * the Chunk with, treated as placeholders within the scope of the Element.
	 * @param boolean $fastMode If true, all MODX tags in chunk will be processed.
	 * @return string The processed output of the Chunk.
	 */
	public function getChunk($name, array $properties = array(), $fastMode = false) {
		$this->loadPdoTools();
		if (!$this->modx->parser) {
			$this->modx->getParser();
		}
		return $this->pdoTools->getChunk($name, $properties, $fastMode);
	}

	/*
	 * Formats date to "10 minutes ago" or "Yesterday in 22:10"
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/7a6039b21c326acf03c956772325e1398801c5fe/engine/modules/viewer/plugs/function.date_format.php
	 * @param $date $time Timestamp to format
	 * */
	public function dateFormat($date, $dateFormat = null) {
		$date = preg_match('/^\d+$/',$date) ?  $date : strtotime($date);
		$dateFormat = !empty($dateFormat) ? $dateFormat : $this->config['dateFormat'];
		$current = time();
		$delta = $current - $date;

		if ($this->config['dateNow']) {
			if ($delta < $this->config['dateNow']) {return $this->modx->lexicon('ticket_date_now');}
		}

		if ($this->config['dateMinutes']) {
			$minutes = round(($delta) / 60);
			if ($minutes < $this->config['dateMinutes']) {
				if ($minutes > 0) {
					return $this->declension($minutes, $this->modx->lexicon('ticket_date_minutes_back',array('minutes' => $minutes)));
				}
				else {
					return $this->modx->lexicon('ticket_date_minutes_back_less');
				}
			}
		}

		if ($this->config['dateHours']) {
			$hours = round(($delta) / 3600);
			if ($hours < $this->config['dateHours']) {
				if ($hours > 0) {
					return $this->declension($hours, $this->modx->lexicon('ticket_date_hours_back',array('hours' => $hours)));
				}
				else {
					return $this->modx->lexicon('ticket_date_hours_back_less');
				}
			}
		}

		if ($this->config['dateDay']) {
			switch(date('Y-m-d', $date)) {
				case date('Y-m-d'):
					$day = $this->modx->lexicon('ticket_date_today');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')) ):
					$day = $this->modx->lexicon('ticket_date_yesterday');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m')  , date('d')+1, date('Y')) ):
					$day = $this->modx->lexicon('ticket_date_tomorrow');
					break;
				default: $day = null;
			}
			if($day) {
				$format = str_replace("day",preg_replace("#(\w{1})#",'\\\${1}',$day),$this->config['dateDay']);
				return date($format,$date);
			}
		}

		$m = date("n", $date);
		$month_arr = $this->modx->fromJSON($this->modx->lexicon('ticket_date_months'));
		$month = $month_arr[$m - 1];

		$format = preg_replace("~(?<!\\\\)F~U", preg_replace('~(\w{1})~u','\\\${1}', $month), $dateFormat);

		return date($format ,$date);
	}


	/*
	 * Declension of words
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/eca10c0186c8174b774a2125d8af3760e1c34825/engine/modules/viewer/plugs/modifier.declension.php
	 *
	 * @param int $count
	 * @param string $forms
	 * @param string $language
	 * @return string
	 * */
	public function declension($count, $forms, $lang = null) {
		if (empty($lang)) {
			$lang = $this->modx->getOption('cultureKey',null,'en');
		}
		$forms = $this->modx->fromJSON($forms);

		if ($lang == 'ru') {
			$mod100 = $count % 100;
			switch ($count%10) {
				case 1:
					if ($mod100 == 11) {$text = $forms[2];}
					else {$text = $forms[0];}
					break;
				case 2:
				case 3:
				case 4:
					if (($mod100 > 10) && ($mod100 < 20)) {$text = $forms[2];}
					else {$text = $forms[1];}
					break;
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 0:
				default: $text = $forms[2];
			}
		}
		else {
			if ($count == 1) {
				$text = $forms[0];
			}
			else {
				$text = $forms[1];
			}
		}
		return $text;

	}


	/*
	 * Logs user views of a Resource. Need for new comments feature.
	 *
	 * @return void
	 * */
	public function logView($resource) {
		if ($this->modx->user->isAuthenticated() && $this->modx->user->id && $this->modx->getCount('modResource', $resource)) {
			$table = $this->modx->getTableName('TicketView');
			$timestamp = date('Y-m-d H:i:s');
			$sql = "INSERT INTO {$table} (`uid`,`parent`,`timestamp`) VALUES ({$this->modx->user->id},{$resource},'{$timestamp}') ON DUPLICATE KEY UPDATE `timestamp` = '{$timestamp}'";
			if ($stmt = $this->modx->prepare($sql)) {$stmt->execute();}
		}
	}

}
