/**
 * js
 *
 * @package ezoom_framework
 * @subpackage home
 * @category js
 * @author Diogo Taparello
 * @copyright 2016 Ezoom
 */
var Home = $(function() {

    function Home() {
        if (!(this instanceof Home)) {
            return new Home();
        };
        this.init();
    };

    Home.prototype = new Main();
    Home.prototype.constructor = Home;

    Home.prototype.init = function() {
        var self = this;

        $(".gallery").owlCarousel({
            slideSpeed : 300,
            paginationSpeed : 400,
            navigation: true,
            items: 4,
            itemsCustom : [
                [0, 1],
                [800, 1],
                [1400, 2],
                [1600, 2]
            ],
        });


        $(".gallery .item .content-home .text").mCustomScrollbar();
    };

    return Home;
}());