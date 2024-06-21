/*!
 * Classe Url
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
(function (window) {
    'use strict';

    var Url = function() {
        this.system = 'cms';
        this.host = window.document.location.host;
        this.hostname = window.document.location.hostname;
    };

    /**
     * Redireciona para a url especificada.
     * @author Ramon Barros <ramon@ezoom.com.br>
     * @date      2015-07-27
     * @copyright Copyright  (c) 2015,         Ezoom
     * @param     {string}   url
     * @return    {void}
     */
    Url.prototype.redirect = function(url) {
        window.location.href = url;
    };

    /**
     * Retorna os segmentos da url
     * @author Ramon Barros <ramon@ezoom.com.br>
     * @date      2015-07-24
     * @copyright Copyright  (c)           2015, Ezoom
     * @return    {string}
     */
    Url.prototype.segments = function(key) {
        var pathname = window.document.location.pathname.replace(/(^\/|\/$)/g, ''),
            segments = String(pathname).split('/'),
            app = segments.indexOf(this.system) + 1;
            segments = segments.slice(app, segments.length);
        return typeof key !== undefined ? segments[key] : segments;
    }

    window.Url = new Url();
    return Url;

}(window));
