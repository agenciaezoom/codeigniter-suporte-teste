"use strict";

function Produtos() {
    this.init();
    this.filters();
}

Produtos.prototype.init = function () {
    var self = this;
};

Produtos.prototype.filters = function () {
    $(document).on("change", "#filter-form input", function (event) {
        const $form = $(this).closest("form");
        $form.submit();
    });
};

$(document).ready(function () {
    new Produtos();
});
