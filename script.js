jQuery(document).ready(function($){
    $('.product-carousel-container').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow: $('.prev'),
        nextArrow: $('.next'),
        autoplay: true,
        autoplaySpeed: 2000,
    });
});