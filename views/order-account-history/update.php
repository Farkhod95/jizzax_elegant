<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\OrderAccountHistory */
?>
<div class="order-account-history-update">

    <?= $this->render('_form', [
        'model' => $model,
        'client_total_debt' => $client_total_debt,
        'order_account_status_old' =>$order_account_status_old,
        'type' => $type,
    ]) ?>

</div>
