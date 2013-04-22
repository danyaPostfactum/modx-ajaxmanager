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
        if( version_compare($version['full_version'], '2.2.7-dev', '>=') && count($this->payload['resolve']) == 6) {
            // Disable patch, targeted to 2.2.6
            array_splice($this->payload['resolve'], 5, 2);
        }
        break;
}

return true;