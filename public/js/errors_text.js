function resolveErrorMessage(key, lang, carrierId) {

    var texts = {
        'already_subscribed': 'You already have an existing subscription',
        'postpaid_restricted': 'This offer is for prepaid customers only'
    };

    return texts[key] || null;


}