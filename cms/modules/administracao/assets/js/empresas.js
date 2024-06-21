/*!
 * Classe Empresas
 *
 * @author Diogo taparello [diogo@ezoom.com.br]
 * @date   2016-02-11
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
"use strict";

$("document").ready(function () {
    /**
     * Inicia propriedades do objeto
     * @author Diogo taparello [diogo@ezoom.com.br]
     * @date   2016-02-11
     */
    var Empresas = function () {
        return this.__constructor();
    };

    /**
     * Extende Comum
     * @type {Comum}
     */
    Empresas.prototype = Comum;
    Empresas.prototype.constructor = Empresas;

    /**
     * Construtor da classe
     * @author Diogo taparello [diogo@ezoom.com.br]
     * @date   2016-02-11
     * @return {Empresas}
     */
    Empresas.prototype.__constructor = function () {
        this.upload({
            formData: {
                gallerypath: "userfiles/empresas/",
                gallerytable: "ez_company",
            },
        });
        this.masks();
        this.bootstrap();
        this.toggleStatus();
        this.address();

        $(".colorpicker2").colorpicker();
        $(".colorpicker2 input").on("keyup", function () {
            var val = $(this).val();
            val = val.indexOf("#") == -1 ? "#" + val : val;
            $(this).parent().colorpicker("setValue", val);
        });
        return this;
    };

    Empresas.prototype.address = function () {
        $(".address-panel").on("keyup", '[name*="zipcode"]', function () {
            var v = $.trim($(this).val().replace(/\D/g, "")),
                $zip = $(this);

            if (v.length == 8 && v != $zip.data("old")) {
                $zip.data("old", v);
                $zip.addClass("loading");

                var tab = $zip.closest(".address-panel");
                $.getJSON(
                    site_url + "endereco/get",
                    {
                        zipcode: v,
                    },
                    function (result) {
                        if (result) {
                            tab.find('input:not([name*="zipcode"])').val("");
                            tab.find('[name*="state"]').val(
                                states[unescape(result["uf"])]
                            );
                            tab.find('[name*="city"]').val(
                                unescape(result["cidade"])
                            );
                            tab.find('[name*="suburb"]').val(
                                unescape(result["bairro"])
                            );
                            tab.find('[name*="street"]').val(
                                unescape(
                                    (result["tipo_logradouro"]
                                        ? result["tipo_logradouro"] + " "
                                        : "") + result["logradouro"]
                                )
                            );
                            tab.find('[name*="number"]').focus();
                        }

                        $zip.removeClass("loading");
                    }
                );
            }
        });
    };

    Empresas.prototype.masks = function () {
        if ($('[class*="inputmask"]').length > 0) {
            $.extend($.inputmask.defaults, {
                autounmask: true,
                clearMaskOnLostFocus: true,
                placeholder: "_",
            });
            $(".inputmask-cnpj").inputmask("mask", {
                mask: "99.999.999/9999-99",
            });
        }

        $(".inputmask-cep").inputmask("mask", { mask: "99999-999" });
        $(".inputmask-phone").inputmask("mask", { mask: "(99) 9999-9999[9]" });
    };

    window.Empresas = new Empresas();
    return Empresas;
});
