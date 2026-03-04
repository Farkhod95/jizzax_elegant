<?php

use yii\helpers\Html;
use app\models\MyTotalDebt;
/* @var $this yii\web\View */
/* @var $model app\models\Sklad */


?>
<div class="sklad-update">

    <?= $this->render('update_form', [
        'model' => $model,
        'my_total_debt' => $my_total_debt,
    ]) ?>

</div>
