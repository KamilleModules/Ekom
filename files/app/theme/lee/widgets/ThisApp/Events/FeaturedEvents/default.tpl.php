<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("pages/events.css");


$width = 200;

?>
<div class="widget widget-last-featured-event window">


    <div class="container">
        <ul class="side-col">
            <li>
                <iframe  src="https://www.youtube.com/embed/fzdQ0VG2EWs" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
            <li>
                <iframe  src="https://www.youtube.com/embed/NsKWBdJPShM" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
            <li>
                <iframe  src="https://www.youtube.com/embed/BqWksmxXcDY" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
            <li>
                <iframe  src="https://www.youtube.com/embed/uAosyBLGg-0" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
        </ul>

        <div class="main-event">
            <a href="#">
                <img width="600" src="<?php echo htmlspecialchars($v['uri']); ?>" alt="<?php echo $v['alt']; ?>">
            </a>
        </div>


        <ul class="side-col">
            <li>
                <iframe  src="https://www.youtube.com/embed/fzdQ0VG2EWs" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
            <li>
                <iframe  src="https://www.youtube.com/embed/NsKWBdJPShM" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
            <li>
                <iframe  src="https://www.youtube.com/embed/BqWksmxXcDY" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
            <li>
                <iframe  src="https://www.youtube.com/embed/uAosyBLGg-0" frameborder="0"
                        gesture="media" allow="encrypted-media" allowfullscreen></iframe>
            </li>
        </ul>
    </div>
</div>