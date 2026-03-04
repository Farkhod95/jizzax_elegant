<?php
use yii\helpers\Url;
use app\models\Client;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\OrderAccount;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'client_id',
        'width' => '280px',
        'filter' => ArrayHelper::map(Client::find()->all(),'id','fio'),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        
        'content' => function ($data) {
            if ($data->client) {
                return '<b style="font-size: 14px">'.$data->client->fio.'</b>';
            }

            // client topilmasa, bo'sh yoki fallback
            return '<span class="text-muted">Mijoz topilmadi</span>';
            
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'created_by',
        'width' => '250px',
        'filter' => ArrayHelper::map(
        User::find()
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
            if ($data->created_by) {
                    return '<b style="font-size: 14px">'.$data->createdBy->surname.' '. $data->createdBy->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'product_summ_dollar',
        'content'=> function($data){
            if ($data->product_summ_dollar) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->product_summ_dollar, 2).' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_summ_dollar',
        'content'=> function($data){
            if ($data->all_summ_dollar) {
                return   '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar, 2).' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_debt',
        'content'=> function($data){
            $orderAccount = OrderAccount::find()->where(['client_id' => $data->client_id])->one();
            if ($orderAccount) {
                return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt??0, 2).' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'150px',
        'attribute'=>'date',
        'content' => function ($data) {
            return \Yii::$app->formatter->asDate($data->date, 'php:d.m.Y');
        },
    ], 
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'comment',
        'width' => '200px',
        'content'=> function($data){
            $commit = $data->comment;
            if (empty($commit)) {
                return '';
            }
            $commitShort = substr($commit, 0, 20); // 40 ta xarflik qismini olamiz
            $commitId = uniqid(); // Modal ID uchun noyob identifikator yaratamiz
    
            $commitColor = '<b style="font-size: 14px;color:red">'.$commitShort.'...</b>';
    
            // Modal tugmachasi
            $modalButton = '<a href="#" data-toggle="modal" data-target="#commitModal-'.$commitId.'">Ko\'proq...</a>';
    
            // Modal HTML kodi
            $modalHtml = '<div class="modal fade" id="commitModal-'.$commitId.'" tabindex="-1" role="dialog" aria-labelledby="commitModalLabel-'.$commitId.'" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="commitModalLabel-'.$commitId.'">To\'liq Izoh</h5>
                                    
                                    </div>
                                    <div class="modal-body">
                                        <b>'.$commit.'</b>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Yopish</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
    
            return $commitColor . ' ' . $modalButton . $modalHtml;
        }
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'exchange_rate',
    // ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'discount_amount',
    // ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'summ_dollar',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'all_summ_dollar',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'total_debt',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'confirmation',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'cr_date_time',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'comment',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => ' {leadProducts} {leadDeleteOld}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadProducts' => function ($url, $model) {
                $url = Url::to(['/vozvrat-order/products' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-fullscreen"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Barcha mahsulotlar','class'=>'btn btn-secondary btn-xs']);
            },
            'leadUpdate' => function ($url, $model) {
                $url = Url::to(['/vozvrat-order/update', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            },
            'leadDeleteOld' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    // Create the button to trigger the modal
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#deleteModal-'.$model->id, // Unique modal ID
                        'title' => 'O\'chirish',
                        'class' => 'btn btn-danger btn-xs',
                    ]) .
                    // Modal HTML
                    '<div class="modal fade" id="deleteModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-'.$model->id.'" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel-'.$model->id.'"><b style="font-size:16px;color:red">'.$model->client->fio.'</b> ning Vozvrat qilingan buyurtmasini oʻchirib tashlamoqchimisiz?</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Vozvrat buyurtmani o\'chirishdan oldin izoh kiriting:</p>
                                    '.Html::beginForm(['/vozvrat-order/delete', 'id' => $model->id], 'post').'
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