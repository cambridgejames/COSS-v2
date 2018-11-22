'use strict';

$(function($) {

    //------------------------------------------------------------------------------------------------------------------
    // Variables
    //------------------------------------------------------------------------------------------------------------------

    var THEME_COLORS = {
        DEFAULT: '#8fb7bf',
        PRIMARY: '#a49fe1',
        SUCCESS: '#8fb7bf',
        INFO   : '#6cc5a7',
        WARNING: '#f7a053',
        DANGER : '#ec6957'
    };

    var SEND_MAIL_PROCESSOR = 'sendmail.php';

    var $body_html = $('body, html'),
        $html = $('html'),
        $body = $('body'),

        $preloader = $('#preloader'),
        $loader = $preloader.find('.loader');

    //------------------------------------------------------------------------------------------------------------------
    // Is mobile
    //------------------------------------------------------------------------------------------------------------------

    var ua_test = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i,
        is_mobile = ua_test.test(navigator.userAgent);

    $html.addClass(is_mobile ? 'mobile' : 'no-mobile');

    //------------------------------------------------------------------------------------------------------------------
    // Smooth Scrolling
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Default smooth scroll time
     * @type {number}
     */
    var SMOOTH_SCROLL_DEFAULT_TIME = 750;

    $.scrollWindow = function(offset) {
        $body_html.animate({
            scrollTop: offset
        }, SMOOTH_SCROLL_DEFAULT_TIME);
    };

    $('a[href^="#"]').on('click', function(event) {

        event.preventDefault();

        var $this = $(this),
            target = $this.attr('href');

        // Don't return false!
        if (target === '#') return;

        if ($this.hasClass('smooth-scroll')) {
            var offset = $(target).offset().top - 50;
            $.scrollWindow(offset);
        }
    });

    //------------------------------------------------------------------------------------------------------------------
    // Affixed Navbar
    //------------------------------------------------------------------------------------------------------------------

    var affix_data = {
        offset: {
            top: 0
        }
    };

    $('.affix').each(function() {

        var $this = $(this);

        if ($this.hasClass('affix-top-hidden')) {
            affix_data.offset.top = $(window).height() - 60;
        } else {
            affix_data.offset.top = $this.height();
        }

        $this.affix(affix_data);
    });

    //------------------------------------------------------------------------------------------------------------------
    // Scroll Reveal
    //------------------------------------------------------------------------------------------------------------------

    window.sr = ScrollReveal();

    $('.reveal').each(function() {

        var $this = $(this),
            config = {
                easing: 'cubic-bezier(.51, .07, .75, .95)',
                mobile: false,
                scale : 1.01
            };

        var data_container = $this.data('reveal-container'),
            data_origin    = $this.data('reveal-origin'),
            data_distance  = $this.data('reveal-distance'),
            data_duration  = $this.data('reveal-duration'),
            data_delay     = $this.data('reveal-delay'),
            data_easing    = $this.data('reveal-easing'),
            data_scale     = $this.data('reveal-scale'),
            data_opacity   = $this.data('reveal-opacity'),
            data_reset     = $this.data('reveal-reset');

        if (typeof data_container !== 'undefined') config['container'] = data_container;
        if (typeof data_origin    !== 'undefined') config['origin']    = data_origin;
        if (typeof data_distance  !== 'undefined') config['distance']  = data_distance;
        if (typeof data_duration  !== 'undefined') config['duration']  = data_duration;
        if (typeof data_delay     !== 'undefined') config['delay']     = data_delay;
        if (typeof data_easing    !== 'undefined') config['easing']    = data_easing;
        if (typeof data_scale     !== 'undefined') config['scale']     = data_scale;
        if (typeof data_opacity   !== 'undefined') config['opacity']   = data_opacity;
        if (typeof data_reset     !== 'undefined') config['reset']     = 1 ? true : false;

        sr.reveal($this, config);
    });

    //------------------------------------------------------------------------------------------------------------------
    // OWL Carousel
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Default value for count of OWL Carousel thumbnails
     * @type {number}
     */
    var OWL_DEFAULT_THUMBNAIL_ITEMS = 4;

    /**
     * Default value for OWL Carousel autoplay timeout
     * @type {number}
     */
    var OWL_DEFAULT_AUTOPLAY_TIMEOUT = 3000;

    /**
     * Callback function which generates thumbnails for OWL Carousel
     */
    var setThumbs = function() {

        // Carousel element
        var $carousel = this.$element,
        // Items array
            items = this._items;

        // REQUIRED
        if (typeof $carousel === 'undefined' || typeof items === 'undefined') return;

        var cnt_thumbnails = $carousel.data('owl-thumnailitems');
        if (typeof cnt_thumbnails === 'undefined') cnt_thumbnails = OWL_DEFAULT_THUMBNAIL_ITEMS;

        // Create wrapper for thumbnails
        var $thumbs = $('<div>').addClass('owl-thumbs');

        // Append Thumbnails Wrapper
        $carousel.append($thumbs);

        // Set thumbnails for each element (if hash value not empty)
        $.each(items, function() {

            var $img = $(this).find('img'),
                hash = $(this).find('[data-hash]').data('hash');

            // REQUIRED
            if ($img.length === 0 || typeof hash === 'undefined') return;

            // Create & Append thumbnail link
            $thumbs.append(
                $('<a>')
                    .attr('href', '#' + hash)
                    .addClass('owl-thumbnail')

                    .append(
                        $('<img>')
                            .attr('src', $img.attr('src'))
                            .addClass('image')
                    )
            );

        });

        // Initialize OWL Carousel for thumbnails and save in this object
        this.thumbs = $thumbs.owlCarousel({
            dots : false,
            items: cnt_thumbnails
        });


        // Current item index
        var _current = this._current;

        // Change active element in thumbnails OWL Carousel
        this.thumbs.trigger('to.owl.carousel', _current);
        // Add active class to link
        this.thumbs.find('.owl-item').eq(_current).find('.owl-thumbnail').addClass('owl-thumbnail-active');
    };

    var changeActiveThumb = function(event) {

        //
        // don't use this function with loop === true
        //

        // Thumbnails OWL Carousel
        var thumbs = this.thumbs,
        // Active item index
            item_index = event.item.index,
        // Items count
            items_count = event.item.count;

        // REQUIRED
        if (typeof thumbs === 'undefined' || !items_count || item_index === null) return;

        // Trigger Event
        thumbs.trigger('to.owl.carousel', item_index);

        // Remove active class from link
        thumbs.find('.owl-thumbnail-active').removeClass('owl-thumbnail-active');
        // Add active class to new link
        thumbs.find('.owl-item').eq(item_index).find('.owl-thumbnail').addClass('owl-thumbnail-active');

    };

    $('.owl-carousel').each(function() {

        // Default OWL Carousel parameters
        var owl_parameters = {
            items: 1,     // Items count
            dots : false, // Disable dots,
            navText: [
                '<i class="icon fa fa-angle-left"></i>',
                '<i class="icon fa fa-angle-right"></i>'
            ]
        };

        // Carousel element
        var $this = $(this),
        // Carousel items count (opt)
            data_items = $this.data('owl-items'),
            items_count = 1;

        // Count of items
        if (typeof data_items !== 'undefined') items_count = parseInt(data_items, 10);

        // Set to config
        owl_parameters['items'] = items_count;

        // Disable mouse drag
        if ($this.hasClass('owl-no-mousedrag')) owl_parameters['mouseDrag'] = false;
        // Show prev/next navigation
        if ($this.hasClass('owl-navigation')) owl_parameters['nav'] = true;
        // Show dots navigation
        if ($this.hasClass('owl-pagination')) owl_parameters['dots'] = true;

        // Enable autoplay
        if ($this.hasClass('owl-autoplay')) {
            owl_parameters['loop'] = true;
            owl_parameters['autoplay'] = true;
            // owl_parameters['autoplayHoverPause'] = true;
            owl_parameters['autoplayTimeout'] = typeof ($this.data('owl-autoplay-timeout')) != 'undefined'
                ? $this.data('owl-autoplay-timeout')
                : OWL_DEFAULT_AUTOPLAY_TIMEOUT;
        }
        
        // Responsive Items Count
        var data_items_responsive = $this.data('owl-items-responsive');
        if (typeof data_items_responsive !== 'undefined') {

            var arr = data_items_responsive.split(';'),
                responsive = {};

            responsive[1000] = { items: items_count };
            responsive[0] = { items: 1 };

            for (var i = 0, j = arr.length; i < j; i++) {

                var _arr = arr[i].split(':');
                if (typeof _arr[0] === 'undefined' || typeof _arr[1] === 'undefined') continue;

                var max_w = parseInt((_arr[0]).trim(), 10),
                    items_cnt = parseInt((_arr[1]).trim(), 10);

                responsive[max_w] = { items: items_cnt }
            }


            owl_parameters['responsive'] = responsive;
            owl_parameters['responsiveClass'] = true;

            console.log(responsive);

        }

        // Enable thumbnails
        if ($this.hasClass('owl-thumbnails')) {

            owl_parameters['dots'] = false;
            owl_parameters['startPosition'] = 'URLHash';
            owl_parameters['onInitialized'] = setThumbs;

            owl_parameters['onChanged'] = changeActiveThumb;
        }

        // Custom Animation
        var animate_in = $(this).data('owl-animate-in'),
            animate_out = $(this).data('owl-animate-out');

        if (typeof animate_in !== 'undefined') owl_parameters['animateIn'] = animate_in;
        if (typeof animate_out !== 'undefined') owl_parameters['animateOut'] = animate_out;

        // ScrollReveal
        if ($this.hasClass('owl-reveal')) owl_parameters['onTranslated'] = sr.animate;

        // Initialize OWL Carousel
        $(this).owlCarousel(owl_parameters);
    });

    //------------------------------------------------------------------------------------------------------------------
    // Magnific
    //------------------------------------------------------------------------------------------------------------------

    $('.popup-image').magnificPopup({
        closeBtnInside: true,
        type          : 'image',
        mainClass     : 'mfp-fade'
    });

    $('.popup-iframe').magnificPopup({
        type      : 'iframe',
        mainClass : 'mfp-fade'
    });

    $('.popup-modal').magnificPopup({
        type      : 'inline',
        modal     : true,
        mainClass : 'mfp-fade'
    });

    $(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

    //------------------------------------------------------------------------------------------------------------------
    // Masonry Grid
    //------------------------------------------------------------------------------------------------------------------

    $('.masonry-grid').masonry({
        itemSelector   : '.masonry-grid-item',
        columnWidth    : '.masonry-grid-sizer',
        percentPosition: true
    });

    //------------------------------------------------------------------------------------------------------------------
    // Stellar Parallax
    //------------------------------------------------------------------------------------------------------------------

    $.stellar({
        responsive: true,
        horizontalOffset: 0,
        verticalOffset: 0,
        horizontalScrolling: false,
        hideDistantElements: false
    });

    //------------------------------------------------------------------------------------------------------------------
    // Animate Numbers
    //------------------------------------------------------------------------------------------------------------------

    // Animate Numbers
    var $animate_number = $('.animate-number');

    if ($animate_number.length > 0) {

        $animate_number.appear();

        $body.on('appear', '.animate-number', function () {

            $animate_number.each(function (index) {

                var $this = $(this);
                if ($this.hasClass('animate-stop')) return;

                $this.animateNumber({
                    number: $this.attr('data-value'),
                    numberStep: $.animateNumber.numberStepFactories.separator(' ')
                }, 400 + (300 * (index + 1)));

                $this.addClass('animate-stop');
            });
        });
    }

    //------------------------------------------------------------------------------------------------------------------
    // Progress Circle
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Default progress type for Circle Progress
     * @type {string}
     */
    var PROGRESS_CIRCLE_DEFAULT_TYPE = 'string';

    /**
     * Default Circle Progress max value
     * @type {number}
     */
    var PROGRESS_CIRCLE_DEFAULT_VALUE_MAX = 100;

    $('.progress-circle').each(function() {

        var params = {};

        var $progress = $(this),
            progress_value = $progress.data('pc-value'),
            progress_type = $progress.data('pc-type');

        // Value - REQUIRED
        if (typeof progress_value === 'undefined') return;
        if (typeof progress_type === 'undefined') progress_type = PROGRESS_CIRCLE_DEFAULT_TYPE;

        params.value = progress_value;

        // Contextual color classes
        if ($progress.hasClass('progress-circle-default')) params.fill = { color: hex2RGBA(THEME_COLORS.DEFAULT, 100) };
        if ($progress.hasClass('progress-circle-primary')) params.fill = { color: hex2RGBA(THEME_COLORS.PRIMARY, 100) };
        if ($progress.hasClass('progress-circle-info'))    params.fill = { color: hex2RGBA(THEME_COLORS.INFO   , 100) };
        if ($progress.hasClass('progress-circle-success')) params.fill = { color: hex2RGBA(THEME_COLORS.SUCCESS, 100) };
        if ($progress.hasClass('progress-circle-warning')) params.fill = { color: hex2RGBA(THEME_COLORS.WARNING, 100) };
        if ($progress.hasClass('progress-circle-danger'))  params.fill = { color: hex2RGBA(THEME_COLORS.DANGER , 100) };

        var progress_target = $progress.data('progress-circle-target');
        if (progress_target === 'undefined') return;

        var $target = $(this).find('.progress-circle-value');
        if ($target.length === 0) return;

        switch (progress_type) {

            case 'percentage':

                $progress.circleProgress(params).on('circle-animation-progress', function(event, progress, stepValue) {
                    $target.html(String(parseInt(stepValue.toFixed(2).substr(1) * 100, 10)) + '%');
                });

                break;

            case 'string':
            default:

                var value_max = $progress.data('pc-value-max');
                if (typeof value_max === 'undefined') value_max = PROGRESS_CIRCLE_DEFAULT_VALUE_MAX;

                var percentage_value = progress_value * 100 / value_max;
                params.value = percentage_value / 100;

                $progress.circleProgress(params).on('circle-animation-progress', function(event, progress, stepValue) {
                    $target.html(parseInt(value_max * stepValue.toFixed(2).substr(1), 10));
                });

                break;

        }

    });

    //------------------------------------------------------------------------------------------------------------------
    // AJAX Forms
    //------------------------------------------------------------------------------------------------------------------

    $('.form-ajax').each(function(){

        $(this).validate({
            submitHandler: function(form) {

                var $submit_button = $(form).find('[type=submit]');
                $submit_button.button('loading');

                $.ajax({

                    type   : 'post',
                    url    : SEND_MAIL_PROCESSOR,
                    data   : $(form).serialize(),

                    success: function() {
                        swal('Success!', 'Message sent successfully!', 'success');
                        $submit_button.button('reset');
                    },

                    error: function(){
                        swal('Error!', 'Error sending message!', 'error');
                        $submit_button.button('reset');
                    }
                });
            }
        });
    });

    //------------------------------------------------------------------------------------------------------------------
    // Google Maps
    //------------------------------------------------------------------------------------------------------------------

    var $canvas_map = $('#canvas-map-contact');

    if ($canvas_map.length > 0) {

        var lat_lng = new google.maps.LatLng(-37.85787, 144.5191615),
            map_center = new google.maps.LatLng(-37.728687, 145.162939),
            //hue ='#ff5555',
            marker_title = 'Company Name',
            marker_information =
                '<div id="map-window" class="map-window">' +
                    '<address class="no-margin">' +
                        '<strong>Your Company, Inc.</strong><br>' +
                        '1355 Market Street, Suite 900<br>' +
                        'San Francisco, CA 94103<br>' +
                        '<abbr title="Contact Phone">Phone:</abbr> (123) 456-7890' +
                    '</address>' +
                '</div>';


        // map settings
        var settings = {
            zoom          : 9,
            center        : map_center,
            mapTypeControl: false,
            mapTypeId     : google.maps.MapTypeId.ROADMAP,
            styles        : [
                {
                    stylers: [
                       { hue: THEME_COLORS.DEFAULT },
                       { saturation: -80 }
                    ]
                }, {
                    featureType: 'road',
                    elementType: 'geometry',
                    stylers    : [
                        { lightness: 100 },
                        { visibility: 'simplified' }
                    ]
                }, {
                    featureType: 'road',
                    elementType: 'labels',
                    stylers    : [
                        { visibility: 'off' }
                    ]
                }
            ]
        };

        // create map
        var map = new google.maps.Map(document.getElementById('canvas-map-contact'), settings);

        // map marker (see global)
        var marker = new google.maps.Marker({
            position: lat_lng,
            map: map,
            title: marker_title
        });

        // tooltip
        var info_window = new google.maps.InfoWindow({
            content: marker_information
        });

        // open tooltip
        info_window.open(map, marker);
    }

    //------------------------------------------------------------------------------------------------------------------
    // TODO: DEMO ONLY
    //------------------------------------------------------------------------------------------------------------------

    //
    // Button: Load more comments
    //

    $('.demo-button-load-more-comments').on('click', function () {

        var $btn = $(this).button('loading');

        setTimeout(function() {
            $btn.button('reset')
        }, 1500);

    });

    //------------------------------------------------------------------------------------------------------------------
    // Finish loading
    //------------------------------------------------------------------------------------------------------------------

    $(window).on('load', function() {

        /* Remove preloader */

        $loader.delay(500).fadeOut();
        $preloader.delay(1500).fadeOut('slow');

        setTimeout(function() { $body.addClass('loaded'); }, 1000);

    });


    //------------------------------------------------------------------------------------------------------------------
    // Additional
    //------------------------------------------------------------------------------------------------------------------

    function hex2RGBA(hex, opacity){

        var r, g, b;

        hex = hex.replace('#', '');

        r = parseInt(hex.substring(0,2), 16);
        g = parseInt(hex.substring(2,4), 16);
        b = parseInt(hex.substring(4,6), 16);

        return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + opacity / 100 + ')';
    }

});