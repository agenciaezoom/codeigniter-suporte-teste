/*!
 * Classe Exceptions
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
(function (window) {
    'use strict';

    var Exceptions = function() {
        this.system = 'cms';
        return this.__constructor();
    };

    /**
     * Construtor da classe
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-04
     * @return {Comum}
     */
    Exceptions.prototype.__constructor = function() {
        this.ajax();
        return this;
    };

    Exceptions.prototype.ajax = function() {
        // Set up a global AJAX error handler to handle the 401
        // unauthorized responses. If a 401 status code comes back,
        // the user is no longer logged-into the system and can not
        // use it properly.
        $.ajaxSetup({
            statusCode: {
                401: function(){
                    openNotification({
                        layout: 'top',
                        text: i18n.sessao_fechada,
                        type: 'error'
                    });
                    // Redirec the to the login page.
                    setTimeout(function(){
                        window.location.href = site_url;
                    }, 10000);
                }
            }
        });
        $( document ).ajaxError(function() {
            openNotification({
                layout: 'top',
                text: i18n.erro_inesperado,
                type: 'error',
                timeout: 6500
            });

            $.each(files, function (index) {
                files[index].abort();
                files[index].textStatus = '';
            });

            if ($('#validateSubmitForm').length){
                $('#validateSubmitForm').removeClass('sending').data('sending',false);
                $('#validateSubmitForm').find('[type="submit"]').html($('#validateSubmitForm').find('[type="submit"]').data('original')).removeClass('loading').removeAttr('disabled');
            }
        });
    };

    window.Exceptions = new Exceptions();
    return Exceptions;

}(window));
