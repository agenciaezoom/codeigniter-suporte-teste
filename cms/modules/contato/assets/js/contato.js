/*!
 * Classe Contato
 *
 * @author Diogo Taparello [diogo@ezoom.com.br]
 * @date   2016-03-31
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
(function ($, window, undefined) {
    'use strict';

    /**
     * Inicia propriedades do objeto
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @date   2016-03-31
     */
    var Contato = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Contato.prototype = Comum;
    Contato.prototype.constructor = Contato;

    /**
     * Construtor da classe
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @date   2016-03-31
     * @return {Contato}
     */
    Contato.prototype.__constructor = function() {
        this.modal();
        this.responder();
        this.reenviar();
        return this;
    };

    Contato.prototype.modal = function() {
        var self = this, $modal = $('#ajax-modal');

        $('.chamaModal').on('click', function(){
            var id = $(this).closest('tr').data('id');
            $modal.load(site_url+self.module + '/visualizar', { id : id}, function() {
                $modal.modal();
            });
        });
    };

    /**
     * Evento para setar o contato como respondido.
     *
     * @author Detley Oliveira [detley@ezoom.com.br]
     * @date   2016-08-12
     * @return void
     */
    Contato.prototype.responder = function()
    {
        $('table a.responder').on('click', function(event){
            var obj = $(this);
            var id = $(this).closest('tr').data('id');

            $.ajax({url: site_url+self.module + '/responder/'+id});
        });
    };

    /**
     * Evento de reenvio de e-mail com os dados do contato.
     *
     * @author Detley Oliveira [detley@ezoom.com.br]
     * @date   2016-08-12
     * @return {[type]}   [description]
     */
    Contato.prototype.reenviar = function()
    {
        $('table a.reenviar').on('click', function(event){
            var obj = $(this);
            var id = $(this).closest('tr').data('id');

            $.ajax({url: site_url+self.module + '/reenviar/'+id, success: function(result) {

                var icon = obj.find('i');
                icon.removeClass();

                if (! result.status) {
                    icon.addClass('fa fa-exclamation text-warning');
                    return;
                }

                icon.addClass('fa fa-check text-success');

                setTimeout(function(){ obj.css('display', 'none') }, 2000);
            }});
        });
    };

    window.Contato = new Contato();
    return Contato;

}(jQuery, window));
