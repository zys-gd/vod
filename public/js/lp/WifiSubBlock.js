// TODO find more flexible way to apply validation attributes#}
function addValidationAttributes(carrierId) {
    var pinPatterns = {
        2253: '^[0-9][0-9]{5}$',
        2254: '^[0-9][0-9]{5}$'
    };

    var msisdnPatterns = {
        2253: '^\\+(201)[0-9]{9}$',
        2254: '^\\+(201)[0-9]{9}$'
    };

    if (msisdnPatterns[carrierId]) {
        $("input[name='mobile_number']").attr('pattern', msisdnPatterns[carrierId])
    }

    if (pinPatterns[carrierId]) {
        $("input[name='pin_code']").attr('pattern', pinPatterns[carrierId])
    }
}

function fillOptionsForCarrierSelect({id, name}) {
    let template = `<option value='${id}'>${name}</option>`;
    $('select[name="carrier_id"]').append(template);
}

function clearOptionsForCarrierSelect(defaultCarrierSelectText) {
    let template = `<option value=''>${defaultCarrierSelectText}</option>`;
    $('select[name="carrier_id"]').empty().append(template);
}

function replaceBodyContent(html) {
    let mainPageContainer = $(html).filter('#main-page-container').html();
    let footer = $(html).filter('footer').html();
    $('.media-body').html(mainPageContainer);
    $('footer').html(footer);
}

function changeCountry(e, url, defaultSelectText) {
    let countryCode = String(e.target.options[e.target.selectedIndex].value);
    if (countryCode.length > 0) {
        $.ajax({
            url: url,
            method: 'GET',
            data: {countryCode},
            beforeSend: () => {
                clearOptionsForCarrierSelect(String(defaultSelectText));
                $._loader();
            },
            success: (response) => response.map(item => fillOptionsForCarrierSelect(item)),
            error: (response) => {
                if (this._response.responseJSON.data.hasOwnProperty('redirectUrl')) {
                    window.location = this._response.responseJSON.data.redirectUrl;
                }
            },
            complete: () => $._loader(true)
        })
    }
}

function changeCarrier(e, url) {
    let carrierId = String(e.target.options[e.target.selectedIndex].value);
    if (carrierId.length > 0) {
        $.ajax({
            url: url,
            method: 'GET',
            data: {carrierId},
            beforeSend: () => {
                $._loader();
            },
            success: response => replaceBodyContent(response),
            complete: () => $._loader(true),
        })
    }
}