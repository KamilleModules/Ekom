<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Theme\LeeTheme;


$uri = "/theme/" . ApplicationParameters::get("theme");


$names = [
    "balance-board",
    "balance-board-logo",
    "balance-board-demo",
    "balance-board-arriere",
    "balance-board",
    "balance-board-logo",
    "balance-board-demo",
    "balance-board-arriere",
];


HtmlPageHelper::js($uri . "/libs/elevate-zoom/jquery.elevateZoom-3.0.8.min.js", null, null, false);


LeeTheme::useLib("slick");

?>
<style>
    .product-box .slick-prev,
    .product-box .slick-next {
        position: static;
        transform: none;
    }
</style>
<div class="product-box window">
    <div class="photos-nav">
        <div class="vertical-carousel" id="the-vertical-carousel">
            <?php foreach ($names as $item): ?>
                <div class="item">
                    <a href="#"><img data-id="<?php echo $item; ?>"
                                     src="<?php echo $uri . "/img/products/balance-board/thumb/$item.jpg"; ?>"></a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="video-container">
            <div class="item video-item">
                <a href="#"><img
                            src="<?php echo $uri . "/img/products/balance-board/thumb/balance-board-video.jpg"; ?>"></a>
            </div>
        </div>
    </div>
    <div class="product-viewbox">

        <div class="image-container">
            <!--            <img id="zoom_01" src='images/small/image1.png' data-zoom-image="images/large/image1.jpg"/>-->
            <img id="zoom01" data-id="<?php echo $uri . "/img/products/balance-board/{type}/{item}.jpg"; ?>"
                 src="<?php echo $uri . "/img/products/balance-board/small/balance-board.jpg"; ?>"
                 data-zoom-image="<?php echo $uri . "/img/products/balance-board/large/balance-board.jpg"; ?>">
            <div class="video-wrapper">
                <video width="100%" height="100%">
                    <source src="/video/Larz Rocking Leaderfit Paris 2017 Step V2.mp4" type="video/mp4">
                    <!--                <source src="movie.ogg" type="video/ogg">-->
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
        <div class="contact-us-banner">
            <span>Besoin d'un conseil ?</span>
            <span>+33 (0)2 47 52 66 01</span>
        </div>
        <div class="contact-us-info">
            (
            Contactez le service client du lundi au vendredi de 09h00 à 12h30
            et de 14h00 à 17h30
            )
        </div>
    </div>
    <div class="product-info">
        <div class="label">Balance Board</div>
        <div class="meta">
            <div class="reference">Réf. 1436</div>
            <div class="rating-container">
                <div class="rating">
                    <span>☆</span><span class="hover">☆</span><span>☆</span><span>☆</span><span>☆</span>
                </div>
                <div class="text">
                    (6 avis)
                </div>
            </div>
        </div>
        <div class="description">
            Plateau de freeman en bois idéal pour travailler les muscles stabilisateurs, l'équilibre et la coordination.
            Ultra résistant grâce à son bois robuste, le plateau dispose d'une surface antidérapante.
        </div>
        <div class="description-link">
            <a href="#">Fiche technique détaillée</a>
        </div>
        <div class="availibity availability-in-stock">
            En stock
        </div>

        <div class="price">
            12,69 € <em>TTC</em>
        </div>
        <div class="pay-helpers">
            <a href="#">Facilités de paiement</a>
        </div>

        <div class="attribute-selectors">
            <div class="attribute-selector attribute-selector-weight">
                <div class="title">Poids</div>
                <ul>
                    <li><a href="#">0,5 KG</a></li>
                    <li class="active"><a href="#">1 KG</a></li>
                    <li><a href="#">2 KG</a></li>
                    <li><a href="#">3 KG</a></li>
                    <li><a href="#">4 KG</a></li>
                    <li><a href="#">5 KG</a></li>
                </ul>
            </div>
        </div>

        <div class="line f-start-end">
            <div class="quantity">
                <div class="title">Quantité</div>
                <input type="number" value="1">
            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks" href="#">Ajouter à ma liste</a>
            </div>
        </div>

        <div class="line">
            <button class="front-button button-black add-to-estimate">Ajouter au devis</button>
            <button class="front-button button-red">Ajouter au panier</button>
        </div>
        <div class="connect">
            <a href="#">Identifiez-vous pour bénéficier du prix professionnel</a>
        </div>


    </div>

</div>


<script>

    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            $('#the-vertical-carousel').slick({
                autoplay: false,
                arrows: true,
                vertical: true,
                draggable: false,
                slidesToShow: 4
            });

            var jZoom1 = $('#zoom01');
            jZoom1.elevateZoom();
            var jVideoWrapper = $(".video-wrapper");


            var video = null;

            function stopVideo() {
                if (null !== video) {
                    video.pause();
                    video.currentTime = 0;
                }
            }


            $('.product-box').on('click', function (e) {


                var jTarget = $(e.target);

                if (jTarget.closest('.video-wrapper').length) {

                    if (null !== video) {

                        if (video.paused) {
                            video.play();
                        }
                        else {
                            video.pause();
                        }
                    }
                    return false;
                }

                else if (jTarget.is('img')) {

                    var jClosest = jTarget.closest(".item");
                    if (jClosest.length > 0) {


                        if (jClosest.hasClass('video-item')) {
                            jVideoWrapper.show();

                            if (null === video) {
                                video = jVideoWrapper.find("video")[0];
                                video.play();
                            }
                            else {
                                if (video.paused) {
                                    video.play();
                                }
                                else {
                                    stopVideo();
                                }
                            }


                        }
                        else {
                            stopVideo();
                            jVideoWrapper.hide();


                            var itemName = jTarget.attr('data-id');
                            var model = jZoom1.attr("data-id");
                            var smallModel = model;
                            var largeModel = model;
                            smallModel = smallModel.replace('{type}', 'small');
                            smallModel = smallModel.replace('{item}', itemName);
                            largeModel = largeModel.replace('{type}', 'large');
                            largeModel = largeModel.replace('{item}', itemName);

                            jZoom1.attr('src', smallModel);
                            jZoom1.attr('data-zoom-image', largeModel);


                            jZoom1.data('zoom-image', largeModel).elevateZoom();
                        }
                        return false;
                    }
                }
                else {
                    console.log(jTarget);
                }
            });

        });
    });
</script>