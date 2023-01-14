<ul class="list-group">
    <?php foreach ($pets as $pet): ?>
        <li class="list-group-item">
            <h5 class="card-title"><?= h($pet->name) ?></h5>
            <img src="<?= h($pet->image) ?>" width="100" height="100">
        </li>
    <?php endforeach; ?>
</ul>