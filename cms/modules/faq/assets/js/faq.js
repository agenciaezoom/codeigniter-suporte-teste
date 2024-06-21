/*!
 * Classe Produtos
 *
 * @author Michael Cruz [michael@ezoom.com.br]
 * @date   2015-04-13
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
(function ($, window, undefined) {
    'use strict';

    /**
     * Inicia propriedades do objeto
     * @author Michael Cruz [michael@ezoom.com.br]
     * @date   2015-04-13
     */
    var Faq = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Faq.prototype = Comum;
    Faq.prototype.constructor = Faq;

    Faq.prototype.__constructor = function() {
        this.upload();
        this.sortable();
        this.bootstrap();
        this.toggleStatus();

        $('form select').on('change', function(){
            if ($(this).val()!=''){
                $(this).closest('.form-group').removeClass('has-error').find('.has-error').remove();
            }
        });
        return this;
    };

    window.Faq = new Faq();
    return Faq;

}(jQuery, window));
