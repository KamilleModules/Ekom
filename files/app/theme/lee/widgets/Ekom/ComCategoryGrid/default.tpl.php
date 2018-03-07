<?php


?><div class="category-grid  window">
    <?php foreach ($v['cats'] as $m): ?>
        <div class="item">
            <div class="image">
                <a href="<?php echo $m['uri']; ?>">
                    <img src="<?php echo $m['imgUri']; ?>"
                         alt="<?php echo htmlspecialchars($m['label']); ?>">
                </a>
            </div>
            <div class="text tcenter">
                <?php echo $m['description']; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
