/**
 * js
 *
 * @package ezoom_framework
 * @subpackage main
 * @category js
 * @author
 * @copyright 2016 Ezoom
 */

"use strict";
var app;
var $window = $(window);
var mobileBreakpoint = 1024;

function Main() {
    // this.loadFonts();
    this.init();
    this.header();
    this.lazyload();
    this.removeHoverTouch();
    this.form();
    this.menu();
    this.tabs();
}

Main.prototype.init = function () {
    if ($(".common-text").find("table").length > 0) {
        $(".common-text").find("table").wrap("<div class='table-wrap'></div>");
    }

    $("select").selectize();
};

Main.prototype.svgArrow =
    '<?xml version="1.0" encoding="UTF-8"?><svg enable-background="new 0 0 491.996 491.996" version="1.1" viewBox="0 0 492 492" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="m484.13 124.99-16.116-16.228c-5.072-5.068-11.82-7.86-19.032-7.86-7.208 0-13.964 2.792-19.036 7.86l-183.84 183.85-184.05-184.05c-5.064-5.068-11.82-7.856-19.028-7.856s-13.968 2.788-19.036 7.856l-16.12 16.128c-10.496 10.488-10.496 27.572 0 38.06l219.14 219.92c5.064 5.064 11.812 8.632 19.084 8.632h0.084c7.212 0 13.96-3.572 19.024-8.632l218.93-219.33c5.072-5.064 7.856-12.016 7.864-19.224 0-7.212-2.792-14.068-7.864-19.128z"/></svg>';

Main.prototype.tabs = function () {
    $(".current-tab").on("click", function () {
        $(this).closest(".container-tabs").toggleClass("open-tabs");

        $(this)
            .closest(".container-tabs")
            .find(".item")
            .on("click", function () {
                $(".container-tabs").removeClass("open-tabs");
            });
    });
};

/**
 * @param {String} select seletor jquery.
 * @param {function} callback função que retorna id e text do item selecionado.
 */
Main.prototype.customSelect = function (select, callback) {
    var $select = $(select),
        $selected = $select.find(".selected"),
        $list = $select.find(".list"),
        itemSelected = { id: 0, text: "" };

    //Abre e fecha popup
    $selected.on("click.toggleSelect", function (e) {
        $select.toggleClass("open");
    });

    //evento quando seleciona uma opção
    $list.find(".item").on("click.optionSelected", function () {
        itemSelected.id = $(this).data("id");
        itemSelected.text = $(this).find("span").text();
        itemSelected.hasSelected = $(this).hasClass("current") ? true : false;

        $selected.html(itemSelected.text);
        $selected.data("id", itemSelected.id);

        $list.find(".item").removeClass("current");
        $(this).addClass("current");
        $select.removeClass("open");

        if (typeof callback == "function") callback(itemSelected);
    });

    //fecha popup quando clica no nada
    $(document).on("click.closeSelect", function (e) {
        if (
            $(e.target).closest($select).length == 0 &&
            $select.hasClass("open")
        ) {
            $select.removeClass("open");
        }
    });
};

Main.prototype.header = function () {
    $("#open-menu").on("click", function () {
        $("#products-dropdown").removeClass("open");
        $("body").toggleClass("menu-open");
    });

    $window
        .on("scroll.scrollMenu", function () {
            if (
                ($window.scrollTop() > 80 &&
                    $window.outerWidth() > mobileBreakpoint) ||
                ($window.scrollTop() > 45 &&
                    $window.outerWidth() <= mobileBreakpoint)
            ) {
                $("#header").addClass("menu-scroll");
            } else {
                $("#header").removeClass("menu-scroll");
            }
        })
        .trigger("scroll.scrollMenu");
};

