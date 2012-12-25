<?php
$events = array();

$events[1] = $modx->newObject('modPluginEvent');
$events[1]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnManagerPageBeforeRender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[2] = $modx->newObject('modPluginEvent');
$events[2]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnManagerPageAfterRender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

return $events;