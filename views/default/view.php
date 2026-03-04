<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
?>
<div class="users-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'permission',
            'username',
            'status',
            'email:email',
            'password',
            'avatar',
            'surname',
            'name',
            'middle_name',
            'phone',
            'last_seen',
            'access_token',
            'registry_date',
            'email_verified:boolean',
            'phone_verified:boolean',
            'referal_id',
        ],
    ]) ?>

</div>
