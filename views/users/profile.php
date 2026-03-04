<?php

use yii\helpers\Html;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/**
 * @var $model
 */
$this->title = 'Profil';
$this->params['breadcrumbs'][] = $this->title;
$label = $model->attributeLabels();
CrudAsset::register($this);

?>
<?php Pjax::begin(['enablePushState' => false, 'id' => 'profile-pjax']) ?>
    <div class="panel panel-inverse user-index">
        <div class="panel-heading">
            <h4 class="panel-title">Profil
            </h4>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="profile-container">
                    <!-- begin profile-section -->
                    <div class="profile-section">
                        <!-- begin profile-left -->
                        <div class="profile-left">
                            <br>
                            <br>
                            <br>
                            <!-- begin profile-image -->
                            <?=Html::img($model->getAvatar(), [
                                'class' => 'img-circle img-responsive',
                                'style' => 'width:100%; object-fit: cover;', 'alt' => 'Avatar'
                            ])?>
                            <!-- end profile-image -->
                            <!-- begin profile-highlight -->
                            <!-- end profile-highlight -->
                        </div>
                        <!-- end profile-left -->
                        <!-- begin profile-right -->
                        <div class="profile-right">
                            <!-- begin profile-info -->
                            <div class="profile-info">
                                <!-- begin table -->
                                <div class="table-responsive">
                                    <table class="table table-profile">
                                        <thead>
                                        <tr>
                                            <th colspan="4" class="text-left">
                                                <h3><?=$model->getFio()?></h3>
                                                <h3 style="margin-top:-10px"><small><b><?=$model->getRoleDescription()?></b></small></h3>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="font-weight: bold;"><?=$label['address']?></td>
                                                <td><?=$model->address?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;"><?=$label['phone']?></td>
                                                <td><?=$model->phone?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;"><?=$label['status']?></td>
                                                <td><?=$model->getStatusDescription()?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: bold;"><?=$label['registry_date']?></td>
                                                <td><?= $model->registry_date != null ?  Yii::$app->dateFormat->dateTime($model->registry_date) : ''?></td>
                                            </tr>

                                            <tr>
                                                <td style="font-weight: bold;"><?=$label['last_seen']?></td>
                                                <td><?= $model->last_seen != null ? Yii::$app->dateFormat->dateTime($model->last_seen) : ''?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table -->
                            </div>
                            <!-- end profile-info -->
                        </div>
                        <!-- end profile-right -->
                    </div>
                    <!-- end profile-section -->
                    <div class="m-b-10">
                        <?=Html::a('<i class="fa fa-pencil"></i> O\'zgartirish', ['change'], ['class' => 'btn btn-warning btn-sm pull-right','role' =>'modal-remote' ])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php Pjax::end() ?>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>