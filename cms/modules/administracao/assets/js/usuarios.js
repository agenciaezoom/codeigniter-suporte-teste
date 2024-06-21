/*!
 * Classe Usuarios
 *
 * @author Diogo taparello [diogo@ezoom.com.br]
 * @date   2016-02-11
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
'use strict';

$('document').ready(function(){
    var images = [];

    /**
     * Inicia propriedades do objeto
     * @author Diogo taparello [diogo@ezoom.com.br]
     * @date   2016-02-11
     */
    var Usuarios = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Usuarios.prototype = Comum;
    Usuarios.prototype.constructor = Usuarios;

    /**
     * Construtor da classe
     * @author Diogo taparello [diogo@ezoom.com.br]
     * @date   2016-02-11
     * @return {Usuarios}
     */
    Usuarios.prototype.__constructor = function()
    {
        this.upload({
            formData: {
                gallerypath: 'cms/userfiles/avatar/',
                gallerytable: 'ez_user',
                resize: true,
                width: 200,
                height: 200,
                fit: 'inside'
            }
        });
        this.deleteRegisters('avatar', '');
        this.sortable();
        this.bootstrap();
        this.toggleStatus();
        this.changeGroup();
        this.permission();
        return this;
    };

    //Troca de grupos
    Usuarios.prototype.changeGroup = function()
    {
        $('#inputGroup').change(function(){
            var v = $(this).val();
            $('.permission-wrapper .loading').addClass('actived');
            $('.permission-data').stop(true).slideUp(300, function(){
                $(this).html('');
                if (v != '') {
                    $.ajax({
                        type: 'POST',
                        url: site_url + 'administracao/usuarios/load-permissions',
                        data: { id: v },
                        dataType: 'html',
                        success: function(data) {
                            $('.permission-wrapper .loading').removeClass('actived');
                            $('.permission-data').stop(true).html(data).slideDown(400, function(){
                                //$('.layout-app .hasNiceScroll').getNiceScroll().resize();
                                //$('.layout-app .hasNiceScroll').animate({scrollTop: $('.permission-wrapper').prev('h3').offset().top - $('.col-unscrollable').offset().top }, 700, 'easeOutCirc');
                            });
                            $('.permission-data .checkbox-custom > input[type=checkbox]').each(function () {
                                var $this = $(this);
                                if ($this.data('checkbox')) return;
                                $this.checkbox($this.data());
                            });

                            //$.startCollapsible();
                        }
                    });
                } else {
                    //$('.hasNiceScroll').getNiceScroll().resize();
                    $('.permission-wrapper .loading').removeClass('actived');
                }
            });
        });
    };

    Usuarios.prototype.permission = function()
    {
        $('.permission-wrapper').on('change', '.widget-head input[type="checkbox"]', function(){
             var $parent = $(this).parents('.widget-head'),
                 $body = $parent.next();

             if ($(this).is(':checked')) {
                $body.find('input[type="checkbox"]').checkbox('check');
             } else {
                 $body.find('input[type="checkbox"]').checkbox('uncheck');
             }
        });
    };

    window.Usuarios = new Usuarios();
    return Usuarios;

});