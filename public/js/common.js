function showAlert(cnfg) {
    $.fancyAlert(cnfg);
}

function showConfirmPopup(cnfg) {
    return new Promise(function (resolve, reject) {
        var opts = $.extend(true, {
            title: 'Are you sure?',
            message: '',
            okButton: 'Yes',
            noButton: 'No',
            clickOutside: "close",
            clickSlide: "close",
            callback: function (value) {
                $._loader(false);
                if (value) {
                    resolve();
                } else {
                    reject();
                }
            }
        }, cnfg || {});

        $.fancyConfirm(opts);
    });
}

$._loader = function (close) {
    var loaderObj = $('#_loader');
    var loaderHtml = '<div id="_loader"><div id="_loader__fill"></div><span id="_loader__roll"></span></div>';
    var loaderExist = loaderObj.length;

    if (loaderExist) {
        if (close === undefined) {
            loaderObj.remove();
        } else if (close === true) {
            loaderObj.remove();
        }
    } else {
        $('body').append(loaderHtml);
    }
};