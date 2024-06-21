/* jslint devel: true, unparam: true, indent: 4 */
(function ($, window, undefined) {
    "use strict";

    var Produtos = function () {
        return this.__constructor();
    };

    Produtos.prototype = Comum;
    Produtos.prototype.constructor = Produtos;

    Produtos.prototype.__constructor = function () {
        this.upload();
        this.sortable();
        this.bootstrap();
        this.toggleStatus();
        this.gallery_infos();

        return this;
    };

    Produtos.prototype.gallery_infos = function () {
        if ($(".info-gallery").length > 0) {
            function format(data) {
                if (!data.id) {
                    return data.text;
                }
                return $(
                    "<span><img class='img-flag' src='" +
                        base_img +
                        "/" +
                        $(data.element).data("image") +
                        "'/> " +
                        data.text +
                        "</span>"
                );
            }

            if ($(".info-language").length > 0) {
                $(".info-language").select2({
                    templateResult: format,
                    templateSelection: format,
                });
            }

            var info;
            $.ajax({
                type: "POST",
                url: site_url + "produtos/get_info_template",
                dataType: "html",
                success: function (data) {
                    info = data;
                },
            });

            $(".more-info").click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $gallery = $(this).closest(".info-gallery"),
                    $add = $gallery.find(".add-infos"),
                    infosSeq = $add.find("li:last-child").data("seq") + 1;
                infosSeq = infosSeq ? infosSeq : 0;
                var vid = info;
                vid = vid.replace(new RegExp("{key}", "g"), infosSeq);
                $add.append(vid);

                if ($(".info-language").length > 0) {
                    $(".info-language:last").select2({
                        templateResult: format,
                        templateSelection: format,
                    });
                }
            });
            $(".add-infos").on("click", ".remove-infos", function () {
                $(this).closest(".group-remove-infos").remove();
            });
        }
    };

    window.Produtos = new Produtos();
    return Produtos;
})(jQuery, window);
