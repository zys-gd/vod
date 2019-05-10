/**
 * @param {string} signatureUrl
 * @param {string} saveBaseVideoDataUrl
 * @param {string} confirmVideosUrl
 *
 * @constructor
 */
function UploadHelper(signatureUrl, saveBaseVideoDataUrl, confirmVideosUrl) {

    this.signatureUrl = signatureUrl;
    this.saveBaseVideoDataUrl = saveBaseVideoDataUrl;
    this.confirmVideosUrl = confirmVideosUrl;

    /**
     * Save general video data from widget callback and pre-upload form data
     *
     * @param {Object} uploadedVideoData
     *
     * @return Promise
     */
    this.saveBaseVideoData = function(uploadedVideoData) {
        // delete uuid to prevent form handling errors
        delete uploadedVideoData.uuid;

        return this.sendPostRequest(this.saveBaseVideoDataUrl, uploadedVideoData);
    };

    /**
     * Save title, description and expired date of each video
     *
     * @param {Object} confirmedVideoData
     *
     * @return Promise
     */
    this.confirmAllUploadedVideos = function(confirmedVideoData) {
        return this.sendPostRequest(this.confirmVideosUrl, confirmedVideoData);
    };

    /**
     * @param {string} url
     * @param {Object} data
     *
     * @return Promise
     */
    this.sendPostRequest = function(url, data) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: url,
                type: 'POST',
                data: JSON.stringify(data),
                dataType: 'json',
                success: function (data) {
                    resolve(data);
                },
                error: function (jqXHR) {
                    reject(jqXHR);
                }
            })
        })
    };

    /**
     * Get callback for generate signature for cloudinary signed uploading
     *
     * @returns {Function}
     */
    this.getSignatureCallback = function () {
        var self = this;

        return function (callback, paramsToSign) {
            $.ajax({
                url : self.signatureUrl,
                type : 'GET',
                dataType: 'text',
                data : { data: paramsToSign },
                success : function(signature) {
                    callback(signature);
                }
            });
        }
    }
}