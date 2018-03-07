<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Module\Ekom\Utils\E;

$prefix = "/theme/" . ApplicationParameters::get("theme");
HtmlPageHelper::js($prefix . "/libs/hero-slider/hero-slider.js", null, null, false);

?>
<div class="slider">
    <section class="cd-hero">
        <ul class="cd-hero-slider autoplay">
            <li class="selected">
                <div class="cd-full-width slider-theme-black top-left">
                    <!--                    <h2>Découvrez nos nouveautés</h2>-->
                    <!--                    <p>Le best du fitness arrive chez vous</p>-->
                    <a href="<?php echo $v['linkSlideOne']['link']; ?>"
                       class="link-full"><?php echo $v['linkSlideOne']['label']; ?></a>
                </div> <!-- .cd-full-width -->
            </li>
            <li class="cd-bg-video">
                <div class="cd-full-width">
                    <!--                    <h2>Zumba</h2>-->
                    <!--                    <p>Ca va zouker!</p>-->
                    <!--                    <a href="#0" class="cd-btn">S'inscrire</a>-->

                    <a href="<?php echo $v['linkSlideTwo']['link']; ?>"
                       class="link-full"><?php echo $v['linkSlideTwo']['label']; ?></a>
                </div> <!-- .cd-full-width -->

                <div class="cd-bg-video-wrapper" data-video="<?php echo $prefix; ?>/assets/video/video">
                    <!-- video element will be loaded using jQuery -->
                </div> <!-- .cd-bg-video-wrapper -->
            </li>

            <li>
                <div class="cd-full-width">
                    <!--                    <h2>Slide title here</h2>-->
                    <!--                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, explicabo.</p>-->
                    <!--                    <a href="#0" class="cd-btn">Start</a>-->
                    <!--                    <a href="#0" class="cd-btn secondary">Learn More</a>-->
                    <a href="<?php echo $v['linkSlideThree']['link']; ?>"
                       class="link-full"><?php echo $v['linkSlideThree']['label']; ?></a>
                </div> <!-- .cd-full-width -->
            </li>

            <!--            <li>-->
            <!--                <div class="cd-full-width">-->
            <!--                    <h2>Slide title here</h2>-->
            <!--                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, explicabo.</p>-->
            <!--                    <a href="#0" class="cd-btn">Start</a>-->
            <!--                    <a href="#0" class="cd-btn secondary">Learn More</a>-->
            <!--                </div> <!-- .cd-full-width -->-->
            <!--            </li>-->


            <!--                <li>-->
            <!--                    <div class="cd-full-width">-->
            <!--                        <h2>Slide title here</h2>-->
            <!--                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, explicabo.</p>-->
            <!--                        <a href="#0" class="cd-btn">Start</a>-->
            <!--                        <a href="#0" class="cd-btn secondary">Learn More</a>-->
            <!--                    </div> <!-- .cd-full-width -->-->
            <!--                </li>-->
        </ul> <!-- .cd-hero-slider -->

        <div class="cd-slider-middle-nav window">
            <button class="left">Left</button>
            <button class="right">Right</button>
        </div>

        <div class="cd-slider-nav">

            <span class="cd-marker item-1"></span>

            <ul>
                <li class="selected"><a href="#0">boo</a></li>
                <li><a href="#0">Tech 1</a></li>
                <li><a href="#0">Tech 2</a></li>
                <!--                <li><a href="#0">Video</a></li>-->
                <!--                    <li><a href="#0">Image</a></li>-->
            </ul>

        </div> <!-- .cd-slider-nav -->
    </section> <!-- .cd-hero -->
</div>
