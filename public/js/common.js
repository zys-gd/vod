function showPopup(message) {

    alert(message)
}

function showConfirmPopup(message) {
    return new Promise(function (resolve, reject) {
        if (confirm(message)) {
            resolve()
        } else {
            reject();
        }
    })
}

$._loader = function(close) {
    var loaderObj = $('#_loader');
    var loaderHtml = '<div id="_loader"><div id="_loader__fill"></div><span id="_loader__roll"></span></div>';
    var loaderExist = loaderObj.length;

    if(loaderExist) {
        if(close === undefined) {
            loaderObj.remove();
        }
        else if (close === true) {
            loaderObj.remove();
        }
    }
    else {
        $('body').append(loaderHtml);
    }
};