/**
 * js
 *
 * @package ezoom_framework
 * @subpackage main
 * @category js
 * @author Ralf da Rocha
 * @copyright 2014 Ezoom
 */
Main.prototype.history = {
    block           : false,
    changeHash      : false,
    first           : true,
    initialURL      : location.href,
    hasHistory      : Modernizr.history,
    pushed          : true,
    save            : true,
    url             : '',
    init            : function(){
        this.pushHistory({
            title: document.title,
            url: segments.join('/') + (location.search != '' ? location.search : '')
        });
    },
    pushHistory     : function(params){
        var self = this;
        if (!self.block){
            if(self.hasHistory){
                if (self.first){
                    self.replaceHistory(params);
                    self.first = false;
                    return true;
                }
                if (history.state == null || history.state.url != params.url){
                    if (self.save){
                        history.pushState(params, false, site_url + params.url);
                        if (params.title !== undefined)
                            document.title = params.title;
                    }
                }
                self.save = true;
            }else{
                if (self.save){
                    self.changeHash = true;
                    window.location.hash = '#/' + params.url;
                    setTimeout(function(){
                        self.changeHash = false;
                    },100);
                    if (params.title !== undefined)
                        document.title = params.title;
                }else{
                    setTimeout(function(){
                        self.save = true;
                        self.changeHash = false;
                    },100);
                }
            }
        }
    },
    popHistory  : function(callback){
        var self = this;
        if(self.hasHistory){
            window.onpopstate = function(e){
                e.preventDefault();
                // Solution for auto popState from Chrome
                var onloadPop = !self.pushed && location.href == self.initialURL;
                self.pushed = true;
                if (onloadPop) return;
                // Do the magic
                if (e.state){
                    if (typeof callback == 'function') {
                        self.save   = false;
                        self.url    = e.state.url.replace(site_url,'');
                        if (e.state.title !== undefined)
                            document.title = e.state.title;
                        return callback(self.url);
                    }
                }
            };
        }else{
            window.onhashchange = function(){
                if (!self.changeHash){
                    if (typeof callback == 'function') {
                        self.save   = false;
                        self.url    = (location.hash).replace('#/','');
                        if (e.state.title !== undefined)
                            document.title = e.state.title;
                        return callback(self.url);
                    }
                }
            };
        }
    },
    replaceHistory: function(params){
        var self = this;
        if(self.hasHistory)
            history.replaceState(params, false, site_url + params.url);
        if (params.title !== undefined)
            document.title = params.title;
    },
    lock: function(){
        var self = this;
        self.block = true;
    },
    unlock: function(){
        var self = this;
        self.block = false;
    }
};
