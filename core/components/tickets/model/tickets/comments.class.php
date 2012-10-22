<?php
require MODX_CORE_PATH . 'components/quip/model/quip/quip.class.php';

class Comments extends Quip {

	public $controller;

	function __construct(modX &$modx,array $config = array()) {
		parent::__construct($modx, $config);

		$ticketsPath = $this->modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/');

		$this->config = array_merge($this->config, array(
			'ticketsPath' => $ticketsPath
			,'processorsPath' => $ticketsPath.'processors/'
			,'allowedTags' => '<br><b><i>'
		), $config);
	}

	public function loadController($controller) {
		if ($this->modx->loadClass('quipController',$this->config['corePath'].'model/quip/request/',true,true)) {
			$classPath = $this->config['ticketsPath'].'controllers/comment/'.$controller.'.php';
			$className = 'Comment'.$controller.'Controller';

			if (file_exists($classPath)) {
				if (!class_exists($className)) {
					$className = require_once $classPath;
				}
				if (class_exists($className)) {
					$this->controller = new $className($this,$this->config);
				} else {
					$this->modx->log(modX::LOG_LEVEL_ERROR,'[Quip] Could not load controller: '.$className.' at '.$classPath);
				}
			} else {
				$this->modx->log(modX::LOG_LEVEL_ERROR,'[Quip] Could not load controller file: '.$classPath);
			}
		} else {
			$this->modx->log(modX::LOG_LEVEL_ERROR,'[Quip] Could not load quipController class.');
		}
		return $this->controller;
	}

	public function cleanse($text, array $scriptProperties = array()) {
		if (empty($text)) {return ' ';}

		if ($snippet = $this->modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			$params = $snippet->getPropertySet('Comment');

			$text = preg_replace('/\{\{\{\{\(*.?\)\}\}\}\}/','',$text);
			$params['input'] =  str_replace(array('[[',']]'), array('{{{{{','}}}}}'), $text);
			$filtered = $snippet->process($params);

			$filtered = str_replace(array('{{{{{','}}}}}','`'), array('&#91;&#91;','&#93;&#93;','&#96;'), $filtered);
			return $filtered;
		}
		else {
			$allowedTags = $this->config['allowedTags'];

			$text = preg_replace("/<script(.*)<\/script>/i",'',$text);
			$text = preg_replace("/<iframe(.*)<\/iframe>/i",'',$text);
			$text = preg_replace("/<iframe(.*)\/>/i",'',$text);
			$text = strip_tags($text,$allowedTags);
			// this causes double quotes on a href tags; commenting out for now
			//$body = str_replace(array('"',"'"),array('&quot;','&apos;'),$body);
			$text = str_replace(array('[',']','`'),array('&#91;','&#93;','&#96;'),$text);

			return $text;
		}
	}

}