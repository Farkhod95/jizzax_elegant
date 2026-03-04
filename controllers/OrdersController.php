<?php

namespace app\controllers;

use Yii;
use app\models\Orders;
use app\models\OrdersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\OrderProducts;
use app\models\Warehouse;
use app\models\ProductCategory;
use app\models\Brands;
use yii\helpers\Url;
/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
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
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrders()
    {
        return $this->render('/');
    }

    public function actionExport()
    {    
        $orders = Orders::find()->select(['cr_date'])->groupBy(['cr_date'])->orderBy(['cr_date'=>SORT_DESC])->all();
        return $this->render('export', ['orders' => $orders]);
    }

    public function actionExportOrder($cr_date)
    {    
        $orders = Orders::find()->where(['cr_date' => $cr_date])->orderBy(['cr_date'=>SORT_DESC])->all();
        return $this->render('export_order', ['orders' => $orders, 'cr_date' => $cr_date]);
    }

    public function actionExportView($order_id, $cr_date, $customer_fio)
    {    
        $orderProducts = OrderProducts::find()->where(['order_id' => $order_id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('export_view', ['orderProducts' => $orderProducts, 'cr_date' => $cr_date, 'customer_fio' => $customer_fio, 'order_id' => $order_id]);
    }

    public function actionProducts($id)
    {    
        $warehouse = OrderProducts::find()->where(['order_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('products', ['warehouse' => $warehouse, 'order_id' => $id]);
    }

    public function actionProduct($id)
    {    
        $warehouse = OrderProducts::find()->where(['order_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('product', ['warehouse' => $warehouse, 'order_id' => $id]);
    }

    public function actionAccept(){
        // Requestni chop etish
        $request = Yii::$app->request;
        $customer_name = $request->post('customer_name');
        $count = $request->post('count');
        $product_details = $request->post('product_details');
        
        $orderModel = new Orders();
        $orderModel->status = 1;
        $orderModel->customer_fio = $customer_name;
        $orderModel->created_by = Yii::$app->user->identity->id;
        $orderModel->cr_date = date('Y-m-d H:i:s');
        $orderModel->cr_date_time = date('Y-m-d H:i:s');
        $orderModel->order_number = '#' . $random = substr(number_format(time() * rand(),0,'',''),0,8);
        $orderModel->save();

        $obj = json_decode($product_details, true);
       
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
        $byGroup = group_by("product_id", $obj);
        foreach ($byGroup as $objOne) {            
            $marka = $objOne['marka'];
            $name = $objOne['name'];
            $size = $objOne['size'];
            $count = $objOne['count'];
            $product_id = $objOne['product_id'];

            $warehouse = Warehouse::findOne($product_id);
            $warehouse->count = $warehouse->count - intval($count);
            $warehouse->save();

            $brand_id = Brands::find()->where(['name' => $marka])->one();
            $product_category_id = ProductCategory::find()->where(['name' => $name])->andWhere(['<>', 'sup_status', 0])->one();

            // print_r($product_category_id->id);
            // die();

            $orderProductsModel = new OrderProducts();
            $orderProductsModel->brand_id = $brand_id->id;
            $orderProductsModel->product_category_id = $product_category_id->id;
            $orderProductsModel->size = $objOne['size'];
            $orderProductsModel->count = $objOne['count'];
            $orderProductsModel->order_id = $orderModel->id;
            $orderProductsModel->cr_date = date('Y-m-d H:i:s');
            $orderProductsModel->save();
        }
        return $this->redirect(['product', 'id' => $orderModel->id]);
    }

    

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($model->getInvoiceHtmlText($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("orderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }

    /**
     * Displays a single Orders model.
     * @param integer $id
     * @return mixed
     */
    
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Buyurtmani ko'rish",
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Orders model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCheck($id)
    {
        $model = $this->findModel($id);
        $model->status = 2;
        $orderProducts = OrderProducts::find()->where(['order_id' => $id])->all();

        foreach ($orderProducts as $orderProduct) {
            $brand_id = $orderProduct->brand_id;
            $product_category_id = $orderProduct->product_category_id;
            $size = $orderProduct->size;
            $count = $orderProduct->count;
            $warehouse = Warehouse::find()
                                    ->andWhere(['brand_id' => $brand_id])
                                    ->andWhere(['product_category_id' => $product_category_id])
                                    ->andWhere(['size' => $size])->one();
            $warehouse->count = $warehouse->count + intval($count);
            $warehouse->save();
        }
        $model->save(false);
        return $this->redirect(['index']);
    }
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->status = 2;
        $model->save();
        $orderProducts = OrderProducts::find()->where(['order_id' => $id])->all();

        foreach ($orderProducts as $orderProduct) {
            $brand_id = $orderProduct->brand_id;
            $product_category_id = $orderProduct->product_category_id;
            $size = $orderProduct->size;
            $count = $orderProduct->count;
            $warehouse = Warehouse::find()
                                    ->andWhere(['brand_id' => $brand_id])
                                    ->andWhere(['product_category_id' => $product_category_id])
                                    ->andWhere(['size' => $size])->one();
            $warehouse->count = $warehouse->count + intval($count);
            $warehouse->save();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }
    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