Main.prototype.menu = function () {
    var self = this;

    var $header = $("#header"),
        $footer_button = $("#products-footer-btn");

    $header
        .find("#menu .products-menu .nav-item")
        .on("click", function (event) {
            $(this).toggleClass("open");
            $(this)
                .closest("li")
                .find(".nav-list > ul")
                .stop(true, false)
                .slideToggle(300);
        });

    $header
        .find("#menu .products-menu .category-item")
        .on("click", function (event) {
            $(this).toggleClass("open");
            $(this).find(".category-lines").stop(true, false).slideToggle(300);
        });

    $footer_button.on("click", function (e) {
        $(".products-menu").trigger("click");
    });
};

Main.prototype.lazyload = function () {
    $(".lazyload:not(.ondemand)").lazyload({ viewport: false });
};

Main.prototype.form = function () {
    var self = this;

    $("body").on("click", ".dismiss-modal", function () {
        $.magnificPopup.close();
    });

    if (mobile) {
        pickout.to({
            el: ".pickout",
            search: true,
            noResults: i18n.select_no_results,
        });

        pickout.updated(".has-val");
    }

    var forms = $(".ajax-form");

    forms.each(function (index, element) {
        var $form = $(this),
            $selects = $form.find("select:not(.select-product)");

        if ($(".mask-phone").length > 0) {
            self.maskPhone();
        }

        if ($selects.length) {
            var opts = {};
            opts.onBlur = function () {
                if (this.getValue() == "" && this.value != "") {
                    // this.setValue(this.value);
                }
            };
            opts.onFocus = function () {
                this.value = this.getValue();
                this.clear();
                if (this.order >= 30) {
                    this.$control_input.removeAttr("readonly");
                } else {
                    // this.$control_input.attr('readonly','readonly');
                }
            };

            $selects
                .selectize(opts)
                .filter("[required]")
                .on("change", function () {
                    $(this).data("required", true);
                    if (this.value == null) {
                        $(this).closest(".field").addClass("error");
                        retorno = false;
                    } else {
                        $(this).closest(".field").removeClass("error");
                    }
                });
        }

        $form.validate({
            errorClass: "error",
            highlight: function (element, errorClass, validClass) {
                $(element).closest(".field").addClass(errorClass);

                // if ($(element).closest('.field').find('select').length > 0) {
                //     var $target = $(element).closest('.field').find('select'),
                //         target = $target[0].selectize;

                //     // $target.attr('placeholder', $target.data('error'));
                //     target.settings.placeholder = $target.data('error');

                //     target.updatePlaceholder();
                // }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).closest(".field").removeClass(errorClass);

                // if ($(element).closest('.field').find('select')) {
                //     $(element).attr('placeholder', $(element).closest('.field').find('select').data('default'));
                // }
            },
            errorPlacement: function (e, ee) {
                console.log(e, ee);
                return false;
            },
            submitHandler: function (form) {
                if ($form.data("loading")) return false;

                $form.data("loading", true);
                $form.find("button").addClass("loading");

                var data = new FormData();
                if ($form.find('[type="file"]').length > 0) {
                    data.append(
                        "file",
                        $form.find('[type="file"]')[0].files[0]
                    );
                }

                $.each($form.serializeArray(), function (i, field) {
                    data.append(field.name, field.value);
                });

                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        response = JSON.parse(response);
                        $(form).data("loading", false);
                        $(form).find("button").removeClass("loading");

                        if (response.status) {
                            $form[0].reset();
                            $form.find("input, textarea").change();
                            // $(".select").val(null).trigger('change');

                            if (
                                response.redirect &&
                                response.redirect.length > 0
                            ) {
                                window.location.href = response.redirect;
                            } else {
                                app.openDialog({
                                    title: response.title,
                                    message: response.message,
                                    buttons: true,
                                    // iconClass : response.class,
                                    callbacks: {
                                        close: function () {
                                            $("body").removeClass("mfp-opened");
                                            $(
                                                "#common-dialog .icons"
                                            ).removeClass(response.class);
                                            location.reload();
                                        },
                                        open: function () {
                                            $("body").addClass("mfp-opened");
                                            if (response.class) {
                                                $(
                                                    "#common-dialog .icons"
                                                ).addClass(response.class);
                                            }
                                        },
                                    },
                                });
                            }

                            // if ($selects.length) {
                            //     $selects.each(function () { this.selectize.setValue(); });
                            // }
                        } else {
                            app.openDialog({
                                title: response.title,
                                message: response.message,
                                buttons: true,
                                iconClass: response.class,
                            });
                        }
                    },
                    error: function (response) {
                        $form.data("loading", false);
                        $form.find("button").removeClass("loading");
                        app.openDialog({
                            title: "Opss!",
                            message: "Ocorreu um erro inesperado.",
                            iconClass: "error",
                            buttons: true,
                        });
                    },
                });
            },
        });
    });
};

