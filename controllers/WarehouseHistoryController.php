<?php

namespace app\controllers;

use Yii;
use app\models\WarehouseHistory;
use app\models\WarehouseHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\Orders;
use app\models\OrderProducts;
use app\models\Brands;
use app\models\ProductCategory;
use yii\helpers\Url;


class WarehouseHistoryController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \app\models\Users::isMenejerRight(Yii::$app->user->identity->id);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all WarehouseHistory models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new WarehouseHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single WarehouseHistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "WarehouseHistory #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    public function actionImport()
    {    
        $warehouse = WarehouseHistory::find()->select(['cr_date'])->groupBy(['cr_date'])->orderBy(['cr_date'=>SORT_DESC])->all();
        return $this->render('import', ['warehouse' => $warehouse]);
    }

    public function actionImportView($cr_date)
    {    
        $warehouse = WarehouseHistory::find()->where(['cr_date' => $cr_date])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('import_view', ['warehouse' => $warehouse, 'cr_date' => $cr_date]);
    }

    public function actionExportView($cr_date)
    {    
        $warehouse = Orders::find()->where(['cr_date' => $cr_date])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('export_view', ['warehouse' => $warehouse, 'cr_date' => $cr_date]);
    }


    public function actionClientOrders($customer_fio, $start_date, $end_date)
    {    
        $orders = Orders::find()->andWhere(['customer_fio' => $customer_fio ])->andWhere(['between', 'cr_date', $start_date, $end_date])->all();
        $brands = new Brands();
        
        $array_Products = [];
        foreach($orders as $val) {
            $orderProducts = OrderProducts::find()->where(['order_id' => $val['id']])->select(['brand_id'])->groupBy(['brand_id'])->all();
            foreach($orderProducts as $value) {
                $array_Products []= [
                    'brand_id' => $value['brand_id'],
                    'brand_name' => $brands->getBrands($value['brand_id']),
                    'order_id' => $val['id'],
                    'cr_date' => $val['cr_date_time'],
                ];
            }
            
        }
        
        // echo '<pre style="margin-left:300px; margin-top:200px">';
        // print_r($array_Products);
        // echo '</pre>';
        return $this->render('client_orders', ['array_Products' => $array_Products, 'customer_fio' => $customer_fio]);
    }

    public function actionClient($start_date = '', $end_date = '')
    {    

        function group_by($key, $data) {
            $result = array();
        
            foreach($data as $val) {
                if(array_key_exists($key, $val)){
                    if(array_key_exists($val[$key], $result)){
                      $result[$val[$key]]['count'] += $val['count'];
                    }else{
                      $result[$val[$key]] = $val;
                    }
                }else{
                    $result[""][] = $val;
                }
            }
        
            return $result;
        }

        $request = Yii::$app->request;
        $start_date = $request->post('start_date');
        $end_date = $request->post('end_date');

        // echo '<pre style="margin-left:300px; margin-top:200px">';
        // print_r($start_date);
        // print_r($end_date);
        // echo '</pre>';
        if (!$start_date) {
            $start_date = date('Y-m-d');
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }

        $orders = Orders::find()->where(['between', 'cr_date', $start_date, $end_date ])->all();
        // echo '<pre style="margin-left:300px; margin-top:200px">';
        // print_r($orders);
        // echo '</pre>';
        $ordersArray = array();
        foreach ($orders as $model) {
            $orderProducts = OrderProducts::find()->where(['order_id' => $model->id])->all();
            $orderCount = 0;
            foreach ($orderProducts as $orderProduct) {
                $orderCount = $orderCount + $orderProduct->count;
            }
            $ordersArray [] = [
                'fio' => $model->customer_fio, 
                'count' => $orderCount, 
                'date' => date("d.m.Y", strtotime($model->cr_date)),
            ];
        }
        $html_td = '';
        $index = 1;
       
        $data = group_by("fio", $ordersArray);

usort($data, function($a, $b) {
    return $b['count'] <=> $a['count'];
});

$countAll = 0;
        foreach ($data as $model) {
            $html_td .='<tr class="handle" >
                    <td style="background-color:#efdfdf;"><b>' . $index . '</b></td>
                    <td style="background-color:#efdfdf;"><b><a href='. Url::toRoute(['warehouse-history/client-orders', 'customer_fio' => $model['fio'], 'start_date' => $start_date, 'end_date' => $end_date]).' class="alert-link" style="font-size: 14px;">'. $model['fio'] . '</a></b></td>
                    <td style="background-color:#efdfdf;"><b>' . $model['count'] . '</b></td>
                </tr>
                ';
            $index = $index + 1;
            $countAll = $countAll + $model['count'];
        }
        
        
        $html =  '<table class="table">
        <thead>
            <tr>
                <th  style="background-color:#90e6e6;" ><b>#</b></th>
                <th  style="background-color:#90e6e6;" nowrap><b>FIO</b></th>
                <th  style="background-color:#90e6e6;" nowrap><b>Karobka soni</b></th>
            </tr>
        </thead>
        <tbody data-count="0" data-increment="0">
            '.$html_td.'
            <tr>
                <td colspan="2" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                <td style="background-color:#2d353c;"><b style="color:white">'. $countAll . '</b></td>
            </tr>
        </tbody>
    </table>';
    
        return $this->render('client_history', ['html' => $html, 'start_date' => $start_date, 'end_date' => $end_date ]);
    }
    public function actionProduct()
    {    
        $request = Yii::$app->request;
        $start_date = $request->post('start_date');
        $end_date = $request->post('end_date');

        if (!$start_date) {
            $start_date = date('Y-m-d');
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }
        $orderProducts = OrderProducts::find()->where(['between', 'cr_date', $start_date, $end_date])->select(['brand_id'])->groupBy(['brand_id'])->all();
     
    
        return $this->render('order_product_history', ['orderProducts' => $orderProducts, 'start_date' => $start_date, 'end_date' => $end_date]);
    }
    // public function actionProduct()
    // {    
    //     $request = Yii::$app->request;
    //     $start_date = $request->post('start_date');
    //     $end_date = $request->post('end_date');

    //     $brands = new Brands();
    //     $productCategory = new ProductCategory();

    //     if (!$start_date) {
    //         $start_date = date('Y-m-d');
    //     }
    //     if (!$end_date) {
    //         $end_date = date('Y-m-d');
    //     }
    //     $orderProducts = OrderProducts::find()->where(['between', 'cr_date', $start_date, $end_date])->select(['SUM(count) as count', 'cr_date', 'brand_id', 'product_category_id', 'size'])->groupBy(['brand_id', 'size','product_category_id'])->all();
    //     $html_td = '';
    //     $index = 1;
    //     usort($orderProducts, fn($a, $b) => $b['count'] <=> $a['count']);
    //     $countAll = 0;
    //     foreach ($orderProducts as $model) {
    //         $html_td .='<tr class="handle" >
    //                 <td style="background-color:#efdfdf;"><b>' . $index . '</b></td>
    //                 <td style="background-color:#efdfdf;"><b>'.  $brands->getBrands($model['brand_id']). '</b></td>
    //                 <td style="background-color:#efdfdf;"><b>'. $productCategory->getProductCategory($model['product_category_id']) . '</b></td>
    //                 <td style="background-color:#efdfdf;"><b>' . $model['size'] . '</b></td>
    //                 <td style="background-color:#efdfdf;"><b>' . $model['count'] . '</b></td>
    //             </tr>
    //             ';
    //         $index = $index + 1;
    //         $countAll = $countAll + $model['count'];
    //     }
        
        
    //     $html =  '<table class="table">
    //     <thead>
    //         <tr>
    //             <th  style="background-color:#90e6e6;" ><b>#</b></th>
    //             <th  style="background-color:#90e6e6;" nowrap><b>Model</b></th>
    //             <th  style="background-color:#90e6e6;" nowrap><b>Nomi</b></th>
    //             <th  style="background-color:#90e6e6;" nowrap><b>O\'lchami</b></th>
    //             <th  style="background-color:#90e6e6;" nowrap><b>Karobka soni</b></th>
    //         </tr>
    //     </thead>
    //     <tbody data-count="0" data-increment="0">
    //         '.$html_td.'
    //         <tr>
    //             <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
    //             <td style="background-color:#2d353c;"><b style="color:white">'. $countAll . '</b></td>
    //         </tr>
    //     </tbody>
    // </table>';
    
    //     return $this->render('order_product_history', ['html' => $html, 'start_date' => $start_date, 'end_date' => $end_date]);
    // }

    protected function findModel($id)
    {
        if (($model = WarehouseHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
