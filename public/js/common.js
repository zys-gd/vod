function showAlert(cnfg) {
    $.fancyAlert(cnfg);
}

function showConfirmPopup(cnfg) {
    return new Promise(function (resolve, reject) {
        var opts = $.extend(true, {
            title       : 'Are you sure?',
            message     : '',
            okButton    : 'Yes',
            noButton    : 'No',
            clickOutside: "close",
            clickSlide  : "close",
            callback    : function (value) {
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
    var loaderObj   = $('#_loader');
    var loaderHtml  = '<div id="_loader"><div id="_loader__fill"></div><span id="_loader__roll"></span></div>';
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

function performCookieEnabledCheck(cookiePageUrl) {
    if (!navigator.cookieEnabled) {
        redirectTo(cookiePageUrl)
    }
}


function redirectTo(url) {
    setTimeout(function () {
        var a = window.document.createElement("a");
        if (a.click) {
            // HTML5 browsers and IE support click() on <a>, early FF does not.
            a.setAttribute("href", url);
            a.style.display = "none";
            window.document.body.appendChild(a);
            a.click();
        } else {
            // Early FF can, however, use this usual method
            // where IE cannot with secure links.
            window.location = url;
        }
    }, 0)
}