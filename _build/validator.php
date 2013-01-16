<?php
/**
 *
 * @author Danil Kostin <danya@postfactum@gmail.com>
 *
 * @package ajaxmanager
 * @subpackage build
 */


    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $version = $object->xpdo->getVersionData();
            $object->xpdo->log(xPDO::LOG_LEVEL_ERROR, $version['full_version'] . print_r(version_compare($version['full_version'], '2.5.6', '<'), 1));
            if (version_compare($version['full_version'], '2.2.6', '<')) {
                $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'This package requires MODX 2.2.6! Aborting installation...');
                return false;
            }
            break;
        default:
    }

return true;