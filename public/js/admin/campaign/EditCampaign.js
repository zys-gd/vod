let containerForFields,
    standardNameForDiv;

function hideOrShowOptionForOneClickIfCheckedIsOneClickFlow(){
    let isOneClickFlow = $('input[id*="_isOneClickFlow"]')[0];
    let whenActiveOneClickFlowCalendar = $('div[id*="_schedule"]').first();
    let isOneClickFlowOnOutOfOffice = $('div[id*="isOneClickFlowOnOutOfOffice"]').first();
    let isOneClickFlowOnOutOfOfficeArabicGeo = $('div[id*="isOneClickFlowOnOutOfOfficeArabicGeo"]').first();

    if (isOneClickFlow.checked) {
        whenActiveOneClickFlowCalendar.show(400);
    } else {
        whenActiveOneClickFlowCalendar.hide(400);
        isOneClickFlowOnOutOfOffice.hide(400);
        isOneClickFlowOnOutOfOfficeArabicGeo.hide(400);
    }
}

function hideOrShowTableIfCheckedPermanently(){
    let isOneClickFlow = $('input[id*="_isOneClickFlow"]')[0];
    let whenActiveOneClickFlowCalendar = $('div[id*="_schedule"]').first();
    let isOneClickFlowOnOutOfOffice = $('div[id*="isOneClickFlowOnOutOfOffice"]').first();
    let isOneClickFlowOnOutOfOfficeArabicGeo = $('div[id*="isOneClickFlowOnOutOfOfficeArabicGeo"]').first();

    if (isOneClickFlow.checked) {
        whenActiveOneClickFlowCalendar.show(400);
        isOneClickFlowOnOutOfOffice.show(400);
        isOneClickFlowOnOutOfOfficeArabicGeo.show(400);
    } else {
        whenActiveOneClickFlowCalendar.hide(400);
        isOneClickFlowOnOutOfOffice.hide(400);
        isOneClickFlowOnOutOfOfficeArabicGeo.hide(400);
    }
}

function DataForOnOutOfOfficeOrArabicGeo(arrabicGeo){

    let isOneClickFlowOnOutOfOffice = $('input[id*="isOneClickFlowOnOutOfOffice"]').first();
    let isOneClickFlowOnOutOfOfficeArabicGeo = $('input[id*="isOneClickFlowOnOutOfOfficeArabicGeo"]').first();
    let dataForTable = null;

    if(arrabicGeo){
        if(isOneClickFlowOnOutOfOfficeArabicGeo.is(':checked')){
            if(isOneClickFlowOnOutOfOffice.is(':checked')){
                $(isOneClickFlowOnOutOfOffice).siblings().click();
            }
            dataForTable = '{"day":0,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":1,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":2,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":3,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":4,"periods":[{"start":"00:00","end":"00:00"}]},{"day":5,"periods":[{"start":"00:00","end":"00:00"}]},{"day":6,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]}';
        }
    } else if(!arrabicGeo){
        if(isOneClickFlowOnOutOfOffice.is(':checked')){
            if(isOneClickFlowOnOutOfOfficeArabicGeo.is(':checked')){
                $(isOneClickFlowOnOutOfOfficeArabicGeo).siblings().click();
            }
            dataForTable = '{"day":0,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":1,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":2,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":3,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":4,"periods":[{"start":"00:00","end":"08:00"},{"start":"21:00","end":"00:00"}]},{"day":5,"periods":[{"start":"00:00","end":"00:00"}]},{"day":6,"periods":[{"start":"00:00","end":"00:00"}]}';
        }
    }

    if(dataForTable === null){
        $("#schedule").jqs().jqs('reset');
    } else{
        $("#schedule").jqs().jqs('reset');
        $("#schedule").jqs().jqs('import', JSON.parse( '[' + dataForTable + ']'));
    }
}

$(document).ready(function () {
    let input = $('input[id*="_when_active_one_click_flow"]')[0];
    containerForFields = $('.sonata-ba-collapsed-fields')[1];
    standardNameForDiv = $('div[id*="_schedule"]')[0].id;
    $(containerForFields).append("<div class='form-group' id='"+ standardNameForDiv +"'><div id='schedule'></div><div id='scheduler'></div></div>");

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

    $("#schedule").jqs().jqs('import', JSON.parse( '[' + $(input).val() + ']'));

    $('button[name="btn_update_and_list"], button[name="btn_update_and_edit"], button[name="btn_create_and_edit"] ,button[name="btn_create_and_list"], button[name="btn_create_and_create"]').on('click', function (e) {
        let newExportString =  $('#schedule').jqs('export');
        newExportString = newExportString.slice(1, -1);
        $('input[id*="_when_active_one_click_flow"]').val(newExportString);
    });
});

