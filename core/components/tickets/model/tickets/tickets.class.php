<?php
/**
 * The base class for Tickets.
 *
 * @package tickets
 */
class Tickets {

	private $prepareCommentCustom = null;
	public $elements = array();

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
		$editorConfig = 'enable_editor:'.$enable_editor;
		if ($enable_editor) {
			$this->modx->regClientScript($this->config['jsUrl'].'web/editor/jquery.markitup.js');
			$this->modx->regClientCSS($this->config['jsUrl'].'web/editor/editor.css');
			$this->modx->regClientCSS($this->config['cssUrl'].'web/tickets.css');
			$editorConfig .= "\n".',editor:{ticket:'.$this->modx->getOption('tickets.editor_config.ticket').'}';
		}

		$this->modx->regClientStartupScript('<script type="text/javascript">
			TicketsConfig = {
				jsUrl: "'.$this->config['jsUrl'].'web/"
				,cssUrl: "'.$this->config['cssUrl'].'web/"
				,'.$editorConfig.'
			};
		</script>');
		$this->modx->regClientScript($this->config['jsUrl'].'web/tickets.js');

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
		$enable_editor = $this->modx->getOption('tickets.enable_editor');
		$editorConfig = 'enable_editor:'.$enable_editor.'';
		if ($enable_editor) {
			$this->modx->regClientScript($this->config['jsUrl'].'web/editor/jquery.markitup.js');
			$this->modx->regClientCSS($this->config['jsUrl'].'web/editor/editor.css');
			$editorConfig .= "\n".',editor:{comment:'.$this->modx->getOption('tickets.editor_config.comment').'}';
		}

		$this->modx->regClientStartupScript('<script type="text/javascript">
			CommentsConfig = {
				jsUrl: "'.$this->config['jsUrl'].'web/"
				,cssUrl: "'.$this->config['cssUrl'].'web/"
				,connector: "'.$this->config['assetsUrl'].'comment.php"
				,'.$editorConfig.'
			};
		</script>');

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
			,'name' => $this->modx->user->Profile->fullname
			,'email' => $this->modx->user->Profile->email
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->id
		));
		$comment->set('id', '0');
		return $this->templateNode($comment->toArray(), $this->config['tplCommentGuest']);
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
			if ($profile = $this->modx->getObject('modUserProfile', array('internalKey' => $comment['createdby']))) {
				$profile = $profile->toArray();
				$comment = array_merge($profile, $comment);
			}
			$arr = array(
				'error' => 0
				,'data' => $this->templateNode($comment, $this->config['tplCommentAuth'])
				,'count' => $this->getTicketComments($this->config['thread'])
			);
			$this->modx->cacheManager->delete('tickets/latest.comments');
			$this->modx->cacheManager->delete('tickets/latest.tickets');
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
	public function getTicketComments($thread = '') {
		//if (empty($thread)) {$thread = $this->modx->resource->id;}
		if (is_numeric($thread)) {$thread = 'resource-'.$thread;}

		$q = $this->modx->newQuery('TicketComment');
		$q->leftJoin('TicketThread', 'TicketThread','TicketThread.id = TicketComment.thread');
		$q->where(array('TicketThread.name' => $thread));
		return $this->modx->getCount('TicketComment', $q);
	}


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


