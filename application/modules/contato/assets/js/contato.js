"use strict";

function Contato() {
  this.init();
}

Contato.prototype.init = function () {
  var self = this;

  $("input[name=phone]").mask("(99) 99999-9999");

  app.ajaxSelect({
    element: "#selectState",
    target: "#selectCity",
  });
};

$(document).ready(function () {
  new Contato();
});
