"use strict";

function Assistencia() {
  this.markers = [];
  this.marker;

  this.init();
  this.filters();
}

Assistencia.prototype.init = function () {
  var self = this;

  if ($("#map").length > 0) {
    $.getScript(
      "https://maps.googleapis.com/maps/api/js?key=AIzaSyBlYRX4JcJLzJQ4S8lnVZy6FdvipuszGKY"
    ).done(function (script, textStatus) {
      self.gmaps();
    });
  }
};

Assistencia.prototype.filters = function () {
  var self = this;

  $(".select").on("change", function () {
    var filter = $(this).val();

    if (filter.length <= 3) {
      return false;
    }

    var $wrapper = $(".units-list");

    $.ajax({
      url: site_url + "contato/units",
      type: "POST",
      dataType: "html",
      data: { filter: filter },
      success: function (data) {
        $wrapper.html(data);
        self.gmaps();
      },
      error: function (data) {
        console.log(data);
      },
    });
  });
};

Assistencia.prototype.gmaps = function () {
  var self = this;
  var markers = [];

  $(".units-item").each(function (index, html) {
    let latLng = new google.maps.LatLng(
      $(this).data("lat"),
      $(this).data("lng")
    );
    markers.push({
      id: $(this).data("id"),
      coords: latLng,
    });
  });

  var mapCanvas = document.getElementById("map"),
    mapOptions = {
      center: markers[0].coords,
      zoom: 15,
      scrollwheel: false,
      draggable: true,
      fullscreenControl: false,
      mapTypeControl: false,
      streetViewControl: false,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
    },
    map = new google.maps.Map(mapCanvas, mapOptions),
    image = new google.maps.MarkerImage(
      site_url + "application/modules/comum/assets/img/marker.png"
    );

  markers.forEach(function (value, index) {
    let item = new google.maps.Marker({
      position: value.coords,
      map: map,
      icon: image,
      url: "http://maps.google.com/maps?q=loc:" + value.coords,
    });

    google.maps.event.addListener(item, "click", function () {
      window.open(this.url, "_blank");
    });

    self.markers[value.id] = item;

    if (index == 0) {
      self.marker = item;
    }
  });

  google.maps.event.addDomListener(window, "resize", function () {
    self.marker.setIcon(image);

    setTimeout(function () {
      map.panTo(self.marker.position);
    }, 5);
  });

  $("#units").on("change", '[name="units"]', function () {
    let id = $(this).val();
    let $list = $(".units-list");

    $list.find(".units-item").hide();
    $list.find("#unit-" + id).show();

    self.marker = self.markers[id];
    map.panTo(self.marker.position);
  });
};

$(document).ready(function () {
  new Assistencia();
});
