/* jslint devel: true, unparam: true, indent: 4 */
(function ($, window, undefined) {
    "use strict";

    var Setores = function () {
        return this.__constructor();
    };

    Setores.prototype = Comum;
    Setores.prototype.constructor = Setores;

    Setores.prototype.__constructor = function () {
        this.sortable();
        this.bootstrap();
        this.toggleStatus();
        this.init();

        return this;
    };

    Setores.prototype.init = function () {};

    window.Setores = new Setores();
    return Setores;
})(jQuery, window);
