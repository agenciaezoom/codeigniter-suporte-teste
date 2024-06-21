"use strict";

function Vendas() {
    this.init();
}

Vendas.prototype.init = function () {
    var self = this;

    $(".question").on("click", ".title", function () {
        $(this).parent().toggleClass("open");
    });

    app.ajaxSelect({
        element: "#selectState",
        target: "#selectCity",
    });
};

$(document).ready(function () {
    new Vendas();
});
