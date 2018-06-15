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

    var translateTable = function() {
        var $locales = $('[data-locales]'),
            $rows = $('.js-translation-row');

        if (!$locales.length || !$rows.length) {
            return;
        }

        $('body').on('click', '.js-translation-row', function() {
            var $this = $(this),
                locales = $locales.data('locales').split(',');

            $('.js-translation-section').show();
            $('.js-translation-row.-active').removeClass('-active');
            $this.addClass('-active');

            locales.forEach(function(locale) {
                $('textarea#' + locale).val($this.attr("data-" + locale));
            });

            $('.js-asset-notes').val($this.data('notes'));
            $('.js-asset-id').val($this.data('id'));
        });

        var $activeRow = $('.js-translation-row.-active');
        if ($activeRow.length) {
            $activeRow.click();
        } else {
            $('.js-translation-section').hide();
        }

        $('.js-toggle-translated-assets').on('click', function(e) {
            e.preventDefault();
            $('.js-translation-row:not(.-pending)').toggle();
        });
    };

    var translateField = function() {
        var $fields = $('textarea.js-translate-field');

        if (!$fields.length) {
            return;
        }

        $fields.on('focusout', function() {
            var $this = $(this),
                $form = $this.parents('form'),
                id = $form.find('[name="id"]').val();
            $('.js-translation-row[data-id="'+id+'"]').attr("data-" + $this.attr('id'), $this.val());
            $.post($form.attr('action'), $form.serialize()).done(function() {
                $this.siblings('.js-translation-success').show().fadeOut(3000);
            }).fail(function() {
                $this.siblings('.js-translation-error').show().fadeOut(3000);
            });
        });
    };

    var exportOptions = function() {
        var $select = $('.js-export-type');

        if (!$select.length) {
            return;
        }

        var toggleXliffOptions = function() {
            $('.js-xliff-options').toggle($select.val() === 'xliff');
        };

        $select.on('change', function() {
            toggleXliffOptions();
        });

        toggleXliffOptions();
    };

    $(document).ready(function () {
        ariaControls();
        checkVisibility('main-nav', mqBreakpoints.desktop);
        watchVisibility();
        translateTable();
        translateField();
        exportOptions();
    });
})
(jQuery);