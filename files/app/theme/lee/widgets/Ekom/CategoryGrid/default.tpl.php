<div class="category-grid window">
    <?php foreach ($v['cats'] as $m): ?>
        <div class="item">
            <div class="image">
                <a href="<?php echo $m['uri']; ?>">
                    <img src="<?php echo $m['imgUri']; ?>"
                         alt="<?php echo htmlspecialchars($m['label']); ?>">
                </a>
            </div>
            <div class="text">
                <p>
                    <?php echo $m['description']; ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