Main.prototype.ajaxSelect = function (params) {
    var xhr = false,
        $element = $(params.element),
        $target = $(params.target),
        url = $element.data("src");

    $element.removeAttr("data-src").on("change", function () {
        var v = this.value;
        if (!mobile) {
            var target = $target[0].selectize;
            if (xhr) xhr.abort();
            target.disable();
            target.clearOptions();
            target.refreshOptions();
        } else {
            // target.enable();
            // $target.prop('disable', true);
        }

        if (v == "") return;

        $target.closest(".field").addClass("loading");

        xhr = $.ajax({
            type: "POST",
            url: url,
            data: { id: v },
            dataType: "json",
            success: function (data) {
                if (!mobile) {
                    for (var i = 0; i < data.length; i++) {
                        target.addOption({
                            value: data[i].id,
                            text: data[i].name,
                        });
                    }
                    target.refreshOptions();
                    if (data.length) {
                        setTimeout(function () {
                            target.enable();
                        }, 10);
                    }
                } else {
                    var html =
                        "<option>" + $target.attr("placeholder") + "</option>";
                    for (var i = 0; i < data.length; i++) {
                        html +=
                            '<option value="' +
                            data[i].id +
                            '">' +
                            data[i].name +
                            "</option>";
                    }
                    $target.html(html);
                    // $target.prop('disable', false);
                    pickout.updated(".pickout");
                }
            },
            complete: function () {
                $target.closest(".field").removeClass("loading");
                xhr = false;
            },
        });
    });
};

Main.prototype.elementInViewport = function (el) {
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var elHeight = $(el).height() / 2;
    var rect = el.getBoundingClientRect();

    return (
        rect.bottom - elHeight <=
        (window.innerHeight || document.documentElement.clientHeight)
    );

    // return (
    //     (rect.bottom - elHeight) <= (window.innerHeight || document.documentElement.clientHeight) &&
    //     rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    // );
};

Main.prototype.initSlick = function (getClass, params) {
    getClass = getClass ? getClass : ".carousel";
    var paramsDefault = {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: false,
        autoplay: false,
        autoplaySpeed: 8000,
        dots: false,
        arrows: false,
        nextArrow:
            '<button type="button" class="arrow-next">' +
            app.svgArrow +
            "<span></span></button>",
        prevArrow:
            '<button type="button" class="arrow-prev">' +
            app.svgArrow +
            "<span></span></button>",
        speed: 600,
        asNavFor: null,
        centerMode: false,
        variableWidth: false,
        fade: false,
        pauseOnHover: false,
        pauseOnFocus: false,
        swipeToSlide: false,
        cssEase: "linear",
        responsive: false,
    };
    params = $.extend(paramsDefault, params);

    $(getClass).slick(params);
};

Main.prototype.masks = function () {
    // $('.phone-mask').mask('(00) 0000.00009');
    $(".cpf-mask").mask("000.000.000-00");
    $(".cnpj-mask").mask("00.000.000/0000-00");
    $(".date-mask").mask("99/99/9999");
    $(".cep-mask").mask("99999-999");

    if ($(".phone-mask").length) {
        var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, "").length === 11
                ? "(00) 00000-0000"
                : "(00) 0000-00009";
        };
        $(".phone-mask").mask(SPMaskBehavior, {
            onKeyPress: function (val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            },
        });
    }
};

