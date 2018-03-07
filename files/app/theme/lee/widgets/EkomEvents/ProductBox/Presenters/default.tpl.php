<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\LeeTheme;


KamilleThemeHelper::css("widgets/widget-product-events.css");
LeeTheme::useLib("slick");

$presenters = $v['presenters'];
$id = uniqid(time());


?>
<div id="widget-product-events-presenters"
     class="widget widget-product-events-presenters window pt20">

    <div class="line top-title">
        <span class="main">PRESENTERS</span>
    </div>


    <div class="carousel-products">
        <div class="window">
            <div class="product-boxes" id="<?php echo $id; ?>">
                <?php foreach ($presenters as $presenter): ?>
                    <div class="image">
                        <img src="<?php echo $presenter['img']; ?>"
                             alt="<?php echo htmlspecialchars($presenter['pseudo']); ?>"
                        >
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            $(document).ready(function () {


                var jCarousel = $('#<?php echo $id; ?>');

                //----------------------------------------
                // FEATURED PRODUCTS
                //----------------------------------------
                jCarousel.slick({
                    autoplay: false,
                    arrows: true,
                    draggable: false,
                    slidesToShow: 3,
                    dots: true
                });
            });
        });
    </script>


</div>