<?php
/**
 * Build the setup options form.
 *
 * @package tickets
 * @subpackage build
 */
$exists = false;
$output = null;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:

	case xPDOTransport::ACTION_UPGRADE:
		$exists = $modx->getObject('transport.modTransportPackage', array('package_name' => 'Jevix')) && $modx->getObject('transport.modTransportPackage', array('package_name' => 'pdoTools'));
	break;

	case xPDOTransport::ACTION_UNINSTALL: break;
}

if (!$exists) {
	switch ($modx->getOption('manager_language')) {
		case 'ru':
			$output = 'Этот компонент требует сниппет <b>Jevix</b> для фильтрации вывода тикетов и <b>pdoTools</b> для быстрой работы с БД.<br/><br/>Могу я автоматически скачать и установить их?';
			break;
		default:
			$output = 'This component is require snippet <b>Jevix</b> for filtering output of tickets and <b>pdoTools</b> for fast work <with></with> database.<br/><br/>Can i automaticly download and install them?';
	}

}

return $output;