<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/widget-product-trainers.css");
$trainers = $v['trainers'];


?>
<div class="widget widget-product-trainers window" id="widget-product-trainers">

    <div class="line top-title">
        <span class="main">FORMATEURS</span>
    </div>
    <div class="list-trainers">
        <?php foreach ($trainers as $trainer): ?>
            <div class="item">
                <img src="<?php echo $trainer['img']; ?>" alt="<?php echo htmlspecialchars($trainer['fName']); ?>">
            </div>
        <?php endforeach; ?>
    </div>

</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jWidget = $('#widget-product-trainers');
            var jCarousel = $(".list-trainers", jWidget);


            //----------------------------------------
            // FEATURED PRODUCTS
            //----------------------------------------
            jCarousel.slick({
                autoplay: false,
                arrows: true,
                draggable: false,
                slidesToShow: 3,
//                centerMode: true,
//                centerPadding: '10px',
                dots: true
//                responsive: [
//                    {
//                        breakpoint: 800,
//                        settings: {
//                            slidesToShow: <?php //echo $nMinus1; ?>//,
//                            arrows: true,
//                            autoplay: false
//                        }
//                    },
//                    {
//                        breakpoint: 700,
//                        settings: {
//                            slidesToShow: <?php //echo $nMinus2; ?>//,
//                            arrows: true,
//                            autoplay: false
//                        }
//                    },
//                    {
//                        breakpoint: 600,
//                        settings: {
//                            slidesToShow: <?php //echo $nMinus3; ?>//,
//                            arrows: true,
//                            autoplay: false
//                        }
//                    },
//                    {
//                        breakpoint: 480,
//                        settings: {
//                            fade: true,
//                            slidesToShow: <?php //echo $nMinus4; ?>//,
//                            dots: false,
//                            arrows: true,
//                            autoplay: false
//                        }
//                    }
//                ]
            });
        });
    });
</script>