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
		if ($snippet = $modx->getObject('modSnippet', array('name' => 'Jevix'))) {
			$exists = true;
		}
		break;
	case xPDOTransport::ACTION_UNINSTALL: break;
}

if (!$exists) {
	switch ($modx->getOption('manager_language')) {
		case 'ru':
			$output = 'Этот компонент требует сниппет <b>Jevix</b> для фильтрации вывода тикетов.<br/><br/>Могу я автоматически скачать его и установить?';
			break;
		default:
			$output = 'This component is require snippet <b>Jevix</b> for filtering output of tickets.<br/><br/>Can i automaticly download and install it?';
	}

}

return $output;