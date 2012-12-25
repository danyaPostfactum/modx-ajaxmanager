<?php
/**
 * AjaxManager Plugin
 *
 * Events: OnManagerPageBeforeRender, OnManagerPageAfterRender
 *
 * @author Danil Kostin <danya.postfactum(at)gmail.com>
 *
 * @package ajaxmanager
 */

$managerUrl = $modx->getOption('manager_url', null, MODX_MANAGER_URL);

$controller =& $scriptProperties['controller'];

switch ($modx->event->name)
{
    case 'OnManagerPageBeforeRender':
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $controller->loadHeader) {
            $action = $modx->actionMap[ (integer) $modx->request->action ];
            $namespaces = explode(',', $modx->getOption('ajaxmanager.compatible_namespaces', null, 'core'));
            if (!in_array($action['namespace'], $namespaces)) {
                die();
            }
            $controller->loadHeader = false;
            $controller->loadFooter = false;
            $controller->packToJSON = true;
        } else {
            $controller->addJavaScript($managerUrl. 'components/ajaxmanager/assets/ajaxmanager.js');
            $controller->packToJSON = false;
        }
        break;
    case 'OnManagerPageAfterRender':
        if ($controller->packToJSON) {

            $content = $controller->content;
            $title = $controller->getPageTitle();

            $styles = array();
            $skip = $_REQUEST['stylesheets'];
            foreach ($controller->head['css'] as $src) {
                if (in_array($src, $skip)) continue;
                $styles[] = $src;
            }

            $scripts = array();

            $sources = array();
            $skip = $_REQUEST['scripts'];
            foreach (array_merge($controller->head['js'], $controller->head['lastjs']) as $src) {
                if (in_array($src, $skip)) continue;
                $sources[] = $src;
            }
            if (count($sources)){
                $scripts[] = $managerUrl.'min/index.php?f='.implode(',',$sources);
            }


            $topics = array();
            $skip = $_REQUEST['topics'];
            foreach($controller->getLanguageTopics() as $topic) {
                if (in_array($topic, $skip)) continue;
                $topics[] = $topic;
            }
            if (count($topics)){
                $scripts[] = $modx->getOption('connectors_url', null, MODX_CONNECTORS_URL). 'components/ajaxmanager/lang.js.php?ctx=mgr&topic='.implode(',', $topics);
            }

            $embedded = array();
            $embedded['styles']   = array();
            $embedded['scripts']  = array();
            foreach ($controller->head['html'] as $src) {
                if (preg_match('/<script(.*?)>/', $src)){
                    $embedded['scripts'][] = $src;
                } else if (preg_match('/<style(.*?)>/', $src)){
                    $embedded['styles'][] = $src;
                }
            }
            
            $response = array(
                'title'     => '',
                'content'   => '',
                'scripts'   => array(),
                'styles'    => array(),
                'embedded'  => array(
                    'scripts'    => array(),
                    'styles'     => array()
                )
            );

            $response['content']    = $content;
            $response['title']      = $title;
            $response['scripts']    = $scripts;
            $response['styles']     = $styles;
            $response['embedded']   = $embedded;

            $controller->content = json_encode($response);

            header('Content-Type: application/json');
        }
}