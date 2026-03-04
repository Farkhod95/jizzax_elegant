<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Users */
/**
 * @var $description
 * @var $langs
 * @var $title
 */

?>
<div class="users-create">
    <?= $this->render('_form', [
        'model' => $model,
        'description' => $description,
        'langs' => $langs,
        'title' => $title,
    ]) ?>
</div>
