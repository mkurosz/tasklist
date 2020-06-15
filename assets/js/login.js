import '../css/login.scss';
import $ from 'jquery';

$(function() {
    $('input').on('change', function() {
        var input = $(this);
        if (input.val().length) {
            input.addClass('populated');
        } else {
            input.removeClass('populated');
        }
    });

    setTimeout(function() {
        $('#inputEmail').trigger('focus');
    }, 500);
});
