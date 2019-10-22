/**
 * imported from store.playwing
 * with small adaptations
 */
let containerForFields,
    standardNameForDiv;

function hideOrShowOptionForOneClickIfCheckedIsOneClickFlow() {
    let isOneClickFlow = $('input[id*="_isOneClickFlow"]')[0];
    let schedule = $('div[id*="_schedule"]').first();

    if (isOneClickFlow.checked) {
        schedule.slideDown(200);
    } else {
        schedule.slideUp(200);
    }
}

function setOneClickFlowOnOutOfOffice()
{
    let dataForTable = null;
// dataForTable = '{"day":0,"periods":[{"start":"08:00","end":"21:00"}]},{"day":1,"periods":[{"start":"08:00","end":"21:00"}]},{"day":2,"periods":[{"start":"08:00","end":"21:00"}]},{"day":3,"periods":[{"start":"08:00","end":"21:00"}]},{"day":4,"periods":[{"start":"08:00","end":"21:00"}]}';
    dataForTable = '{"day":0,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":1,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":2,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":3,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":4,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":5,"periods":[{"start":"00:00","end":"00:00"}]},{"day":6,"periods":[{"start":"00:00","end":"00:00"}]}';
    $("#schedule").jqs().jqs('reset');
    $("#schedule").jqs().jqs('import', JSON.parse('[' + dataForTable + ']'));
}

function setOneClickFlowOnOutOfOfficeArabicGeo()
{
    let dataForTable = null;
// dataForTable = '{"day":0,"periods":[{"start":"08:00","end":"21:00"}]},{"day":1,"periods":[{"start":"08:00","end":"21:00"}]},{"day":2,"periods":[{"start":"08:00","end":"21:00"}]},{"day":3,"periods":[{"start":"08:00","end":"21:00"}]},{"day":6,"periods":[{"start":"08:00","end":"21:00"}]}';
    dataForTable = '{"day":0,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":1,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":2,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":3,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":4,"periods":[{"start":"00:00","end":"00:00"}]},{"day":5,"periods":[{"start":"00:00","end":"00:00"}]},{"day":6,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]}';
    $("#schedule").jqs().jqs('reset');
    $("#schedule").jqs().jqs('import', JSON.parse('[' + dataForTable + ']'));
}

function resetSchedule()
{
    $("#schedule").jqs().jqs('reset');
}

$(document).ready(function () {
    let input = $('input[id*="_schedule"]')[0];
    containerForFields = $('input[id*="_schedule"]').prev('div');
    standardNameForDiv = '_schedule';
    $(containerForFields).append("<div class='form-group' id='" + standardNameForDiv + "'><div id='schedule'></div><div id='scheduler'></div></div>");

    hideOrShowOptionForOneClickIfCheckedIsOneClickFlow();

    $(document).on('ifChecked ifUnchecked', 'input[id*="_isOneClickFlow"]', function () {
        hideOrShowOptionForOneClickIfCheckedIsOneClickFlow();
    });

    $("#schedule").jqs({
        onInit: function () {
            $buttonsHTMLBlock = "<tr class='jqs-schedule-button-tr'><td border='0'><button id='setOneClickFlowOnOutOfOffice' class='btn btn-small btn-primary' type='button'>Set 'Out Of Office' Schedule</button><button id='setOneClickFlowOnOutOfOfficeArabicGeo' class='btn btn-small btn-primary' type='button'>Set 'Out Of Office ArabicGeo' Schedule</button><button type='button' class='btn btn-small btn-danger' id='resetSchedule'>Reset Schedule</button></td></tr>";
            $('.jqs-table tr:last').after($buttonsHTMLBlock);
        }
    })
    .jqs('import', JSON.parse('[' + $(input).val() + ']'));


    $(document).on('submit', 'form[role="form"]', function (e) {
        $(this).find('.nav-tabs-custom .tab-content > div:first').addClass('fade active in');
        $(this).find('.nav-tabs-custom a:first').click();
        let newExportString = $('#schedule').jqs('export');
        newExportString = newExportString.slice(1, -1);
        $('input[id*="_schedule"]').val(newExportString);
    });
});

$(document).on('click', '#setOneClickFlowOnOutOfOffice, #setOneClickFlowOnOutOfOfficeArabicGeo, #resetSchedule', function () {
    eval( $(this).attr('id') )();
});

