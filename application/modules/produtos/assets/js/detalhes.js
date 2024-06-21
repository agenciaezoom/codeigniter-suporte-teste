"use strict";

function Detalhes() {
    this.init();
    this.video();
    this.tabs();
}

Detalhes.prototype.init = function () {
    var self = this;

    $("a.zoom").each(function (i, el) {
        $(el).zoom({ url: $(el).attr("href") });
    });

    $(".categories-products span").on("click", function (event) {
        var $el = $(this).closest("li");
        $el.toggleClass("open");
        $el.find("ul").stop(true, false).slideToggle(400);
    });

    $.getScript(
        site_url + "application/modules/comum/assets/plugins/slick/slick.min.js"
    ).done(function (script, textStatus) {
        // app.initSlick(".gallery", {
        //     slidesToShow: 1,
        //     arrows: false,
        //     dots: true,
        //     fade: true,
        // });
    });
};

Detalhes.prototype.video = function () {
    $(".popup-video").magnificPopup({
        type: "iframe",
        mainClass: "mfp-fade",
        removalDelay: 160,
        preloader: false,
        iframe: {
            patterns: {
                youtube: {
                    index: "youtube.com",
                    id: "v=",
                    src: "https://www.youtube.com/embed/%id%",
                },
            },
            srcAction: "iframe_src",
        },
        fixedContentPos: false,
    });
};

Detalhes.prototype.tabs = function () {
    $("#tabs").on("click", ".tabs-item", function (event) {
        event.preventDefault();

        $(this).addClass("active").siblings(".tabs-item").removeClass("active");
        $($(this).attr("href"))
            .addClass("active")
            .siblings(".tabs-body-item")
            .removeClass("active");
    });
};

$(document).ready(function () {
    new Detalhes();
});
