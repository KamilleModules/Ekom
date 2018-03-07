<div class="category-grid window">
    <?php


    use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

    $prefix = "/theme/" . ApplicationParameters::get("theme");


    $items = [
        [
            'img' => 'cross-training',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'halterophilie',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'musculation',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'video-teaser',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'cardio-training',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'soin-du-sportif',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'fitness',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'pilates-yoga',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
        [
            'img' => 'video-teaser2',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'musculation',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'halterophilie',
            'text' => "Barre de compétition, rack,disque,barre de traction, plateau, bagu, sol",
        ],
        [
            'img' => 'equipement-de-salle',
            'text' => "Cage, station, suspension, balle, kettlebell,plyobox, medecine ball, corde, ab whell",
        ],
    ];

    shuffle($items);



    ?>

    <?php foreach ($items as $m): ?>
        <div class="item">
            <div class="image">
                <a href="#">
                    <img src="<?php echo $prefix . '/img/category/' . $m['img'] . ".jpg"; ?>"
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
