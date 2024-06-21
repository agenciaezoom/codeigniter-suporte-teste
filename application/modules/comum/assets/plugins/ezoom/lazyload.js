/*
 *  jquery-lazyload - v1.5.1
 *  Dynamic loading of images
 *  http://ralfdarocha.com
 *
 *  Made by Ralf da Rocha
 *  Under MIT License
 */
;(function ( $, window, document, undefined ) {
    var pluginName = "lazyload",
        defaults = {
            autoload: true,
            background: false,
            beforeLoad: null,
            class: 'lazyload-image',
            onLoad: null,
            time: 0,
            viewport: false
        };

    function Plugin ( element, options ) {
        this.element = element;
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this._timeout = '';
        this._init();
    }

    $.extend(Plugin.prototype, {
        _init: function () {
            $(this.element).addClass('loading').data('status', 'waiting');
            var data = $(this.element).data();
            $.each(data,$.proxy(function(name,value){
                if (typeof this.settings[name] !== 'undefined')
                    this.settings[name] = value;
            },this));
            if (this.settings.autoload && !this.settings.viewport)
                this.load();
            else if (this.settings.autoload && this.settings.viewport)
                this._scroll();
        },
        _guid: function(){
            var max = 9999999999,
                uid = ((Math.random() * max) + ((1 + Math.random()) * max)).toString();
            if (uid.length > 8){
                var from = Math.floor(Math.random() * (uid.length - 8));
                uid = uid.substring(from, from + 8);
            }
            return uid.length < 8 ? ("00000000".substring(0, 8 - uid.length) + uid).toString() : uid;
        },
        _loadImg: function(path, callback) {
            var fakeImg = $('<img/>', {
                src : path
            }).on('load', function () {
                if (typeof callback == 'function') {
                    return callback(this);
                }
            });
        },
        _scroll: function(){
            this.uid = this._guid() + this._guid();
            $(window).on('scroll.'+this.uid, $.proxy(function(){
                var from = $(this.element).offset().top,
                    to = from + $(this.element).outerHeight();
                    viewportMin = $(window).scrollTop(),
                    viewportMax = viewportMin + $(window).height();
                if (viewportMin <= to && viewportMax >= from){
                    this.load();
                    $(window).off('scroll.'+this.uid).off('resize.'+this.uid);
                }
            }, this)).on('resize.'+this.uid, $.proxy(function(){
                $(window).trigger('scroll.'+this.uid);
            },this)).trigger('scroll.'+this.uid);
        },
        load: function () {
            if ($(this.element).data('status') !== 'waiting')
                return false;
            clearTimeout(this._timeout);
            $(this.element).data('status', 'loading');

            if (typeof this.settings.beforeLoad == 'function')
                this.settings.beforeLoad(this.element);

            this._loadImg($(this.element).data('src'), $.proxy(function(img){
                var el = this.settings.background ? $('<div style="background-image: url('+img.src+')" />') : $(img);
                el.addClass(this.settings.class);
                if ($(this.element).data('alt') !== undefined)
                    el.attr('alt', $(this.element).data('alt'));

                $(this.element).prepend(el);

                setTimeout($.proxy(function() {
                    $(this.element).removeClass('loading').addClass('loaded').data('status', 'loaded');
                    if (typeof this.settings.onLoad == 'function')
                        this.settings.onLoad(el);
                }, this),50);
                $(this.element).removeAttr('data-src').removeAttr('data-alt');

            }, this));
        },
        unload: function(reload) {
            if ($(this.element).data('status') === 'waiting')
                return false;
            this.destroy();
            $(this.element).removeClass('loaded').addClass('loading').data('status', 'waiting');
            if (this.settings.time > 0){
                this._timeout = setTimeout($.proxy(function(){
                    $(this.element).children('.' + this.settings.class).remove();
                    if (typeof reload !== 'undefined' && reload === true)
                        this.load();
                }, this), this.settings.time);
            }else{
                $(this.element).children('.' + this.settings.class).remove();
                if (typeof reload !== 'undefined' && reload === true)
                    this.load();
            }
        },
        reload: function() {
            if ($(this.element).data('status') === 'waiting')
                return this.load();
            this.unload(true);
        },
        destroy: function() {
            if (this.settings.viewport && $(this.element).data('status') !== 'waiting'){
                $(window).off('scroll.'+this.uid);
            }
        },
        start: function() {
            if (this.settings.viewport && $(this.element).data('status') === 'waiting'){
                this._scroll();
            }
        }
    });

    $.fn[ pluginName ] = function ( options ) {
        this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }else{
                var pl = $(this).data( "plugin_" + pluginName );
                if (typeof options === "object" || !options) {
                    return this;
                }else if (pl && options.indexOf("_") != 0 && pl[options] && typeof pl[options] == "function") {
                    return pl[options](Array.prototype.slice.call(arguments, 1));
                } else if (!pl) {
                    $.error("Plugin must be initialised before using method: " + options)
                }else if (options.indexOf("_") == 0) {
                    $.error("Method " + options + " is private!");
                } else {
                    $.error("Method " + options + " does not exist.");
                }
            }
        });
        return this;
    };

})( jQuery, window, document );