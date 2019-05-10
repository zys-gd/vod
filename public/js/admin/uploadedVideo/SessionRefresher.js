/**
 * Send empty requests to backend for refresh session during video uploading
 *
 * @constructor
 */
function SessionRefresher() {
    this.intervalId = null;

    this.start = function (pingUrl) {
        if (!this.intervalId) {
            this.intervalId = setInterval(function () {
                $.ajax({
                    url: pingUrl,
                    method: 'GET'
                })
            }, 60000)
        }
    };

    this.stop = function () {
        clearInterval(this.intervalId);
        this.intervalId = null;
    }
}