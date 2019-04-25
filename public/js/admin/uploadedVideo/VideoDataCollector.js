/**
 * @param {Function} uploadedVideoTemplate - handlebars library template
 * @param {Function} notSavedTemplate - handlebars library template
 *
 * @constructor
 */
function VideoDataCollector(uploadedVideoTemplate, notSavedTemplate) {
    /**
     * Uploaded and saved video data
     *
     * @type {Object[]}
     */
    this.successfullySaved = [];

    /**
     * Uploaded, but not saved video titles
     *
     * @type {String[]}
     */
    this.notSaved = [];

    /**
     * Handlebars library template
     *
     * @type {Function}
     */
    this.uploadedVideoTemplate = uploadedVideoTemplate;

    /**
     * Handlebars library template
     *
     * @type {Function}
     */
    this.notSavedTemplate = notSavedTemplate;

    /**
     * Add uploaded and saved video data to collection
     *
     * @param {Object} uploadedVideoData
     */
    this.addSuccessfullySaved = function (uploadedVideoData) {
        this.successfullySaved.push(uploadedVideoData);
    };

    /**
     * Add uploaded but saved with error video title
     *
     * @param {string} uploadedVideoTitle
     */
    this.addNotSaved = function (uploadedVideoTitle) {
        this.notSaved.push(uploadedVideoTitle);
    };

    /**
     * Check if there is at least one item in the collections
     *
     * @returns {boolean}
     */
    this.hasElementsToRender = function () {
        return this.successfullySaved.length > 0 || this.notSaved.length > 0;
    };

    /**
     * Render saved and uploaded but not saved videos
     *
     * @param parentElement - container for append list
     */
    this.render = function(parentElement) {
        var container = $('<div></div>');
        var self = this;

        if (this.notSaved.length > 0) {
            var notSavedTitles = this.notSaved.join(', ');

            var html = self.notSavedTemplate({
                videos: notSavedTitles
            });

            container.append(html);
        }

        this.successfullySaved.forEach(function (uploadedVideo, index) {
            var startThumbnail = uploadedVideo.thumbnails.shift();
            var endThumbnail = uploadedVideo.thumbnails.pop();

            if (index > 0) {
                container.append($('<hr/>'));
            }

            var html = self.uploadedVideoTemplate({
                uuid: uploadedVideo.uuid,
                title: uploadedVideo.title,
                startThumbnail: startThumbnail,
                endThumbnail: endThumbnail
            });

            container.append(html);
        });

        if (this.successfullySaved.length > 0) {
            var saveButton = $('<button></button>')
                .addClass('btn btn-primary')
                .attr('id', 'confirm-all-button')
                .html('Confirm all');

            container.append(saveButton);
        }

        parentElement.html(container);
    };
}