<?php
$settings = array();

$settings['compatible_namespaces']= $modx->newObject('modSystemSetting');
$settings['compatible_namespaces']->fromArray(array(
        'key' => 'ajaxmanager.compatible_namespaces',
        'xtype' => 'textfield',
        'value' => 'core,clientconfig,minishop2,resourcehider,gallery',
        'namespace' => 'ajaxmanager',
        'area' => 'general'
    ),'',true,true);

return $settings;