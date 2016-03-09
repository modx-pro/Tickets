<?php

class Tickets {
	/** @var modX $modx */
	public $modx;
	/** @var pdoFetch $pdoTools */
	public $pdoTools;
	public $initialized = array();
	public $authenticated = false;
	private $prepareCommentCustom = null;
	private $last_view = 0;


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('tickets.core_path', $config, $this->modx->getOption('core_path') . 'components/tickets/');
		$assetsPath = $this->modx->getOption('tickets.assets_path', $config, $this->modx->getOption('assets_path') . 'components/tickets/');
		$assetsUrl = $this->modx->getOption('tickets.assets_url', $config, $this->modx->getOption('assets_url') . 'components/tickets/');
		$actionUrl = $this->modx->getOption('tickets.action_url', $config, $assetsUrl . 'action.php');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'jsPath' => $assetsPath . 'js/',
			'imagesUrl' => $assetsUrl . 'img/',

			'connectorUrl' => $connectorUrl,
			'actionUrl' => $actionUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',

			'fastMode' => false,
			'dateFormat' => 'd F Y, H:i',
			'dateNow' => 10,
			'dateDay' => 'day H:i',
			'dateMinutes' => 59,
			'dateHours' => 10,
			'charset' => $this->modx->getOption('modx_charset'),
			'snippetPrepareComment' => $this->modx->getOption('tickets.snippet_prepare_comment'),
			'commentEditTime' => $this->modx->getOption('tickets.comment_edit_time', null, 180),
			'depth' => 0,

			'gravatarUrl' => 'https://www.gravatar.com/avatar/',
			'gravatarSize' => 24,
			'gravatarIcon' => 'mm',

			'json_response' => true,
			'nestedChunkPrefix' => 'tickets_',
			'allowGuest' => false,
			'allowGuestEdit' => false,
			'allowGuestEmails' => false,
			'enableCaptcha' => false,

