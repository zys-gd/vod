function firstClickOtpBtn(translate) {
    let otpBtn = $('.x-otp-button');
    if (otpBtn.is('a')) {
        otpBtn.each((key, el) => {
            $(el).removeClass('x-otp-button')
                .addClass('x-subscribe-button')
                .find('button').text(translate);
        });
    } else if (otpBtn.is('button')) {
        otpBtn.each((key, el) => {
            $(el).removeClass('x-otp-button')
                .addClass('x-subscribe-button')
                .text(translate);
        });
    }
}