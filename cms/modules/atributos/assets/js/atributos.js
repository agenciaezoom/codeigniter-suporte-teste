/* jslint devel: true, unparam: true, indent: 4 */
(function ($, window, undefined) {
    "use strict";

    var Atributos = function () {
        return this.__constructor();
    };

    Atributos.prototype = Comum;
    Atributos.prototype.constructor = Atributos;

    Atributos.prototype.__constructor = function () {
        this.upload();
        this.sortable();
        this.bootstrap();
        this.toggleStatus();
        this.deleteRegisters("image", "");

        return this;
    };

    window.Atributos = new Atributos();
    return Atributos;
})(jQuery, window);
