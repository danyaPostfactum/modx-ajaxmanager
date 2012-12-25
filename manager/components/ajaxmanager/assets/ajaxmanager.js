(function(){

if (!window.addEventListener || !history.pushState)
	return;

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

Ext.onReady(function(){

	var loadPage = function(url){

		MODx.request = MODx.getURLParameters();
		var panel = Ext.getCmp('modx-content');
		if (!panel._mask)
			panel._mask = new Ext.LoadMask(Ext.get('modx-content'));
		panel._mask.show();

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

		//console.log(loadedScripts);

		Ext.Ajax.request({
			params: {'scripts[]': loadedScripts,'stylesheets[]': loadedStyleSheets, 'topics[]': loadedTopics},
			url: url,
			success: function(response, opts) {
				if (!response.responseText) {
					location.href = url;
					return false;
				}

				var component = JSON.parse(response.responseText);
				//console.log(component);

				document.title = component.title;

				try{
					panel.removeAll();
				} catch (e) {
					console.log && console.log(e);
					location.href = url;
					return false;
				}

				MODx.activePage && MODx.activePage.ab && MODx.activePage.ab.destroy();

				var scriptsToLoad = {};

				var init = function(){
					Ext.get(Ext.get('modx-panel-holder').dom.nextElementSibling).update(component.content, true);

					component.embedded.scripts.forEach(function(content){
						var script = document.createElement('script');

						script.innerHTML = content.replace(/<script(.*?)>/, '').replace(/<\/script(.*?)>/ig, '');
						document.body.appendChild(script);
					});
				};

				var onLoad = function(e){
					scriptsToLoad[this.src] = 1;
					for (var src in scriptsToLoad)
					{
						if (!scriptsToLoad[src])
							return;
					}

					init();
				};

				component.scripts.forEach(function(src){
					var script = document.createElement('script');
					script.onload = onLoad;
					script.src = src;
					scriptsToLoad[script.src] = 0;
					document.body.appendChild(script);
				});

				component.styles.forEach(function(src){
					var link = document.createElement('link');
					link.rel = 'stylesheet';
					link.href = src;
					document.head.appendChild(link);
				});

				if (Object.keys(scriptsToLoad).length == 0)
					init();

				panel._mask.hide();


		    },
		    failure: function(response, opts) {
				console.log('server-side failure with status code ' + response.status);
		    }
		});
	};

	MODx.loadAction = function(a,p){
            var url = '?a='+a+'&'+(p || '');
			history.pushState({}, "", url);
			loadPage(url);
	}

	MODx.loadPage = function(url){
	    if (typeof url == "number")
	        this.loadAction.apply(this, arguments);
	    else {
			history.pushState({}, "", url);
			loadPage(url);
		}
	}

	window.addEventListener('popstate',function(e){
		if (e.state !== null) {
			loadPage(location.href);
		}
	});

	document.querySelector('#modx-navbar').addEventListener('click', function(e){
		var target = e.target;

		if (target.nodeName == 'SPAN')
			target = target.parentNode;

		if (target.nodeName !== 'A')
			return;

		if (target.href.indexOf('http://') !== 0)
			return;

		e.preventDefault();

		if (e.ctrlKey) 
			location.href = target.href;
		else
			MODx.loadPage(target.href);

	});

});

})();