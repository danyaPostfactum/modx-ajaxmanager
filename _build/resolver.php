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
		if (count($this->payload['resolve']) == 6) {
			if( version_compare($version['full_version'], '2.2.7-dev', '>=')) {
				// Disable patch, targeted to 2.2.6
				array_splice($this->payload['resolve'], 4, 2);
			} else {
				$object->xpdo->log(xPDO::LOG_LEVEL_WARN,'You are using 2.2.6 version. Your manager folder will be patched!');
			}
		}
		break;
}

return true;