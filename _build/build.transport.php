<?php
/**
 * AjaxManager build script
 *
 * @package ajaxmanager
 * @subpackage build
 */
 
$tstart = microtime(true);
set_time_limit(0);
 
/* define version */
define('PKG_NAME','AjaxManager');
define('PKG_NAMESPACE','ajaxmanager');
define('PKG_VERSION','1.1.5');
define('PKG_RELEASE','dev');
 
/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'lexicon' => $root . 'core/components/'.PKG_NAMESPACE.'/lexicon/',
    'documents' => $root.'core/components/'.PKG_NAMESPACE.'/documents/',
    'elements' => $root.'core/components/'.PKG_NAMESPACE.'/elements/',
    'source_manager_assets' => $root.'manager/assets/components/'.PKG_NAMESPACE,
    'source_core' => $root.'core/components/'.PKG_NAMESPACE,
    'source_connectors' => $root.'connectors/components/'.PKG_NAMESPACE,
    'modext_patch' => $root.'manager/assets/modext/',
    'controllers_patch' => $root.'manager/controllers/default/',
);
unset($root);
 
/* load modx */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
echo XPDO_CLI_MODE ? '' : '<pre>';
$modx->setLogTarget('ECHO');
 
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAMESPACE,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAMESPACE,false,true,'{core_path}components/'.PKG_NAMESPACE.'/');
 
/* create the plugin object */
$plugin= $modx->newObject('modPlugin');
$plugin->set('id',1);
$plugin->set('name', PKG_NAME);
$plugin->set('description', 'Speeds up manager panel by using ajax page loading.');
$plugin->set('plugincode', file_get_contents($sources['source_core'] . '/elements/plugins/'.PKG_NAMESPACE.'.plugin.php'));
$plugin->set('category', 0);

/* add plugin events */
$events = include $sources['data'].PKG_NAMESPACE.'.plugin.events.php';
if (is_array($events) && !empty($events)) {
    $plugin->addMany($events);
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events!');
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events.'); flush();
unset($events);

/* load plugin properties */
/*$properties = include $sources['data'].'properties.inc.php';
$plugin->setProperties($properties);
$modx->log(xPDO::LOG_LEVEL_INFO,'Setting '.count($properties).' Plugin Properties.'); flush();*/

$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
);
$vehicle = $builder->createVehicle($plugin, $attributes);

$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to plugin...');
$vehicle->validate('php',array(
    'source' => $sources['build'].'validator.php',
    'name' => 'validator',
    'type' => 'php'
));
$vehicle->resolve('php',array(
    'source' => $sources['build'].'resolver.php',
    'name' => 'resolve',
    'type' => 'php'
));
$vehicle->resolve('file',array(
    'source' => $sources['source_manager_assets'],
    'target' => "return MODX_MANAGER_PATH . 'assets/components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_connectors'],
    'target' => "return MODX_CONNECTORS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['modext_patch'],
    'target' => "return MODX_MANAGER_PATH . 'assets/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['controllers_patch'],
    'target' => "return MODX_MANAGER_PATH . 'controllers/';",
));
$builder->putVehicle($vehicle);

/* load system settings */
$settings = include $sources['data'].PKG_NAMESPACE.'.settings.php';
if (is_array($settings) && !empty($settings)) {
    $attributes= array(
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
    );
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting,$attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not package System Settings.');
}
unset($settings,$setting);

$modx->log(modX::LOG_LEVEL_INFO,'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['documents'] . 'license.txt'),
    'readme' => file_get_contents($sources['documents'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['documents'] . 'changelog.txt'),
/*    'setup-options' => array(
        'source' => $sources['build'].'setup.options.php',
    ),*/
));
 
/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();
 
$tend= microtime(true);
$totalTime= sprintf("%2.4f s", ($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");
exit ();