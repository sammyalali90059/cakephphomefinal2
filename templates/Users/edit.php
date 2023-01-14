<!-- File: templates/pets/edit.php -->
<h1>Edit Pet</h1>
<?= $this->Form->create($pet) ?>
    <fieldset>
        <legend><?= __('Edit Pet') ?></legend>
        <?= $this->Form->control('name') ?>
        <?= $this->Form->control('image') ?>
        <?= $this->Form->control('type') ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>
