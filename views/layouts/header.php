<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Users;
use app\models\ExchangeRate;

$model = Users::findOne(Yii::$app->user->identity->id);
$url = Url::to(['/prices/update' , 'id' => 1]);
$exchangeRate = ExchangeRate::findOne(1);


?>

<div id="header1" class="header navbar navbar-inverse navbar-fixed-top">
            <!-- begin container-fluid -->
            <div class="container-fluid">
                <!-- begin mobile sidebar expand / collapse button -->
                <div class="navbar-header">
                    <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand"><span class="navbar-logo"></span> <?= Yii::$app->name ?></a>
                    <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <!-- end mobile sidebar expand / collapse button -->
                
                <!-- begin header navigation right -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- <li>
                        <a class="dropdown-toggle f-s-14">
                            <span class="btn btn-info btn-xs m-r-5"> $exchangeRate->dollar  $</span>

                        </a>
                    </li> -->
                    <!-- <li>
                         Html::a('<span class="btn btn-warning btn-xs m-r-5"><i class="fa fa-usd"></i> Dollar kurni o\'zgartirish</span>', ['/exchange-rate/update', 'id' => 1], ['role'=>'modal-remote', 'data-toggle'=>'tooltip',]); 
                    </li> -->
                    <!-- <li>
                        <a href="<?=Yii::$app->params['site_name']?>" target="_blank" class="dropdown-toggle f-s-14">
                            <span class="btn btn-info btn-xs m-r-5">Сайт<span class=" fa fa-arrow-right"></span></span>

                        </a>
                    </li> -->
                    <!-- <li class="dropdown">
                        <a href="#" data-pjax="0" class="dropdown-toggle f-s-14">
                            <i class="fa fa-bell-o"></i>
                            <span class="label">34</span>
                        </a>
                    </li> -->
                    <li class="dropdown navbar-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $model != null ? $model->getAvatar() : ''?>" alt="" />
                            <span class="hidden-xs"><?= $model != null ? $model->getFio() : '' ?></span> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu animated fadeInLeft">
                            <li class="arrow"></li>
                            <li><?= Html::a('<i class="fa fa-user"></i> Profil', ['/users/profile'], []); ?></li>
                            <li class="divider"></li>
                            <li><?= Html::a(
                                    'Chiqish',
                                    ['/site/logout'], 
                                    ['data-method' => 'post',]   
                                ) ?></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>