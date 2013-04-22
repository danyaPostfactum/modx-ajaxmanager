(function(){

if (!window.history || !window.history.pushState)
	return;

// override ExtJS Array prototype modification to prevent some bugs
Object.defineProperty(Array.prototype, "remove", {
    value : function(o){
        var index = this.indexOf(o);
        if(index != -1){
            this.splice(index, 1);
        }
        return this;
    },
    enumerable : false
});

var debug = MODx.config['ajaxmanager.debug'] == true;

function log(message, data) {
	if (!debug) return;
	var date = new Date();
	console.log(message, date.toLocaleString() + ' ' + date.getMilliseconds(), data);
}

Ext.onReady(function(){
	var panel = Ext.getCmp('modx-content');
	var mask = new Ext.LoadMask(panel.el);
	var requestStack = [];
	var transaction = null;

	var loadPage = function(url){
		if (transaction) {
			Ext.Ajax.abort(transaction);
		}
		MODx.request = MODx.getURLParameters();
		document.title = MODx.lang['loading'] + ' | MODx Revolution';

		mask.show();

		var timeStamp = new Date().valueOf();
		requestStack.push(timeStamp);

		var loadedScripts 		= [];
		var loadedStyleSheets	= [];
		var loadedTopics 		= [];

		Array.prototype.forEach.call(document.scripts, function(script){
			if (!script.src)
				return;

			if (script.src.indexOf('.php') >= 0){
				if (script.src.indexOf('min/index.php') >= 0) {
					var sources = script.src.substring(script.src.indexOf('?f=')+3).split(',');
					sources.forEach(function(src){
						loadedScripts.push(src);
					});
					return;
				}
				if (script.src.indexOf('lang.js.php') >= 0) {
					var query = script.src.substring(script.src.indexOf('?')+1);
					var parameters = query.split('&');;
					var topics = parameters[1].substring(parameters[1].indexOf('=')+1).split(',');
					topics.forEach(function(topic){
						loadedTopics.push(topic);
					});
					return;
				}
			} else {
				loadedScripts.push(script.src.replace(location.protocol + '//' + location.host, ''));
			}
		});

		Array.prototype.forEach.call(document.styleSheets, function(styleSheet){
			if (!styleSheet.href)
				return;

			if (styleSheet.href.indexOf('min/index.php') >= 0){
				var sources = styleSheet.href.substring(styleSheet.href.indexOf('?f=')+3).split(',');
				sources.forEach(function(src){
					loadedStyleSheets.push(src);
				});
			} else {
				loadedStyleSheets.push(styleSheet.href.replace(location.protocol + '//' + location.host, ''));
			}
		});

		log('request', url);

		transaction = Ext.Ajax.request({
			params: {'scripts[]': loadedScripts,'stylesheets[]': loadedStyleSheets, 'topics[]': loadedTopics},
			url: url,
			success: function(response, opts) {
				if (!response.responseText) {
					log('Silent server response, fallback...', response);
					location.href = url;
					return false;
				}

				try{
					var component = JSON.parse(response.responseText);
				} catch (e) {
					log('Invalid server response, fallback...', response);
					location.href = url;
					return false;
				}

				log('response', component);

				var scriptsToLoad = {};

				var init = function(){
					// render last requested component only
					if (timeStamp !== requestStack[requestStack.length-1])
						return;

					log('initialize', component.title);

					try {
						document.title = component.title + ' | MODx Revolution';
						// sometimes resource panel is not removed from dom
						var cmp = panel.items.get(0);
						var bwrap = cmp && cmp.bwrap;
						panel.removeAll();
						bwrap && bwrap.remove();
					} catch (e) {
						log('Error while clearing panel', e);
						location.href = url;
						return false;
					}
					if (MODx.activePage) {
						if (MODx.activePage.config.resource) {
							MODx.releaseLock(MODx.activePage.config.resource);
						}
						MODx.activePage.ab && MODx.activePage.ab.destroy();
						MODx.activePage.destroy();
						MODx.activePage = null;
					}

					// remove all panel content except bwrap
					var panelChildren = panel.el.dom.children;
					var i = panelChildren.length;
					while (i--) {
						if (panelChildren[i] !== panel.bwrap.dom) {
							panelChildren[i].parentNode.removeChild(panelChildren[i]);
						}
					}

					// append new content to panel
					panel.el.dom.insertAdjacentHTML('afterbegin', component.content);

					// move all scripts out of panel content to run it
					Array.prototype.forEach.call(panel.el.dom.getElementsByTagName('script'), function(script){
						component.embedded.scripts.push(script.innerHTML);
						script.parentNode.removeChild(script);
					});

					// append all inline scripts to the body to execute these
					component.embedded.scripts.forEach(function(content){
						var script = document.createElement('script');
						script.innerHTML = content.replace(/<script(.*?)>/, '').replace(/<\/script(.*?)>/ig, '');
						document.body.appendChild(script);
					});
					mask.hide();
					log('afterrender', component.title);
				};

				var onLoad = function(e){
					scriptsToLoad[this.src] = 1;
					for (var src in scriptsToLoad)
					{
						if (!scriptsToLoad[src])
							return;
					}

					log('scriptsloaded', scriptsToLoad);
					// all required scripts are loaded, initialize
					init();
				};

				// append new external scripts to the body
				component.scripts.forEach(function(src){
					var script = document.createElement('script');
					script.onload = onLoad;
					script.src = src;
					scriptsToLoad[script.src] = 0;
					document.body.appendChild(script);
				});

				// append new external stylesheets to the head
				component.styles.forEach(function(src){
					var link = document.createElement('link');
					link.rel = 'stylesheet';
					link.href = src;
					document.head.appendChild(link);
				});

				// if nothing to load, run initialization immediately
				if (Object.keys(scriptsToLoad).length == 0)
					init();

			},
			failure: function(response, opts) {
				log('server-side failure', response.status);
				location.href = url;
			}
		});
	};
	MODx.on('beforeLoadPage', function(url){
			history.pushState({}, "", url);
			loadPage(url);
			return false;
	});

	history.replaceState({}, "", location.href);

	window.addEventListener('popstate',function(e){
		if (e.state !== null) {
			loadPage(location.href);
		}
	});

	document.querySelector('#modx-navbar').addEventListener('click', function(e){
		var target = e.target;

		if (target.localName === 'span')
			target = target.parentNode;

		if (e.button !== 0 || target.localName !== 'a' || target.onclick !== null)
			return;

		e.preventDefault();

		MODx.loadPage(target.href);
	});


});

})();