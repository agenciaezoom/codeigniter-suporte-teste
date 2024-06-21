"use strict";

function Endereco() {
    this.init();
}

Endereco.prototype.vars = {
    autoCity: "",
    map: undefined,
    marker: false,
    rendered: false,
    scripts: false,
    trigger: false,
};

Endereco.prototype.init = function () {
    var self = this;
    self.formListeners();
};

Endereco.prototype.formListeners = function () {
    var self = this;

    if (self.vars.scripts) return;

    self.vars.scripts = true;

    // Select
    if (
        $("#address-sidebar select.select2:not(.select2-offscreen)").length > 0
    ) {
        $("#address-sidebar select.select2:not(.select2-offscreen)").select2();
    }

    $.extend($.inputmask.defaults, {
        autounmask: true,
        clearMaskOnLostFocus: true,
        placeholder: "_",
    });

    // CEP / Estado / Cidade / Bairro
    $("#address-sidebar").on("keyup", '[name*="zip_code"]', function () {
        if ($("#inputCountry").val() != 32) return;
        var v = $.trim($(this).val().replace(/\D/g, "")),
            $zip = $(this);
        if (v.length == 8 && v != $zip.data("old")) {
            $zip.data("old", v);
            $zip.addClass("loading");
            var tab = $zip.closest(".address-wrapper");
            $.getJSON(
                site_url + "endereco/get",
                {
                    zipcode: v,
                },
                function (result) {
                    if (result) {
                        // Zera outros campos
                        tab.find('input:not([name*="zip_code"])').val("");
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
                            unescape(result["logradouro"])
                        );
                        tab.find('[name*="number"]').focus();
                        self.initMaps();
                        $(".address-wrapper .alert").fadeOut(100);
                        setTimeout(function () {
                            $(".find-place").trigger("click");
                        }, 150);
                    }

                    $zip.removeClass("loading");
                }
            );
        }
    });

    $('[name*="id_country"]')
        .data("old", $('[name*="id_country"]').val())
        .on("change", function () {
            var v = $(this).val();
            if (v == 32) {
                $("#inputCEP").inputmask({
                    mask: "99999-999",
                    keepStatic: true,
                });
            } else {
                $("#inputCEP").inputmask("remove");
            }
            if (v != $(this).data("old")) {
                $(this).data("old", v);
                $("#address-sidebar input").val("");
                if (v == 32) {
                    $(".address-wrapper .alert").fadeIn(100);
                } else {
                    $(".address-wrapper .alert").fadeOut(100);
                }
            }
        })
        .trigger("change");

    if ($("#address-map").length) {
        var $scripts = [
            site_url +
                "modules/comum/assets/plugins/jquery-ui-map/jquery.ui.map.js",
            site_url +
                "modules/comum/assets/plugins/jquery-ui-map/jquery.ui.map.extensions.js",
            site_url +
                "modules/comum/assets/plugins/jquery-ui-map/jquery.ui.map.services.js",
        ];
        $.each($scripts, function (k, v) {
            if ($('[src="' + v + '"]').length) return true;
            var scriptNode = document.createElement("script");
            scriptNode.src = v;
            $("head").prepend($(scriptNode));
        });
    }

    if ($(".address-wrapper").closest(".tab-pane").length > 0) {
        if ($(".address-wrapper").closest(".tab-pane").hasClass("active")) {
            self.initMaps();
        } else {
            $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                if ($($(e.target).attr("href")).find("#address-map").length) {
                    // self.initMaps();
                    // self.centerMap();
                }
            });
        }
    } else {
        // self.initMaps();
    }
};

