/*!
 * Classe Banners
 *
 * @author Diogo taparello [diogo@ezoom.com.br]
 * @date   2015-03-04
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
'use strict';

$('document').ready(function(){

    /**
     * Inicia propriedades do objeto
     * @author Diogo taparello [diogo@ezoom.com.br]
     * @date   2015-03-04
     */
    var Downloads = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Downloads.prototype = Comum;
    Downloads.prototype.constructor = Downloads;

    /**
     * Construtor da classe
     * @author Diogo taparello [diogo@ezoom.com.br]
     * @date   2015-03-04
     * @return {Downloads}
     */
    Downloads.prototype.__constructor = function() {
        this.upload();
        this.sortable();
        this.bootstrap();
        this.toggleStatus();
        this.deleteRegisters('archive', '');

        return this;
    };

    window.Downloads = new Downloads();
    return Downloads;

});
