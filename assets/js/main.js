(function ($) {
    var mqBreakpoints = {
            mobile: 480,
            phablet: 600,
            tablet: 768,
            desktop: 992,
            wide: 1200
        },
        $window = $(window),
        windowWidth = $window.innerWidth();


    /*
     * Function
     * Expand aria-controls elements
     */
    var ariaControls = function () {
        if ($('.js-aria-control').length > 0) {
            var $trigger = $('.js-aria-control');
            $trigger.bind('click', function (e) {
                var $_this = $(this),
                    $_targetEl = $('#' + $_this.attr('aria-controls')),
                    state = $_this.attr('aria-expanded') === 'false' ? false : true;

                $_this.attr('aria-expanded', !state);
                $_targetEl.attr('aria-hidden', state);

                e.preventDefault();
            });
        }
    };


    /*
     * Function
     * Check visibility for ARIA controlled element and add attributes (hidden and expanded)
     */
    var checkVisibility = function (id, breakpoint) {
        var $_el = $('#' + id),
            controller = $('[aria-controls=' + id + ']');

        if ($('window').innerWidth() <= breakpoint) {
            $_el.attr('aria-hidden', true);
            controller.attr('aria-expanded', false);
        } else {
            $_el.attr('aria-hidden', false);
            controller.attr('aria-expanded', true);
        }
    };


    /*
     * Function
     * When window is resized, re-check visibility
     */
    var watchVisibility = function () {
        // If orientation change (for mobile and tablet)
        window.addEventListener("orientationchange", function () {
            // Change windowWidth
            windowWidth = window.innerWidth();
        }, false);

        $(window).resize(function () {
            // Check if width really change (mobile consider scrolling as a width change)
            if ($window.innerWidth() != windowWidth) {
                checkVisibility('main-nav', mqBreakpoints.tablet);
            }
        });
    };

    $(document).ready(function () {
        ariaControls();
        checkVisibility('main-nav', mqBreakpoints.desktop);
        watchVisibility();
    });
})
(jQuery);