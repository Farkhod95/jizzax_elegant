<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\Warehouse;
use app\models\WarehouseFile;


return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn', 
    //     'attribute'=>'warehouse_id',
    //     // 'width' => '250px',
    //     'filter' => ArrayHelper::map( Warehouse::find()->all(),
    //         'id',
    //         'name' // This will use the concatenated full name for filtering
    //     ),
    //     'filterType' => GridView::FILTER_SELECT2,
    //     'filterWidgetOptions' => [
    //         'size' => Select2::SMALL,
    //         'options' => ['prompt' => Yii::t('app','Tanlang...'),],
    //         'pluginOptions' => ['allowClear' => true,'multiple' => false],
    //     ],
    //     'content' => function ($data) {
    //         if ($data->warehouse_id) {
    //             return '<b style="font-size: 14px;" >'. $data->brand->name .'</b>';
    //         }
    //     },
    // ],
       [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        // 'width' => '200px',
        'filter' => WarehouseFile::getType(),
        'content' => function($model){
            if ($model->type == 1) {
                return '<b style="font-size: 14px;color:green" >'. $model->getTypeView($model->type) .'</b>';
            }else{
                return '<b style="font-size: 14px;color:#f38d1f" >'. $model->getTypeView($model->type) .'</b>';
            }
            
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'file_path',
        'width' => '200px',
        'format' => 'raw',
        'content' => function($model) {
            $mediaUrl = $model->getImage(); // Bu metod video yoki rasm pathini qaytaradi
            $modalId = 'media-modal-' . $model->id;

            $thumbnail = $model->type == 1
                ? '<img src="' . $mediaUrl . '" style="height:50px; width:50px; border-radius:20%;">'
                : '<video src="' . $mediaUrl . '" style="height:50px; width:50px; border-radius:20%;"></video>';

            $modalBody = $model->type == 1
                ? '<img src="' . $mediaUrl . '" class="img-fluid" style="max-width:100%; max-height:90vh;">'
                : '<video src="' . $mediaUrl . '" class="w-100" style="max-height:40vh;" controls autoplay></video>';

            return '
                <a href="#" data-toggle="modal" data-target="#' . $modalId . '">' . $thumbnail . '</a>

                <div class="modal fade" id="' . $modalId . '" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel-' . $model->id . '" aria-hidden="true">
                <div class="modal-dialog" role="document" style="max-width: 700px;">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mediaModalLabel-' . $model->id . '">' . ($model->type == 1 ? 'Rasm' : 'Video') . ' ko‘rinishi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Yopish">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        ' . $modalBody . '
                    </div>
                    </div>
                </div>
                </div>
            ';
        }
    ],



    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'file_path',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
        [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'vAlign'=>'middle',
        'template' => '{leadUpdate} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/warehouse-file/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/warehouse-file/delete', 'id' => $model->id]);
                // if($model->id != \app\models\Users::SUPER_ADMIN_ID){
                return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', $url, [
                    'role'=>'modal-remote','title'=>'O\'chirish','class'=>'btn btn-danger btn-xs' ,
                    'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                    'data-request-method'=>'post',
                    'data-toggle'=>'tooltip',
                    'data-confirm-title'=>'Ishonchingiz komilmi?',
                    'data-confirm-message'=>'Haqiqatan ham bu elementni oʻchirib tashlamoqchimisiz',
                ]);
                // }
            },
        ],
    ],

];   