Main.prototype.initMagnific = function (parent) {
    parent = parent ? parent : $(".magnific-gallery");

    parent.each(function () {
        $(this).magnificPopup({
            delegate: "a",
            type: "image",
            iframe: {
                patterns: {
                    youtube: {
                        index: "youtube.com",
                        id: "v=",
                        src: "//www.youtube.com/embed/%id%?autoplay=1",
                    },
                },
            },
            tClose: "",
            mainClass: "mfp-fade",
            removalDelay: 300,
            tLoading: "Carregando",
            image: {
                tError: "Não foi possível carregar a imagem.",
            },
            gallery: {
                enabled: true,
                tPrev: "",
                tNext: "",
                tCounter: '<span class="mfp-counter">%curr% / %total%</span>',
            },
        });
    });
};

Main.prototype.closeDialog = function () {
    $.magnificPopup.close();
};

Main.prototype.openDialog = function (options) {
    var defaults = {
            src: "#common-dialog",
            fixedContentPos: false,
            fixedBgPos: true,
            overflowY: "auto",
            closeBtnInside: true,
            preloader: false,
            midClick: true,
            removalDelay: 300,
            tClose: "",
            mainClass: "my-mfp-zoom-in",
            message: false,
            title: false,
            modal: false,
            buttons: null,
            callbacks: {
                open: function () {
                    $("body").addClass("mfp-opened");
                    if (options.iconClass) {
                        $("#common-dialog .icons").addClass(options.iconClass);
                    }
                },
                close: function () {
                    $("body").removeClass("mfp-opened");
                    $("#common-dialog .icons").removeClass(options.iconClass);
                },
            },
        },
        params = $.extend({}, defaults, options);

    params.items = {
        type: "inline",
        src: params.src,
    };

    if (params.title) {
        $(params.src).find(".common-title").html(params.title);
    } else if (params.src == "#common-dialog") {
        $(params.src).find(".common-title").empty();
    }
    if (params.message) {
        $(params.src).find(".common-text").html(params.message);
    } else if (params.src == "#common-dialog") {
        $(params.src).find(".common-text").empty();
    }
    if (params.buttons === true) {
        $(params.src).find(".common-buttons").show();
    } else if (
        params.buttons === false ||
        (params.buttons !== true && params.src == "#common-dialog")
    ) {
        $(params.src).find(".common-buttons").hide();
    }

    $("body").on("click", ".dismiss-modal", function () {
        app.closeDialog();
    });

    delete params.buttons;
    delete params.src;
    delete params.title;
    delete params.message;
    delete params.iconClass;

    $.magnificPopup.open(params, 0);
};

//=====================================
//  Função que troca :hover por :active na regra de css quando tiver touch
//  Evita o bug de quando clicar, fazer o hover e nao abrir o link nos mobiles
//=====================================
Main.prototype.removeHoverTouch = function () {
    function hasTouch() {
        return (
            "ontouchstart" in document.documentElement ||
            navigator.maxTouchPoints > 0 ||
            navigator.msMaxTouchPoints > 0
        );
    }

    if (hasTouch()) {
        try {
            // exceção para navegadores que não suportam DOM StyleSheets
            for (var si in document.styleSheets) {
                var styleSheet = document.styleSheets[si];
                if (!styleSheet.rules) continue;

                for (var ri = styleSheet.rules.length - 1; ri >= 0; ri--) {
                    if (!styleSheet.rules[ri].selectorText) continue;

                    if (styleSheet.rules[ri].selectorText.match(":hover")) {
                        styleSheet.rules[ri].selectorText = styleSheet.rules[
                            ri
                        ].selectorText.replace(/\:hover/g, ":active");
                    }
                }
            }
        } catch (ex) {}
    }
};

$(document).ready(function () {
    app = new Main();
});
