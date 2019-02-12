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