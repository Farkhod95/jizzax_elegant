<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\Consignor;
use app\models\User;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'consignor_id',
        'width' => '250px',
        'filter' => ArrayHelper::map( Consignor::find()->all(),
            'id',
            'name' // This will use the concatenated full name for filtering
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->consignor_id) {
                return '<b style="font-size: 14px;" >'. $data->consignor->name .'</b>';
            }
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_debt',
        'content'=> function($data){
            return '<b style="font-size: 14px;color:red" >'. Yii::$app->formatter->asDecimal($data->total_debt?: 0, 2) .' $</b>';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            return '<b style="font-size: 14px;" >'. \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y') .'</b>';
        },
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn', 
    //     'attribute'=>'created_by',
    //     'width' => '250px',
    //     'filter' => ArrayHelper::map( User::find()
    //         ->select(['id', new \yii\db\Expression("CONCAT(surname, ' ', name) AS full_name")])
    //         ->asArray()
    //         ->all(),
    //         'id',
    //         'full_name' // This will use the concatenated full name for filtering
    //     ),
    //     'filterType' => GridView::FILTER_SELECT2,
    //     'filterWidgetOptions' => [
    //         'size' => Select2::SMALL,
    //         'options' => ['prompt' => Yii::t('app','Tanlang...'),],
    //         'pluginOptions' => ['allowClear' => true,'multiple' => false],
    //     ],
    //     'content' => function ($data) {
    //         if ($data->created_by) {
    //             return '<b style="font-size: 14px;" >'. $data->createdBy->surname.' '.$data->createdBy->name .'</b>';
    //         }
    //     },
    // ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'update_by',
        'width' => '250px',
        'filter' => ArrayHelper::map( User::find()
            ->select(['id', new \yii\db\Expression("CONCAT(surname, ' ', name) AS full_name")])
            ->asArray()
            ->all(),
            'id',
            'full_name' // This will use the concatenated full name for filtering
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->update_by) {
                return '<b style="font-size: 14px;" >'. $data->updateBy->surname.' '.$data->updateBy->name .'</b>';
            }
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'width' => '180px',
        'template' => '{leadUpdate} {leadView} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
        
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/my-total-debt/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-usd"></span>', $url, [ 'role'=>'modal-remote', 'target' =>'_blank', 'data-toggle'=>'tooltip', 'title'=>'Qarz to\'lash','class'=>'btn btn-warning btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/my-total-debt-history/index', 'id' => $model->id ]);
                return Html::a('<span class="glyphicon glyphicon-fullscreen"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'To\'lov tarixi','class'=>'btn btn-success btn-xs']);
            },
            // 'leadDelete' => function ($url, $model) {
            //     $url = Url::to(['/orders/delete', 'id' => $model->id]);
            //     return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Buyurtmani bekor qilish','class'=>'btn btn-danger btn-xs']);
            // },
            'leadDelete' => function ($url, $model) {
                $url = Url::to(['/my-total-debt/one-delete', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'chirish','class'=>'btn btn-danger btn-xs']);
            },
            'leadDeleteOld' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 5){
                    $consignor_name = "";
                    if ($model->consignor_id) {
                        $consignor_name = $model->consignor->name;
                    }
                    
                    // Create the button to trigger the modal
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#deleteModal-'.$model->id, // Unique modal ID
                        'title' => 'O\'chirish',
                        'class' => 'btn btn-danger btn-xs',
                    ]) .
                    // Modal HTML
                    '<div class="modal fade" id="deleteModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-'.$model->id.'" >
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel-'.$model->id.'"><b style="font-size:16px;color:red">'.$consignor_name.'</b> dan qarzingiz qolmadimi?</h5>
                                </div>
                                <div class="modal-body">
                                    <p>O\'chirishdan oldin izoh kiriting:'.'</p>
                                    '.Html::beginForm(['my-total-debt/delete', 'id' => $model->id], 'post').'
                                    <div class="form-group">
                                        '.Html::input('text', 'delete_reason', '', ['class' => 'form-control', 'placeholder' => 'Sababini kiriting...', 'required' => true]).'
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                                    '.Html::submitButton('O\'chirish', ['class' => 'btn btn-danger']).'
                                    '.Html::endForm().'
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            },
        ],
    ],

];   