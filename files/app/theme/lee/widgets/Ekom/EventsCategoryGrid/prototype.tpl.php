<div class="category-grid  window">
    <?php


    use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

    $prefix = "/theme/" . ApplicationParameters::get("theme");


    $items = [
        [
            'img' => 'pilates',
            'title' => "2 événements prévus",
        ],
        [
            'img' => 'bienetre',
            'title' => "1 événement prévu",
        ],
        [
            'img' => 'crosstraining',
            'title' => "1 événement prévu",
        ],
    ];

    shuffle($items);


    ?>

    <?php foreach ($items as $m): ?>
        <div class="item">
            <div class="image">
                <a href="#">
                    <img src="<?php echo $prefix . '/img/category/events-' . $m['img'] . ".jpg"; ?>"
                         alt="blabla">
                </a>
            </div>
            <div class="text tcenter">
                <b><?php echo $m['title']; ?></b>
                <br>
                <p>Convention Pilates, convention zen</p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
