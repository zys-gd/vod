$(function () {
    $('.accordion')
        .on('click', '.faq-accordion_card_header_button__plus', function () {
            $('.faq-accordion_card_header_button').removeClass('faq-accordion_card_header_button__minus')
                .addClass('faq-accordion_card_header_button__plus');

            $(this).removeClass('faq-accordion_card_header_button__plus')
                .addClass('faq-accordion_card_header_button__minus')
                .parent().siblings('.faq-accordion_card__delimiter').addClass('d-none');
        })
        .on('click', '.faq-accordion_card_header_button__minus', function () {
            $(this).removeClass('faq-accordion_card_header_button__minus')
                .addClass('faq-accordion_card_header_button__plus');

            $('.faq-accordion_card__delimiter').removeClass('d-none');
        });
});