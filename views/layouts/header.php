<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Users;
use app\models\ExchangeRate;

$model = Users::findOne(Yii::$app->user->identity->id);
$exchangeRate = ExchangeRate::findOne(1);
?>

<div id="header1" class="header navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">

        <div class="navbar-header">
            <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                <span class="navbar-logo"></span> <?= Yii::$app->name ?>
            </a>
            <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <ul class="nav navbar-nav navbar-right" style="display:flex; align-items:center;">

            <?php if (Yii::$app->user->identity->permission == 1 || Yii::$app->user->identity->permission == 2): ?>

                <!-- ✅ Dollar kurs (TEXT, button emas) -->
                <li style="margin-right:10px;">
                    <span style="
                        color:#fff;
                        font-weight:600;
                        font-size:16px;
                        background: rgba(255,255,255,0.15);
                        padding:4px 10px;
                        border-radius:6px;
                    ">
                        Dollar kurs: <?= $exchangeRate ? number_format((float)$exchangeRate->dollar, 0, '.', ' ') : '0.00' ?> 
                    </span>
                </li>

                <!-- ✅ Button -->
                <li style="margin-right:10px;">
              <?= Html::a(
                    '<i class="fa fa-usd"></i> Dollar kursni o‘zgartirish',
                    ['/exchange-rate/update', 'id' => 1],
                    [
                        'class' => 'btn btn-info btn-xs',
                        'style' => '
                            padding: 3px 8px;
                            font-size: 14px;
                            line-height: 1.2;
                        ',
                        'role' => 'modal-remote',
                        'data-toggle' => 'tooltip',
                        'data-pjax' => 0,
                    ]
                ) ?>
                </li>

            <?php endif; ?>

            <!-- ✅ USER -->
            <li class="dropdown navbar-user">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?= $model != null ? $model->getAvatar() : '' ?>" alt="" />
                    <span class="hidden-xs"><?= $model != null ? $model->getFio() : '' ?></span>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu animated fadeInLeft">
                    <li class="arrow"></li>
                    <li><?= Html::a('<i class="fa fa-user"></i> Profil', ['/users/profile']) ?></li>
                    <li class="divider"></li>
                    <li><?= Html::a('Chiqish', ['/site/logout'], ['data-method' => 'post']) ?></li>
                </ul>
            </li>

        </ul>
    </div>
</div>