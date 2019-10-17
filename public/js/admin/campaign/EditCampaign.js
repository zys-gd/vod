/**
 * imported from store.playwing
 * with small adaptations
 */
let containerForFields,
    standardNameForDiv;

function hideOrShowOptionForOneClickIfCheckedIsOneClickFlow() {
    let isOneClickFlow = $('input[id*="_isOneClickFlow"]')[0];
    let whenActiveOneClickFlowCalendar = $('div[id*="_schedule"]').first();
    let isOneClickFlowOnOutOfOffice = $('div[id*="isOneClickFlowOnOutOfOffice"]').first();
    let isOneClickFlowOnOutOfOfficeArabicGeo = $('div[id*="isOneClickFlowOnOutOfOfficeArabicGeo"]').first();

    if (isOneClickFlow.checked) {
        whenActiveOneClickFlowCalendar.slideDown(200);
    } else {
        whenActiveOneClickFlowCalendar.slideUp(200);
        isOneClickFlowOnOutOfOffice.slideUp(200);
        isOneClickFlowOnOutOfOfficeArabicGeo.slideUp(200);
    }
}

function hideOrShowTableIfCheckedPermanently() {
    let isOneClickFlow = $('input[id*="_isOneClickFlow"]')[0];
    let whenActiveOneClickFlowCalendar = $('div[id*="_schedule"]').first();
    let isOneClickFlowOnOutOfOffice = $('div[id*="isOneClickFlowOnOutOfOffice"]').first();
    let isOneClickFlowOnOutOfOfficeArabicGeo = $('div[id*="isOneClickFlowOnOutOfOfficeArabicGeo"]').first();

    if (isOneClickFlow.checked) {
        whenActiveOneClickFlowCalendar.slideDown(200);
        isOneClickFlowOnOutOfOffice.slideDown(200);
        isOneClickFlowOnOutOfOfficeArabicGeo.slideDown(200);
    } else {
        whenActiveOneClickFlowCalendar.slideUp(200);
        isOneClickFlowOnOutOfOffice.slideUp(200);
        isOneClickFlowOnOutOfOfficeArabicGeo.slideUp(200);
    }
}

function DataForOnOutOfOfficeOrArabicGeo(arrabicGeo) {

    let isOneClickFlowOnOutOfOffice = $('input[id*="isOneClickFlowOnOutOfOffice"]').first();
    let isOneClickFlowOnOutOfOfficeArabicGeo = $('input[id*="isOneClickFlowOnOutOfOfficeArabicGeo"]').first();
    let dataForTable = null;

    if (arrabicGeo) {
        if (isOneClickFlowOnOutOfOfficeArabicGeo.is(':checked')) {
            if (isOneClickFlowOnOutOfOffice.is(':checked')) {
                $(isOneClickFlowOnOutOfOffice).siblings().click();
            }
            // dataForTable = '{"day":0,"periods":[{"start":"08:00","end":"21:00"}]},{"day":1,"periods":[{"start":"08:00","end":"21:00"}]},{"day":2,"periods":[{"start":"08:00","end":"21:00"}]},{"day":3,"periods":[{"start":"08:00","end":"21:00"}]},{"day":6,"periods":[{"start":"08:00","end":"21:00"}]}';
            dataForTable = '{"day":0,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":1,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":2,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":3,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":4,"periods":[{"start":"00:00","end":"00:00"}]},{"day":5,"periods":[{"start":"00:00","end":"00:00"}]},{"day":6,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]}';
        }
    } else if (!arrabicGeo) {
        if (isOneClickFlowOnOutOfOffice.is(':checked')) {
            if (isOneClickFlowOnOutOfOfficeArabicGeo.is(':checked')) {
                $(isOneClickFlowOnOutOfOfficeArabicGeo).siblings().click();
            }
            // dataForTable = '{"day":0,"periods":[{"start":"08:00","end":"21:00"}]},{"day":1,"periods":[{"start":"08:00","end":"21:00"}]},{"day":2,"periods":[{"start":"08:00","end":"21:00"}]},{"day":3,"periods":[{"start":"08:00","end":"21:00"}]},{"day":4,"periods":[{"start":"08:00","end":"21:00"}]}';
            dataForTable = '{"day":0,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":1,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":2,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":3,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":4,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":5,"periods":[{"start":"00:00","end":"00:00"}]},{"day":6,"periods":[{"start":"00:00","end":"00:00"}]}';
        }
    }

    if (dataForTable === null) {
        $("#schedule").jqs().jqs('reset');
    } else {
        $("#schedule").jqs().jqs('reset');
        $("#schedule").jqs().jqs('import', JSON.parse('[' + dataForTable + ']'));
    }
}

$(document).ready(function () {
    let input = $('input[id*="_schedule"]')[0];
    containerForFields = $('input[id*="_schedule"]').prev('div');
    standardNameForDiv = '_schedule';
    $(containerForFields).append("<div class='form-group' id='" + standardNameForDiv + "'><div id='schedule'></div><div id='scheduler'></div></div>");

    hideOrShowOptionForOneClickIfCheckedIsOneClickFlow();
    hideOrShowTableIfCheckedPermanently();

    $(document).on('ifChecked ifUnchecked', 'input[id*="_isOneClickFlow"]', function () {
        hideOrShowOptionForOneClickIfCheckedIsOneClickFlow();
    });
    $(document).on('ifChecked ifUnchecked', function () {
        hideOrShowTableIfCheckedPermanently();
    });
    $(document).on('ifChecked ifUnchecked', 'input[id*="_isOneClickFlowOnOutOfOffice"]', function (e) {
        let arabicGeo = e.currentTarget.id.indexOf('isOneClickFlowOnOutOfOfficeArabicGeo') != -1 ? true : false;
        DataForOnOutOfOfficeOrArabicGeo(arabicGeo);
    });

    $("#schedule").jqs().jqs('import', JSON.parse('[' + $(input).val() + ']'));

    $(document).on('click', 'button[name="btn_update_and_list"], button[name="btn_update_and_edit"], button[name="btn_create_and_edit"] ,button[name="btn_create_and_list"], button[name="btn_create_and_create"]', function (e) {
        let newExportString = $('#schedule').jqs('export');
        newExportString = newExportString.slice(1, -1);
        $('input[id*="_schedule"]').val(newExportString);
    });
});

