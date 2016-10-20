<?= yii\helpers\Html::a('Create new item', 
    ['/menu/items/edit', 'menuId' => $menu->id], 
    ['class' => 'btn btn-primary', 'data-skip' => 1, 'data-op' => 'modal', 'data-title' => 'Edit menu']
); ?>
<br />
<br />
<?= $this->render('_list', compact('menu', 'provider')) ?>