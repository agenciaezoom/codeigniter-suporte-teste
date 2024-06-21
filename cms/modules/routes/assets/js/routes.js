/*!
 * Classe Routes
 *
 * @author Ramon Barros [ramon@ezoom.com.br]
 * @date   2015-03-04
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
'use strict';

$('document').ready(function(){

    /**
     * Inicia propriedades do objeto
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-04
     */
    var Routes = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Routes.prototype = Comum;
    Routes.prototype.constructor = Routes;

    /**
     * Construtor da classe
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-04
     * @return {Routes}
     */
    Routes.prototype.__constructor = function() {
        this.modal();
        this.copyToClipboard();
        this.sortable();
        this.bootstrap();
        this.toggleStatus();
        return this;
    };

    Routes.prototype.modal = function() {
        var self = this, $modal = $('#ajax-modal');

        $('.chamaModal').on('click', function(){
            var id = $(this).closest('tr').data('id');
            // $('#response-message').hide(0);
            $modal.load(site_url+self.module + '/dump', { id : id}, function() {
                $modal.modal();
            });
        });
    };

    Routes.prototype.copyToClipboard = function() {

        $(document).on('click', '#copy-btn', function(event) {
            var copyTextarea = $('#queries');

            copyTextarea.select();

            try {
                var successful = document.execCommand('copy');
                var successClass = successful ? 'btn-success' : 'btn-error';
                // var msg = successful ? 'Copiado com sucesso!' : 'Ocorreu um erro ao copiar!';

                $(this).addClass(successClass);

                setTimeout(function() {
                    $('#copy-btn').removeClass(successClass);
                }, 2000);

            } catch (err) {
                console.error('Oops, unable to copy: ' + err);
            }
        });

    };

    window.Routes = new Routes();
    return Routes;

});
