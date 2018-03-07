<div class="category-grid window">
    <?php


    use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

    $prefix = "/theme/" . ApplicationParameters::get("theme");


    $items = [
        [
            'img' => 'communication-print',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'identite-graphique',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'communication-web',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'print-grand-format',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'plan-3d',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'conseil',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
    ];

//    shuffle($items);



    ?>

    <?php foreach ($items as $m): ?>
        <div class="item">
            <div class="image">
                <a href="#">
                    <img src="<?php echo $prefix . '/img/category/com-' . $m['img'] . ".jpg"; ?>"
                         alt="<?php echo htmlspecialchars($m['text']); ?>">
                </a>
            </div>
            <div class="text">
                <p>
                    <?php echo $m['text']; ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
