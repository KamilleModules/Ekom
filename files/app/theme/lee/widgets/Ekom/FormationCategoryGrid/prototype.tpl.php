<div class="category-grid  window">
    <?php


    use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

    $prefix = "/theme/" . ApplicationParameters::get("theme");




    $items = [
        [
            'img' => 'pilates',
        ],
        [
            'img' => 'zen',
        ],
        [
            'img' => 'nutrition',
        ],
        [
            'img' => 'sante',
        ],
        [
            'img' => 'management',
        ],
        [
            'img' => 'training',
        ],
    ];

    shuffle($items);


    ?>

    <?php foreach ($items as $m): ?>
        <div class="item">
            <div class="image">
                <a href="#">
                    <img src="<?php echo $prefix . '/img/category/formation-' . $m['img'] . ".jpg"; ?>"
                         alt="Matwork, femme enceinte & sénior, petit matériel, one to one, pilates thérapie">
                </a>
            </div>
            <div class="text tcenter">
                <b>16 modules de formations</b>
                <br>
                <b>2 cursus complets</b>
                <p>
                    Matwork, femme enceinte & sénior, petit matériel, one to one, pilates thérapie
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