Endereco.prototype.initMaps = function () {
    var self = this;
    if (self.vars.rendered) return;
    self.vars.rendered = true;

    if ($("#address-map").length) {
        $("#address-map").height(
            $(window).height() -
                ($("footer").height() +
                    $("header").height() +
                    $(".breadcrumb").height() +
                    91)
        );
        $(window)
            .on("resize.endereco", function () {
                $("#address-map").height(
                    $(window).height() -
                        ($("footer").height() +
                            $("header").height() +
                            $(".breadcrumb").height() +
                            91)
                );
            })
            .trigger("resize.endereco");

        $("#address-map")
            .gmap({
                panControl: false,
                streetViewControl: false,
                mapTypeControl: false,
                overviewMapControl: false,
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoom: 12,
                zoomControl: true,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.SMALL,
                    position: google.maps.ControlPosition.TOP_RIGHT,
                },
            })
            .bind("init", function (event, map) {
                self.vars.map = $("#address-map").gmap("get", "map");

                var overlay = new google.maps.OverlayView();
                overlay.draw = function () {};
                overlay.setMap(self.vars.map);

                google.maps.event.addDomListener(window, "resize", function () {
                    self.centerMap();
                });

                // Adiciona Marker caso lat/lng estejam definidos
                if (
                    $('#address-sidebar input[name*="lat"]').val() != "" &&
                    $('#address-sidebar input[name*="lng"]').val() != ""
                ) {
                    var lat = $('#address-sidebar input[name*="lat"]').val(),
                        lng = $('#address-sidebar input[name*="lng"]').val(),
                        zoom = 15;
                    $("#address-map")
                        .gmap(
                            "addMarker",
                            {
                                position: new google.maps.LatLng(lat, lng),
                                draggable: true,
                                bounds: false,
                                animation: google.maps.Animation.DROP,
                            },
                            function (map, marker) {
                                $("#address-map").gmap(
                                    "option",
                                    "center",
                                    marker.getPosition()
                                );
                                $("#address-map").gmap("option", "zoom", 15);
                                self.vars.marker = marker;
                            }
                        )
                        .dragend(function (event) {
                            this.setAnimation(google.maps.Animation.BOUNCE);
                            setTimeout(
                                $.proxy(function () {
                                    this.setAnimation(null);
                                }, this),
                                600
                            );
                            self.findPlace(event.latLng, this);
                        });
                } else {
                    $("#address-map").gmap(
                        "option",
                        "center",
                        new google.maps.LatLng(-29.1655089, -51.173753)
                    );
                    $("#address-map").gmap("option", "zoom", 12);
                }
            });

        $(".find-place").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            self.initMaps();

            var array = {},
                address = "";
            $('#address-sidebar [name^="location"]').each(function () {
                array[this.name.match(/\[(.*?)\]/)[1]] = $(this).val();
            });

            if (array.street != undefined && array.street != "") {
                address += array.street;
                if (array.number != "") address += ", " + array.number;
            }
            if (array.city != undefined && array.city != "") {
                address += address.length > 0 ? ", " : "";
                address += $('[name*="city"]').val();
            }

            if (array.state != undefined && array.state != "") {
                address += address.length > 0 ? ", " : "";
                address += $('[name*="state"]').val();
            }

            if (address.length == 0 && array.zip_code != "")
                address += array.zip_code;

            if (array.id_country != undefined && array.id_country != "") {
                address += address.length > 0 ? ", " : "";
                address += $(
                    '[name*="id_country"] option[value="' +
                        array.id_country +
                        '"]'
                ).text();
            }
            if (address.length != 0) {
                $("#address-map").gmap(
                    "search",
                    {
                        address: address,
                    },
                    function (results, status) {
                        if (status === "OK") {
                            var lat = results[0].geometry.location.lat(),
                                lng = results[0].geometry.location.lng();
                            self.configInputs(results, false);
                            if (self.vars.marker) self.vars.marker.setMap(null);
                            $("#address-map")
                                .gmap(
                                    "addMarker",
                                    {
                                        position: new google.maps.LatLng(
                                            lat,
                                            lng
                                        ),
                                        draggable: true,
                                        bounds: false,
                                        animation: google.maps.Animation.DROP,
                                    },
                                    function (map, marker) {
                                        $("#address-map").gmap(
                                            "option",
                                            "center",
                                            marker.getPosition()
                                        );
                                        $("#address-map").gmap(
                                            "option",
                                            "zoom",
                                            15
                                        );
                                        self.vars.marker = marker;
                                    }
                                )
                                .dragend(function (event) {
                                    this.setAnimation(
                                        google.maps.Animation.BOUNCE
                                    );
                                    setTimeout(
                                        $.proxy(function () {
                                            this.setAnimation(null);
                                        }, this),
                                        600
                                    );
                                    self.findPlace(event.latLng, this);
                                });
                        }
                    }
                );
            } else {
                var obj = {
                    layout: "top",
                    text: i18n.preencha_localizacao,
                    type: "error",
                };
                openNotification(obj);
            }
        });
    }
};

