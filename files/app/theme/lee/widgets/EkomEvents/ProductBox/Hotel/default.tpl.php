<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;


KamilleThemeHelper::css("widgets/widget-product-events.css");


$hotels = $v['hotels'];

?>


<div id="widget-product-events-hotel"
     class="widget widget-product-events-hotel window pt20">


    <div class="line top-title">
        <span class="main">HÔTEL</span>
    </div>


    <div class="hotel-container">
        <?php if ($hotels): ?>
            <?php foreach ($hotels

                           as $hotel): ?>

                <div class="left">
                    <?php if ($hotel['extra']): ?>
                        <div class="extra1 client">
                            <!--                <div class="pad" style="padding-top:30px;">-->
                            <!--                    <b>CAMPANILE</b>-->
                            <!--                    <span style="margin-left: 100px;color: #929090;">1 NUIT</span>-->
                            <!--                    <b style="margin-left: 100px;">100 €</b>-->
                            <!--                </div>-->
                            <?php echo $hotel['extra']; ?>
                        </div>
                    <?php else: ?>
                        <div class="extra1 hotel-label">
                            <?php echo $hotel['label']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="extra2 client">
                        <!--                <div style="color: #929090;padding:10px;">-->
                        <!--                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur est eum fuga nemo nobis-->
                        <!--                    ratione-->
                        <!--                    repellat repudiandae tempora voluptate. Eum odio optio provident quae repudiandae voluptate.-->
                        <!--                    Accusantium-->
                        <!--                    adipisci dolorum in!-->
                        <!--                </div>-->
                        <?php echo $hotel['extra2']; ?>
                    </div>
                    <?php if ($hotel['link']): ?>
                        <a href="<?php echo $hotel['link']; ?>" class="lee-red-button more-info">PLUS D'INFORMATIONS</a>
                    <?php endif; ?>
                </div>
                <div class="right">
                    <img src="<?php echo $hotel['img']; ?>" alt="<?php echo htmlspecialchars($hotel['label']); ?>">
                </div>

            <?php endforeach; ?>


        <?php else: ?>
            <div class="no-hotel">
                Aucun hôtel partenaire pour cet événement.
            </div>
        <?php endif; ?>
    </div>
</div>
