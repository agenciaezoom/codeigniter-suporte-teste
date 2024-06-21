function StdObject(options) {
    this.posX = false
    this.posY = false
    this.beforeInit = false
    this.onInit = false
    this.afterInit = false
    this.onBlur = false
    this.onClick = false
    this.onFocus = false
    this.onResize = false
    this.onMouseMove = false
    this.onWindowResize = false
    this.onWindowLoad = false
    this.onWindowScroll = false
    this.lazyload = false
    this.lazyloadAddback = false
    this.lazyloadSelector = '.lazyload'
    this.lazyloadOptions = {}
    this.lazyloadCount = 0
    this.lazyloadAreLoaded = 0
    this.lazyloadOnLoadAll = false
    this.pluginName = false
    this.pluginSelector = false
    this.pluginOptions = false
    this.flexInit = false
    this.flexContainer = '.flex-container'
    this.flexAuto = '.flex-auto'
    this.flexGrow = '.flex-grow'
    this.flexFixed = '.flex-fixed'
    this.flexShrink = '.flex-shrink'

    this.init(options);
}

StdObject.prototype.init = function(options) {
    for (var attrname in options) { this[attrname] = options[attrname] }

    if(this.selector !== undefined) {
        if(typeof this.selector === 'string') {
            this.$element = $(this.selector)
        } else if(typeof this.selector === 'object') {
            this.$element = this.selector
        }
    }

    if(typeof this.beforeInit === 'function') {
        this.beforeInit()
    }


    $window.load($.proxy(function() {
        if(typeof this.pluginName === 'string') {
            this.initiatePlugin(this.pluginName, this.pluginOptions, this.pluginSelector)
        }

        if(this.lazyload) {
            var $lazyload
            var lazyloadOnLoad = (typeof this.lazyloadOptions.onLoad === 'function')?this.lazyloadOptions.onLoad:false;
            if(this.lazyloadAddback) {
                $lazyload = this.$element.find(this.lazyloadSelector).addBack(this.lazyloadSelector)
            } else {
                $lazyload = this.$element.find(this.lazyloadSelector)
            }
            this.lazyloadCount = $lazyload.length

            this.lazyloadOptions.onLoad = $.proxy(function(img) {
                this.lazyloadAreLoaded++
                if(this.lazyloadAreLoaded === this.lazyloadCount && typeof this.lazyloadOnLoadAll === 'function') {
                    this.lazyloadOnLoadAll($lazyload)
                }
                if(typeof lazyloadOnLoad === 'function') {
                    lazyloadOnLoad(img)
                }
            }, this)
            $lazyload.lazyload(this.lazyloadOptions)
        }

        if(typeof this.onWindowLoad === 'function') {
            this.onWindowLoad()
        }
    }, this))

    if(typeof this.onWindowScroll === 'function') {
        $window.scroll($.proxy(function(e) {
            this.onWindowScroll(e)
        }, this))
    }

    if(typeof this.onWindowResize === 'function') {
        $window.resize($.proxy(function(e) {
            this.onWindowResize(e)
        }, this))
    }

    if(typeof this.onBlur === 'function') {
        this.$element.blur($.proxy(function(e) {
            this.onBlur(e);
        }, this))
    }

    if(typeof this.onFocus === 'function') {
        this.$element.focus($.proxy(function(e) {
            this.onFocus(e)
        }, this))
    }

    if(typeof this.onClick === 'function') {
        this.$element.click($.proxy(function(e) {
            this.onClick(e)
        }, this))
    }

    if(typeof this.onMouseMove === 'function') {
        this.$element.on('mousemove', $.proxy(function(e) {
            this.onMouseMove(e)
        }, this))
    }

    this.fireInitEvents()

    if(this.flexInit) {
        $window.load($.proxy(function() {
            this.flex()
        }, this))
    }
}

StdObject.prototype.beforeInit = function() {
    if(typeof this.beforeInit === 'function') {
        this.beforeInit(this)
    }
}

StdObject.prototype.afterInit = function(callback) {
    if(typeof this.afterInit === 'function') {
        this.afterInit(this)
    }
}

StdObject.prototype.fireInitEvents = function() {
    if(typeof this.onInit === 'function') {
        this.onInit()
    }

    setTimeout($.proxy(function() {
        if(typeof this.afterInit === 'function') {
            this.afterInit()
        }
    }, this), 5)
}

StdObject.prototype.flex = function() {
    var $contaiers = this.$element.find(this.flexContainer)

    if($contaiers.length > 0) {
        $contaiers.each($.proxy(function(i, el) {
            var $this = $(el)
            var availWidth = Math.floor($this.width())
            var $flexAuto = $this.find(this.flexAuto)
            var autoWidth = 0
            var $flexFixed = $this.find(this.flexFixed)
            var fixedWidth = 0
            var $flexGrow = $this.find(this.flexGrow)

            if($flexAuto.length > 0) {
                $flexAuto.each(function(i, el) {
                    var $el = $(el)
                    var w = $el.outerWidth()
                    $el.width(w)
                    autoWidth += w
                })
            }

            if($flexFixed.length > 0) {
                $flexFixed.each(function(i, el) {
                    var $el = $(el)
                    var w = $el.outerWidth()
                    $el.width(w)
                    fixedWidth += w
                })
            }

            if($flexGrow.length > 0)
                $flexGrow.width((availWidth - fixedWidth - autoWidth) / $flexGrow.length)
        }, this))
    }
}

StdObject.prototype.heightEquilibrium = function($elements, classes, callback) {
    var maxHeights = {}

    if(typeof $elements === 'string')
        $elements = this.$element.find($elements)

    classes.forEach(function(c) {
        maxHeights[c] = 0
    });

    $.each(classes, function(i, c) {

        $elements.each(function() {
            var $this = $(this)
            var f = function($el) {
                var h = 0;
                $el.children().each(function() {
                    h += $(this).outerHeight()
                })
                return h
            }
            var h = f($this.find(c).addBack(c))

            if(maxHeights[c] < h) {
                maxHeights[c] = h
            }

        })

    })

    for (var attrname in maxHeights) { $elements.find(attrname).addBack(attrname).height(maxHeights[attrname]); }

    if(typeof callback === 'function') {
        callback(this);
    }
}

StdObject.prototype.resize = function(callback) {
    this.onResize(this)

    if(typeof callback === 'function') {
        return callback(this)
    }
}

StdObject.prototype.initiatePlugin = function(pluginName, options, selector) {
    if(typeof this.$element[pluginName] !== 'function') {
        console.error(pluginName + ' plugin not found')
        return false
    }
    if(selector)
        this.$element.find(selector)[pluginName](options||{})
    else
        this.$element[pluginName](options||{})
}

StdObject.prototype.translate = function(x, y, $elements, callback) {
    this.posX = parseInt(x)
    this.posY = parseInt(y)

    if($elements === undefined) {
        $elements = this.$element
    }

    if(typeof x === 'number' || x.search(/(%|em|rem|pt|px)/) < 0) {
        x = x + 'px'
    }

    if(typeof y === 'number' || y.search(/(%|em|rem|pt|px)/) < 0) {
        y = y + 'px'
    }

    var transformObj = {
        '-ms-transform':'translate(' + x + ',' + y + ')',
        '-webkit-transform':'translate(' + x + ',' + y + ')',
        '-moz-transform':'translate(' + x + ',' + y + ')',
        'transform':'translate(' + x + ',' + y + ')'
    }

    $elements.css(transformObj)

    if(typeof callback === 'function') {
        callback()
    }
}