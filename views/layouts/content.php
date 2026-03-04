<?php
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;

/**
 * @var  $content
 */
?>

<div id="content" class="content">
    <div style="margin-top: -10px;margin-bottom: 5px;">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </div>
    <?= Alert::widget() ?>
    <?= $content ?>
</div>