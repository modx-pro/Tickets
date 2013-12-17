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


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx,array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('tickets.core_path',$config,$this->modx->getOption('core_path').'components/tickets/');
		$assetsPath = $this->modx->getOption('tickets.assets_path', $config, $this->modx->getOption('assets_path').'components/tickets/');
		$assetsUrl = $this->modx->getOption('tickets.assets_url', $config, $this->modx->getOption('assets_url').'components/tickets/');
		$actionUrl = $this->modx->getOption('tickets.action_url', $config, $assetsUrl.'action.php');
		$connectorUrl = $assetsUrl.'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl
			,'cssUrl' => $assetsUrl.'css/'
			,'jsUrl' => $assetsUrl.'js/'
			,'jsPath' => $assetsPath.'js/'
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

			,'fastMode' => false
			,'dateFormat' => 'd F Y, H:i'
			,'dateNow' => 10
			,'dateDay' => 'day H:i'
			,'dateMinutes' => 59
			,'dateHours' => 10
			,'charset' => $this->modx->getOption('modx_charset')
			,'snippetPrepareComment' => $this->modx->getOption('tickets.snippet_prepare_comment')
			,'commentEditTime' => $this->modx->getOption('tickets.comment_edit_time', null, 180)
			,'depth' => 0

			,'gravatarUrl' => 'http://www.gravatar.com/avatar/'
			,'gravatarSize' => 24
			,'gravatarIcon' => 'mm'

			,'json_response' => true
			,'nestedChunkPrefix' => 'tickets_'
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
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array $scriptProperties
	 *
	 * @return boolean
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
				if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
					$config = $this->makePlaceholders($this->config);

					if ($css = $this->modx->getOption('tickets.frontend_css')) {
						$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
					}

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
					$config_js = preg_replace(array('/^\n/', '/\t{6}/'), '', '
						TicketsConfig = {
							jsUrl: "'.$this->config['jsUrl'].'web/"
							,cssUrl: "'.$this->config['cssUrl'].'web/"
							,actionUrl: "'.$this->config['actionUrl'].'"
							,formBefore: '.$formBefore.'
							,close_all_message: "'.$this->modx->lexicon('tickets_message_close_all').'"
							,tpanel: '.($this->modx->user->isAuthenticated() ? 1 : 0).'
							,thread_depth: '.$this->config['depth'].'
							,'.$editorConfig.'
						};
					');

					if (file_put_contents($this->config['jsPath'] . 'web/config.js', $config_js)) {
						$this->modx->regClientStartupScript($this->config['jsUrl'] . 'web/config.js');
					}
					else {
						$this->modx->regClientStartupScript("<script type=\"text/javascript\">\n".$config_js."\n</script>", true);
					}

					if ($js = trim($this->modx->getOption('tickets.frontend_js'))) {
						if (!empty($js) && preg_match('/\.js/i', $js)) {
							$this->modx->regClientScript(preg_replace(array('/^\n/', '/\t{7}/'), '', '
							<script type="text/javascript">
								if(typeof jQuery == "undefined") {
									document.write("<script src=\"'.$this->config['jsUrl'].'web/lib/jquery.min.js\" type=\"text/javascript\"><\/script>");
								}
							</script>
							'), true);
							$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
						}
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
	 * Returns sanitized preview of Ticket
	 *
	 * @param array $data section, pagetitle, text, etc
	 *
	 * @return array
	 */
	public function previewTicket($data = array()) {
		$message = '';
		foreach ($data as $k => $v) {
			if ($k == 'content') {
				if (!$data[$k] = $this->Jevix($v, 'Ticket')) {
					return $this->error($this->modx->lexicon('err_no_jevix'));
				}
			}
			else {
				$data[$k] = $this->sanitizeString($v);
			}
		}

		$preview = $this->getChunk($this->config['tplPreview'], $data);
		$preview = $this->pdoTools->fastProcess($preview);

		return $this->success($message, array('preview' => $preview));
	}


	/**
	 * Save ticket through processor and redirect to it
	 *
	 * @param array $data section, pagetitle, text, etc
	 *
	 * @return array
	 */
	public function saveTicket($data = array()) {
		$allowedFields = array_map('trim', explode(',', $this->config['allowedFields']));
		$allowedFields = array_unique(array_merge($allowedFields, array('parent','pagetitle','content')));
		$requiredFields = array_map('trim', explode(',', $this->config['requiredFields']));
		$requiredFields = array_unique(array_merge($requiredFields, array('parent','pagetitle','content')));

		$fields = array();
		foreach ($allowedFields as $field) {
			if (in_array($field, $allowedFields) && array_key_exists($field, $data)) {
				$value = $data[$field];
				if ($field !== 'content') {
					$value = $this->sanitizeString($value);
				}
				$fields[$field] = $value;
			}
		}

		$errors = array();
		foreach ($requiredFields as $v) {
			if (empty($fields[$v])) {
				$errors[$v] = $this->modx->lexicon('field_required');
			}
		}
		if (!empty($errors)) {
			return $this->error($this->modx->lexicon('ticket_err_form'), $errors);
		}

		$fields['class_key'] = 'Ticket';
		if (!empty($data['tid'])) {
			$fields['id'] = (integer) $data['tid'];
			if ($ticket = $this->modx->getObject('Ticket', array('class_key' => 'Ticket', 'id' => $fields['id']))) {
				$fields['context_key'] = $ticket->get('context_key');
				$response = $this->modx->runProcessor('resource/update', $fields);
			}
			else {
				return $this->error($this->modx->lexicon('ticket_err_id', array('id' => $fields['id'])));
			}
		}
		else {
			$response = $this->modx->runProcessor('resource/create', $fields);
		}

		/* @var modProcessorResponse $response */
		if ($response->isError()) {
			$message = $response->getMessage();
			if (empty($message)) {$message = $this->modx->lexicon('ticket_err_form');}
			$tmp = $response->getFieldErrors();
			$errors = array();
			foreach ($tmp as $v) {
				$errors[$v->field] = $v->message;
			}

			return $this->error($message, $errors);
		}
		else if (empty($data['tid']) && $this->modx->getOption('tickets.mail_bcc_level') >= 1) {
			if ($bcc = $this->modx->getOption('tickets.mail_bcc')) {
				$bcc = array_map('trim', explode(',', $bcc));
				if (!empty($bcc) && $resource = $this->modx->getObject('Ticket', $response->response['object']['id'])) {
					$resource = $resource->toArray();
					foreach ($bcc as $uid) {
						if ($uid == $resource['createdby']) {continue;}
						$this->addQueue(
							$uid
							,$this->modx->lexicon('ticket_email_bcc', $resource)
							,$this->getChunk($this->config['tplTicketEmailBcc'], $resource, false)
						);
					}
				}
			}
		}

		$id = $response->response['object']['id'];
		if (empty($data['published'])) {$id = $data['parent'];}
		$redirect = $this->modx->makeUrl($id,'','','full');

		return $this->success('', array('redirect' => $redirect));
	}


	/**
	 * Returns sanitized preview of Comment
	 *
	 * @access public
	 * @param array $data section, pagetitle, comment, etc
	 *
	 * @return array
	 */
	public function previewComment($data = array()) {
		$comment = $this->modx->newObject('TicketComment', array(
			'text' => $this->Jevix($data['text'], 'Comment')
			,'fullname' => $this->modx->user->Profile->get('fullname')
			,'email' => $this->modx->user->Profile->get('email')
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->get('id')
			,'resource' => $this->config['resource']
			,'mode' => 'preview'
		));
		$comment->set('id', 0);

		$preview = $this->templateNode($comment->toArray(), $this->config['tplCommentGuest']);
		return $this->success('', array('preview' => $preview));
	}


	/**
	 * Returns sanitized preview of Comment
	 *
	 * @param array $data section, pagetitle, comment, etc
	 *
	 * @return array
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
			return $this->error($response->getMessage());
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

			if (empty($data['id'])) {
				$this->sendCommentMails($this->prepareComment($comment));
			}

			if ($comment['published']) {
				$this->modx->cacheManager->delete('tickets/latest.comments');
				$this->modx->cacheManager->delete('tickets/latest.tickets');
				$comment = $this->templateNode($comment, $this->config['tplCommentAuth']);
				return $this->success('', $comment);
			}
			else {
				return $this->success($this->modx->lexicon('ticket_unpublished_comment'));
			}
		}
	}


	/**
	 * Returns Comment for edit by its author
	 *
	 * @param integer $id Id of an comment
	 *
	 * @return array
	 */
	public function getComment($id) {
		$response = $this->runProcessor('web/comment/get', array('id' => $id));
		if ($response->isError()) {
			return $this->error($response->getMessage());
		}

		$comment = $response->response['object'];
		$time = time() - strtotime($comment['createdon']);
		$time_limit = $this->config['commentEditTime'];

		if ($comment['createdby'] != $this->modx->user->id) {
			return $this->error($this->modx->lexicon('ticket_comment_err_wrong_user'));
		}
		else if ($this->modx->getCount('TicketComment', array('parent' => $comment['id']))) {
			return $this->error($this->modx->lexicon('ticket_comment_err_has_replies'));
		}
		else if ($time >= $time_limit) {
			return $this->error($this->modx->lexicon('ticket_comment_err_no_time'));
		}
		else {
			return $this->success('', array(
				'raw' => $comment['raw']
				,'time' => $time_limit - $time
			));
		}
	}


	/**
	 * Return unseen comments of thread for user
	 *
	 * @param $name
	 *
	 * @return array
	 */
	public function getNewComments($name) {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error($this->modx->lexicon('access_denied'));
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

						$tmp = $this->templateNode($row);
						$comments[$row['id']] = $tmp;
					}

					$this->logView($thread->resource);
					return $this->success('', array(
						'comments' => $comments
					));
				}
			}
		}
		return $this->error('');
	}


	/**
	 * Sanitize any text through Jevix snippet
	 *
	 * @param string $text Text for sanitization
	 * @param string $setName Name of property set for get parameters from
	 * @param boolean $replaceTags Replace MODX tags?
	 *
	 * @return string
	 */
	public function Jevix($text = null, $setName = 'Ticket', $replaceTags = true) {
		if (empty($text)) {return ' ';}
		if (!$snippet = $this->modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			return 'Could not load snippet Jevix';
		}
		// Loading parser if needed - it is for mgr context
		if (!is_object($this->modx->parser)) {
			$this->modx->getParser();
		}

		$params = array();
		if ($setName) {
			$params = $snippet->getPropertySet($setName);
		}

		$text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
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
	 * Sanitize MODX tags
	 *
	 * @param string $string Any string with MODX tags
	 *
	 * @return string String with html entities
	 */
	public function sanitizeString($string = '') {
		$string = htmlentities(trim($string), ENT_QUOTES, "UTF-8");
		$string = preg_replace('/^@.*\b/', '', $string);

		$arr1 = array('[',']','`');
		$arr2 = array('&#091;','&#093;','&#096;');
		return str_replace($arr1, $arr2, $string);
	}


	/**
	 * Recursive template of the comment node
	 *
	 * @param array $node
	 * @param null $tpl
	 *
	 * @return string
	 */
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

		if (!empty($children) || !empty($node['has_children'])) {
			$node['children'] = $children;
			$node['comment_edit_link'] = false;
		}
		elseif ($node['createdby'] == $this->modx->user->id && (time() - strtotime($node['createdon']) <= $this->config['commentEditTime'])) {
			$node['comment_edit_link'] = true;
		}
		else {
			$node['children'] = '';
		}
		$node['comment_was_edited'] = $node['editedby'] && $node['editedon'];
		$node['comment_new'] = $node['createdby'] != $this->modx->user->id && $this->last_view > 0 && strtotime($node['createdon']) > $this->last_view;

		return $this->getChunk($tpl, $node, $this->config['fastMode']);
	}


	/**
	 * Render of the comment
	 *
	 * @param array $data
	 *
	 * @return array
	 */
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


	/**
	 * Method for transform array to placeholders
	 *
	 * @var array $array With keys and values
	 * @var string $prefix Prefix for array keys
	 *
	 * @return array $array Two nested arrays with placeholders and values
	 */
	public function makePlaceholders(array $array = array(), $prefix = '') {
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}

		return $this->pdoTools->makePlaceholders($array, $prefix);
	}


	/**
	 * Email notifications about new comment
	 *
	 * @param array $comment
	 *
	 * @return void
	 */
	public function sendCommentMails($comment = array()) {
		$owner_uid = $reply_uid = null;
		$subscribers = array();
		$q = $this->modx->newQuery('TicketThread');
		$q->leftJoin('modResource', 'modResource','TicketThread.resource = modResource.id');
		$q->select('modResource.createdby as uid, modResource.id as resource, modResource.pagetitle, TicketThread.subscribers');
		$q->where(array('TicketThread.id' => $comment['thread']));
		if ($q->prepare() && $q->stmt->execute()) {
			$res = $q->stmt->fetch(PDO::FETCH_ASSOC);
			if (!empty($res)) {
				$comment = array_merge($comment, array(
					'resource' => $res['resource']
					,'pagetitle' => $res['pagetitle']
					,'author' => $res['uid']
				));
				$owner_uid = $res['uid'];
				$subscribers = $this->modx->fromJSON($res['subscribers']);
			}
		}

		if (empty($subscribers)) {
			$subscribers = array();
		}

		// It is a reply for a comment
		if ($comment['parent']) {
			$q = $this->modx->newQuery('TicketComment');
			$q->select('TicketComment.createdby as uid, TicketComment.text');
			$q->where(array('TicketComment.id' => $comment['parent'], 'TicketComment.createdby:!=' => $comment['createdby']));
			if ($q->prepare() && $q->stmt->execute()) {
				if ($res = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$reply_uid = $res['uid'];
					$comment['parent_text'] = $res['text'];
				}
			}
		}

		$comment = $this->prepareComment($comment);
		unset($comment['properties']);

		// We always send replies for comments
		if (!empty($reply_uid)) {
			$this->addQueue(
				$reply_uid
				,$this->modx->lexicon('ticket_comment_email_reply', $comment)
				,$this->getChunk($this->config['tplCommentEmailReply'], $comment, false)
			);
		}

		// Then we send emails to subscribers
		foreach ($subscribers as $uid) {
			if ($uid == $reply_uid || $uid == $comment['createdby']) {
				continue;
			}
			else if ($uid == $owner_uid) {
				$this->addQueue(
					$uid
					,$this->modx->lexicon('ticket_comment_email_owner', $comment)
					,$this->getChunk($this->config['tplCommentEmailOwner'], $comment, false)
				);
			}
			else {
				$this->addQueue(
					$uid
					,$this->modx->lexicon('ticket_comment_email_subscription', $comment)
					,$this->getChunk($this->config['tplCommentEmailSubscription'], $comment, false)
				);
			}
		}

		// Then we make blind copy
		if ($this->modx->getOption('tickets.mail_bcc_level') >= 2) {
			if ($bcc = $this->modx->getOption('tickets.mail_bcc')) {
				$bcc = array_map('trim', explode(',', $bcc));
				foreach ($bcc as $uid) {
					if ($uid != $reply_uid && $uid != $owner_uid && $uid != $comment['createdby']) {
						$this->addQueue(
							$uid
							,$this->modx->lexicon('ticket_comment_email_bcc', $comment)
							,$this->getChunk($this->config['tplCommentEmailBcc'], $comment, false)
						);
					}
				}
			}
		}
	}


	/**
	 * Adds emails to queue
	 *
	 * @param $uid
	 * @param $subject
	 * @param $body
	 *
	 * @return bool|string
	 */
	public function addQueue($uid, $subject, $body) {
		$uid = (integer) $uid;
		if ($uid <= 0) {return false;}

		/* @var TicketQueue $queue */
		$queue = $this->modx->newObject('TicketQueue', array(
				'uid' => (integer) $uid
				,'subject' => $subject
				,'body' => $body
			)
		);

		return $this->modx->getOption('tickets.mail_queue', null, false, true)
			? $queue->save()
			: $queue->Send();
	}


	/**
	 * This method subscribe or unsubscribe users for notifications about new comments in thread.
	 *
	 * @param string $name Name of tickets thread for subscribe or unsubscribe
	 *
	 * @return array
	 */
	public function Subscribe($name) {
		if (!$this->modx->user->isAuthenticated()) {
			return $this->error('ticket_err_access_denied');
		}
		/* @var TicketThread $thread */
		if ($thread = $this->modx->getObject('TicketThread', array('name' => $name))) {
			$message = $thread->Subscribe() ? 'ticket_thread_subscribed' : 'ticket_thread_unsubscribed';
			return $this->success($this->modx->lexicon($message));
		}
		else {
			return $this->error($this->modx->lexicon('ticket_err_wrong_thread'));
		}
	}


	/**
	 * Loads an instance of pdoTools
	 *
	 * @return boolean
	 */
	public function loadPdoTools() {
		if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
			if ($this->modx->getService('pdoFetch')) {
				$this->pdoTools = new pdoFetch($this->modx, $this->config);
			}
			else {
				return false;
			}
		}
		return true;
	}


	/**
	 * Process and return the output from a Chunk by name.
	 *
	 * @param string $name The name of the chunk.
	 * @param array $properties An associative array of properties to process the Chunk with, treated as placeholders within the scope of the Element.
	 * @param boolean $fastMode If false, all MODX tags in chunk will be processed.
	 *
	 * @return string The processed output of the Chunk.
	 */
	public function getChunk($name, array $properties = array(), $fastMode = false) {
		if (!$this->modx->parser) {
			$this->modx->getParser();
		}
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}

		return $this->pdoTools->getChunk($name, $properties, $fastMode);
	}


	/**
	 * Formats date to "10 minutes ago" or "Yesterday in 22:10"
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/7a6039b21c326acf03c956772325e1398801c5fe/engine/modules/viewer/plugs/function.date_format.php

	 * @param string $date Timestamp to format
	 * @param string $dateFormat
	 *
	 * @return string
	 */
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


	/**
	 * Declension of words
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/eca10c0186c8174b774a2125d8af3760e1c34825/engine/modules/viewer/plugs/modifier.declension.php
	 *
	 * @param int $count
	 * @param string $forms
	 * @param string $lang
	 *
	 * @return string
	 */
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


	/**
	 * Logs user views of a Resource. Need for new comments feature.
	 *
	 * @param integer $resource An id of resource
	 *
	 * @return void
	 */
	public function logView($resource) {
		if ($this->modx->user->isAuthenticated() && $this->modx->user->id && $this->modx->getCount('modResource', $resource)) {
			$table = $this->modx->getTableName('TicketView');
			$timestamp = date('Y-m-d H:i:s');
			$sql = "INSERT INTO {$table} (`uid`,`parent`,`timestamp`) VALUES ({$this->modx->user->id},{$resource},'{$timestamp}') ON DUPLICATE KEY UPDATE `timestamp` = '{$timestamp}'";
			if ($stmt = $this->modx->prepare($sql)) {
				$stmt->execute();
			}
		}
	}


	/**
	 * This method returns an error of the cart
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 */
	public function error($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => false
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}


	/* This method returns an success of the cart
	 *
	 * @param string $message A lexicon key for success message
	 * @param array $data.Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
	public function success($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => true
			,'message' => $this->modx->lexicon($message, $placeholders)
			,'data' => $data
		);

		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}

}
