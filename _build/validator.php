<?php
/**
 *
 * @author Danil Kostin <danya.postfactum@gmail.com>
 *
 * @package ajaxmanager
 * @subpackage build
 */
$version = $object->xpdo->getVersionData();
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        if (version_compare($version['full_version'], '2.2.6-dev', '<')) {
            $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'This package requires MODX 2.2.6 at least ! Aborting installation...');
            return false;
        }
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        if ($version['major_version'] == 2 && $version['minor_version'] == 6 && $options[xPDOTransport::PREEXISTING_MODE] != xPDOTransport::RESTORE_PREEXISTING) {
            $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Please press “Uninstall” button again and set “Restore” mode! Aborting...');
            return false;
        }
        break;
}

return true;