Endereco.prototype.centerMap = function () {
    var self = this;
    if (self.vars.marker)
        $("#address-map").gmap(
            "option",
            "center",
            self.vars.marker.getPosition()
        );
};

Endereco.prototype.findPlace = function (location, marker) {
    var self = this;
    self.initMaps();
    $("#address-map").gmap(
        "search",
        {
            location: location,
        },
        function (results, status) {
            if (status === "OK") {
                self.configInputs(results, true);
            }
        }
    );
};

Endereco.prototype.configInputs = function (results, force) {
    var self = this,
        address = {};
    $.each(results[0].address_components, function (i, v) {
        address[v.types[0]] = {
            short_name: v.short_name !== undefined ? v.short_name : "",
            long_name: v.long_name !== undefined ? v.long_name : "",
        };
    });

    if (
        address.country !== undefined &&
        address.country.short_name !== undefined
    )
        $('#address-sidebar select[name*="id_country"]')
            .val(
                $(
                    '#address-sidebar select[name*="id_country"] option[data-code="' +
                        address.country.short_name +
                        '"]'
                ).attr("value")
            )
            .trigger("change");

    // CEP
    if (address.postal_code !== undefined) {
        if (force || $('#address-sidebar input[name*="zip_code"]').val() == "")
            $('#address-sidebar input[name*="zip_code"]')
                .val(address.postal_code.long_name)
                .data("old", address.postal_code.long_name);
    } else if (address.postal_code_prefix !== undefined) {
        if (
            force ||
            $('#address-sidebar input[name*="zip_code"]').val() == ""
        ) {
            var cep =
                address.postal_code_prefix.long_name +
                "-" +
                (address.postal_code_sufix !== undefined
                    ? address.postal_code_sufix.long_name
                    : "000");
            $('#address-sidebar input[name*="zip_code"]')
                .val(cep)
                .data("old", cep);
        }
    } else {
        if (force)
            $('#address-sidebar input[name*="zip_code"]')
                .val("")
                .data("old", "");
    }
    // Estado
    if (force && address.administrative_area_level_1 !== undefined)
        $('#address-sidebar [name*="state"]').val(
            address.administrative_area_level_1.long_name
        );
    // Cidade
    var city =
        address.locality !== undefined
            ? address.locality.long_name
            : address.administrative_area_level_2.long_name !== undefined
            ? address.administrative_area_level_2.long_name
            : "";
    $('#address-sidebar [name*="city"]').val(city);
    // Complemento
    if (
        force &&
        !(
            address.route !== undefined &&
            (address.route !== undefined) ===
                $('#address-sidebar input[name*="street"]').val()
        )
    )
        $('#address-sidebar input[name*="additional_info"]').val("");
    // Bairro
    if (force || $('#address-sidebar input[name*="suburb"]').val() == "")
        $('#address-sidebar input[name*="suburb"]').val(
            address.neighborhood !== undefined
                ? address.neighborhood.long_name
                : address.sublocality_level_1 !== undefined
                ? address.sublocality_level_1.long_name
                : ""
        );
    // Rua
    if (force || $('#address-sidebar input[name*="street"]').val() == "")
        $('#address-sidebar input[name*="street"]').val(
            address.route !== undefined ? address.route.long_name : ""
        );

    // self.vars.marker.setTitle(results[0].formatted_address);

    // Lat e Lng
    var lat = results[0].geometry.location.lat(),
        lng = results[0].geometry.location.lng();
    $('#address-sidebar input[name*="lat"]').val(lat);
    $('#address-sidebar input[name*="lng"]').val(lng);
};

$("document").ready(function () {
    new Endereco();
});
