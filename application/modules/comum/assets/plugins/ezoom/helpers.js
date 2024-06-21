/**
 * js
 *
 * @package ezoom_framework
 * @subpackage main
 * @category js
 * @author Ralf da Rocha
 * @copyright 2015 Ezoom
 */

Main.prototype.helpers = {
    /**
     * Size Helpers
     * @author Felipe Rohde
     * @copyright 2013 Ezoom
     */
    minH: 0,
    minW: 960,
    ww: $(window).width(),
    wh: $(window).height(),
    vw: function(arg){
        if(arg) return arg;
        return (this.ww < this.minW) ? this.minW : this.ww; //viewport W
    },
    vh: function(arg){
        if(arg) return arg;
        return (this.wh < this.minH) ? this.minH : this.wh; //viewport H
    },
    getOffset: function(){
        return window.pageYOffset ? window.pageYOffset : (document.body.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop);
    },
    /**
     * Scroll Helpers
     * @author Ralf da Rocha
     * @copyright 2014 Ezoom
     */
    theMouseWheel: function(e) {
        e = e || window.event;
        if (e.preventDefault)
            e.preventDefault();
        e.returnValue = false;
    },
    disable_scroll: function() {
        if (window.addEventListener) {
            window.addEventListener('DOMMouseScroll', this.theMouseWheel, false);
        }
        window.onmousewheel = document.onmousewheel = this.theMouseWheel;
    },
    enable_scroll: function() {
        if (window.removeEventListener) {
            window.removeEventListener('DOMMouseScroll', this.theMouseWheel, false);
        }
        window.onmousewheel = document.onmousewheel = null;
    },
};