/*!
 * Classe Comum
 *
 * @author Ramon Barros [ramon@ezoom.com.br]
 * @date   2015-03-04
 * Copyright (c) 2014 Ezoom Agency
 */
/* jslint devel: true, unparam: true, indent: 4 */
(function (window) {
    /**
     * Inicia propriedades da classe
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-04
     */
    var Comum = function () {
        this.basePath = "";
        this.commonPath = "../assets/";
        this.rootPath = "../";
        this.DEV = false;
        this.componentsPath = "../assets/components/";
        this.layoutApp = false;
        this.module = module;

        this.primaryColor = "#5BC0DE";
        this.dangerColor = "#bd362f";
        this.successColor = "#8bbf61";
        this.infoColor = "#4193d0";
        this.warningColor = "#ab7a4b";
        this.inverseColor = "#424242";

        this.themerPrimaryColor = this.primaryColor;

        if (Url.host == "localhost" || /192\.168\.1/.test(Url.host)) {
            localStorage.debug = true;
        } else {
            delete localStorage.debug;
        }

        $("body").removeClass("preload");

        return this.__constructor();
    };

    /**
     * Construtor da classe
     * @author Ramon Barros [ramon@ezoom.com.br]
     * @date   2015-03-04
     * @return {Comum}
     */
    Comum.prototype.__constructor = function () {
        var self = this;
        this.validateSubmitForm();
        this.deleteRegisters();
        this.sortPicker();
        this.filter();
        this.formFilter();
        this.pagination();
        this.notify();
        this.profiler();
        this.subMenuFather();
        this.select2();
        this.menu();
        this.sidebar();
        this.magnific();
        this.changePassword();
        this.initTable();

        $(window).load(function () {
            self.inputEditor();
            self.tableEditor();
            self.listEditor();
        });

        $(".nav-pills").each(function (i, el) {
            if ($(el).find("li > a").length <= 1)
                $(el).addClass("hidden").hide();
        });

        return this;
    };

    /**
     * Para criar uma aba com multiplo select
     * @author Gabriel Stringari && Ralf da Rocha
     * @date   2017-10-11
     * @example Exemplo pode ser visto em sulbrasil\cms\modules\produtos
     */
    Comum.prototype.multipleSelectTable = function (id, name) {
        // cacheia os  elementos para utilizar
        var $box = $("#" + id);
        if ($box.length == 0) return;

        var $select = $box.find('select[name="select_' + name + '"]'),
            $table = $box.find("#" + id + "-table"),
            layout = $table.find(".no-selected").next()[0].outerHTML;

        $table.find(".no-selected").next().remove();

        // verifica mudança no select principal
        $select.on("change", function () {
            // pega informações sobre a opção selecionada
            var selected = $(this).find("option:selected"),
                value = selected.val(),
                title = selected.text().trim(),
                alreadyAdd = false;

            // verifica se a opção selecionada não é o placeholder
            if (value <= 0) {
                return false;
            }

            // remove a opção selecionada
            selected.remove();

            // esconde linha com texto de nenhum item selecionado
            if ($box.find(".no-selected").is(":visible")) {
                $box.find(".no-selected").hide();
            }

            // verifica se por acaso o item selecionado já exista na lista de selecionados
            alreadyAdd =
                $table.find('input[name="' + name + "[" + value + '][id]"]')
                    .length > 0;

            // se o item não existir cria elemento na lista
            if (!alreadyAdd) {
                var dataAttr = selected.data();
                var html = layout;
                html = html.replace(new RegExp("{id}", "g"), value);
                html = html.replace(new RegExp("{title}", "g"), title);
                $.each(dataAttr, function (i, el) {
                    html = html.replace(new RegExp("{" + i + "}", "g"), el);
                });
                $box.find("#" + id + "-table").append(html);
            }

            // reseta sortable
            $box.find("#" + id + "-table").sortable("refresh");

            // reseta select para o placeholder
            $(this).val(null).trigger("change");
        });

        // função para corrigir width
        var fixHelper = function (e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        };

        // carrega plugin para ordenação
        $box.find("#" + id + "-table").sortable({
            axis: "y",
            containment: "parent",
            cursor: "move",
            handle: ".handle",
            helper: fixHelper,
            tolerance: "pointer",
        });

        // ao clicar em deletar algum item
        $("body").on("click", "#" + id + " .delete-item", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // pega informações sobre o item a ser excluído
            var value = $(this).data("id"),
                title = $(this).data("title"),
                dataAttr = "";
            $.each($(this).closest(".selectable").data(), function (i, val) {
                if (i != "sortableItem")
                    dataAttr += " data-" + i + '="' + val + '"';
            });

            // retorna a opção dele no select principal
            $select.append(
                '<option value="' +
                    value +
                    '"' +
                    dataAttr +
                    ">" +
                    title +
                    "</option>"
            );

            // remove elemento da listagem
            $box.find('tr[data-id="' + value + '"]').remove();

            // verifica se a listagem está vazia
            if ($box.find("#" + id + "-table").find("tr").length == 1) {
                // se estiver exibe mensagem de nenhum item selecionado
                $box.find(".no-selected").show();
            }

            // reseta sortable
            $box.find("#" + id + "-table").sortable("refresh");
        });
    };

    /**
     * Remove a imagem single-file (gallery)
     * @author Michael Cruz [michael@ezoom.com.br]
     * @date   2016-01-25
     * @return {[type]}   [description]
     */
    Comum.prototype.removeImage = function () {
        $("body").on("click", ".remove-image", function (e) {
            e.stopPropagation();
            e.preventDefault();

            var wrapper = $(this).closest(".upload-wrapper"),
                id = wrapper.find('input[type="file"]').attr("name");
            $.each(files, function (k, v) {
                if (id == v.divId) {
                    files.splice(k, 1);
                    return false;
                }
            });

            wrapper
                .find("a.fancybox")
                .removeClass("fancybox")
                .prop("href", "#");
            wrapper.find(".remove-image").addClass("hide");
            wrapper.find('input[type="checkbox"]').prop("checked", true);

            wrapper
                .find(".upload-image")
                .html(
                    "<span>" +
                        wrapper.find(".upload-image").data("dim") +
                        "</span>"
                );
        });
    };

    /**
     * [inputEditor description]
     * @author Henrique[henrique@ezoom.com.br]
     * @date   2017-01-03
     */
    Comum.prototype.inputEditor = function ($selector) {
        $selector = $selector ? $selector : $(".inputWithCK");

        if ($selector.length) {
            var myToolBar = [
                {
                    name: "verticalCustomToolbar",
                    groups: ["basicstyles"],
                    items: ["Bold"],
                },
            ];
            var config = {};
            config.toolbar = myToolBar;
            config.autoParagraph = false;
            config.enterMode = Number(2);
            config.fillEmptyBlocks = false;
            config.basicEntities = false;

            $selector.each(function (i, data) {
                CKEDITOR.inline($(data).attr("id"), config);
            });

            for (var i in CKEDITOR.instances) {
                if (
                    $("#" + CKEDITOR.instances[i].name).hasClass("inputWithCK")
                ) {
                    if (CKEDITOR.instances[i].prevented === true) continue;

                    //PREVINE PULAR LINHA
                    CKEDITOR.instances[i].on("key", function (e) {
                        if (e.data.keyCode == 13 || e.data.keyCode == 2228237) {
                            // ENTER ou SHIFT ENTER
                            e.cancel();
                            if (e.data.keyCode == 13)
                                // ENTER
                                $("#validateSubmitForm").submit();
                        }
                    });

                    //ATUALIZAR VALOR INPUT REAL
                    CKEDITOR.instances[i].on("change", function (e) {
                        if (typeof CKEDITOR !== "undefined") {
                            var instance;

                            for (instance in CKEDITOR.instances) {
                                CKEDITOR.instances[instance].updateElement();
                                $("#" + instance).trigger("blur");
                            }
                        }
                    });

                    CKEDITOR.instances[i].prevented = true;
                }
            }
        }
    };

    Comum.prototype.tableEditor = function ($selector) {
        $selector = $selector ? $selector : $(".table-editor");

        if ($selector.length) {
            var myToolBar = [
                {
                    name: "verticalCustomToolbar",
                    items: ["Table"],
                },
                {
                    name: "basicstyles",
                    items: [
                        "Format",
                        "Bold",
                        "Italic",
                        "Strike",
                        "RemoveFormat",
                    ],
                },
            ];

            var config = {};
            config.toolbar = myToolBar;

            $selector.each(function (i, data) {
                CKEDITOR.replace($(data).attr("id"), config);
            });
        }
    };

    Comum.prototype.listEditor = function ($selector) {
        $selector = $selector ? $selector : $(".list-editor");

        if ($selector.length) {
            var myToolBar = [
                {
                    name: "Paragraph",
                    items: ["NumberedList", "BulletedList"],
                },
                {
                    name: "basicstyles",
                    items: [
                        "Format",
                        "Bold",
                        "Italic",
                        "Strike",
                        "RemoveFormat",
                    ],
                },
            ];

            var config = {};
            config.toolbar = myToolBar;

            $selector.each(function (i, data) {
                CKEDITOR.replace($(data).attr("id"), config);
            });
        }
    };

    /**
     * Construtor da classe
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @return {Comum}
     */
    Comum.prototype.colorPicker = function () {
        setTimeout(function () {
            $(".colorpickerField").each(function () {
                var $this = $(this);
                $(this)
                    .ColorPicker({
                        onSubmit: function (hsb, hex, rgb, el) {
                            $(el).val(hex);
                            $(el).ColorPickerHide();
                        },
                        onBeforeShow: function () {
                            $this.ColorPickerSetColor(this.value);
                        },
                        onChange: function (hsb, hex, rgb) {
                            $this.val(hex);
                        },
                    })
                    .bind("keyup", function () {
                        $(this).ColorPickerSetColor(this.value);
                    });
            });
        }, 1000);
    };

    Comum.prototype.magnific = function (getClass) {
        var mfpOpen = true;

        getClass = getClass
            ? getClass
            : $(".magnific:not(.slick-cloned)").parent();

        getClass.each(function (index, el) {
            $(this).magnificPopup({
                delegate: ".magnific:not(.slick-cloned)",
                type: "image",
                iframe: {
                    patterns: {
                        youtube: {
                            index: "youtube.com",
                            id: "v=",
                            src: "//www.youtube.com/embed/%id%?autoplay=0",
                        },
                    },
                },
                tClose: "",
                mainClass: "mfp-fade",
                removalDelay: 300,
                disableOn: function () {
                    return mfpOpen;
                },
                gallery: {
                    enabled: true,
                    tPrev: "",
                    tNext: "",
                    tCounter:
                        '<span class="mfp-counter">%curr% / %total%</span>',
                },
            });
        });

        // Set the flag using slick’s events
        $(".slick-slider").on("beforeChange", function () {
            mfpOpen = false;
        });

        $(".slick-slider").on("afterChange", function () {
            mfpOpen = true;
        });
    };

    Comum.prototype.profiler = function () {
        $("#codeigniter_profiler").css("padding", "").addClass("menu-on");
    };

    Comum.prototype.ajaxSelect = function (params) {
        var xhr = false,
            $element = $(params.element),
            $target = $(params.target),
            url = $element.data("src"),
            item = $element.data("item");

        $element.removeAttr("data-src").on("change", function () {
            var v = this.value;
            if (v == "") return;

            $target.closest(".field").addClass("loading");

            xhr = $.ajax({
                type: "POST",
                url: url,
                data: { id: v, item: item },
                dataType: "json",
                success: function (data) {
                    var html = "";
                    for (var i = 0; i < data.length; i++) {
                        html +=
                            '<option value="' +
                            data[i].id +
                            '">' +
                            data[i].title +
                            "</option>";
                    }
                    $target.html(html);
                    $target.trigger("change");
                },
                complete: function () {
                    $target.closest(".field").removeClass("loading");
                    xhr = false;
                },
            });
        });
    };

    Comum.prototype.subMenuFather = function () {
        $(".subMenuFather").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var menu = $(this).closest("li.hasSubmenu");
            if (menu.hasClass("collapse-in")) {
                menu.removeClass("collapse-in")
                    .find("> ul")
                    .css("display", "none");
            } else {
                menu.addClass("collapse-in")
                    .find("> ul")
                    .css("display", "block");
            }
            setTimeout(function () {
                $("#menu-list").mCustomScrollbar("update");
            }, 500);
        });
        // Auto open Menu
        $("#menu-list")
            .find(".active")
            .each(function () {
                $(this)
                    .parents("li")
                    .children(".subMenuFather")
                    .siblings("ul")
                    .css("display", "block")
                    .parent()
                    .addClass("collapse-in");
                $(this)
                    .parents("li")
                    .siblings(".collapse-in")
                    .children(".subMenuFather")
                    .click();
            });
    };

    /**
     * Construtor da classe
     * @author Diogo Taparello e Ralf da Rocha [diogo@ezoom.com.br]
     * @return {Comum}
     */
    Comum.prototype.cropImage = function () {
        var imgCrop = false;
        $(".crop-image").on("click", function (e) {
            var timestamp = new Date().getTime(),
                el = this,
                img = $(this).data("imagesite"),
                id = $(this).closest(".template-upload").data("id");

            $("#modal-crop").prop("data-id", id);
            $("#modal-crop .image-crop-holder").addClass("loading");
            $("<img/>", {
                src: site_url + img + "?" + timestamp,
            }).load(function () {
                var image = $(this);
                image.addClass("img-responsive");
                $("#modal-crop .image-crop-holder").append(image);
                setTimeout(function () {
                    $("#modal-crop .image-crop-holder").removeClass("loading");
                    setTimeout(function () {
                        var w = image.width(),
                            h = image.height();
                        $("#modal-crop").find('input[name="image"]').val(img);
                        $("#modal-crop")
                            .find('input[name="image_width"]')
                            .val(w);
                        $("#modal-crop")
                            .find('input[name="image_height"]')
                            .val(h);
                        $("#modal-crop").find(".submit").removeAttr("disabled");
                        image.Jcrop(
                            {
                                onChange: updatePreview,
                                onSelect: updatePreview,
                            },
                            function () {
                                imgCrop = this;
                                imgCrop.setOptions({
                                    bgFade: true,
                                });
                                imgCrop.setSelect([
                                    w / 4,
                                    h / 4,
                                    w / 4 + w / 2,
                                    h / 4 + h / 2,
                                ]);
                                imgCrop.ui.selection.addClass(
                                    "jcrop-selection"
                                );
                            }
                        );
                    }, 500);
                }, 50);
            });
        });

        $("#modal-crop").on("hidden.bs.modal", function (e) {
            if (imgCrop != false) imgCrop.destroy();
            $("#modal-crop .image-crop-holder img").remove();
            $("#modal-crop").find('input[type="hidden"]').val("");
            $("#modal-crop").find(".submit").attr("disabled", "disabled");
        });

        $("#modal-crop").on("click", ".crop-cancel", function (e) {
            e.preventDefault();
            $("#modal-crop .close").trigger("click");
        });

        function updatePreview(c) {
            $("#modal-crop").find('input[name="crop_x"]').val(c.x);
            $("#modal-crop").find('input[name="crop_y"]').val(c.y);
            $("#modal-crop").find('input[name="crop_width"]').val(c.w);
            $("#modal-crop").find('input[name="crop_height"]').val(c.h);
        }

        $("body").on("click", "#modal-crop form button.submit", function () {
            var form = $("#modal-crop form");
            $(form)
                .find(".submit")
                .attr("disabled", "disabled")
                .html(
                    '<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>'
                )
                .addClass("loading");
            $.ajax({
                type: "POST",
                url: $(form).attr("action"),
                data: $(form).serialize(),
                dataType: "json",
                complete: function () {
                    $(form)
                        .find(".submit")
                        .removeAttr("disabled")
                        .html(i18n.recortar)
                        .removeClass("loading");
                },
                success: function (data) {
                    var obj = {
                        layout: "top",
                        text: data.message,
                        type: data.classe,
                    };
                    openNotification(obj);

                    if (data.status) {
                        // Atualiza thumb
                        var timestamp = new Date().getTime();
                        $(
                            '.imageOlds .template-upload[data-id="' +
                                $("#modal-crop").prop("data-id") +
                                '"] .preview img'
                        ).attr("src", data.image + "&" + timestamp);
                        $(
                            '.imageOlds .template-upload[data-id="' +
                                $("#modal-crop").prop("data-id") +
                                '"] .config-file-close'
                        ).trigger("click");
                        $("#modal-crop .close").trigger("click");
                    }
                },
            });
        });
    };

    Comum.prototype.menu = function () {
        $(".company-config-dropdown ul li").on("click", function () {
            $.ajax({
                type: "POST",
                url: site_url + "comum/choose_company",
                data: {
                    id: $(this).data("id"),
                },
                dataType: "json",
                success: function (data) {
                    if (data.status)
                        window.location.href = window.location.href.replace(
                            site_url,
                            site_url + data.lang + "/"
                        );
                },
            });
        });
        $("#menu-config-company > div.pull-left").on("click", function () {
            $(".menu-config-dropdown").stop().hide("slow");
            $(".company-config-dropdown").stop().toggle("slow");
        });

        $("#menu-config > span").on("click", function () {
            $(".company-config-dropdown").stop().hide("slow");
            $(".menu-config-dropdown").stop().toggle("slow");
        });

        $("#toggle-sidebar").on("click", function () {
            if ($("#menu-left").hasClass("hidden")) {
                $("#menu-left").removeClass("hidden").removeClass("hidden-xs");
                $(".containerWithSidebar, footer")
                    .removeClass("menu-off")
                    .addClass("menu-on");
            } else {
                $(".containerWithSidebar, footer")
                    .removeClass("menu-on")
                    .addClass("menu-off");
                $("#menu-left").addClass("hidden");
            }
        });

        $(".menu-action-language span").on("click", function () {
            $(".menu-action-language ul").toggleClass("hide show");
        });

        $("#menu-list").mCustomScrollbar({
            axis: "x",
        });
    };

    Comum.prototype.sidebar = function () {
        var self = this;
        $.expr[":"].icontains = function (obj, index, meta, stack) {
            return (
                (obj.textContent || obj.innerText || jQuery(obj).text() || "")
                    .toLowerCase()
                    .indexOf(meta[3].toLowerCase()) >= 0 ||
                self
                    .urlSlug(obj.innerText, { delimiter: " " })
                    .indexOf(meta[3].toLowerCase()) >= 0
            );
        };

        var $menu = $("#menu-list"),
            $noModuldes = $("#menu-no-modules");
        $menu.find(".collapse-in").addClass("original");
        $("#sidebar-search")
            .on("keyup", "input", function (e) {
                if ($.inArray(e.keyCode, [9, 16, 38, 40]) !== -1) {
                    return false;
                }
                var v = $(this).val();
                $noModuldes.addClass("hidden");
                if (v.length > 0) {
                    $menu.find("ul, li").hide();
                    var $found = $menu
                        .find("span")
                        .filter(":icontains(" + v + ")");
                    $found
                        .show()
                        .parentsUntil("#menu-list")
                        .filter("li,ul")
                        .show()
                        .find(".hasSubmenu")
                        .addClass("collapse-in")
                        .children("ul")
                        .show();
                    $found
                        .closest("li")
                        .filter(".hasSubmenu")
                        .find("ul, li")
                        .show();
                    if (!$found.length) {
                        $noModuldes.removeClass("hidden");
                    }
                } else {
                    $menu.find("li").removeClass("collapse-in").show();
                    $menu.find("ul").hide();
                    $menu
                        .find(".original")
                        .children(".subMenuFather")
                        .siblings("ul")
                        .css("display", "block")
                        .parent()
                        .addClass("collapse-in");
                }
                $menu.mCustomScrollbar("update");
            })
            .on("keydown", "input", function (e) {
                if (e.keyCode == 40) {
                    e.preventDefault();
                    $menu.find("a").filter(":visible:first").trigger("focus");
                } else if (e.keyCode == 38) {
                    e.preventDefault();
                    $menu.find("a").filter(":visible:last").trigger("focus");
                }
            })
            .on("submit", function (e) {
                e.preventDefault();
            });

        $menu
            .find("a")
            .on("focus", function () {
                if ($(this).is(":active")) {
                    return;
                }
                $(this)
                    .addClass("unreal-focus")
                    .on("keydown.kbNavigate", function (e) {
                        var eq = 0,
                            found = false;
                        if (e.keyCode == 40) {
                            e.preventDefault();
                            $menu.find("a:visible").each(function () {
                                if (found !== false) return false;
                                eq++;
                                if ($(this).hasClass("unreal-focus")) {
                                    found = eq;
                                }
                            });
                            if (
                                $menu
                                    .find("a")
                                    .filter(":visible:eq(" + eq + ")").length
                            ) {
                                $menu
                                    .find("a")
                                    .filter(":visible:eq(" + eq + ")")
                                    .trigger("focus");
                            } else {
                                $("#sidebar-search").find("input").focus();
                            }
                        } else if (e.keyCode == 38) {
                            e.preventDefault();
                            $menu.find("a:visible").each(function () {
                                if (found !== false) return false;
                                if ($(this).hasClass("unreal-focus")) {
                                    found = eq;
                                    eq--;
                                } else {
                                    eq++;
                                }
                            });
                            if (found == 0) {
                                $("#sidebar-search").find("input").focus();
                            } else {
                                $menu
                                    .find("a")
                                    .filter(":visible:eq(" + eq + ")")
                                    .trigger("focus");
                            }
                        }
                    });
                var top = $(window).scrollTop();
                $("#menu-list").mCustomScrollbar(
                    "scrollTo",
                    $(this).offset().top -
                        top -
                        ($("#menu-list").offset().top - top) -
                        ($("#menu-list .mCSB_container").offset().top - top)
                );
            })
            .on("blur", function () {
                $(this).removeClass("unreal-focus").off("keydown.kbNavigate");
            });

        if (module == "home") {
            $("#sidebar-search").find("input").focus();
        }
    };

    Comum.prototype.formFilter = function () {
        if ($("#form_filter").length > 0) {
            $("#form_filter").on("submit", function () {
                var $btn = $(this).find('[type="submit"].btn-primary');
                if ($btn.length) {
                    $btn.attr("disabled", "disabled")
                        .html(
                            '<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>'
                        )
                        .addClass("loading");
                }
            });
        }
    };

    /**
     * Submits dos formularios com as imagens da simple/multi gallery
     * @author Ralf da Rocha [ralf@ezoom.com.br]
     * $formulario
     * @date   2015-04-02
     */
    Comum.prototype.validateSubmitForm = function () {
        if ($("#validateSubmitForm").length > 0) {
            $.validator.setDefaults({
                submitHandler: function (form) {
                    if (
                        $(form).data("sending") === true ||
                        $(form).hasClass("sending")
                    )
                        return false;

                    $(form).addClass("sending").data("sending", true);

                    var $btn = $(form).find('[type="submit"]');
                    $btn.data("original", $btn.html());
                    $btn.attr("disabled", "disabled")
                        .html(
                            '<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>'
                        )
                        .addClass("loading");

                    if ($(".required-image").length > 0) {
                        // Verifica imagens obrigatórias
                        if (
                            $("form")
                                .find($(".required-image"))
                                .find($(".img-holder")).length > 0
                        ) {
                            var obj = {
                                layout: "top",
                                text: i18n.campos_obrigatorios,
                                type: "error",
                            };
                            openNotification(obj);
                            $(form).data("sending", false);
                            $btn.html($btn.data("original"))
                                .removeClass("btn-success btn-stroke")
                                .addClass("btn-primary")
                                .removeAttr("disabled");
                        } else {
                            $.each(files, function (index, value) {
                                value.submit();
                            });
                            sendForm(form);
                        }
                    } else {
                        if (files.length > 0) {
                            // Envia Imagens
                            $.each(files, function (index, value) {
                                value.submit();
                            });
                        }
                        sendForm(form);
                    }
                },
                showErrors: function (map, list) {
                    sendFormError(this, list);
                },
            });
            $("#validateSubmitForm").validate({
                rules: "",
                ignore: [],
                invalidHandler: function (event, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        var obj = {
                            layout: "top",
                            text: i18n.campos_obrigatorios,
                            type: "error",
                        };
                        openNotification(obj);
                    }
                },
            });
        }
    };

    /**
     * Construtor da classe
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @return {Comum}
     */
    Comum.prototype.deleteRegisters = function (images, galleries) {
        $("body").off("click", ".delete-button button");
        $("body").on("click", ".delete-button button", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var url = $(this).closest("a").attr("href"),
                $table = $(this).closest("table"),
                self = $(this).closest("a");
            bootbox.confirm(i18n.efetuar_exclusao, function (result) {
                if (result) {
                    if (self.hasClass("no-ajax")) {
                        window.location = url;
                    } else {
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                delete: "true",
                                images: images,
                                galleries: galleries,
                            },
                            dataType: "json",
                            success: function (data) {
                                var obj = {
                                    layout: "top",
                                    text: data.message,
                                    type: data.classe,
                                };
                                openNotification(obj);
                                if (data.status && data.classe == "success") {
                                    var id = url.split("/").pop();
                                    $table
                                        .find('tr[data-id="' + id + '"]')
                                        .css("position", "relative")
                                        .fadeOut(1000, function () {
                                            $(this).remove();
                                            if (
                                                $table.find(
                                                    "tr.selectable .checkbox :checked"
                                                ).length > 0
                                            )
                                                $table
                                                    .nextAll(".row:eq(0)")
                                                    .find(".checkboxs_actions")
                                                    .removeClass("hide")
                                                    .show();
                                            else
                                                $table
                                                    .nextAll(".row:eq(0)")
                                                    .find(".checkboxs_actions")
                                                    .hide();
                                            updateDeleteForm($table);
                                        });
                                }
                            },
                            error: function (data) {
                                var obj = {
                                    layout: "top",
                                    text: i18n.verifique_relacionamento,
                                    type: "error",
                                };
                                openNotification(obj);
                            },
                        });
                    }
                }
            });
        });
        $("body").off("submit", ".delete-all-form");
        if (images !== undefined && images.length > 0) {
            $(".delete-all-form").append(
                '<input type="hidden" name="images" value="' + images + '" />'
            );
        }
        if (galleries !== undefined && galleries.length > 0) {
            $(".delete-all-form").append(
                '<input type="hidden" name="galleries" value="' +
                    galleries +
                    '" />'
            );
        }
        $("body").on("submit", ".delete-all-form", function (e) {
            e.preventDefault();
            var currentForm = this,
                $table = $(".table");
            self = $(this);
            bootbox.confirm(i18n.efetuar_exclusao, function (result) {
                if (result) {
                    if (self.hasClass("no-ajax")) {
                        currentForm.submit();
                    } else {
                        $.ajax({
                            type: "POST",
                            url: $(currentForm).attr("action"),
                            data: $(currentForm).serialize(),
                            dataType: "json",
                            success: function (data) {
                                var obj = {
                                    layout: "top",
                                    text: data.message,
                                    type: data.classe,
                                };
                                openNotification(obj);
                                var i,
                                    id = data.id
                                        ? data.id.split(",")
                                        : [url.split("/").pop()];
                                for (i = 0; i <= id.length; i++) {
                                    if (id.hasOwnProperty(i)) {
                                        $table
                                            .find('tr[data-id="' + id[i] + '"]')
                                            .css("position", "relative")
                                            .fadeOut(1000, function () {
                                                $(this).remove();

                                                if (
                                                    $table.find(
                                                        "tr.selectable .checkbox :checked"
                                                    ).length > 0
                                                )
                                                    $table
                                                        .nextAll(".row:eq(0)")
                                                        .find(
                                                            ".checkboxs_actions"
                                                        )
                                                        .removeClass("hide")
                                                        .show();
                                                else
                                                    $table
                                                        .nextAll(".row:eq(0)")
                                                        .find(
                                                            ".checkboxs_actions"
                                                        )
                                                        .hide();
                                                updateDeleteForm($table);
                                            });
                                    }
                                }
                            },
                            error: function (data) {
                                var obj = {
                                    layout: "top",
                                    text: i18n.verifique_relacionamento,
                                    type: "error",
                                };
                                openNotification(obj);
                            },
                        });
                    }
                }
            });
        });
    };

    Comum.prototype.select2 = function () {
        if ($(".select2:not(.clonable)").length > 0)
            $(".select2:not(.clonable)").select2({
                width: "100%",
                allowClear: true,
            });
    };

    Comum.prototype.notify = function () {
        // Auto Notify
        if ($(".auto-notify").length > 0) {
            openNotification({
                layout: "top",
                text: $(".auto-notify").data("message"),
                type: $(".auto-notify").data("classe"),
            });
            $(".auto-notify").remove();
        }
    };

    Comum.prototype.filter = function () {
        // Filtro
        $("#filter-show, .filter-show").on("change", function () {
            $(this).closest("form").submit();
        });
    };

    Comum.prototype.pagination = function () {
        // Paginação
        $(".pagination").on("click", ".disabled > a", function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
    };

    Comum.prototype.bootstrap = function () {
        if ($('[data-toggle="tooltip"]').length > 0)
            $('[data-toggle="tooltip"]').tooltip();

        if ($(".datetimepicker").length > 0) {
            $(".datetimepicker").datetimepicker({
                locale: "pt-br",
                format: "DD/MM/YYYY HH:mm",
            });
        }

        if ($(".yearpicker").length > 0) {
            $(".yearpicker").datetimepicker({
                locale: "pt-br",
                format: "YYYY",
            });
        }

        if ($(".datepicker").length > 0) {
            $(".datepicker").datetimepicker({
                locale: "pt-br",
                format: "DD/MM/YYYY",
            });
        }

        if ($(".timepicker").length > 0) {
            $(".timepicker").datetimepicker({
                locale: "pt-br",
                format: "HH:mm",
            });
        }
    };

    Comum.prototype.sortable = function (newUrl) {
        var self = this,
            newUrl =
                typeof newUrl !== "undefined"
                    ? newUrl
                    : site_url + self.module + "/sort/";
        $("#sortable").sortable({
            axis: "y",
            update: function (event, ui) {
                var data = $(this).sortable("serialize");
                $.ajax({
                    data:
                        data +
                        "&show=" +
                        $("#filter-show").val() +
                        "&page=" +
                        ($(".pagination").length
                            ? $(".pagination .active").text()
                            : 1),
                    dataType: "json",
                    type: "POST",
                    url: newUrl,
                    success: function (data) {
                        var obj = {
                            layout: "top",
                            text: data.message,
                            type: data.classe,
                        };
                        openNotification(obj);
                        if (data.status && data.redirect) {
                            setTimeout(function () {
                                window.location = window.location.href;
                            }, 400);
                        }
                    },
                    error: function (data) {
                        var obj = {
                            layout: "top",
                            text: i18n.erro_ordem,
                            type: "error",
                        };
                        openNotification(obj);
                    },
                });
            },
        });
        $("#sortable td")
            .not(".moveSortable")
            .mousedown(function (event) {
                event.stopImmediatePropagation();
            });
    };

    Comum.prototype.sortPicker = function () {
        $("body").on("change", ".sort-picker", function (e) {
            $(this).siblings(".sort-picker").addClass("loading");
            $(this).closest(".sort-form").submit();
        });
    };

    Comum.prototype.toggleStatus = function (newUrl) {
        var self = this,
            newUrl =
                typeof newUrl !== "undefined"
                    ? newUrl
                    : site_url + self.module + "/active";

        // ATIVAR/DESATIVAR
        $(".table .make-switch :checkbox").change(function () {
            if (!$(this).parents().hasClass("tab-pane")) {
                $.ajax({
                    type: "POST",
                    url: newUrl,
                    data: {
                        id: $(this).parents("tr").data("id"),
                        actived: $(this).is(":checked"),
                        type: $(this).attr("name"),
                    },
                    dataType: "json",
                    success: function (data) {
                        var obj = {
                            layout: "top",
                            text: data.message,
                            type: data.classe,
                        };
                        openNotification(obj);
                    },
                });
            }
        });
    };

    /**
     * Construtor da classe
     * @author Diogo Taparello e Ralf da Rocha [diogo@ezoom.com.br]
     * @return {Comum}
     */
    Comum.prototype.upload = function (options, chunkSize) {
        chunkSize = typeof chunkSize === "undefined" ? 1000000 : chunkSize; // 1 MB
        var self = this,
            defaults = {
                formData: {
                    id: $("#inputId").val(),
                    gallerypath: "userfiles/" + self.module,
                    gallerytype: "image",
                    gallerytable: self.module,
                    width: 2000,
                    height: 2000,
                    resize: true,
                },
                dataType: "json",
                autoUpload: false,
                maxNumberOfFiles: 2,
                previewMaxWidth: 75,
                previewMaxHeight: 75,
                previewCrop: true,
                progress: function (e, data) {
                    var progress = parseInt(
                        (data.loaded / data.total) * 100,
                        10
                    );
                    $("#" + data.divId + " .progress .progress-bar").css(
                        "width",
                        progress + "%"
                    );

                    if (progress == 100) {
                        setTimeout(function () {
                            $("#" + data.divId + " .progress").removeClass(
                                "active"
                            );
                            $("#" + data.divId + " .progress .progress-bar")
                                .removeClass("progress-bar-info")
                                .addClass("progress-bar-success");
                        }, 1000);
                    }
                },
                send: function (e, dataFiles) {
                    if (!self.checkExtension(dataFiles)) {
                        if ($("#validateSubmitForm").length) {
                            $("#validateSubmitForm")
                                .removeClass("sending")
                                .data("sending", false);
                            $("#validateSubmitForm")
                                .find('[type="submit"]')
                                .html(
                                    $("#validateSubmitForm")
                                        .find('[type="submit"]')
                                        .data("original")
                                )
                                .removeClass("loading")
                                .removeAttr("disabled");
                        }
                        return false;
                    }
                    if ($("#full-preloader").length) {
                        $("#full-preloader").stop(true).slideDown(300);
                        var t = $("#full-preloader").data("total");
                        t = t === undefined ? 0 : parseInt(t);
                        t += parseInt(dataFiles.total);
                        $("#full-preloader").data("total", t);
                    }
                },
                processalways: function (e, dataFiles) {
                    var uploadFile = dataFiles.files[0],
                        wrapper = $(this).closest(".upload-wrapper");

                    if (/\.(php|html)$/i.test(uploadFile.name)) {
                        var obj = {
                            layout: "top",
                            text:
                                i18n.o_arquivo +
                                " '" +
                                uploadFile.name +
                                "' " +
                                i18n.arquivo_nao_enviado +
                                "\n PHP, HTML.",
                            type: "error",
                        };
                        openNotification(obj);
                        return false;
                    }

                    if (typeof dataFiles.ext !== "undefined") {
                        if (dataFiles.ext[0] == "!") {
                            var patt = new RegExp(
                                dataFiles.ext.replace("!", "")
                            );
                            if (patt.test(uploadFile.name)) {
                                var obj = {
                                    layout: "top",
                                    text:
                                        i18n.o_arquivo +
                                        " '" +
                                        uploadFile.name +
                                        "' " +
                                        i18n.arquivo_nao_enviado_extensao,
                                    type: "error",
                                };
                                openNotification(obj);
                                return false;
                            }
                        } else {
                            var patt = new RegExp(dataFiles.ext);
                            if (!patt.test(uploadFile.name.toLowerCase())) {
                                var ext = dataFiles.ext
                                        .replace(/\|/g, ", ")
                                        .toUpperCase(),
                                    obj = {
                                        layout: "top",
                                        text:
                                            i18n.o_arquivo +
                                            " '" +
                                            uploadFile.name +
                                            "' " +
                                            i18n.arquivo_nao_enviado +
                                            " " +
                                            ext,
                                        type: "error",
                                    };
                                openNotification(obj);
                                return false;
                            }
                        }
                    }
                    if (dataFiles.autoUpload)
                        processImage(dataFiles, $("#validateSubmitForm"));
                    else {
                        dataFiles.divId = $(this).data("id");
                        $.each(files, function (k, v) {
                            if (dataFiles.divId == v.divId) {
                                files.splice(k, 1);
                                return false;
                            }
                        });

                        wrapper
                            .find(".remove-image, .remove-archive")
                            .removeClass("hide");
                        wrapper
                            .find('input[type="checkbox"]')
                            .prop("checked", false);

                        if (dataFiles.imgtype == "archive") {
                            wrapper.find(".download-archive").remove();
                            wrapper
                                .find(".archivePlaceholder")
                                .removeClass("pointer")
                                .html(dataFiles.files[0].name)
                                .attr("title", dataFiles.files[0].name);
                        } else if (dataFiles.imgtype == "favicon")
                            $("#validateSubmitForm")
                                .find("." + dataFiles.imgtype + " span")
                                .addClass("fa fa-file-image-o")
                                .html("");
                        else {
                            wrapper
                                .find("." + dataFiles.imgtype)
                                .html(dataFiles.files[0].preview);
                            wrapper
                                .find("a.magnific")
                                .removeClass("magnific")
                                .prop("href", "#");
                        }

                        files.push(dataFiles);
                    }
                },
                change: function (e, dataFiles) {
                    var opts = $(this).fileupload("option");
                    if ($(this).hasClass("chunk")) {
                        $(this).fileupload("option", {
                            maxChunkSize: chunkSize,
                            formData: {
                                id: opts.formData.id,
                                gallerypath: opts.formData.gallerypath,
                                gallerytype: opts.formData.gallerytype,
                                gallerytable: opts.formData.gallerytable,
                                galleryname: $(this).attr("name"),
                                gallerychunk: true,
                                width: $(this)
                                    .closest(".upload-wrapper")
                                    .data("width"),
                                height: $(this)
                                    .closest(".upload-wrapper")
                                    .data("height"),
                                resize: $(this)
                                    .closest(".upload-wrapper")
                                    .data("resize"),
                            },
                        });
                    } else {
                        $(this).fileupload("option", {
                            maxChunkSize: 0,
                            formData: {
                                id: opts.formData.id,
                                gallerypath: opts.formData.gallerypath,
                                gallerytype: opts.formData.gallerytype,
                                gallerytable: opts.formData.gallerytable,
                                galleryname: $(this).attr("name"),
                                gallerychunk: false,
                                width: $(this)
                                    .closest(".upload-wrapper")
                                    .data("width"),
                                height: $(this)
                                    .closest(".upload-wrapper")
                                    .data("height"),
                                resize: $(this)
                                    .closest(".upload-wrapper")
                                    .data("resize"),
                            },
                        });
                    }

                    if (!self.checkExtension(dataFiles)) return false;
                },
                done: function (e, dataFiles) {
                    $.each(files, function (index, value) {
                        if (value.divId == dataFiles.divId) {
                            value.textStatus = dataFiles.textStatus;
                            return false;
                        }
                    });
                    if (dataFiles.jqXHR.responseJSON.status) {
                        $("#validateSubmitForm").append(
                            '<input type="hidden" name="' +
                                dataFiles.paramName +
                                '" value="' +
                                dataFiles.jqXHR.responseJSON.file_id +
                                '" />'
                        );
                        // $('#validateSubmitForm').append('<input type="hidden" name="files['+ dataFiles.imgtype +'][chunk]" value="'+dataFiles.formData.gallerychunk+'" />');
                    }
                    sendForm($("#validateSubmitForm"));
                },
            },
            options = $.extend(true, defaults, options);

        $(document).ready(function () {
            if (
                $(
                    ".fileuploadImage, .fileuploadArchive, .fileuploadVideo, .fileuploadAudio, .fileupload"
                ).length > 0
            ) {
                $(
                    ".fileuploadImage, .fileuploadArchive, .fileuploadVideo, .fileuploadAudio, .fileupload"
                ).each(function () {
                    var chunk = $(this).hasClass("chunk");
                    options.formData.galleryname = $(this).attr("name");
                    options.formData.gallerychunk = chunk;
                    if (chunk) {
                        options.maxChunkSize = chunkSize;
                    } else {
                        options.maxChunkSize = 0;
                    }
                    if ($(this).hasClass("fileuploadImage")) {
                        options.formData.gallerytype = "image";

                        $(".fileuploadImage").each(function (e) {
                            options.formData.width = $(this)
                                .closest(".upload-wrapper")
                                .data("width");
                            options.formData.height = $(this)
                                .closest(".upload-wrapper")
                                .data("height");
                            options.formData.resize = $(this)
                                .closest(".upload-wrapper")
                                .data("resize");
                            options.formData.gallerytype = "image";
                            $(this).fileupload(options);
                        });
                    }
                    if ($(this).hasClass("fileuploadArchive")) {
                        options.formData.gallerytype = "archive";
                    }
                    if ($(this).hasClass("fileuploadVideo")) {
                        options.formData.gallerytype = "video";
                    }
                    if ($(this).hasClass("fileuploadAudio")) {
                        options.formData.gallerytype = "audio";
                    }
                    $(this).fileupload(options);
                });
            }

            if ($(".fileupload").length > 0) {
                $(".fileupload").fileupload(options);
            }
        });

        $("body").on("click", ".upload-image", function (e) {
            if (!$(this).hasClass("magnific")) {
                $(this).siblings(".fileuploadImage").trigger("click");
            }
        });

        this.removeImage();

        $(".archivePlaceholder").click(function () {
            $(this)
                .closest(".upload-wrapper")
                .find("label.alinhamento")
                .trigger("click");
        });

        if ($(".upload-archive .remove-archive").length > 0) {
            $(".upload-archive .remove-archive").on("click", function (e) {
                e.stopPropagation();
                e.preventDefault();

                var wrapper = $(this).closest(".upload-archive"),
                    id = wrapper.find('input[type="file"]').attr("name");

                $.each(files, function (k, v) {
                    if (id == v.divId) {
                        files.splice(k, 1);
                        return false;
                    }
                });

                $(this).addClass("hide");
                wrapper.find('input[type="checkbox"]').prop("checked", true);
                wrapper.find(".download-archive").remove();

                wrapper
                    .find(".archivePlaceholder")
                    .addClass("pointer")
                    .html(i18n.selecione_arquivo)
                    .attr("title", i18n.selecione_arquivo);
            });
        }
    };

    /**
     * Construtor da classe
     * @author Ralf da Rocha, Henrique Orlandin [ralf@ezoom.com.br, henrique@ezoom.com.br]
     * @date   2016-07-27
     * @return {Comum}
     */
    Comum.prototype.gallery_video = function () {
        if ($(".video-gallery").length > 0) {
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

            if ($(".video-language").length > 0) {
                $(".video-language").select2({
                    templateResult: format,
                    templateSelection: format,
                });
            }

            var video;

            $.ajax({
                type: "POST",
                url: site_url + "comum/get_video_template",
                dataType: "html",
                data: { module: module },
                success: function (data) {
                    video = data;
                },
            });

            $(".more-video").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $gallery = $(this).closest(".video-gallery"),
                    $add = $gallery.find(".add-videos"),
                    videosSeq = $add.find("li:last-child").data("seq") + 1;

                videosSeq = videosSeq ? videosSeq : 1;
                var vid = video;

                vid = vid.replace(new RegExp("{key}", "g"), videosSeq);

                // $(vid).find('.order-input').val(videosSeq);

                $add.append(vid);

                if ($(".video-language").length > 0) {
                    $(".video-language:last").select2({
                        templateResult: format,
                        templateSelection: format,
                    });
                }
            });
            $(".add-videos").on("click", ".remove-videos", function () {
                $(this).closest(".group-remove-videos").remove();
            });
        }
    };

    /**
     * Construtor da classe
     * @author Ralf da Rocha [ralf@ezoom.com.br]
     * @date   2016-07-27
     * @return {Comum}
     */
    Comum.prototype.getZipcode = function (callback) {
        $("body").on("keyup", 'input[name*="zipcode"]', function () {
            var v = $(this).val().replace(/\D/g, ""),
                $zip = $(this);
            if (v.length == 8 && v != $zip.data("old")) {
                $zip.data("old", v);
                $zip.parent().addClass("searching");
                var tab = $zip.closest(".address-container");
                $.post(
                    site_url + "comum/get-cep",
                    {
                        cep: v,
                    },
                    function (data) {
                        if (data.resultado == 1) {
                            tab.find('[name*="id_city"]').data(
                                "auto",
                                data.cidade
                            );
                            tab.find('[name*="address"]').val(
                                data.tipo_logradouro + " " + data.logradouro
                            );
                            var stateId = "";
                            tab.find('[name*="id_state"] > option').each(
                                function (i, el) {
                                    if (this.text.substr(0, 2) == data.uf) {
                                        stateId = $(el).attr("value");
                                        $(el).attr("selected", true);
                                        return false;
                                    }
                                }
                            );
                            tab.find('[name*="id_state"]')
                                .val(stateId)
                                .trigger("change");
                            tab.find('[name*="district"]').val(data.bairro);
                            tab.find('[name*="number"]').focus();

                            if (typeof callback === "function") callback(data);
                        }
                        $zip.parent().removeClass("searching");
                    }
                );
            }
        });
    };

    /**
     * Construtor da classe
     * @author Diogo Taparello [diogo@ezoom.com.br]
     * @date   2016-07-27
     * @return {Comum}
     */
    Comum.prototype.changePassword = function () {
        $("body").on("click", ".change-password", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var href = $(this).attr("href"),
                self = this,
                $modal = $("#ajax-modal");

            $modal.load(href, function () {
                $modal.modal();
            });
        });

        $("body").on("submit", ".change-password-modal", function (e) {
            e.preventDefault();
            var content = $(this),
                url = content.prop("action"),
                id = content.find("#inputId").val();

            $.ajax({
                type: "POST",
                url: url,
                dataType: "json",
                data: $(".change-password-modal").serialize(),
                success: function (data) {
                    var obj = {
                        layout: "top",
                        text: data.message,
                        type: data.classe,
                    };
                    openNotification(obj);
                    if (data.status)
                        content
                            .closest(".modal-content")
                            .find(".close")
                            .trigger("click");
                },
                error: function (data) {
                    var obj = {
                        layout: "top",
                        text: i18n.verifique_relacionamento,
                        type: "error",
                    };
                    openNotification(obj);
                },
            });
        });
    };

    /**
     * Create a web friendly URL slug from a string.
     *
     * Requires XRegExp (http://xregexp.com) with unicode add-ons for UTF-8 support.
     *
     * Although supported, transliteration is discouraged because
     *     1) most web browsers support UTF-8 characters in URLs
     *     2) transliteration causes a loss of information
     *
     * @author Sean Murphy <sean@iamseanmurphy.com>
     * @copyright Copyright 2012 Sean Murphy. All rights reserved.
     * @license http://creativecommons.org/publicdomain/zero/1.0/
     *
     * @param string s
     * @param object opt
     * @return string
     */
    Comum.prototype.urlSlug = function (s, opt) {
        s = String(s);
        opt = Object(opt);

        var defaults = {
            delimiter: "-",
            limit: undefined,
            lowercase: true,
            replacements: {},
            transliterate: typeof XRegExp === "undefined" ? true : false,
        };

        // Merge options
        for (var k in defaults) {
            if (!opt.hasOwnProperty(k)) {
                opt[k] = defaults[k];
            }
        }

        var char_map = {
            // Latin
            À: "A",
            Á: "A",
            Â: "A",
            Ã: "A",
            Ä: "A",
            Å: "A",
            Æ: "AE",
            Ç: "C",
            È: "E",
            É: "E",
            Ê: "E",
            Ë: "E",
            Ì: "I",
            Í: "I",
            Î: "I",
            Ï: "I",
            Ð: "D",
            Ñ: "N",
            Ò: "O",
            Ó: "O",
            Ô: "O",
            Õ: "O",
            Ö: "O",
            Ő: "O",
            Ø: "O",
            Ù: "U",
            Ú: "U",
            Û: "U",
            Ü: "U",
            Ű: "U",
            Ý: "Y",
            Þ: "TH",
            ß: "ss",
            à: "a",
            á: "a",
            â: "a",
            ã: "a",
            ä: "a",
            å: "a",
            æ: "ae",
            ç: "c",
            è: "e",
            é: "e",
            ê: "e",
            ë: "e",
            ì: "i",
            í: "i",
            î: "i",
            ï: "i",
            ð: "d",
            ñ: "n",
            ò: "o",
            ó: "o",
            ô: "o",
            õ: "o",
            ö: "o",
            ő: "o",
            ø: "o",
            ù: "u",
            ú: "u",
            û: "u",
            ü: "u",
            ű: "u",
            ý: "y",
            þ: "th",
            ÿ: "y",

            // Latin symbols
            "©": "(c)",

            // Greek
            Α: "A",
            Β: "B",
            Γ: "G",
            Δ: "D",
            Ε: "E",
            Ζ: "Z",
            Η: "H",
            Θ: "8",
            Ι: "I",
            Κ: "K",
            Λ: "L",
            Μ: "M",
            Ν: "N",
            Ξ: "3",
            Ο: "O",
            Π: "P",
            Ρ: "R",
            Σ: "S",
            Τ: "T",
            Υ: "Y",
            Φ: "F",
            Χ: "X",
            Ψ: "PS",
            Ω: "W",
            Ά: "A",
            Έ: "E",
            Ί: "I",
            Ό: "O",
            Ύ: "Y",
            Ή: "H",
            Ώ: "W",
            Ϊ: "I",
            Ϋ: "Y",
            α: "a",
            β: "b",
            γ: "g",
            δ: "d",
            ε: "e",
            ζ: "z",
            η: "h",
            θ: "8",
            ι: "i",
            κ: "k",
            λ: "l",
            μ: "m",
            ν: "n",
            ξ: "3",
            ο: "o",
            π: "p",
            ρ: "r",
            σ: "s",
            τ: "t",
            υ: "y",
            φ: "f",
            χ: "x",
            ψ: "ps",
            ω: "w",
            ά: "a",
            έ: "e",
            ί: "i",
            ό: "o",
            ύ: "y",
            ή: "h",
            ώ: "w",
            ς: "s",
            ϊ: "i",
            ΰ: "y",
            ϋ: "y",
            ΐ: "i",

            // Turkish
            Ş: "S",
            İ: "I",
            Ç: "C",
            Ü: "U",
            Ö: "O",
            Ğ: "G",
            ş: "s",
            ı: "i",
            ç: "c",
            ü: "u",
            ö: "o",
            ğ: "g",

            // Russian
            А: "A",
            Б: "B",
            В: "V",
            Г: "G",
            Д: "D",
            Е: "E",
            Ё: "Yo",
            Ж: "Zh",
            З: "Z",
            И: "I",
            Й: "J",
            К: "K",
            Л: "L",
            М: "M",
            Н: "N",
            О: "O",
            П: "P",
            Р: "R",
            С: "S",
            Т: "T",
            У: "U",
            Ф: "F",
            Х: "H",
            Ц: "C",
            Ч: "Ch",
            Ш: "Sh",
            Щ: "Sh",
            Ъ: "",
            Ы: "Y",
            Ь: "",
            Э: "E",
            Ю: "Yu",
            Я: "Ya",
            а: "a",
            б: "b",
            в: "v",
            г: "g",
            д: "d",
            е: "e",
            ё: "yo",
            ж: "zh",
            з: "z",
            и: "i",
            й: "j",
            к: "k",
            л: "l",
            м: "m",
            н: "n",
            о: "o",
            п: "p",
            р: "r",
            с: "s",
            т: "t",
            у: "u",
            ф: "f",
            х: "h",
            ц: "c",
            ч: "ch",
            ш: "sh",
            щ: "sh",
            ъ: "",
            ы: "y",
            ь: "",
            э: "e",
            ю: "yu",
            я: "ya",

            // Ukrainian
            Є: "Ye",
            І: "I",
            Ї: "Yi",
            Ґ: "G",
            є: "ye",
            і: "i",
            ї: "yi",
            ґ: "g",

            // Czech
            Č: "C",
            Ď: "D",
            Ě: "E",
            Ň: "N",
            Ř: "R",
            Š: "S",
            Ť: "T",
            Ů: "U",
            Ž: "Z",
            č: "c",
            ď: "d",
            ě: "e",
            ň: "n",
            ř: "r",
            š: "s",
            ť: "t",
            ů: "u",
            ž: "z",

            // Polish
            Ą: "A",
            Ć: "C",
            Ę: "e",
            Ł: "L",
            Ń: "N",
            Ó: "o",
            Ś: "S",
            Ź: "Z",
            Ż: "Z",
            ą: "a",
            ć: "c",
            ę: "e",
            ł: "l",
            ń: "n",
            ó: "o",
            ś: "s",
            ź: "z",
            ż: "z",

            // Latvian
            Ā: "A",
            Č: "C",
            Ē: "E",
            Ģ: "G",
            Ī: "i",
            Ķ: "k",
            Ļ: "L",
            Ņ: "N",
            Š: "S",
            Ū: "u",
            Ž: "Z",
            ā: "a",
            č: "c",
            ē: "e",
            ģ: "g",
            ī: "i",
            ķ: "k",
            ļ: "l",
            ņ: "n",
            š: "s",
            ū: "u",
            ž: "z",
        };

        // Make custom replacements
        for (var k in opt.replacements) {
            s = s.replace(RegExp(k, "g"), opt.replacements[k]);
        }

        // Transliterate characters to ASCII
        if (opt.transliterate) {
            for (var k in char_map) {
                s = s.replace(RegExp(k, "g"), char_map[k]);
            }
        }

        // Replace non-alphanumeric characters with our delimiter
        var alnum =
            typeof XRegExp === "undefined"
                ? RegExp("[^a-z0-9]+", "ig")
                : XRegExp("[^\\p{L}\\p{N}]+", "ig");
        s = s.replace(alnum, opt.delimiter);

        // Remove duplicate delimiters
        s = s.replace(
            RegExp("[" + opt.delimiter + "]{2,}", "g"),
            opt.delimiter
        );

        // Truncate slug to max. characters
        s = s.substring(0, opt.limit);

        // Remove delimiter from ends
        s = s.replace(
            RegExp("(^" + opt.delimiter + "|" + opt.delimiter + "$)", "g"),
            ""
        );

        return opt.lowercase ? s.toLowerCase() : s;
    };

    Comum.prototype.checkExtension = function (dataFiles) {
        var uploadFile = dataFiles.files[0],
            element = $("#" + dataFiles.fileInput[0].id);
        ext = element.data("ext");

        var patt = new RegExp(ext);

        if (element.hasClass("fileuploadVideo")) {
            if (!patt.test(uploadFile.name)) {
                var obj = {
                    layout: "top",
                    text:
                        "O arquivo '" +
                        uploadFile.name +
                        "' não será enviado.\n\nVocê pode enviar arquivos com extensão:\n" +
                        ext.toLowerCase() +
                        ".",
                    type: "error",
                };
                openNotification(obj);
                return false;
            }
        }
        return true;
    };

    /**
     * Controle de select estado / cidade
     * @author Rodrigo Danna [rodrigo.danna@equipe.ezoom.com.br]
     * @date   2018-06-18
     * @return {Comum}
     */
    Comum.prototype.changeState = function () {
        // Busca cidades quando troca de estado
        $('[name*="id_state"]')
            .on("change", function () {
                var value = $(this).val();
                var city_select = $(this)
                    .parents(".tab-content")
                    .find('[name*="id_city"]');
                var selected_city = city_select.data("selected")
                    ? city_select.data("selected")
                    : false;

                if (!value || value == undefined) {
                    return false;
                }

                $.ajax({
                    url: site_url + "comum/get_cities",
                    dataType: "JSON",
                    type: "POST",
                    data: { id_state: value },
                    success: function (response) {
                        if (response.status) {
                            var cities = response.cities;
                            var options = "";

                            for (var i = 0; i < cities.length; i++) {
                                if (
                                    selected_city &&
                                    cities[i].id == selected_city
                                )
                                    options +=
                                        '<option value="' +
                                        cities[i].id +
                                        '" selected>' +
                                        cities[i].name +
                                        "</option>";
                                else
                                    options +=
                                        '<option value="' +
                                        cities[i].id +
                                        '">' +
                                        cities[i].name +
                                        "</option>";
                            }

                            city_select.html(options);
                            city_select.removeAttr("disabled");
                            city_select.select2("destroy");
                            city_select.select2({
                                width: "100%",
                            });
                        }
                    },
                });
            })
            .change();
    };

    Comum.prototype.modal = function () {
        var self = this,
            $modal = $("#ajax-modal");

        $(".chamaModal").on("click", function () {
            var id = $(this).closest("tr").data("id");
            $modal.load(
                site_url + self.module + "/visualizar",
                { id: id },
                function () {
                    $modal.modal();
                }
            );
        });
    };

    /**
     * Cria clonable widget
     * @author Ralf da Rocha
     * @date   2018-05-03
     */
    Comum.prototype.clonable = function (settings) {
        var options = {
                container: null,
                list: null,
                beforeClone: function () {
                    return true;
                }, // Condição para clonar (deve retornar true ou false)
                afterClone: function (item, index) {}, // Retorna o objeto Jquery do item inserido e o indice dele
                afterRemove: function () {},
            },
            clone = false;

        $.extend(options, settings);

        if (
            !options.list ||
            options.list.length == 0 ||
            !options.container ||
            options.container.length == 0
        )
            return;

        var $el = options.list.find("li.hidden");
        $el.removeClass("hidden");
        clone = $el[0].outerHTML;
        $el.remove();

        options.container.on("click", ".add-clonable", function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof options.beforeClone === "function") {
                var canClone = options.beforeClone.apply(options.list);
                if (!canClone) {
                    return false;
                }
            }
            var seq = options.list.find("li:last-child").data("seq") + 1,
                item = clone;
            seq = isNaN(seq) ? 0 : seq;
            item = item.replace(new RegExp("{key}", "g"), seq);
            options.list.append(item);
            item = options.list.find("li:last-child");
            item.removeClass("hidden");
            options.container.find(".no-items").addClass("hidden");

            if (typeof options.afterClone === "function") {
                options.afterClone.apply(options.list, [item, seq]);
            }
        });

        options.container.on("click", ".remove-clonable", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest("li").remove();
            if (options.list.find("li").length == 0) {
                options.container.find(".no-items").removeClass("hidden");
            }
            if (typeof options.afterRemove === "function") {
                options.afterRemove.apply(options.list);
            }
        });
    };

    /**
     * Inicia os componentes de table
     * @author Matheus Cuba
     * @date   2019-03-07
     */
    Comum.prototype.initTable = function () {
        $("[data-table]").each(function () {
            new Table(this);
        });
    };

    /**
     * js
     *
     * @package cms
     * @subpackage table
     * @category js
     * @author Matheus Cuba
     * @copyright 2019 Ezoom
     */

    function Table(element) {
        "use strict";

        var self = this;

        // Váriaveis
        self.data = {
            1: {},
        };

        self.currentLang = 1;

        //Elementos
        self.mainElement;
        self.table = function () {
            return $(self.mainElement).find(
                'table[data-lang="' + self.currentLang + '"]'
            );
        };
        self.tableElementHead = function () {
            return self.table().find("thead");
        };

        self.tableElementBody = function () {
            return self.table().find("tbody");
        };

        self.init = function (element) {
            self.mainElement = element;

            self.initEvents();
            self.buidData();
            self.toggleAddLine();
        };

        self.initEvents = function () {
            $(self.mainElement)
                .find(".multilang-drop .dropdown-menu li a")
                .click(function () {
                    var $element =
                        this.innerHTML + '<span class="caret"></span>';
                    $(self.mainElement)
                        .find(".multilang-drop-view")
                        .html($element);

                    self.changeLang($(this).data("lang"));
                });

            $(self.mainElement)
                .find(".add-line")
                .click(function () {
                    self.addLine();
                });

            $(self.mainElement)
                .find(".add-column")
                .click(function () {
                    self.addColumn();
                    self.toggleAddLine();
                });

            $(self.mainElement)
                .find(".clear-table")
                .click(function () {
                    self.clearTable(true);
                    self.toggleAddLine();
                });

            $(document).on("click", "thead tr th span", function () {
                self.removeColumn($(this).parents("th"));
            });

            $(document).on("click", "tbody tr td span", function () {
                self.removeLine($(this).parents("tr"));
            });
        };

        self.getColumnsCount = function () {
            if (
                self.data[self.currentLang] &&
                self.data[self.currentLang].columns
            )
                return self.data[self.currentLang].columns;

            return 0;
        };

        self.getRowsCount = function () {
            if (self.data[self.currentLang] && self.data[self.currentLang].rows)
                return self.data[self.currentLang].rows;

            return 0;
        };

        self.toggleAddLine = function () {
            $(self.mainElement)
                .find(".add-line")
                .attr("disabled", !self.getColumnsCount());
        };

        self.getNextColumn = function () {
            if (!self.tableElementHead().find("th").length) return 1;

            return (
                Math.max.apply(
                    Math,
                    Array.from(
                        self.tableElementHead().find("th"),
                        function (el) {
                            return parseInt(el.dataset["column"]);
                        }
                    )
                ) + 1
            );
        };

        self.getNextLine = function () {
            if (!self.tableElementBody().find("tr").length) return 1;

            return (
                Math.max.apply(
                    Math,
                    Array.from(
                        self.tableElementBody().find("tr"),
                        function (el) {
                            return parseInt(el.dataset["line"]);
                        }
                    )
                ) + 1
            );
        };

        self.addColumn = function () {
            var currentColumn = self.getNextColumn();

            var commonName =
                "table[langs][" +
                self.currentLang +
                "][columns][" +
                currentColumn +
                "]";
            var $column = $(
                '<th data-column="' +
                    currentColumn +
                    '"><input name="' +
                    commonName +
                    '[title]" placeholder="Clique para Editar"><input type="hidden" name="' +
                    commonName +
                    '[order_by]"><span></span></th>'
            );

            if (
                !self.getColumnsCount() &&
                !self.tableElementHead().find("tr").length
            )
                self.tableElementHead().append('<tr data-line="0">');

            self.tableElementHead().find("tr").append($column);
            self.tableElementBody()
                .find("tr")
                .each(function ($i) {
                    $(this).append(
                        $(
                            '<td><input name="' +
                                commonName +
                                "[rows][" +
                                $(this).data("line") +
                                '][title]"><input type="hidden" name="' +
                                commonName +
                                "[rows][" +
                                $(this).data("line") +
                                '][order_by]"><span></span></td>'
                        )
                    );
                });

            if (self.getColumnsCount()) self.data[self.currentLang].columns++;
            else self.data[self.currentLang] = { columns: 1 };
        };

        self.addLine = function () {
            var currentLine = self.getNextLine();
            var line = $('<tr data-line="' + currentLine + '">');

            for (var index = 0; index < self.getColumnsCount(); index++) {
                var columnn = self
                    .tableElementHead()
                    .find("tr th:nth-child(" + (index + 1) + ")");
                var commonName =
                    "table[langs][" +
                    self.currentLang +
                    "][columns][" +
                    columnn.data("column") +
                    "][rows][" +
                    currentLine +
                    "]";
                line.append(
                    $(
                        '<td><input name="' +
                            commonName +
                            '[title]"><input type="hidden" name="' +
                            commonName +
                            '[order_by]"><span></span></td>'
                    )
                );
            }

            self.tableElementBody().append(line);

            if (self.getRowsCount()) self.data[self.currentLang].rows++;
            else self.data[self.currentLang].rows = 1;
        };

        self.removeLine = function (line) {
            $(line).remove();
            self.data[self.currentLang].rows--;
        };

        self.removeColumn = function (column) {
            var index = $(column).parents("tr").find("th").index(column);
            $(column).remove();
            self.tableElementBody()
                .find("tr td:nth-child(" + (index + 1) + ")")
                .remove();
            self.data[self.currentLang].columns--;
            self.toggleAddLine();
        };

        self.clearTable = function () {
            self.tableElementBody().empty();
            self.tableElementHead().empty();

            self.data[self.currentLang] = {};
        };

        self.buidData = function () {
            $(self.mainElement)
                .find("table[data-lang]")
                .each(function () {
                    self.data[$(this).data("lang")] = {
                        columns: $(this).find("thead tr th").size(),
                        rows: $(this).find("tbody tr").size(),
                    };
                });
        };

        self.changeLang = function (lang) {
            self.table().hide();
            self.currentLang = lang;
            self.table().show();

            self.toggleAddLine();
        };

        self.init(element);
    }

    window.Comum = new Comum();
    return Comum;
})(window);
