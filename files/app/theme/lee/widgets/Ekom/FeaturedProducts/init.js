
//----------------------------------------
// FEATURED PRODUCTS
//----------------------------------------
$('#featured-products-boxes').slick({
    autoplay: false,
    arrows: true,
    draggable: false,
    slidesToShow: 5,
    dots: true,
    responsive: [
        {
            breakpoint: 800,
            settings: {
                slidesToShow: 4,
                autoplay: false
            }
        },
        {
            breakpoint: 700,
            settings: {
                slidesToShow: 3,
                autoplay: false
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                autoplay: false
            }
        },
        {
            breakpoint: 480,
            settings: {
                fade: true,
                slidesToShow: 1,
                dots: false,
                arrows: true,
                autoplay: false
            }
        }
    ]
});