<?php


namespace Theme\Lee\Ekom\Carousel;


use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\LeeTheme;

class CarouselProductsRenderer
{

    private $id;


    public function __construct()
    {
        $this->id = 'a' . StringTool::getUniqueCssId();
    }

    public static function create()
    {
        return new static();
    }


    public function render(array $model)
    {
        LeeTheme::useLib("slick");
        LeeTheme::useLib("featherlight");
        KamilleThemeHelper::css("product-carousel.css");


        $renderer = CarouselItemRenderer::create();
        $title = $model['title'];
        $products = $model['products'];



        $n = count($products);
        if ($n > 5) {
            $n = 5;
        }

        $nMinus1 = $n - 1;
        $nMinus2 = $n - 2;
        $nMinus3 = $n - 3;
        $nMinus4 = $n - 4;
        if ($nMinus1 < 1) {
            $nMinus1 = 1;
        }
        if ($nMinus2 < 1) {
            $nMinus2 = 1;
        }
        if ($nMinus3 < 1) {
            $nMinus3 = 1;
        }
        if ($nMinus4 < 1) {
            $nMinus4 = 1;
        }


        ?>

        <?php if ($n < 5): ?>
        <style>
            <?php echo '#' . $this->id; ?>
            .slick-list {
                padding-left: 80px;
                padding-right: 80px;
            }
        </style>
    <?php endif; ?>

        <div class="carousel-products">
            <div class="window">
                <span class="title"><?php echo $title; ?></span>
                <div class="product-boxes" id="<?php echo $this->id; ?>">
                    <?php foreach ($products as $p) {
                        $renderer->render($p);
                    }
                    ?>
                </div>
            </div>
        </div>


        <script>
            jqueryComponent.ready(function () {


                var jCarousel = $('#<?php echo $this->id; ?>');

//                    jCarousel.on('click', function (e) {
//                        var jTarget = $(e.target);
//                        var jProductBox = jTarget.closest(".product-box");
//                        if (jProductBox.length) {
//                            var uriCard = jProductBox.find('.title').attr('href');
//                            location.href = uriCard;
//                            return false;
//                        }
//                    });

                //----------------------------------------
                // FEATURED PRODUCTS
                //----------------------------------------
                jCarousel.slick({
                    autoplay: false,
                    arrows: true,
                    draggable: false,
                    slidesToShow: <?php echo $n; ?>,
                    dots: true,
                    responsive: [
                        {
                            breakpoint: 800,
                            settings: {
                                slidesToShow: <?php echo $nMinus1; ?>,
                                arrows: true,
                                autoplay: false
                            }
                        },
                        {
                            breakpoint: 700,
                            settings: {
                                slidesToShow: <?php echo $nMinus2; ?>,
                                arrows: true,
                                autoplay: false
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: <?php echo $nMinus3; ?>,
                                arrows: true,
                                autoplay: false
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                fade: true,
                                slidesToShow: <?php echo $nMinus4; ?>,
                                dots: false,
                                arrows: true,
                                autoplay: false
                            }
                        }
                    ]
                });
            });
        </script>
        <?php
    }
}