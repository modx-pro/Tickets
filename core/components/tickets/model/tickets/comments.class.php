<?php
require MODX_CORE_PATH . 'components/quip/model/quip/quip.class.php';

class Comments extends Quip {

	public $controller;

	function __construct(modX &$modx,array $config = array()) {
		parent::__construct($modx, $config);

		$ticketsPath = $this->modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/');

		$this->config = array_merge($this->config, array(
			'ticketsPath' => $ticketsPath,
		));
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

}