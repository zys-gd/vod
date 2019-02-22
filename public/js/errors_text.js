function resolveErrorMessage(key, lang, carrierId) {

    var texts = {
        'already_subscribed': 'You are already have an existing subscription'
    };

    return texts[key] || null;


}