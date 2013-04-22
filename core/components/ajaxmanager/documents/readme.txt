--------------------
Extra: AjaxManager
--------------------
Version: 1.1.5
Created: April 22nd, 2013
Since: December 10th, 2012
Author: Danil Kostin <danya.postfactum@gmail.com>
License: GNU GPLv2 (or later at your option)

Minimal required MODx version is 2.2.6!

This plugin ajaxifies MODx Manager. No more page reloading and waiting for Tree appearing!

Works with modern browsers only (requires HTML5 History API).

It is compatible with such backend editors as Ace and CKEditor. TinyMCE (yet) and CodeMirror (never) are not supported!


Installation Instructions

- Unistall previous version of AjaxManager correctly. Unfortunately AjaxManager 1.1.4 has the bug, causing manager/assets/modext/ and manager/controllers/default/ folder removing. Sorry, you will have to restore manager folder from MODx distro..
- Install via Package Browser, clear MODX cache, clear Browser cache, set the list of your working correctly components in ajaxmanager.compatible_namespaces system setting. See http://modx.com/extras/package/ajaxmanager/ for list of compatible components

Notes for modx less than 2.2.7:
- This package will modify your manager assets and controllers. Make sure your manager folder is writable !
- Check Restore radio in Uninstall dialog if you will have to uninstall AjaxManager!