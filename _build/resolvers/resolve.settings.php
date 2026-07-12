<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            break;

        case xPDOTransport::ACTION_UPGRADE:
            // Only fill empty BCC so existing admins start receiving notifications
            if ($setting = $modx->getObject('modSystemSetting', array('key' => 'tickets.mail_bcc'))) {
                if ($setting->get('value') === '' || $setting->get('value') === null) {
                    $setting->set('value', '1');
                    $setting->save();
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modx->removeCollection('modSystemSetting', array(
                'namespace' => 'tickets',
            ));
            break;
    }
}
return true;