	/**
	 * Returns latest created and published tickets
	 *
	 * @access public
	 * @param array $data Various params for processor
	 * @return mixed Rendered results
	 */
	public function getLatestTickets($data) {
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
				$v['date_ago'] = $this->dateFormat($v['createdon']);
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


	/**
	 * Returns latest comments
	 *
	 * @access public
	 * @param array $data Various params for processor
	 * @return mixed Rendered results
	 */
	public function getLatestComments($data) {
		if (!empty($data['parents'])) {$data['parents'] = explode(',', $data['parents']);}
		$response = $this->runProcessor('web/comment/getlist_latest', $data);
		$result = json_decode($response->response, true);
		$output = '';
		$sections = array();
		if ($result['total']) {
			foreach ($result['results'] as $v) {
				$v['comments'] = $this->getTicketComments($v['resource']);
				if (!array_key_exists($v['section'], $sections)) {
					$q = $this->modx->newQuery('TicketsSection', array('id' => $v['section']));
					$q->select('pagetitle');
					if ($q->prepare() && $q->stmt->execute()) {
						$sections[$v['section']] = $q->stmt->fetch(PDO::FETCH_COLUMN);
					}
				}
				$v['sectiontitle'] = $sections[$v['section']];
				$v['date_ago'] = $this->dateFormat($v['createdon']);
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


	/*
	 * Returns all comments of the resource with given id
	 * */
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

		$this->modx->regClientCSS($this->config['assetsUrl'] . 'css/web/comments.css');
		if ($this->modx->user->isAuthenticated()) {
			$this->modx->regClientScript($this->config['assetsUrl'] . 'js/web/comments.js');
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
	public function templateNode($node = array(), $tpl = '') {
		$children = null;
		if (!empty($node['children'])) {
			foreach ($node['children'] as $v) {
				$children .= $this->templateNode($v, $tpl);
			}
		}

		if (!empty($children)) {$node['children'] = $children;}
		else if ($node['createdby'] == $this->modx->user->id && (time() - strtotime($node['createdon']) <= $this->config['commentEditTime'])) {
			if (!array_key_exists($tpl, $this->elements)) {
				$this->getChunk($tpl);
			}
			$node['ticket_comment_edit_link'] = @$this->elements[$tpl]['placeholders']['ticket_comment_edit_link'];
		}
		if ($node['editedby'] && $node['editedon']) {
			$node['ticket_comment_was_edited'] = @$this->elements[$tpl]['placeholders']['ticket_comment_was_edited'];
		}
		$node = $this->prepareComment($node);

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
			$data['avatar'] = $this->config['gravatarUrl'] . md5($data['email']) .'?s=' . $this->config['gravatarSize'] . '&d=' . $this->config['gravatarIcon'];
			if (!empty($data['resource'])) {
				$data['url'] = $this->modx->makeUrl($data['resource'], '', '', 'full');
			}
			$data['createdon'] = date($this->config['dateFormat'], strtotime($data['createdon']));
			$data['editedon'] = date($this->config['dateFormat'], strtotime($data['editedon']));
			$data['deletedon'] = date($this->config['dateFormat'], strtotime($data['deletedon']));
			$data['date_ago'] = $this->dateFormat($data['createdon']);
			if ($data['deleted']) {
				$data['text'] = $this->modx->lexicon('ticket_comment_deleted_text');
			}
			return $data;
		}
	}


	/*
	 * Returns array with separated placeholders and values for fast render without processing chunk
	 * */
	public function makePlaceholders($arr = array(), $prefix = '') {
		$placeholders = array();

		foreach ($arr as $k => $v) {
			if (is_array($v)) {
				$prefix .= $k.'_';
				$placeholders = array_merge($placeholders, $this->makePlaceholders($v, $prefix));
			}
			else {
				$placeholders['pl'][] = "[[+$k]]";
				$placeholders['vl'][] = $v;
			}
		}
		return $placeholders;
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
		$output = null;

		if (!array_key_exists($name, $this->elements)) {
			/* @var modChunk $element */
			if ($element = $this->modx->getObject('modChunk', array('name' => $name))) {
				$element->setCacheable(false);
				$content = $element->getContent();

				// processing lexicon placeholders
				preg_match_all('/\[\[%(.*?)\]\]/',$content, $matches);
				$src = $dst = array();
				foreach ($matches[1] as $k => $v) {
					$src[] = $matches[0][$k];
					$dst[] = $this->modx->lexicon($v);
				}
				$content = str_replace($src,$dst,$content);

				// processing special tags
				preg_match_all('/\<!--ticket(.*?)[\s|\n|\r\n](.*?)-->/s', $content, $matches);
				$src = $dst = $placeholders = array();
				foreach ($matches[1] as $k => $v) {
					$src[] = $matches[0][$k];
					$dst[] = '';
					$placeholders['ticket'.$v] = $matches[2][$k];
				}
				$content = str_replace($src,$dst,$content);

				$chunk = array(
					'object' => $element
					,'content' => $content
					,'placeholders' => $placeholders
				);

				$this->elements[$name] = $chunk;
			}
			else {
				return false;
			}
		}
		else {
			$chunk = $this->elements[$name];
			$chunk['object']->_processed = false;
		}

		if (!empty($properties) && $chunk['object'] instanceof modChunk) {
			$pl = $this->makePlaceholders($properties);
			$content = str_replace($pl['pl'], $pl['vl'], $chunk['content']);
			$content = str_replace($pl['pl'], $pl['vl'], $content);
			if ($fastMode) {
				$matches = $tags = array();
				$this->modx->parser->collectElementTags($content, $matches);
				foreach ($matches as $v) {
					$tags[] = $v[0];
				}
				$output = str_replace($tags, '', $content);
			}
			else {
				$output = $chunk['object']->process($properties, $content);
			}
		}
		else {
			$output = $chunk['content'];
		}

		return $output;
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

}