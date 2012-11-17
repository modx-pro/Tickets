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
			,'tplCommentAuth' => 'tpl.Tickets.comment.one.auth'
			,'tplCommentGuest' => 'tpl.Tickets.comment.one.guest'
			,'tplComments' => 'tpl.Tickets.comment.wrapper'
			,'tplLoginToComment' => 'tpl.Tickets.comment.login'
			,'tplPreview' => 'tpl.Tickets.form.preview'
			,'tplCommentEmailOwner' => 'tpl.Tickets.comment.email.owner'
			,'tplCommentEmailReply' => 'tpl.Tickets.comment.email.reply'

			,'fastMode' => true
			,'dateFormat' => '%d %b %Y %H:%M'
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
				if ($object['createdby'] != $this->modx->user->id  && !$this->modx->hasPermission('edit_document')) {
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
	 * Returns form for create/update Comment
	 *
	 * @access public
	 * @return mixed Rendered form
	 */
	public function getCommentForm() {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->modx->getChunk($this->config['tplLoginToComment']);
		}
		$enable_editor = $this->modx->getOption('tickets.enable_editor');
		$htmlBlock = 'enable_editor:'.$enable_editor.'';
		if ($enable_editor) {
			$this->modx->regClientStartupScript($this->config['jsUrl'].'web/editor/jquery.markitup.js');
			$this->modx->regClientCSS($this->config['jsUrl'].'web/editor/editor.css');
			$this->modx->regClientCSS($this->config['cssUrl'].'web/tickets.css');
			$htmlBlock .= ',editor:{comment:'.$this->modx->getOption('tickets.editor_config.comment').'}';
		}

		$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
			Comments = new Object();
			Comments.config = {'.$htmlBlock.'};
		</script>');

		$arr = array(
			'assetsUrl' => $this->config['assetsUrl']
			,'thread' => $this->config['thread']
		);
		return $this->modx->getChunk($this->config['tplCommentForm'], $arr);
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
			'id' => '0'
			,'text' => $this->Jevix($data['text'], 'Comment')
			,'name' => $this->modx->user->Profile->fullname
			,'email' => $this->modx->user->Profile->email
			,'createdby' => $this->modx->user->id
			,'createdon' => date('Y-m-d H:i:s')
		));
		$comment->set('id', '0');
		$comment = $this->prepareComment($comment->toArray());
		return $this->modx->getChunk($this->config['tplCommentGuest'], $comment);
	}


	/**
	 * Returns sanitized preview of Comment
	 *
	 * @access public
	 * @param array $data section, pagetitle,comment, etc
	 * @return mixed rendered preview of Comment for frontend
	 */
	public function saveComment($data = array()) {
		$data['text'] = $this->Jevix($data['text'], 'Comment');
		$response = $this->runProcessor('web/comment/create', $data);
		if ($response->isError()) {
			$arr = array(
				'message' => $response->getMessage()
				,'error' => 1
			);
		}
		else {
			$comment = $response->response['object'];
			$arr = array(
				'error' => 0
				,'data' => $this->modx->getChunk($this->config['tplCommentAuth'], $this->prepareComment($comment))
				,'count' => $this->getTicketComments($this->config['thread'])
			);
			$this->modx->cacheManager->delete('tickets/latest.comments');
			$this->sendCommentMails($comment);
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
	public function Jevix($text = null, $setName = 'Ticket') {
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
		$text = html_entity_decode($text);
		$params['input'] =  str_replace(array('[',']'), array('{{{{{','}}}}}'), $text);

		$snippet->setCacheable(false);
		$filtered = $snippet->process($params);

		$filtered = str_replace(array('{{{{{','}}}}}','`'), array('&#91;','&#93;','&#96;'), $filtered);
		return $filtered;
	}


	/**
	 * Returns comments count for ticket
	 *
	 * @access public
	 * @param int|string $id Id of ticket
	 * @return integer Number of comments
	 */
	function getTicketComments($thread = '') {
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
	function sanitizeString($string = '') {
		$string = htmlentities($string, ENT_QUOTES, "UTF-8");
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


	/**
	 * Returns latest not deleted comments
	 *
	 * @access public
	 * @param array $data Various params for processor
	 * @return mixed Rendered results
	 */
	function getLatestComments($data) {
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
	function getCommentThread($thread = '') {
		$data = array_merge($this->config, array(
			'thread' => is_numeric($thread) ? 'resource-'.$thread : $thread
		));
		$response = $this->runProcessor('web/thread/get', $data);
		if ($response->isError()) {
			return $response->getMessage();
		}
		$data = json_decode($response->response,true);
		$comments = '';
		if ($data['total'] > 0) {
			$tpl = $this->modx->user->isAuthenticated() ? $this->config['tplCommentAuth'] : $this->config['tplCommentGuest'];
			if ($chunk = $this->modx->getObject('modChunk', array('name' => $tpl))) {
				$tpl = $chunk->get('snippet');
			}
			foreach ($data['results'] as $node) {
				$comments .= $this->templateNode($node, $tpl);
			}
		}
		if ($this->config['useCss']) {
			$scriptProperties['useCss'] = 0;
			$this->modx->regClientCSS($this->config['assetsUrl'] . 'css/web/comments.css');
		}
		if ($this->config['useJs'] && $this->modx->user->isAuthenticated()) {
			$this->modx->regClientScript($this->config['assetsUrl'] . 'js/web/comments.js');
		}
		$arr = array(
			'total' => $data['total']
			,'comments' => $comments
		);
		return $this->modx->getChunk($this->config['tplComments'], $arr);
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
		$node = $this->prepareComment($node);

		if ($this->config['fastMode']) {
			$pl = $this->makePlaceholders($node);
			$row = str_replace($pl['pl'], $pl['vl'], $tpl);
		}
		else {
			$chunk = $this->modx->newObject('modChunk');
			$chunk->setCacheable(false);
			$row = $chunk->process($node, $tpl);
		}

		return preg_replace('/\[\[(.*?)\]\]/', '', $row);
	}


	/*
	 * Render of the comment
	 * */
	function prepareComment($data = array()) {
		$data['avatar'] = $this->config['gravatarUrl'] . md5($data['email']) .'?s=' . $this->config['gravatarSize'] . '&d=' . $this->config['gravatarIcon'];
		if (!empty($data['resource'])) {
			$data['url'] = $this->modx->makeUrl($data['resource'], '', '', 'full');
		}
		$data['createdon'] = strftime($this->config['dateFormat'], strtotime($data['createdon']));
		$data['editedon'] = strftime($this->config['dateFormat'], strtotime($data['editedon']));
		$data['deletedon'] = strftime($this->config['dateFormat'], strtotime($data['deletedon']));
		if ($data['deleted']) {
			$data['text'] = $this->modx->lexicon('ticket_comment_deleted_text');
		}
		return $data;
	}


	/*
	 * Returns array with separated placeholders and values for fast render without processing chunk
	 * */
	function makePlaceholders($arr = array()) {
		$placeholders = array();

		foreach ($arr as $k => $v) {
			$placeholders['pl'][] = "[[+$k]]";
			$placeholders['vl'][] = $v;
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
	}

}