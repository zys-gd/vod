$(document).ready(function () {
    $.fancyConfirm = function (opts) {
        $.fancybox.close(true);
        opts = $.extend(true, {
            title: 'Are you sure?',
            message: '',
            okButton: 'Yes',
            noButton: 'No',
            callback: $.noop,
            clickOutside: "close",
            clickSlide: "close",
        }, opts || {});

        $.fancybox.open({
            type: 'html',
            src:
                '<div class="fc-content">' +
                '<h3 class="text-center">' + opts.title + '</h3>' +
                '<p class="text-center">' + opts.message + '</p>' +
                '<div class="d-flex align-self-center align-items-center justify-content-between">' +
                '<a href="#" class="button-gray button button-confirm button-confirm__no" data-value="0" data-fancybox-close>' + opts.noButton + '</a>' +
                '<button data-value="1" data-fancybox-close class=" button button-confirm button-confirm__yes">' + opts.okButton + '</button>' +
                '</div>' +
                '</div>',

            opts: {
                animationDuration: 350,
                animationEffect: 'material',
                modal: false,
                baseTpl:
                    '<div class="fancybox-container fc-container" role="dialog" tabindex="-1">' +
                    '<div class="fancybox-bg"></div>' +
                    '<div class="fancybox-inner">' +
                    '<div class="fancybox-stage"></div>' +
                    '</div>' +
                    '</div>',
                beforeClose: function (instance, current, e) {
                    var button = e ? e.target || e.currentTarget : null;
                    var value = button ? $(button).data('value') : 0;

                    opts.callback(value);
                }
            },
        });
    };

    $.fancyAlert = function (opts) {
        $.fancybox.close(true);

        opts = $.extend(true, {
            title: "Success",
            message: "",
            closeText: "Close",
            reload: false
        }, opts || {});

        $.fancybox.open({
            type: 'html',
            src:
                '<div class="fc-content">' +
                '<h3 class="text-center">' + opts.title + '</h3>' +
                '<p class="text-center">' + opts.message + '</p>' +
                '<div class="d-flex align-self-center align-items-center justify-content-center">' +
                '<a href="#" class="button-gray button button-confirm" data-value="0" data-fancybox-close>' + opts.closeText + '</a>' +
                '</div>' +
                '</div>',
            afterClose: function () {
                if(opts.reload === true) {
                    location.reload();
                }
            }
        });
    };
});