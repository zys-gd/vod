$(document).on('click', '.x-game-download-button', function (e) {

    e.preventDefault();
    e.stopPropagation();

    var button = $(this);
    var parent = button.parents('.x-game-download-button-wrapper');
    var link   = parent.attr('data-href');

    $.ajax({
        url       : link,
        method    : 'GET',
        beforeSend: function () {
            button.attr('disabled', 1)
        },
        complete  : function () {
            button.attr('disabled', null)
        },
        success   : function (data) {
            if (data.url) {
                window.location = data.url;
            } else {
                showAlert({title: 'Internal error. Please try again later'})
            }
        }
    })
});