			'requiredFields' => '',
		), $config);

		$this->modx->addPackage('tickets', $this->config['modelPath']);
		$this->modx->lexicon->load('tickets:default');

		if ($name = $this->config['snippetPrepareComment']) {
			if ($snippet = $this->modx->getObject('modSnippet', array('name' => $name))) {
				$this->prepareCommentCustom = $snippet->get('content');
			}
		}

		$this->authenticated = $this->modx->user->isAuthenticated($this->modx->context->get('key'));
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
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}
		$this->pdoTools->setConfig($this->config);

		$this->config['ctx'] = $ctx;
		if (empty($this->initialized[$ctx])) {
			$config_js = array(
				'ctx' => $ctx,
				'jsUrl' => $this->config['jsUrl'] . 'web/',
				'cssUrl' => $this->config['cssUrl'] . 'web/',
				'actionUrl' => $this->config['actionUrl'],
				'close_all_message' => $this->modx->lexicon('tickets_message_close_all'),
				'tpanel' => (int)$this->authenticated,
				'enable_editor' => (int)$this->modx->getOption('tickets.enable_editor'),
			);
			$this->modx->regClientStartupScript('<script type="text/javascript">TicketsConfig=' . $this->modx->toJSON($config_js) . ';</script>', true);
			if ($config_js['enable_editor']) {
				$this->modx->regClientStartupScript('<script type="text/javascript">TicketsConfig.editor={ticket: ' . $this->modx->getOption('tickets.editor_config.ticket') . ',comment: ' . $this->modx->getOption('tickets.editor_config.comment') . '};</script>', true);
				$this->modx->regClientScript($this->config['jsUrl'] . 'web/editor/jquery.markitup.js');
				$this->modx->regClientCSS($this->config['jsUrl'] . 'web/editor/editor.css');
			}
			$this->initialized[$ctx] = true;
		}

		if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
			$config = $this->makePlaceholders($this->config);

			$css = !empty($this->config['frontend_css'])
				? $this->config['frontend_css']
				: $this->modx->getOption('tickets.frontend_css');
			if (!empty($css) && preg_match('/\.css/i', $css)) {
				$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
			}

			$js = !empty($this->config['frontend_js'])
				? $this->config['frontend_js']
				: $this->modx->getOption('tickets.frontend_js');
			if (!empty($js) && preg_match('/\.js/i', $js)) {
				$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
			}
		}

		return true;
	}


	/**
	 * Shorthand for the call of processor
	 *
	 * @access public
	 *
	 * @param string $action Path to processor
	 * @param array $data Data to be transmitted to the processor
	 *
	 * @return mixed The result of the processor
	 */
	public function runProcessor($action = '', $data = array()) {
		if (empty($action)) {
			return false;
		}
		$this->modx->error->reset();
		$processorsPath = !empty($this->config['processorsPath'])
			? $this->config['processorsPath']
			: MODX_CORE_PATH . 'components/tickets/processors/';

		return $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));
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
		$requiredFields = array_map('trim', explode(',', $this->config['requiredFields']));
		$requiredFields = array_unique(array_merge($requiredFields, array('parent', 'pagetitle', 'content')));
		$allowedFields = array_map('trim', explode(',', $this->config['allowedFields']));
		$allowedFields = array_unique(array_merge($allowedFields, $requiredFields));
		$bypassFields = array_map('trim', explode(',', $this->config['bypassFields']));

		$fields = array();
		foreach ($allowedFields as $field) {
			if (in_array($field, $allowedFields) && array_key_exists($field, $data)) {
				$value = $data[$field];
				if ($field !== 'content' && !in_array($field, $bypassFields)) {
					$value = $this->sanitizeString($value);
				}
				$fields[$field] = $value;
			}
		}

		switch ($data['action']) {
			case 'ticket/save':
				$fields['published'] = null;
				break;
			case 'ticket/draft':
				$fields['published'] = false;
				break;
			default:
				$fields['published'] = true;
		}

		$fields['requiredFields'] = $requiredFields;
		$fields['class_key'] = 'Ticket';
		if (!empty($this->config['sections']) && is_array($this->config['sections'])) {
			$fields['sections'] = $this->config['sections'];
		}
		if (!empty($data['tid'])) {
			$fields['id'] = (int)$data['tid'];
			if ($ticket = $this->modx->getObject('Ticket', array('class_key' => 'Ticket', 'id' => $fields['id']))) {
				$fields['context_key'] = $ticket->get('context_key');
				$fields['alias'] = $ticket->get('alias');
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
			$this->modx->log(modX::LOG_LEVEL_INFO, '[Tickets] Unable to save Ticket: ' . $response->getMessage() . print_r($response->getFieldErrors(), 1));
			return $this->error($response->getMessage(), $response->getFieldErrors());
		}
		elseif ($ticket = $this->modx->getObject('Ticket', $response->response['object']['id'])) {
			$ticket = $ticket->toArray();
			$this->sendTicketMails($ticket);
		}

		$id = $response->response['object']['id'];
		$message = '';
		$results = array();
		switch ($data['action']) {
			case 'ticket/save':
				$message = $this->modx->lexicon('ticket_saved');
				break;
			case 'ticket/draft':
				if (!empty($this->config['redirectUnpublished'])) {
					$url = $this->modx->makeUrl((int)$this->config['redirectUnpublished'], '', '', 'full');
				}
				else {
					$url = $_SERVER['HTTP_REFERER'];
					if (!preg_match('/\b' . $id . '\b/', $url)) {
						$url .= strpos($url, '?') !== false
							? '&tid=' . $id
							: '?tid=' . $id;
					}
				}
				if (empty($url)) {
					$url = $this->modx->getOption('site_url');
				}
				$results['redirect'] = $url;
				break;
			default:
				$url = $this->modx->makeUrl($id, '', '', 'full');
				if (empty($url)) {
					$url = $this->modx->getOption('site_url');
				}
				$results['redirect'] = $url;
		}

		return $this->success($message, $results);
	}


	/**
	 * Vote for ticket
	 *
	 * @param $id
	 * @param int $value
	 *
	 * @return array|string
	 */
	public function voteTicket($id, $value = 1) {
		$data = array('id' => $id, 'value' => $value);
		/** @var modProcessorResponse $response */
		if (!empty($id)) {
			$response = $this->runProcessor('web/ticket/vote', $data);
			if ($response->isError()) {
				return $this->error($response->getMessage());
			}
			else {
				$data = $response->getObject();
				$data['title'] = $this->modx->lexicon('ticket_rating_total')
					. " {$data['rating']}: ↑{$data['rating_plus']} "
					. $this->modx->lexicon('ticket_rating_and')
					. " ↓{$data['rating_minus']}";
				if ($data['rating'] > 0) {
					$data['rating'] = '+' . $data['rating'];
					$data['status'] = 1;
				}
				elseif ($data['rating'] < 0) {
					$data['status'] = -1;
				}
				else {
					$data['status'] = 0;
				}
				return $this->success('', $data);
			}
		}

		return $this->error('tickets_err_unknown');
	}


	/**
	 * Star for ticket
	 *
	 * @param $id
	 *
	 * @return array|string
	 */
	public function starTicket($id) {
		$data = array('id' => $id);
		/** @var modProcessorResponse $response */
		if (!empty($id)) {
			$response = $this->runProcessor('web/ticket/star', $data);
			if ($response->isError()) {
				return $this->error($response->getMessage());
			}
			else {
				$data = $response->getObject();
				return $this->success('', $data);
			}
		}

		return $this->error('tickets_err_unknown');
	}


	/**
	 * Returns sanitized preview of Comment
	 *
	 * @access public
	 *
	 * @param array $data section, pagetitle, comment, etc
	 *
	 * @return array
	 */
	public function previewComment($data = array()) {
		unset($data['action']);

		// Additional properties
		$properties = array();
		$meta = $this->modx->getFieldMeta('TicketComment');
		foreach ($data as $k => $v) {
			if (!isset($meta[$k])) {
				$properties[$k] = $this->modx->stripTags($v);
			}
		}
		// Create comment
		$comment = $this->modx->newObject('TicketComment', array(
			'text' => $this->Jevix($data['text'], 'Comment'),
			'createdon' => date('Y-m-d H:i:s'),
			'createdby' => $this->modx->user->id,
			'resource' => $this->config['resource'],
			'properties' => $properties,
			'mode' => 'preview',
		));
		$comment = $comment->toArray();

		/** @var modUser $user */
		if ($this->authenticated && $user = $this->modx->getObject('modUser', $this->modx->user->id)) {
			$comment['name'] = $this->modx->user->Profile->fullname;
			$comment['email'] = $this->modx->user->Profile->email;
			/** @var modUserProfile $profile */
			$profile = $this->modx->user->Profile;
			$comment = array_merge($profile->toArray(), $user->toArray(), $comment);
		}
		else {
			$comment['name'] = !empty($data['name'])
				? $data['name']
				: '';
			$comment['email'] = !empty($data['email'])
				? $data['email']
				: '';
		}
		$preview = $this->templateNode($comment, $this->config['tplCommentGuest']);
		$preview = preg_replace('/\[\[.*?\]\]/', '', $preview);

		return $this->success('', array('preview' => $preview));
	}


	/**
	 * Create or update Comment
	 *
	 * @param array $data section, pagetitle, comment, etc
	 *
	 * @return array
	 */
	public function saveComment($data = array()) {
		unset($data['action']);
		$data['raw'] = trim($data['text']);
		$data['text'] = $this->Jevix($data['text'], 'Comment');
		$data['allowGuest'] = !empty($this->config['allowGuest']);
		$data['allowGuestEdit'] = !empty($this->config['allowGuestEdit']);
		$data['requiredFields'] = $this->config['requiredFields'];
		$data['published'] = (!$this->authenticated && empty($this->config['autoPublishGuest'])) || ($this->authenticated && empty($this->config['autoPublish']))
			? false
			: true;
		if ($this->authenticated) {
			if (empty($data['name'])) {
				$data['name'] = $this->modx->user->Profile->get('fullname');
			}
			$data['email'] = $this->modx->user->Profile->get('email');
		}
		else {
			if (!empty($this->config['enableCaptcha'])) {
				if ($data['captcha'] != $_SESSION['TicketComments']['captcha']) {
					$captcha = $this->modx->lexicon('ticket_comment_captcha', $this->getCaptcha());
					return $this->error($this->modx->lexicon('ticket_comment_err_captcha'), array('captcha' => $captcha));
				}
			}
			$data['name'] = !empty($data['name'])
				? $data['name']
				: '';
			$data['email'] = !empty($data['email'])
				? $data['email']
				: '';
		}
		unset($data['rating'], $data['rating_plus'], $data['rating_minus']);

		if (!empty($data['id'])) {
			$response = $this->runProcessor('web/comment/update', $data);
		}
		else {
			$response = $this->runProcessor('web/comment/create', $data);
		}
		/** @var modProcessorResponse $response */
		if ($response->isError()) {
			$this->modx->log(modX::LOG_LEVEL_INFO, '[Tickets] Unable to save Comment: ' . $response->getMessage() . print_r($response->getFieldErrors(), 1));
			return $this->error($response->getMessage(), $response->getFieldErrors());
		}
		else {
			$comment = $response->getObject();
			$comment['mode'] = 'save';
			$comment['new_parent'] = $data['parent'];
			$comment['resource'] = $this->config['resource'];
			$comment['vote'] = $comment['star'] = '';

			/** @var modUser $user */
			if ($user = $this->modx->getObject('modUser', $comment['createdby'])) {
				/** @var modUserProfile $profile */
				$profile = $user->getOne('Profile');
				$comment = array_merge($profile->toArray(), $user->toArray(), $comment);
			}

			if (empty($data['id'])) {
				$this->sendCommentMails($comment);
			}

			$data = array();
			$data['captcha'] = empty($comment['createdby']) && !empty($this->config['enableCaptcha'])
				? $this->modx->lexicon('ticket_comment_captcha', $this->getCaptcha())
				: '';
			if ($comment['published']) {
				$this->modx->cacheManager->delete('tickets/latest.comments');
				$this->modx->cacheManager->delete('tickets/latest.tickets');
				$comment = $this->templateNode($comment, $this->config['tplCommentAuth']);
				$data['comment'] = preg_replace('/\[\[.*?\]\]/', '', $comment);
				return $this->success('', $data);
			}
			else {
				return $this->success($this->modx->lexicon('ticket_unpublished_comment'), $data);
			}
		}
	}


	/**
	 * Vote for comment
	 *
	 * @param $id
	 * @param int $value
	 *
	 * @return array|string
	 */
	public function voteComment($id, $value = 1) {
		$data = array('id' => $id, 'value' => $value);

		/** @var modProcessorResponse $response */
		if (!empty($id) && !empty($value)) {
			$response = $this->runProcessor('web/comment/vote', $data);
			if ($response->isError()) {
				return $this->error($response->getMessage());
			}
			else {
				$data = $response->getObject();
				$data['title'] = $this->modx->lexicon('ticket_rating_total')
					. " {$data['rating']}: ↑{$data['rating_plus']} "
					. $this->modx->lexicon('ticket_rating_and')
					. " ↓{$data['rating_minus']}";
				if ($data['rating'] > 0) {
					$data['rating'] = '+' . $data['rating'];
					$data['status'] = 1;
				}
				elseif ($data['rating'] < 0) {
					$data['status'] = -1;
				}
				else {
					$data['status'] = 0;
				}
				return $this->success('', $data);
			}
		}

		return $this->error('tickets_err_unknown');
	}


	/**
	 * Star for comment
	 *
	 * @param $id
	 *
	 * @return array|string
	 */
	public function starComment($id) {
		$data = array('id' => $id);
		/** @var modProcessorResponse $response */
		if (!empty($id)) {
			$response = $this->runProcessor('web/comment/star', $data);
			if ($response->isError()) {
				return $this->error($response->getMessage());
			}
			else {
				$data = $response->getObject();
				return $this->success('', $data);
			}
		}

		return $this->error('tickets_err_unknown');
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

		$comment = $response->getObject();
		$time = time() - strtotime($comment['createdon']);
		$time_limit = $this->config['commentEditTime'];

		if ($this->authenticated && $this->modx->user->id != $comment['createdby']) {
			return $this->error($this->modx->lexicon('ticket_comment_err_wrong_user'));
		}
		elseif (!$this->authenticated) {
			if (!$this->config['allowGuest'] || !$this->config['allowGuestEdit']) {
				return $this->error($this->modx->lexicon('ticket_comment_err_guest_edit'));
			}
			elseif (!isset($_SESSION['TicketComments']['ids'][$id])) {
				return $this->error($this->modx->lexicon('ticket_comment_err_wrong_user'));
			}
		}
		elseif ($this->modx->getCount('TicketComment', array('parent' => $comment['id']))) {
			return $this->error($this->modx->lexicon('ticket_comment_err_has_replies'));
		}
		elseif ($time >= $time_limit) {
			return $this->error($this->modx->lexicon('ticket_comment_err_no_time'));
		}

		$data = array(
			'raw' => $comment['raw'],
			'time' => $time_limit - $time,
		);
		if (empty($comment['createdby'])) {
			$data['name'] = $comment['name'];
			$data['email'] = $comment['email'];
		}

		return $this->success('', $data);
	}


	/**
	 * Return unseen comments of thread for user
	 *
	 * @param $name
	 *
	 * @return array
	 */
	public function getNewComments($name) {
		if (!$this->authenticated) {
			return $this->error($this->modx->lexicon('access_denied'));
		}
		elseif ($thread = $this->modx->getObject('TicketThread', array('name' => $name))) {
			if ($this->authenticated && $view = $this->modx->getObject('TicketView', array('uid' => $this->modx->user->id, 'parent' => $thread->get('resource')))) {
				$date = $view->get('timestamp');
				$q = $this->modx->newQuery('TicketComment');
				$q->leftJoin('modUser', 'User', '`User`.`id` = `TicketComment`.`createdby`');
				$q->leftJoin('modUserProfile', 'Profile', '`Profile`.`internalKey` = `TicketComment`.`createdby`');
				$q->where(array(
					'`TicketComment`.`published`' => 1,
					'`TicketComment`.`thread`' => $thread->id,
					'`TicketComment`.`createdby`:!=' => $this->modx->user->id
				));
				$q->andCondition(array(
					'`TicketComment`.`createdon`:>' => $date,
					'OR:`TicketComment`.`editedon`:>' => $date
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
		if (empty($text)) {
			return ' ';
		}
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
		$params['input'] = str_replace(
			array('[', ']', '{', '}'),
			array('*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'),
			$text
		);

		$snippet->setCacheable(false);
		$filtered = $snippet->process($params);

		if ($replaceTags) {
			$filtered = str_replace(
				array('*(*(*(*(*(*', '*)*)*)*)*)*', '`', '~(~(~(~(~(~', '~)~)~)~)~)~'),
				array('&#91;', '&#93;', '&#96;', '&#123;', '&#125;'),
				$filtered
			);
		}
		else {
			$filtered = str_replace(
				array('*(*(*(*(*(*', '*)*)*)*)*)*', '~(~(~(~(~(~', '~)~)~)~)~)~'),
				array('[', ']', '{', '}'),
				$filtered
			);
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
		if (is_array($string)) {
			foreach ($string as $key => $value) {
				$string[$key] = $this->sanitizeString($value);
			}
			return $string;
		}

		$string = htmlentities(trim($string), ENT_QUOTES, "UTF-8");
		$string = preg_replace('/^@.*\b/', '', $string);
		$string = str_replace(
			array('[', ']', '`', '{', '}'),
			array('&#91;', '&#93;', '&#96;', '&#123;', '&#125;'),
			$string
		);

		return $string;
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
		$node['has_parent'] = !empty($node['parent']);

		// Handling rating
		if (!$this->authenticated || $this->modx->user->id == $node['createdby']) {
			$node['cant_vote'] = 1;
		}
		elseif (array_key_exists('vote', $node)) {
			if (empty($node['vote'])) {
				$node['can_vote'] = 1;
			}
			elseif ($node['vote'] > 0) {
				$node['voted_plus'] = 1;
				$node['cant_vote'] = 1;
			}
			elseif ($node['vote'] < 0) {
				$node['voted_minus'] = 1;
				$node['cant_vote'] = 1;
			}
		}
		if ($node['rating'] > 0) {
			$node['rating'] = '+' . $node['rating'];
			$node['rating_positive'] = 1;
			$node['bad'] = '';
		}
		elseif ($node['rating'] < 0) {
			$node['rating_negative'] = 1;
			$node['bad'] = $node['rating'] >= -5
				? ' bad bad' . abs($node['rating'])
				: ' bad bad5';
		}
		else {
			$node['bad'] = '';
		}

		// Handling stars
		if ($this->authenticated && array_key_exists('star', $node)) {
			$node['can_star'] = 1;
			$node['stared'] = !empty($node['star']);
			$node['unstared'] = empty($node['star']);
		}

		// Checking comment novelty
		if (isset($node['resource']) && $this->last_view === 0) {
			if ($this->authenticated && $view = $this->modx->getObject('TicketView', array('parent' => $node['resource'], 'uid' => $this->modx->user->id))) {
				$this->last_view = strtotime($view->get('timestamp'));
			}
			else {
				$this->last_view = -1;
			}
		}

		// Processing comment and selecting needed template
		$node = $this->prepareComment($node);
		if (empty($tpl)) {
			$tpl = $this->authenticated || !empty($this->config['allowGuest'])
				? $this->config['tplCommentAuth']
				: $this->config['tplCommentGuest'];
		}
		if ($node['deleted']) {
			$tpl = $this->config['tplCommentDeleted'];
		}
		// Special processing for guests
		if (!empty($node['user_email'])) {
			$node['email'] = $node['user_email'];
		}
		unset($node['user_email']);
		if (empty($node['fullname']) && !empty($node['name'])) {
			$node['fullname'] = $node['name'];
		}
		$node['guest'] = empty($node['createdby']);
		// --

		if (!empty($children) || !empty($node['has_children'])) {
			$node['children'] = $children;
			$node['comment_edit_link'] = false;
		}
		elseif ((time() - strtotime($node['createdon']) <= $this->config['commentEditTime'])) {
			if ($this->modx->user->id && $node['createdby'] == $this->modx->user->id) {
				$node['comment_edit_link'] = true;
			}
			elseif ($this->config['allowGuest'] && $this->config['allowGuestEdit']) {
				if (isset($_SESSION['TicketComments']['ids'][$node['id']])) {
					$node['comment_edit_link'] = true;
				}
			}
			$node['children'] = '';
		}
		else {
			$node['children'] = '';
		}
		$node['comment_was_edited'] = (bool)$node['editedon'];
		$node['comment_new'] = $this->authenticated && $node['createdby'] != $this->modx->user->id && $this->last_view > 0 && strtotime($node['createdon']) > $this->last_view;

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
			$data['gravatar'] = $this->config['gravatarUrl'] . md5(strtolower($data['email'])) . '?s=' . $this->config['gravatarSize'] . '&d=' . $this->config['gravatarIcon'];
			$data['avatar'] = !empty($data['photo'])
				? $data['photo']
				: $data['gravatar'];
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
	 * @param array $ticket
	 *
	 * @return void
	 */
	public function sendTicketMails($ticket = array()) {
		// We need only the first publication of ticket
		if (empty($ticket['published']) || $ticket['createdon'] != $ticket['publishedon']) {
			return;
		}
		elseif (($ticket['editedon'] != 0 && $ticket['editedon'] != $ticket['createdon'])) {
			return;
		}

		/** @var TicketsSection $section */
		if ($section = $this->modx->getObject('TicketsSection', $ticket['parent'], false)) {
			$properties = $section->get('properties');
			$subscribers = !empty($properties['subscribers'])
				? $properties['subscribers']
				: array();
			$ticket = array_merge($ticket, $section->toArray('section.'));
		}
		/** @var modUser $user */
		if ($user = $this->modx->getObject('modUser', $ticket['createdby'])) {
			if ($profile = $user->getOne('Profile')) {
				$ticket = array_merge($ticket, array_merge($profile->toArray('user.'), $user->toArray('user.')));
			}
		}

		// Send notifications to admin
		$sent = array();
		if ($this->modx->getOption('tickets.mail_bcc_level') >= 1) {
			if ($bcc = $this->modx->getOption('tickets.mail_bcc')) {
				$bcc = array_map('trim', explode(',', $bcc));
				if (!empty($bcc)) {
					foreach ($bcc as $uid) {
						if ($uid == $ticket['createdby']) {
							continue;
						}
						$this->addQueue(
							$uid,
							$this->modx->lexicon('ticket_email_bcc', $ticket),
							$this->getChunk($this->config['tplTicketEmailBcc'], $ticket, false)
						);
						$sent[] = $uid;
					}
				}
			}
		}

		// Then we send emails to subscribers
		if (!empty($subscribers)) {
			foreach ($subscribers as $uid) {
				if (in_array($uid, $sent) || $ticket['createdby'] == $uid) {
					continue;
				}
				else {
					$this->addQueue(
						$uid,
						$this->modx->lexicon('tickets_section_email_subscription', $ticket),
						$this->getChunk($this->config['tplTicketEmailSubscription'], $ticket, false)
					);
				}
			}
		}
	}


	/**
	 * Email notifications about new comment
	 *
	 * @param array $comment
	 *
	 * @return void
	 */
	public function sendCommentMails($comment = array()) {
		$owner_uid = $reply_uid = $reply_email = null;
		$subscribers = array();
		$q = $this->modx->newQuery('TicketThread');
		$q->leftJoin('modResource', 'modResource', 'TicketThread.resource = modResource.id');
		$q->select('modResource.createdby as uid, modResource.id as resource, modResource.pagetitle, TicketThread.subscribers');
		$q->where(array('TicketThread.id' => $comment['thread']));
		if ($q->prepare() && $q->stmt->execute()) {
			$res = $q->stmt->fetch(PDO::FETCH_ASSOC);
			if (!empty($res)) {
				$comment = array_merge($comment, array(
					'resource' => $res['resource'],
					'pagetitle' => $res['pagetitle'],
					'author' => $res['uid'],
				));
				$owner_uid = $res['uid'];
				$subscribers = $this->modx->fromJSON($res['subscribers']);
			}
		}

		$comment = $this->prepareComment($comment);
		$sent = array();

		// It is a reply for a comment
		if ($comment['parent']) {
			$q = $this->modx->newQuery('TicketComment');
			$q->select('TicketComment.createdby as uid, TicketComment.text, TicketComment.email');
			$q->where(array('TicketComment.id' => $comment['parent']));
			if ($q->prepare() && $q->stmt->execute()) {
				if ($res = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
					$reply_uid = $res['uid'];
					$reply_email = $res['email'];
					$comment['parent_text'] = $res['text'];
				}
			}
		}

		$published = !empty($comment['published']) && !array_key_exists('was_published', $comment['properties']);
		$comment['manager_url'] = trim($this->modx->getOption('site_url'), '/') . MODX_MANAGER_URL . '?a=home&namespace=tickets';

		if ($published) {
			// We always send replies for comments
			if (($reply_uid && $reply_uid != $comment['createdby']) || ($reply_email && $reply_email != $comment['email'])) {
				$this->addQueue(
					$reply_uid,
					$this->modx->lexicon('ticket_comment_email_reply', $comment),
					$this->getChunk($this->config['tplCommentEmailReply'], $comment, false),
					$reply_email
				);
				$sent[] = $reply_uid;
			}
		}

		// Then we make blind copy to administrators
		if ($this->modx->getOption('tickets.mail_bcc_level') >= 2 || !$published) {
			if ($bcc = $this->modx->getOption('tickets.mail_bcc')) {
				$bcc = array_map('trim', explode(',', $bcc));
				foreach ($bcc as $uid) {
					if ($published && (in_array($uid, $sent) || $uid == $owner_uid || $uid == $comment['createdby'])) {
						continue;
					}
					$this->addQueue(
						$uid,
						!$published
							? $this->modx->lexicon('ticket_comment_email_unpublished_bcc', $comment)
							: $this->modx->lexicon('ticket_comment_email_bcc', $comment),
						!$published
							? $this->getChunk($this->config['tplCommentEmailUnpublished'], $comment, false)
							: $this->getChunk($this->config['tplCommentEmailBcc'], $comment, false)
					);
					$sent[] = $uid;
				}
			}
		}

		if ($published) {
			if (!empty($subscribers)) {
				// And send emails to subscribers
				foreach ($subscribers as $uid) {
					if (in_array($uid, $sent) || $uid == $comment['createdby']) {
						continue;
					}
					elseif ($uid == $owner_uid) {
						$this->addQueue(
							$uid,
							$this->modx->lexicon('ticket_comment_email_owner', $comment),
							$this->getChunk($this->config['tplCommentEmailOwner'], $comment, false)
						);
					}
					else {
						$this->addQueue(
							$uid,
							$this->modx->lexicon('ticket_comment_email_subscription', $comment),
							$this->getChunk($this->config['tplCommentEmailSubscription'], $comment, false)
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
	 * @param $email
	 *
	 * @return bool|string
	 */
	public function addQueue($uid, $subject, $body, $email = '') {
		$uid = (int)$uid;
		$email = trim($email);

		if (empty($uid) && (empty($this->config['allowGuestEmails']) || empty($email))) {
			return false;
		}

		/* @var TicketQueue $queue */
		$queue = $this->modx->newObject('TicketQueue', array(
				'uid' => $uid,
				'subject' => $subject,
				'body' => $body,
				'email' => $email,
			)
		);

		return $this->modx->getOption('tickets.mail_queue', null, false, true)
			? $queue->save()
			: $queue->Send();
	}


	/** @deprecated */
	public function subscribe($name) {
		return $this->subscribeThread($name);
	}


	/**
	 * This method subscribe or unsubscribe users for notifications about new comments in thread.
	 *
	 * @param string $name Name of tickets thread for subscribe or unsubscribe
	 *
	 * @return array
	 */
	public function subscribeThread($name) {
		if (!$this->authenticated) {
			return $this->error('ticket_err_access_denied');
		}
		/* @var TicketThread $thread */
		if ($thread = $this->modx->getObject('TicketThread', array('name' => $name))) {
			$message = $thread->Subscribe()
				? 'ticket_thread_subscribed'
				: 'ticket_thread_unsubscribed';
			return $this->success($this->modx->lexicon($message));
		}
		else {
			return $this->error($this->modx->lexicon('ticket_err_wrong_thread'));
		}
	}


	/**
	 * This method subscribe or unsubscribe users for notifications about new tickets in section.
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function subscribeSection($id) {
		if (!$this->authenticated) {
			return $this->error('ticket_err_access_denied');
		}
		/* @var TicketsSection $section */
		if ($section = $this->modx->getObject('TicketsSection', array('id' => $id, 'class_key' => 'TicketsSection'))) {
			$message = $section->Subscribe()
				? 'tickets_section_subscribed'
				: 'tickets_section_unsubscribed';
			return $this->success($this->modx->lexicon($message));
		}
		else {
			return $this->error($this->modx->lexicon('ticket_err_wrong_section'));
		}
	}


	/**
	 * Loads an instance of pdoTools
	 *
	 * @return boolean
	 */
	public function loadPdoTools() {
		if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
			$this->pdoTools = $this->modx->getService('pdoFetch');
			$this->pdoTools->setConfig($this->config);
		}
		return !empty($this->pdoTools) && $this->pdoTools instanceof pdoTools;
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
	 *
	 * @param string $date Timestamp to format
	 * @param string $dateFormat
	 *
	 * @return string
	 */
	public function dateFormat($date, $dateFormat = null) {
		$date = preg_match('/^\d+$/', $date)
			? $date
			: strtotime($date);
		$dateFormat = !empty($dateFormat)
			? $dateFormat
			: $this->config['dateFormat'];
		$current = time();
		$delta = $current - $date;

		if ($this->config['dateNow']) {
			if ($delta < $this->config['dateNow']) {
				return $this->modx->lexicon('ticket_date_now');
			}
		}

		if ($this->config['dateMinutes']) {
			$minutes = round(($delta) / 60);
			if ($minutes < $this->config['dateMinutes']) {
				if ($minutes > 0) {
					return $this->declension($minutes, $this->modx->lexicon('ticket_date_minutes_back', array('minutes' => $minutes)));
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
					return $this->declension($hours, $this->modx->lexicon('ticket_date_hours_back', array('hours' => $hours)));
				}
				else {
					return $this->modx->lexicon('ticket_date_hours_back_less');
				}
			}
		}

		if ($this->config['dateDay']) {
			switch (date('Y-m-d', $date)) {
				case date('Y-m-d'):
					$day = $this->modx->lexicon('ticket_date_today');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))):
					$day = $this->modx->lexicon('ticket_date_yesterday');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))):
					$day = $this->modx->lexicon('ticket_date_tomorrow');
					break;
				default:
					$day = null;
			}
			if ($day) {
				$format = str_replace("day", preg_replace("#(\w{1})#", '\\\${1}', $day), $this->config['dateDay']);
				return date($format, $date);
			}
		}

		$m = date("n", $date);
		$month_arr = $this->modx->fromJSON($this->modx->lexicon('ticket_date_months'));
		$month = $month_arr[$m - 1];

		$format = preg_replace("~(?<!\\\\)F~U", preg_replace('~(\w{1})~u', '\\\${1}', $month), $dateFormat);

		return date($format, $date);
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
			$lang = $this->modx->getOption('cultureKey', null, 'en');
		}
		$forms = $this->modx->fromJSON($forms);

		if ($lang == 'ru') {
			$mod100 = $count % 100;
			switch ($count % 10) {
				case 1:
					if ($mod100 == 11) {
						$text = $forms[2];
					}
					else {
						$text = $forms[0];
					}
					break;
				case 2:
				case 3:
				case 4:
					if (($mod100 > 10) && ($mod100 < 20)) {
						$text = $forms[2];
					}
					else {
						$text = $forms[1];
					}
					break;
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 0:
				default:
					$text = $forms[2];
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
		$key = 'Tickets_User';

		if (!$this->authenticated) {
			if (!$this->modx->getOption('tickets.count_guests', false)) {
				return;
			}
			$guest_key = $_SESSION[$key];
		}
		else {
			if (!empty($_SESSION[$key])) {
				$table = $this->modx->getTableName('TicketView');
				$this->modx->exec("DELETE FROM {$table} WHERE `uid` = 0 AND `guest_key` = '{$_SESSION[$key]}' AND `parent` = {$resource};");
			}
			$guest_key = '';
		}

		$key = array(
			'uid' => $this->modx->user->get('id'),
			'guest_key' => $guest_key,
			'parent' => $resource,
		);
		if (!$view = $this->modx->getObject('TicketView', $key)) {
			$view = $this->modx->newObject('TicketView');
			$view->fromArray($key, '', true, true);
		}
		$view->set('timestamp', date('Y-m-d H:i:s'));
		$view->save();
	}


	/**
	 * Generate captcha and set it to session
	 *
	 * @return array
	 */
	public function getCaptcha() {
		$min = !empty($this->config['minCaptcha'])
			? (int)$this->config['minCaptcha']
			: 1;
		$max = !empty($this->config['maxCaptcha'])
			? (int)$this->config['maxCaptcha']
			: 10;
		$a = mt_rand($min, $max);
		$b = mt_rand($min, $max);
		$_SESSION['TicketComments']['captcha'] = $a + $b;

		return array('a' => $a, 'b' => $b);
	}


	/**
	 * Upload file for ticket
	 *
	 * @param $data
	 * @param string $class
	 *
	 * @return array|string
	 */
	public function fileUpload($data, $class = 'Ticket') {
		if (!$this->authenticated || empty($this->config['allowFiles'])) {
			return $this->error('ticket_err_access_denied');
		}

		$data['source'] = $this->config['source'];
		$data['class'] = $class;

		/** @var modProcessorResponse $response */
		$response = $this->runProcessor('web/file/upload', $data);
		if ($response->isError()) {
			return $this->error($response->getMessage());
		}
		$file = $response->getObject();
		$file['size'] = round($file['size'] / 1024, 2);
		$file['new'] = empty($file['new']);

		$tpl = $file['type'] == 'image'
			? $this->config['tplImage']
			: $this->config['tplFile'];
		$html = $this->getChunk($tpl, $file);

		return $this->success('', $html);
	}


	/**
	 * Delete or restore uploaded file
	 *
	 * @param $id
	 *
	 * @return array|string
	 */
	public function fileDelete($id) {
		if (!$this->authenticated || empty($this->config['allowFiles'])) {
			return $this->error('ticket_err_access_denied');
		}
		/** @var modProcessorResponse $response */
		$response = $this->runProcessor('web/file/delete', array('id' => $id));
		if ($response->isError()) {
			return $this->error($response->getMessage());
		}

		return $this->success();
	}


	/**
	 * This method returns an error of the cart
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data Additional data
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 */
	public function error($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => false,
			'message' => $this->modx->lexicon($message, $placeholders),
			'data' => $data,
		);

		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}


	/**
	 * This method returns an success of the cart
	 *
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 *
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => true,
			'message' => $this->modx->lexicon($message, $placeholders),
			'data' => $data,
		);

		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}


	/**
	 * Compares MODX version
	 *
	 * @param string $version
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function systemVersion($version = '2.3.0', $dir = '>=') {
		$this->modx->getVersionData();

		return !empty($this->modx->version) && version_compare($this->modx->version['full_version'], $version, $dir);
	}


	/**
	 * @param modManagerController $controller
	 * @param array $properties
	 */
	public function loadManagerFiles(modManagerController $controller, array $properties = array()) {
		$modx23 = (int)$this->systemVersion();
		$ticketsAssetsUrl = $this->config['assetsUrl'];
		$connectorUrl = $this->config['connectorUrl'];
		$ticketsCssUrl = $this->config['cssUrl'] . 'mgr/';
		$ticketsJsUrl = $this->config['jsUrl'] . 'mgr/';

		if (!empty($properties['config'])) {
			$tmp = array(
				'assets_js' => $ticketsAssetsUrl,
				'connector_url' => $connectorUrl,
			);
			$controller->addHtml('<script type="text/javascript">MODx.modx23 = ' . $modx23 . ';Tickets.config = ' . $this->modx->toJSON($tmp) . ';</script>', true);
		}
		if (!empty($properties['utils'])) {
			$controller->addJavascript($ticketsJsUrl . 'tickets.js');
			$controller->addLastJavascript($ticketsJsUrl . 'misc/utils.js');
			$controller->addLastJavascript($ticketsJsUrl . 'misc/combos.js');
		}
		if (!empty($properties['css'])) {
			$controller->addCss($ticketsCssUrl . 'tickets.css');
			$controller->addCss($ticketsCssUrl . 'bootstrap.buttons.css');
			if (!$modx23) {
				$controller->addCss($ticketsCssUrl . 'font-awesome.min.css');
			}
		}

		if (!empty($properties['section'])) {
			$controller->addLastJavascript($ticketsJsUrl . 'section/section.common.js');
			$controller->addLastJavascript($ticketsJsUrl . 'ticket/tickets.panel.js');
			$controller->addLastJavascript($ticketsJsUrl . 'ticket/tickets.grid.js');
		}
		if (!empty($properties['ticket'])) {
			$controller->addLastJavascript($ticketsJsUrl . 'ticket/ticket.common.js');
		}
		if (!empty($properties['tickets'])) {
			$controller->addLastJavascript($ticketsJsUrl . 'ticket/tickets.panel.js');
			$controller->addLastJavascript($ticketsJsUrl . 'ticket/tickets.grid.js');
		}
		if (!empty($properties['threads'])) {
			$controller->addLastJavascript($ticketsJsUrl . 'thread/threads.panel.js');
			$controller->addLastJavascript($ticketsJsUrl . 'thread/threads.grid.js');
			$controller->addLastJavascript($ticketsJsUrl . 'thread/thread.window.js');
			$controller->addLastJavascript($ticketsJsUrl . 'comment/comments.grid.js');
			$controller->addLastJavascript($ticketsJsUrl . 'comment/comment.window.js');
		}
		if (!empty($properties['comments'])) {
			$controller->addLastJavascript($ticketsJsUrl . 'comment/comments.panel.js');
			$controller->addLastJavascript($ticketsJsUrl . 'comment/comments.grid.js');
			$controller->addLastJavascript($ticketsJsUrl . 'comment/comment.window.js');
		}
		if (!empty($properties['authors'])) {
			$controller->addLastJavascript($ticketsJsUrl . 'author/authors.panel.js');
			$controller->addLastJavascript($ticketsJsUrl . 'author/authors.grid.js');
		}
	}

}
