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
        if (version_compare($version['full_version'], '2.2.7-dev', '<')) {
            $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'This package requires MODX 2.2.6 at least ! Aborting installation...');
            return false;
        }
        break;
}

return true;