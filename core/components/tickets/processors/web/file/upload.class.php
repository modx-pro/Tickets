<?php

class TicketFileUploadProcessor extends modObjectProcessor {
	public $classKey = 'TicketFile';
	public $languageTopics = array('tickets:default');
	public $permission = 'ticket_file_upload';
	/** @var modMediaSource $mediaSource */
	public $mediaSource;
	/** @var Ticket $ticket */
	private $ticket = 0;
	private $class = 'Ticket';



	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}

		$tid = $this->getProperty('tid');
		if (!$this->ticket = $this->modx->getObject('Ticket', $tid)) {
			$this->ticket = $this->modx->newObject('Ticket');
			$this->ticket->set('id', 0);
		}

		if ($source = $this->getProperty('source')) {
			/** @var modMediaSource $mediaSource */
			$mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
			$mediaSource->set('ctx', $this->modx->context->key);
			if ($mediaSource->initialize()) {
				$this->mediaSource = $mediaSource;
			}
		}

		if (!$this->mediaSource) {
			return $this->modx->lexicon('ticket_err_source_initialize');
		}

		$this->class = $this->getProperty('class', 'Ticket');
		return true;
	}



	public function process() {
		if (!$data = $this->handleFile()) {
			return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
		}

		$properties = $this->mediaSource->getPropertyList();
		$tmp = explode('.', $data['name']);
		$extension = strtolower(end($tmp));

		$image_extensions = $allowed_extensions = array();
		if (!empty($properties['imageExtensions'])) {
			$image_extensions = array_map('trim', explode(',', strtolower($properties['imageExtensions'])));
		}
		if (!empty($properties['allowedFileTypes'])) {
			$allowed_extensions = array_map('trim', explode(',', strtolower($properties['allowedFileTypes'])));
		}
		if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
			return $this->failure($this->modx->lexicon('ticket_err_file_ext'));
		}
		elseif (in_array($extension, $image_extensions)) {
			$type = 'image';
		}
		else {
			$type = $extension;
		}
		$hash = sha1($data['stream']);

		$path = '0/';
		$filename = !empty($properties['imageNameType']) && $properties['imageNameType'] == 'friendly'
			? $this->ticket->cleanAlias($data['name'])
			: $hash . '.' . $extension;
		if (strpos($filename, '.' . $extension) === false) {
			$filename .= '.' . $extension;
		}
		// Check for existing file
		$where = $this->modx->newQuery($this->classKey, array('class' => $this->class));
		if (!empty($this->ticket->id)) {
			$where->andCondition(array('parent:IN' => array(0, $this->ticket->id)));
		}
		else {
			$where->andCondition(array('parent' => 0));
		}
		$where->andCondition(array('file' => $filename, 'OR:hash:=' => $hash), null, 1);
		if ($this->modx->getCount($this->classKey, $where)) {
			return $this->failure($this->modx->lexicon('ticket_err_file_exists', array('file' => $data['name'])));
		}

		/* @var TicketFile $ticket_file */
		$ticket_file = $this->modx->newObject('TicketFile', array(
			'parent' => 0,
			'name' => $data['name'],
			'file' => $filename,
			'path' => $path,
			'source' => $this->mediaSource->id,
			'type' => $type,
			'createdon' => date('Y-m-d H:i:s'),
			'createdby' => $this->modx->user->id,
			'deleted' => 0,
			'hash' => $hash,
			'size' => $data['size'],
			'class' => $this->class,
			'properties' => $data['properties'],
		));

		$this->mediaSource->createContainer($ticket_file->path, '/');
		unset($this->mediaSource->errors['file']);
		$file = $this->mediaSource->createObject(
			$ticket_file->get('path')
			, $ticket_file->get('file')
			, $data['stream']
		);

		if ($file) {
			$url = $this->mediaSource->getObjectUrl($ticket_file->get('path') . $ticket_file->get('file'));
			$ticket_file->set('url', $url);
			$ticket_file->save();

			$ticket_file->generateThumbnail($this->mediaSource);
			return $this->success('', $ticket_file->toArray());
		}
		else {
			$this->modx->log(modX::LOG_LEVEL_ERROR, '[Tickets] Could not save file: ' . print_r($this->mediaSource->getErrors(), 1));
			return $this->failure($this->modx->lexicon('ticket_err_file_save'));
		}
	}


	/**
	 * @return array|bool
	 */
	public function handleFile() {
		$stream = $name = null;

		$contentType = isset($_SERVER["HTTP_CONTENT_TYPE"])
			? $_SERVER["HTTP_CONTENT_TYPE"]
			: $_SERVER["CONTENT_TYPE"];

		$file = $this->getProperty('file');
		if (!empty($file) && file_exists($file)) {
			$tmp = explode('/', $file);
			$name = end($tmp);
			$stream = file_get_contents($file);
		}
		elseif (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				$name = $_FILES['file']['name'];
				$stream = file_get_contents($_FILES['file']['tmp_name']);
			}
		}
		else {
			$name = $this->getProperty('name', @$_REQUEST['name']);
			$stream = file_get_contents('php://input');
		}

		if (!empty($stream)) {
			$data = array(
				'name' => $name,
				'stream' => $stream,
				'size' => strlen($stream),
			);

			$tf = tempnam(MODX_BASE_PATH, 'tkt_');
			file_put_contents($tf, $stream);
			$tmp = getimagesize($tf);
			if (is_array($tmp)) {
				$data['properties'] = array(
					'width' => $tmp[0],
					'height' => $tmp[1],
					'bits' => $tmp['bits'],
					'mime' => $tmp['mime'],
				);
			}
			unlink($tf);
			return $data;
		}
		else {
			return false;
		}
	}

}

return 'TicketFileUploadProcessor';