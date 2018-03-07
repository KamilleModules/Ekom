<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;


KamilleThemeHelper::css("widgets/widget-product-events.css");


$location = $v['location'];
$key = $location['googleMapKey'];

$address = implode(' ', [
    $location['address'],
    $location['postcode'],
    $location['city'],
    $location['country'],
]);

?>


<div id="widget-product-events-location"
     class="widget widget-product-events-location window pt20">


    <div class="line top-title">
        <span class="main">LIEU</span>
    </div>

    <div class="location-container">
        <div class="map">
            <iframe
                    width="100%"
                    height="100%"
                    frameborder="0" style="border:0"
                    src="https://www.google.com/maps/embed/v1/place?key=<?php echo $key; ?>
    &q=<?php echo urlencode($address); ?>" allowfullscreen>
            </iframe>
        </div>
        <div class="info">
            <div class="top">
                <span class="main"><?php echo $location['label']; ?></span>
                <span class="address"><?php echo $location['address']; ?></span><br>
                <span class="address-2"><?php echo $location['postcode']; ?> <?php echo strtoupper($location['city']); ?></span>
            </div>
            <div class="bottom">
                <a href="#" class="lee-red-button">PLUS D'INFORMATIONS</a>
            </div>
        </div>
    </div>

</div>