$(function () {
    console.log('Xin Chào');

    $('.menu-c2').slideUp();

    $('.menu-c1-title-container').click(function (event) {
        console.log('đã click');

        $(this).next().slideToggle();

    });
});
// menu cấp 2 click menu cấp 3
$(function () {
    console.log('Xin Chào');

    $('.menu-c3').slideUp();

    $('.menu-c2-item-container-title').click(function (event) {
        console.log('đã click');

        $(this).next().slideToggle();

    });
});