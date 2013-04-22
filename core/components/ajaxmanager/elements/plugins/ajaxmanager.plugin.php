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

$enabled = $modx->getOption('ajaxmanager.enabled', null, true);

if (!$enabled){
    return;
}

$managerUrl = $modx->getOption('manager_url', null, MODX_MANAGER_URL);

$controller =& $scriptProperties['controller'];

$preload = $modx->getOption('ajaxmanager.preload', null, true);

$files = $preload ? array(
    'sections/context/list.js',
    'sections/context/update.js',
    'sections/context/view.js',
    'sections/element/chunk/create.js',
    'sections/element/chunk/update.js',
    'sections/element/plugin/create.js',
    'sections/element/plugin/update.js',
    'sections/element/propertyset/index.js',
    'sections/element/snippet/create.js',
    'sections/element/snippet/update.js',
    'sections/element/template/create.js',
    'sections/element/template/update.js',
    'sections/element/tv/create.js',
    'sections/element/tv/update.js',
    'sections/fc/list.js',
    'sections/fc/profile/update.js',
    'sections/fc/set/update.js',
    'sections/resource/create.js',
    'sections/resource/data.js',
    'sections/resource/schedule.js',
    'sections/resource/static/create.js',
    'sections/resource/static/update.js',
    'sections/resource/symlink/create.js',
    'sections/resource/symlink/update.js',
    'sections/resource/update.js',
    'sections/resource/weblink/create.js',
    'sections/resource/weblink/update.js',
    'sections/search.js',
    'sections/security/access/list.js',
    'sections/security/access/policy/template/update.js',
    'sections/security/access/policy/update.js',
    'sections/security/access/policy.js',
//    'sections/security/forms/list.js',
    'sections/security/message/list.js',
    'sections/security/permissions/list.js',
    'sections/security/profile/update.js',
    'sections/security/resourcegroup/list.js',
//    'sections/security/role/create.js',
//    'sections/security/role/list.js',
    'sections/security/user/create.js',
    'sections/security/user/list.js',
    'sections/security/user/update.js',
    'sections/security/usergroup/create.js',
    'sections/security/usergroup/update.js',
    'sections/source/index.js',
    'sections/source/update.js',
    'sections/system/action.js',
    'sections/system/content.type.js',
    'sections/system/dashboards/create.js',
    'sections/system/dashboards/list.js',
    'sections/system/dashboards/update.js',
    'sections/system/dashboards/widget/create.js',
    'sections/system/dashboards/widget/update.js',
    'sections/system/error.log.js',
    'sections/system/file/create.js',
    'sections/system/file/edit.js',
    'sections/system/help.js',
    'sections/system/import/html.js',
    'sections/system/import/resource.js',
    'sections/system/info.js',
    'sections/system/logs.js',
    'sections/system/settings.js',
    'sections/welcome.js',
//    'util/datetime.js',
//    'util/eventfix.js',
//    'util/lightbox.js',
//    'util/uploaddialog.js',
//    'util/utilities.js',
    'widgets/core/modx.combo.js',
    'widgets/core/modx.console.js',
    'widgets/core/modx.grid.js',
    'widgets/core/modx.grid.local.property.js',
    'widgets/core/modx.grid.settings.js',
    'widgets/core/modx.orm.js',
    'widgets/core/modx.panel.js',
    'widgets/core/modx.panel.wizard.js',
    'widgets/core/modx.portal.js',
    'widgets/core/modx.rte.browser.js',
    'widgets/core/modx.tabs.js',
//    'widgets/core/modx.tree.checkbox.js',
//    'widgets/core/modx.tree.column.js',
    'widgets/core/modx.tree.js',
    'widgets/core/modx.window.js',
    'widgets/element/modx.grid.element.properties.js',
    'widgets/element/modx.grid.plugin.event.js',
    'widgets/element/modx.grid.template.tv.js',
    'widgets/element/modx.grid.tv.security.js',
    'widgets/element/modx.grid.tv.template.js',
    'widgets/element/modx.panel.chunk.js',
    'widgets/element/modx.panel.plugin.js',
    'widgets/element/modx.panel.property.set.js',
    'widgets/element/modx.panel.snippet.js',
    'widgets/element/modx.panel.template.js',
    'widgets/element/modx.panel.tv.js',
    'widgets/element/modx.panel.tv.renders.js',
    'widgets/element/modx.tree.element.js',
    'widgets/fc/modx.fc.common.js',
    'widgets/fc/modx.grid.fcprofile.js',
    'widgets/fc/modx.grid.fcset.js',
    'widgets/fc/modx.panel.fcprofile.js',
    'widgets/fc/modx.panel.fcset.js',
    'widgets/modx.panel.search.js',
    'widgets/modx.panel.welcome.js',
    'widgets/modx.treedrop.js',
    'widgets/resource/modx.grid.resource.active.js',
    'widgets/resource/modx.grid.resource.security.js',
    'widgets/resource/modx.grid.resource.security.local.js',
    'widgets/resource/modx.panel.resource.data.js',
    'widgets/resource/modx.panel.resource.js',
    'widgets/resource/modx.panel.resource.schedule.js',
    'widgets/resource/modx.panel.resource.static.js',
    'widgets/resource/modx.panel.resource.symlink.js',
    'widgets/resource/modx.panel.resource.tv.js',
    'widgets/resource/modx.panel.resource.weblink.js',
    'widgets/resource/modx.tree.resource.js',
    'widgets/resource/modx.tree.resource.simple.js',
    'widgets/security/modx.grid.access.context.js',
    'widgets/security/modx.grid.access.policy.js',
    'widgets/security/modx.grid.access.policy.template.js',
    'widgets/security/modx.grid.access.resourcegroup.js',
    'widgets/security/modx.grid.actiondom.js',
    'widgets/security/modx.grid.message.js',
    'widgets/security/modx.grid.role.js',
    'widgets/security/modx.grid.role.user.js',
    'widgets/security/modx.grid.user.group.category.js',
    'widgets/security/modx.grid.user.group.context.js',
    'widgets/security/modx.grid.user.group.js',
    'widgets/security/modx.grid.user.group.resource.js',
    'widgets/security/modx.grid.user.group.source.js',
    'widgets/security/modx.grid.user.js',
    'widgets/security/modx.grid.user.recent.resource.js',
    'widgets/security/modx.grid.user.settings.js',
    'widgets/security/modx.panel.access.policy.js',
    'widgets/security/modx.panel.access.policy.template.js',
    'widgets/security/modx.panel.actiondom.js',
    'widgets/security/modx.panel.groups.roles.js',
    'widgets/security/modx.panel.resource.group.js',
    'widgets/security/modx.panel.user.group.js',
    'widgets/security/modx.panel.user.js',
    'widgets/security/modx.tree.resource.group.js',
    'widgets/security/modx.tree.user.group.js',
    'widgets/source/modx.grid.source.access.js',
    'widgets/source/modx.grid.source.properties.js',
    'widgets/source/modx.panel.source.js',
    'widgets/source/modx.panel.sources.js',
    'widgets/system/modx.grid.content.type.js',
    'widgets/system/modx.grid.context.js',
    'widgets/system/modx.grid.context.settings.js',
    'widgets/system/modx.grid.dashboard.widgets.js',
    'widgets/system/modx.grid.manager.log.js',
    'widgets/system/modx.grid.system.event.js',
    'widgets/system/modx.panel.actions.js',
    'widgets/system/modx.panel.context.js',
    'widgets/system/modx.panel.dashboard.js',
    'widgets/system/modx.panel.dashboard.widget.js',
    'widgets/system/modx.panel.dashboards.js',
    'widgets/system/modx.panel.error.log.js',
    'widgets/system/modx.panel.import.html.js',
    'widgets/system/modx.panel.import.resources.js',
    'widgets/system/modx.panel.system.settings.js',
    'widgets/system/modx.tree.action.js',
    'widgets/system/modx.tree.directory.js',
    'widgets/system/modx.tree.menu.js',
    'widgets/system/mysql/modx.grid.databasetables.js',
    'widgets/system/sqlsrv/modx.grid.databasetables.js',
    'widgets/windows.js',
    'workspace/combos.js',
    'workspace/index.js',
    'workspace/lexicon/combos.js',
    'workspace/lexicon/index.js',
    'workspace/lexicon/language.grid.js',
    'workspace/lexicon/lexicon.grid.js',
    'workspace/lexicon/lexicon.panel.js',
    'workspace/lexicon/lexicon.topic.grid.js',
    'workspace/namespace/index.js',
    'workspace/namespace/modx.namespace.panel.js',
    'workspace/package/index.js',
    'workspace/package/package.panel.js',
    'workspace/package/package.versions.grid.js',
    'workspace/package.browser.panels.js',
    'workspace/package.browser.tree.js',
    'workspace/package.containers.js',
    'workspace/package.grid.js',
    'workspace/package.panels.js',
    'workspace/package.windows.js',
    'workspace/provider.grid.js',
    'workspace/workspace.panel.js',
) : array();

switch ($modx->event->name)
{
    case 'OnManagerPageBeforeRender':
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $controller->loadHeader) {
            $action = $modx->actionMap[ (integer) $modx->request->action ];
            $namespaces = explode(',', $modx->getOption('ajaxmanager.compatible_namespaces', null, 'core'));
            if ($modx->request->action && !in_array($action['namespace'], $namespaces)) {
                die();
            }
            $controller->loadHeader = false;
            $controller->loadFooter = false;
            $controller->packToJSON = true;
        } else {
            foreach ($files as $file) {
                    $controller->addJavaScript($managerUrl . 'assets/modext/' . $file);
            }
            $controller->addJavaScript($managerUrl. 'assets/components/ajaxmanager/ajaxmanager.js');
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