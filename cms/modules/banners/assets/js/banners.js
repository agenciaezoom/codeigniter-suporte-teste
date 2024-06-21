/*!
 * Classe Banners
 *
 * @author Bruno Marsilio [bruno@ezoom.com.br]
 * @date   2019-11-04
 * Copyright (c) 2019 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
'use strict';

$('document').ready(function(){

    /**
     * Inicia propriedades do objeto
     * @author Bruno Marsilio [bruno@ezoom.com.br]
     * @date   2019-11-04
     */
    var Banners = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Banners.prototype = Comum;
    Banners.prototype.constructor = Banners;

    /**
     * Construtor da classe
     * @author Bruno Marsilio [bruno@ezoom.com.br]
     * @date   2019-11-04
     * @return {Banners}
     */
    Banners.prototype.__constructor = function() {
        this.upload();
        this.sortable();
        this.deleteRegisters('image/image_mobile', '');
        this.bootstrap();
        this.toggleStatus();

        return this;
    };

    window.Banners = new Banners();
    return Banners;

});
