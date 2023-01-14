<div class="row">
    <?php foreach ($users as $user): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= h($user->name) ?> <?= h($user->surname) ?></h5>
                    <p class="card-text">Email: <?= h($user->email) ?></p>
                    <?php if ($user->isBanned == 'true'): ?>
                        <p class="text-danger">This user is banned</p>
                    <?php endif; ?>
                    <?php if ($isAuthenticated && $loggedInUser->role == 'admin'): ?>
                        <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'ban', $user->id]) ?>" class="btn btn-danger">Ban</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>