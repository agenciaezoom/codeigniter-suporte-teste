/*!
 * Classe Paginas
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
    var Paginas = function() {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Paginas.prototype = Comum;
    Paginas.prototype.constructor = Paginas;

    /**
     * Construtor da classe
     * @author Ralf da Rocho [ralf@ezoom.com.br]
     * @date   2016-07-26
     * @return {Paginas}
     */
    Paginas.prototype.__constructor = function() {
        this.upload();
        Comum.magnific();
        this.gallery_video();
        this.slugger();
        this.imgDim();
        return this;
    };

    /**
     * Auxilia a criação do slug
     * @author Fábio Augustin Neis [fabio@ezoom.com.br]
     * @date   2016-03-23
     */
    Paginas.prototype.slugger = function() {
        var self = this;
        if($('input[name="id"]').length == 0) {
            $('input[name="area"], input[name="subarea"], input[name="value[1][title]"]').on('keyup', function() {
                var text = $('input[name="area"]').val();
                text += '-'+$('input[name="subarea"]').val();
                $('input[name="slug"]').val(self.urlSlug(text));
            });
        }
    }

    /**
     * Auxilia a configuração do upload da imagem
     * @author Fábio Augustin Neis [fabio@ezoom.com.br]
     * @date   2017-10-19
     */
    Paginas.prototype.imgDim = function() {
        var self = this;
        if($('.upload-wrapper').length > 0) {
            $('input[name="image_width"], input[name="image_height"]').on('keyup', function() {
                var width = new Number($('input[name="image_width"]').val());
                var height = new Number($('input[name="image_height"]').val());

                if(!isNaN(width) && !isNaN(height)) {
                    console.log($('.upload-image'));
                    $('.upload-wrapper').data('width', width);
                    $('.upload-wrapper').data('height', height);
                    $('.upload-image.image').data('dim', width+'x'+height);
                    $('.upload-image.image').find('span').html(width+'x'+height);
                }
            });

            $('input[name="image_mobile_width"], input[name="image_mobile_height"]').on('keyup', function() {
                var width = new Number($('input[name="image_mobile_width"]').val());
                var height = new Number($('input[name="image_mobile_height"]').val());

                if(!isNaN(width) && !isNaN(height)) {
                    console.log($('.upload-image'));
                    $('.upload-image.image_mobile').data('dim', width+'x'+height);
                    $('.upload-image.image_mobile').find('span').html(width+'x'+height);
                }
            });

            $('input[name="image_width"], input[name="image_height"]').on('keydown', function(e){
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                        return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105))
                    e.preventDefault();
            });
        }
    }



    window.Paginas = new Paginas();
    return Paginas;
});
