<?php
class TicketFile extends xPDOSimpleObject {
	public $file;
	/* @var modPhpThumb $phpThumb */
	public $phpThumb;
	/* @var modMediaSource $mediaSource */
	public $mediaSource;


	/**
	 * @param modMediaSource $mediaSource
	 *
	 * @return bool|string
	 */
	public function prepareSource(modMediaSource $mediaSource = null) {
		if ($mediaSource) {
			$this->mediaSource = $mediaSource;
		}
		elseif (empty($this->mediaSource) && $source = $this->get('source')) {
			/** @var modMediaSource $mediaSource */
			if ($mediaSource = $this->xpdo->getObject('sources.modMediaSource', $source)) {
				$mediaSource->set('ctx', $this->xpdo->context->key);
				$mediaSource->initialize();
				$this->mediaSource = $mediaSource;
			}
			else {
				return 'Could not initialize media source with id = '.$source;
			}
		}

		return !empty($this->mediaSource) && $this->mediaSource instanceof modMediaSource;
	}


	public function getSourceProperties() {
		$properties = $this->mediaSource->getPropertyList();
		$thumbnails = array();
		if (array_key_exists('thumbnail', $properties) && !empty($properties['thumbnail'])) {
			$thumbnails = $this->xpdo->fromJSON($properties['thumbnail']);
		}
		if (empty($thumbnails)) {
			$thumbnails = array(
				'thumb' => array(
					'w' => 120,
					'h' => 90,
					'q' => 90,
					'zc' => 'T',
					'bg' => '000000',
				)
			);
		}
		if (!is_array(current($thumbnails))) {
			$thumbnails = array('thumb' => $thumbnails);
		}
		foreach ($thumbnails as &$set) {
			if (empty($thumbnails['f'])) {
				$set['f'] = !empty($properties['thumbnailType'])
					? $properties['thumbnailType']
					: 'jpg';
			}
		}

		return $thumbnails;
	}

	/**
	 * @param modMediaSource $mediaSource
	 *
	 * @return bool|string
	 */
	public function generateThumbnail(modMediaSource $mediaSource = null) {
		if ($this->get('type') != 'image') {return true;}

		$prepare = $this->prepareSource($mediaSource);
		if ($prepare !== true) {return $prepare;}

		$this->file = $this->mediaSource->getObjectContents($this->get('path').$this->get('file'));
		if (!empty($this->mediaSource->errors['file'])) {
			return 'Could not retrieve file "'.$this->path.$this->file.'" from media source. '.$this->mediaSource->errors['file'];
		}

		$properties = $this->getSourceProperties();
		foreach ($properties as $name => $set) {
			if ($image = $this->makeThumbnail($set)) {
				$this->saveThumbnail($image, $set['f'], $name);
			}
		}

		return true;
	}


	/**
	 * @param array $options
	 *
	 * @return bool|null
	 */
	public function makeThumbnail($options = array()) {
		require_once  MODX_CORE_PATH . 'model/phpthumb/modphpthumb.class.php';
		$phpThumb = new modPhpThumb($this->xpdo);
		$phpThumb->initialize();

		$tf = tempnam(MODX_BASE_PATH, 'ms_');
		file_put_contents($tf, $this->file['content']);
		$phpThumb->setSourceFilename($tf);

		foreach ($options as $k => $v) {
			$phpThumb->setParameter($k, $v);
		}

		if ($phpThumb->GenerateThumbnail()) {
			ImageInterlace($phpThumb->gdimg_output, true);
			if ($phpThumb->RenderOutput()) {
				@unlink($phpThumb->sourceFilename);
				@unlink($tf);
				return $phpThumb->outputImageData;
			}
		}
		else {
			$this->xpdo->log(xpdo::LOG_LEVEL_ERROR, 'Could not generate thumbnail for "'.$this->get('url').'". '.print_r($phpThumb->debugmessages,1));
		}
		return false;
	}


	/**
	 * @param $raw_image
	 * @param string $ext
	 * @param string $name
	 *
	 * @return bool
	 */
	public function saveThumbnail($raw_image, $ext = 'jpg', $name = 'thumb') {
		$path = $this->get('path');
		$filename = preg_replace('/\.[a-z]+$/i', '', $this->get('file')) . '_' . $name . '.' . $ext;
		if ($file = $this->mediaSource->createObject($path, $filename, $raw_image)) {
			$url = $this->mediaSource->getObjectUrl($path.$filename);
			// Add thumbs
			$thumbs = $this->get('thumbs');
			if (!is_array($thumbs)) {
				$thumbs = array();
			}
			$thumbs[$name] = $url;
			$this->set('thumbs', $thumbs);
			// Main thumb
			if ($name == 'thumb') {
				$this->set('thumb', $url);
			}

			return $this->save();
		}
		return false;
	}


	public function save($cacheFlag= null) {
		if ($this->isDirty('parent')) {
			if ($this->prepareSource()) {
				$old_path = $this->get('path');
				$file = $this->get('file');
				$new_path = $this->get('parent') . '/';

				$this->mediaSource->createContainer($new_path, '/');
				if ($this->mediaSource->moveObject($old_path.$file, $new_path)) {
					$this->set('path', $new_path);
					$this->set('url', $this->mediaSource->getObjectUrl($new_path.$file));
				}
				if (!$thumbs = $this->get('thumbs')) {
					$thumbs = array('thumb' => $this->get('thumb'));
				}
				foreach ($thumbs as $key => $thumb) {
					if (empty($thumb)) {continue;}
					$tmp = explode('/', $thumb);
					$thumb = end($tmp);
					if ($this->mediaSource->moveObject($old_path.$thumb, $new_path)) {
						$thumbs[$key] = $this->mediaSource->getObjectUrl($new_path.$thumb);
						if ($key == 'thumb') {
							$this->set('thumb', $this->mediaSource->getObjectUrl($new_path.$thumb));
						}
					}
				}
				$this->set('thumbs', $thumbs);
			}
		}

		return parent::save($cacheFlag);
	}

	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors= array ()) {
		if ($this->prepareSource() === true) {
			if ($this->mediaSource->removeObject($this->get('path').$this->get('file'))) {
				if (!$thumbs = $this->get('thumbs')) {
					$thumbs = array('thumb' => $this->get('thumb'));
				}
				foreach ($thumbs as $thumb) {
					if (empty($thumb)) {continue;}
					$tmp = explode('/', $thumb);
					$filename = end($tmp);
					$this->mediaSource->removeObject($this->path . $filename);
				}
			}
			else {
				$this->xpdo->log(xPDO::LOG_LEVEL_ERROR,
					'Could not remove file at "'.$this->get('path').$this->get('file').'": '.$this->mediaSource->errors['file']
				);
			}
		}
		return parent::remove($ancestors);
	}

}