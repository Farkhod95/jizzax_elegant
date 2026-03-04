<?php

namespace app\controllers;

use Yii;
use app\models\Sklad;
use app\models\SkladSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\models\Warehouse;
use app\models\WarehouseHistory;
use yii\filters\AccessControl;
use app\models\ElegantHistoryUpdate;
use app\models\Consignor;
use app\models\MyTotalDebt;
use app\models\PriceProduct;
use app\models\MyTotalDebtHistory;
use app\models\Prices;
/**
 * SkladController implements the CRUD actions for Sklad model.
 */
class SkladController extends Controller
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
                        'actions' => ['all-list'],
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
     * Lists all Sklad models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new SkladSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProducts($id)
    {    
        $searchModel2 = new SkladSearch(['consignor_id' => $id]);
        $dataProvider = $searchModel2->search2(Yii::$app->request->queryParams);

        return $this->render('index2', [
            'searchModel2' => $searchModel2,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionImport()
    {    
        $orders = Sklad::find()->where(['!=', 'actived', 0])->select(['cr_date'])->groupBy(['cr_date'])->orderBy(['cr_date'=>SORT_DESC])->all();
        return $this->render('import', ['orders' => $orders]);
    }

    public function actionImportOrder($cr_date)
    {    
        $orders = Sklad::find()->andWhere(['!=', 'actived', 0])->andWhere(['cr_date' => $cr_date])->orderBy(['cr_date'=>SORT_DESC])->all();
        $sum_dollars = 0;
        $given_sum_dollars = 0;
        $discount_amounts = 0;
        foreach ($orders as $value) {
            $sum_dollars = $sum_dollars + $value['sum_dollar'];
            $given_sum_dollars = $given_sum_dollars + $value['given_sum_dollar'];
            $discount_amounts = $discount_amounts + $value['discount_amount'];
        }

        $debtRepayments = MyTotalDebtHistory::find()->where(['cr_date' => $cr_date])->orderBy(['cr_date'=>SORT_DESC])->all();
        $deb_all_summ_dollars = 0;
        foreach ($debtRepayments as $value) {
            $deb_all_summ_dollars = $deb_all_summ_dollars + $value['all_summ_dollar'];
        }

        return $this->render('import_order', [
            'orders' => $orders, 
            'cr_date' => $cr_date, 

            'sum_dollars' => $sum_dollars, 
            'given_sum_dollars' => $given_sum_dollars, 
            'discount_amounts' => $discount_amounts, 

            'debtRepayments' => $debtRepayments, 
            'deb_all_summ_dollars' => $deb_all_summ_dollars, 
        ]);
    }
    
    public function actionImportView($id, $cr_date)
    {    
        $model = $this->findModel($id); 
        $warehouse = WarehouseHistory::find()->where(['sklad_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $model->consignor_id])->one();
        return $this->render('import_view', ['warehouse' => $warehouse, 'cr_date' => $cr_date, 'id' => $id, 'model' => $model, 'myTotalDebt' => $myTotalDebt]);
    }

    public function actionSkladView($id, $cr_date)
    {    
        $model = $this->findModel($id); 
        $warehouse = WarehouseHistory::find()->where(['sklad_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $model->consignor_id])->one();
        return $this->render('sklad_view', ['warehouse' => $warehouse, 'cr_date' => $cr_date, 'id' => $id, 'model' => $model, 'myTotalDebt' => $myTotalDebt]);
    }   

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlText($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("ClientOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }
    
    public function actionSkladView2($id, $cr_date)
    {    
        $warehouse = WarehouseHistory::find()->where(['sklad_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('sklad_view2', ['warehouse' => $warehouse, 'cr_date' => $cr_date, 'id' => $id]);
    }
    /**
     * Displays a single Sklad model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Sklad #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    public function actionQarzs($id){
        $request = Yii::$app->request;
        // $client_id = $request->get('client_id');
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $id])->one();
        
        $qarz_sum = 0;
        if ($myTotalDebt) {
            $qarz_sum = $myTotalDebt->total_debt;
        }
        return $qarz_sum;
    }

    public function actionQarzss($id=17){
        $request = Yii::$app->request;
        // $client_id = $request->get('client_id');
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $id])->one();
        
        $qarz_sum = 0;
        if ($myTotalDebt) {
            $qarz_sum = $myTotalDebt->total_debt;
        }
        return $qarz_sum;
    }

    public function actionImportCheck($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id); 
        $model->status = 2;
        $model->save(false);

        // $priceProducts = PriceProduct::find()->all();

        // foreach ($priceProducts as $priceProduct) {
        //     // Warehouse jadvaliga yangi yozuv qo'shish
        //     $warehouse = new Warehouse();
        //     // $warehouse->id = $priceProduct->id;
        //     $warehouse->brand_id = $priceProduct->brand_id;
        //     $warehouse->product_category_id = $priceProduct->product_category_id;
        //     $warehouse->size = $priceProduct->size;
        //     $warehouse->price = $priceProduct->real_price;
        //     $warehouse->count = 1000;
        //     $warehouse->cr_date = $priceProduct->cr_date;
        //     $warehouse->type = $priceProduct->type;
            
        //     // Qo'shimcha ustunlarga qiymat berish
        //     $warehouse->created_by = 'admin'; // Misol uchun, admin deb belgilayapmiz
        //     $warehouse->update_by = 'admin';
        //     $warehouse->consignor_id = 1; // Yuk jo'natuvchi ID sini 1 deb belgilayapmiz
        //     $warehouse->all_sum_dollar = 0; // Boshlang'ich qiymat
        //     $warehouse->all_discount_amount = 0; // Boshlang'ich qiymat
        //     $warehouse->all_my_total_debt = 0; // Boshlang'ich qiymat
        //     $warehouse->comment = 'PriceProduct dan kiritildi';

        //     // Warehouse jadvaliga saqlash
        //     $warehouse->save(false);
        // }

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
     * Creates a new Sklad model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sklad();
        
        if ($model->load(Yii::$app->request->post()) ) {
            $post = Yii::$app->request->post();
            
            $importProducts = $post['Sklad']['allValue'];

            // $my_total_debts = $post['Sklad']['my_total_debts'];
            $consignor_id = 31;
            $dates = $post['Sklad']['dates'];
            $exchange_rates = $post['Sklad']['exchange_rates'];
            // $given_sum_dollars = $post['Sklad']['given_sum_dollars'];
            $given_sum_dollars = 0;
            // $sum_dollars = $post['Sklad']['sum_dollars'];
            $sum_dollars = 0;
            // $discount_amounts = $post['Sklad']['discount_amounts'];
            $discount_amounts = 0;
            $comments = $post['Sklad']['comments'];
            
            $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $consignor_id])->one();
        
            $my_total_debts = 0;
            if ($myTotalDebt) {
                $my_total_debts = $myTotalDebt->total_debt;
            }

            // $order_account_statuses = $post['Sklad']['order_account_statuses'];
            
            $consignor = Consignor::find()->where(['id' => $consignor_id])->one();
            
            $sklad = new Sklad();
            $sklad->created_by = Yii::$app->user->identity->id;
            $sklad->comment = $comments;
            $sklad->given_sum_dollar = 0;
            $sklad->sum_dollar = $sum_dollars;
            $sklad->exchange_rate = $exchange_rates;
            $sklad->discount_amount = $discount_amounts;
            $sklad->my_total_debt = 0;
            $sklad->old_my_total_debt = $my_total_debts;
            $sklad->cr_date_time = date('Y-m-d H:i:s');
            $sklad->cr_date = date('Y-m-d',strtotime($dates));
            $sklad->consignor_id = $consignor->id;
            $sklad->status = 1;
            $sklad->actived = 0;
            $sklad->save(false);

            

            $sum_all_product = 0;
            $given_sum_dollars = 0;
            foreach ($importProducts as $value) {
                // echo '<pre>';
                // print_r("value['brand_id'] ". $value['brand_id'] . "<br/>");
                // print_r("value['product_category_id'] ". $value['product_category_id']."<br/>");
                // print_r("value['type'] ". $value['type']."<br/>");
                // print_r("value['size'] ". $value['size']."<br/>");
                // echo '</pre>';

                $warehouse = Warehouse::find()
                                ->andWhere(['brand_id' => (int)$value['brand_id']])
                                ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                                ->andWhere(['type' => $value['type']])
                                ->andWhere(['size' => (float)$value['size']])->one();

                if (!$warehouse) {
                    $relative = new Warehouse();
                    $relative->brand_id = $value['brand_id'];
                    $relative->product_category_id = $value['product_category_id'];
                    $relative->size = $value['size'];
                    $relative->count = 0;
                    $relative->price = $value['price'];
                    $relative->type = $value['type'];

                    $relative->all_my_total_debt = 0;
                    $relative->all_sum_dollar = 0;
                    $relative->all_discount_amount = 0;
                    // $relative->order_account_statuse = $value['order_account_statuses'];

                    $relative->cr_date = date('Y-m-d',strtotime($dates));
                    $relative->save(false);
                    $given_sum_dollars = $given_sum_dollars + (float)$value['price'] * 0;

                    $relativeHistory = new WarehouseHistory();
                    $relativeHistory->sklad_id = $sklad->id;
                    $relativeHistory->brand_id = $value['brand_id'];
                    $relativeHistory->product_category_id = $value['product_category_id'];
                    $relativeHistory->size = $value['size'];
                    $relativeHistory->type = $value['type'];
                    $relativeHistory->price = $value['price'];
                    $relativeHistory->count = 0;
                    $relativeHistory->cr_date = date('Y-m-d H:i:s');
                    $relativeHistory->cr_date_time = date('Y-m-d H:i:s');
                    $relativeHistory->save(false);
                    $error = $relativeHistory->errors;

                    $modelPrices = new Prices();  
                    $modelPrices->warehouse_id = $relative->id;
                    $modelPrices->price = $value['price'];
                    $modelPrices->save();

                }else {
                    
                    $warehouse->all_my_total_debt = 0;
                    $warehouse->all_sum_dollar =0;
                    $warehouse->all_discount_amount = 0;
                    $warehouse->count = $warehouse->count + 0;
                    $warehouse->save(false);
                    $given_sum_dollars = $given_sum_dollars + (float)$value['price'] * 0;

                    $relativeHistory = new WarehouseHistory();
                    $relativeHistory->sklad_id = $sklad->id;
                    $relativeHistory->brand_id = $value['brand_id'];
                    $relativeHistory->product_category_id = $value['product_category_id'];
                    $relativeHistory->size = $value['size'];
                    $relativeHistory->count = 0;
                    $relativeHistory->type = $value['type'];
                    $relativeHistory->price = $value['price'];
                    $relativeHistory->cr_date = date('Y-m-d H:i:s');
                    $relativeHistory->save(false);
                    $error = $relativeHistory->errors;
                    
                    $price = Prices::find()->where(['warehouse_id' => $warehouse->id])->one();
                    if ($price) {
                        $price->warehouse_id = $warehouse->id;
                        $price->price = $value['price'];
                        $price->save(false);
                    }else {
                        $modelPrices = new Prices();  
                        $modelPrices->warehouse_id = $warehouse->id;
                        $modelPrices->price = $value['price'];
                        $modelPrices->save();
                    }
                    
                }
                    
            }
            $sklad->given_sum_dollar = $given_sum_dollars;
            $sklad->my_total_debt = $given_sum_dollars -  ($sum_dollars -  $discount_amounts);
            $sklad->save(false);

            $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $consignor->id])->one();
            if ($myTotalDebt) {
                $myTotalDebt->total_debt = $my_total_debts + ($given_sum_dollars -  $sum_dollars - $discount_amounts);
                $myTotalDebt->update_by = Yii::$app->user->identity->id;
                $myTotalDebt->cr_date = date('Y-m-d H:i:s');
                $myTotalDebt->save(false);
            }else{
                $myTotalDebt = new MyTotalDebt();
                $myTotalDebt->consignor_id = $consignor->id;
                $myTotalDebt->total_debt = $my_total_debts + ($given_sum_dollars -  $sum_dollars - $discount_amounts);
                $myTotalDebt->update_by = Yii::$app->user->identity->id;
                $myTotalDebt->cr_date = date('Y-m-d H:i:s');
                $myTotalDebt->save(false);
            }
            return $this->redirect(['warehouse/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreateProduct()
    {
        $model = new Sklad();
        
        if ($model->load(Yii::$app->request->post()) ) {
            $post = Yii::$app->request->post();
            
            $importProducts = $post['Sklad']['allValue'];
           
            $dates = $post['Sklad']['dates'];
            $exchange_rates = $post['Sklad']['exchange_rates'];
            $given_sum_dollars = 0;
            $sum_dollars = 0;
            $discount_amounts = 0;
            $comments = $post['Sklad']['comments'];
            
            $sklad = new Sklad();
            $sklad->created_by = Yii::$app->user->identity->id;
            $sklad->comment = $comments;
            $sklad->given_sum_dollar = 0;
            $sklad->sum_dollar = $sum_dollars;
            $sklad->exchange_rate = $exchange_rates;
            $sklad->discount_amount = $discount_amounts;
            $sklad->my_total_debt = 0;
            $sklad->cr_date_time = date('Y-m-d H:i:s');
            $sklad->cr_date = date('Y-m-d',strtotime($dates));
            $sklad->status = 1;
            $sklad->actived = 0;
            $sklad->save(false);


            foreach ($importProducts as $value) {
                

                $warehouse = Warehouse::find()
                                ->andWhere(['brand_id' => (int)$value['brand_id']])
                                ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                                ->andWhere(['type' => $value['type']])
                                ->andWhere(['size' => (float)$value['size']])->one();
                
                if (!$warehouse) {
                    $relative = new Warehouse();
                    $relative->brand_id = $value['brand_id'];
                    $relative->product_category_id = $value['product_category_id'];
                    $relative->size = $value['size'];
                    $relative->count = 0;
                    $relative->price = $value['price'];
                    $relative->type = $value['type'];

                    $relative->all_my_total_debt = 0;
                    $relative->all_sum_dollar = 0;
                    $relative->all_discount_amount = 0;

                    $relative->cr_date = date('Y-m-d',strtotime($dates));
                    $relative->save(false);

                    $relativeHistory = new WarehouseHistory();
                    $relativeHistory->sklad_id = $sklad->id;
                    $relativeHistory->brand_id = $value['brand_id'];
                    $relativeHistory->product_category_id = $value['product_category_id'];
                    $relativeHistory->size = $value['size'];
                    $relativeHistory->type = $value['type'];
                    $relativeHistory->price = $value['price'];
                    $relativeHistory->count = 0;
                    $relativeHistory->cr_date = date('Y-m-d H:i:s');
                    $relativeHistory->cr_date_time = date('Y-m-d H:i:s');
                    $relativeHistory->save(false);
                    $error = $relativeHistory->errors;

                    $modelPrices = new Prices();  
                    $modelPrices->warehouse_id = $relative->id;
                    $modelPrices->price = $value['price'];
                    $modelPrices->save();

                }else {
                    $warehouse->price = $value['price'];
                    $warehouse->all_my_total_debt = 0;
                    $warehouse->all_sum_dollar =0;
                    $warehouse->all_discount_amount = 0;
                    $warehouse->count = $warehouse->count + 0;
                    $warehouse->save(false);
                    // echo '<pre>';
                    // print_r($value['price']);
                    // echo '</pre>';

                    $relativeHistory = new WarehouseHistory();
                    $relativeHistory->sklad_id = $sklad->id;
                    $relativeHistory->brand_id = $value['brand_id'];
                    $relativeHistory->product_category_id = $value['product_category_id'];
                    $relativeHistory->size = $value['size'];
                    $relativeHistory->count = 0;
                    $relativeHistory->type = $value['type'];
                    $relativeHistory->price = $value['price'];
                    $relativeHistory->cr_date = date('Y-m-d H:i:s');
                    $relativeHistory->save(false);
                    $error = $relativeHistory->errors;
                    
                    $price = Prices::find()->where(['warehouse_id' => $warehouse->id])->one();
                    if ($price) {
                        $price->warehouse_id = $warehouse->id;
                        $price->price = $value['price'];
                        $price->save(false);
                    }else {
                        $modelPrices = new Prices();  
                        $modelPrices->warehouse_id = $warehouse->id;
                        $modelPrices->price = $value['price'];
                        $modelPrices->save();
                    }
                    
                }
                    
            }
            return $this->redirect(['warehouse/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Sklad model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);  
             

        $consignor_id = $model->consignor_id;
        $my_total_debt_old = $model->my_total_debt;
        $date_old = $model->cr_date;
        $exchange_rate_old = $model->exchange_rate;

        $given_sum_dollar_old = $model->given_sum_dollar;
        $sum_dollar_old = $model->sum_dollar;
        $discount_amount_old = $model->discount_amount;

        $consignor = Consignor::find()->where(['id' => $consignor_id])->one();
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $consignor->id])->one();
        

        if ($model->load(Yii::$app->request->post())) {
            $my_total_debt_new = Yii::$app->request->post('Sklad')['my_total_debt'];
            $datees_new = Yii::$app->request->post('Sklad')['date'];
            $exchange_rate_new = Yii::$app->request->post('Sklad')['exchange_rate'];

            $given_sum_dollar_new = Yii::$app->request->post('Sklad')['given_sum_dollar'];
            $sum_dollar_new = Yii::$app->request->post('Sklad')['sum_dollar'];
            $discount_amount_new = Yii::$app->request->post('Sklad')['discount_amount'];

            // echo '<pre>';
            // print_r("given_sum_dollar_new: ". $given_sum_dollar_new . "<br/>");
            // echo '</pre>';

            $updateReason = Yii::$app->request->post('Sklad')['comments'];
            $allValues_new = Yii::$app->request->post('Sklad')['allValue'];
            $warehouseHistories = WarehouseHistory::find()->where(['sklad_id' => $id])->all();
            foreach ($warehouseHistories as $value) {
                $warehouse = Warehouse::find()->andWhere(['brand_id' => $value->brand_id])
                                    ->andWhere(['product_category_id' => $value->product_category_id])
                                    ->andWhere(['type' => $value->type])
                                    ->andWhere(['size' => $value->size])->one();

                $warehouse->all_my_total_debt = $my_total_debt_new - ($given_sum_dollar_old -  ($sum_dollar_old - $discount_amount_old));
                $warehouse->all_sum_dollar =$warehouse->all_sum_dollar - $sum_dollar_old;
                $warehouse->all_discount_amount = $warehouse->all_discount_amount - $discount_amount_old;
                $warehouse->count = $warehouse->count - $value->count;
                $warehouse->save(false);
                $value->delete();
                if ($myTotalDebt) {
                    $myTotalDebt->total_debt = $my_total_debt_new - ($given_sum_dollar_old -  ($sum_dollar_old - $discount_amount_old));
                    $myTotalDebt->update_by = Yii::$app->user->identity->id;
                    $myTotalDebt->cr_date = date('Y-m-d H:i:s');
                    $myTotalDebt->save(false);
                }
                
            }
            // WarehouseHistory::deleteAll(['sklad_id' => $id]);
            if ($allValues_new) {
                foreach ($allValues_new as $value) {
                    $warehouse = Warehouse::find()
                                ->andWhere(['brand_id' => (int)$value['brand_id']])
                                ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                                ->andWhere(['type' => $value['type']])
                                ->andWhere(['size' => (float)$value['size']])->one();
                    // echo '<pre>';
                    // print_r("warehouse ". $warehouse->id. "<br/>");
                    // echo '</pre>';
                    if (!$warehouse) {
                        $relative = new Warehouse();
                        $relative->brand_id = $value['brand_id'];
                        $relative->product_category_id = $value['product_category_id'];
                        $relative->size = $value['size'];
                        $relative->count = $value['count'];
                        $relative->price = $value['price'];
                        $relative->type = $value['type'];
                        $relative->all_my_total_debt = $my_total_debt_new + ($given_sum_dollar_new -  ($sum_dollar_new - $discount_amount_new));
                        $relative->all_sum_dollar = $sum_dollar_new;
                        $relative->all_discount_amount = $discount_amount_new;
                        $relative->cr_date = date('Y-m-d',strtotime($datees_new));
                        $relative->save(false);
    
                        $relativeHistory = new WarehouseHistory();
                        $relativeHistory->sklad_id = $id;
                        $relativeHistory->brand_id = $value['brand_id'];
                        $relativeHistory->product_category_id = $value['product_category_id'];
                        $relativeHistory->size = $value['size'];
                        $relativeHistory->type = $value['type'];
                        $relativeHistory->price = $value['price'];
                        $relativeHistory->count = $value['count'];
                        $relativeHistory->cr_date = date('Y-m-d H:i:s');
                        $relativeHistory->cr_date_time = date('Y-m-d H:i:s');
                        $relativeHistory->save(false);    
                    }else {
                        $warehouse->all_my_total_debt = $warehouse->all_my_total_debt + ($given_sum_dollar_new -  ($sum_dollar_new - $discount_amount_new));
                        $warehouse->all_sum_dollar =$warehouse->all_sum_dollar + $sum_dollar_new;
                        $warehouse->all_discount_amount = $warehouse->all_discount_amount + $discount_amount_new;
                        $warehouse->count = $warehouse->count + $value['count'];
                        $warehouse->save(false);
                        $relativeHistory = new WarehouseHistory();
                        $relativeHistory->sklad_id = $id;
                        $relativeHistory->brand_id = $value['brand_id'];
                        $relativeHistory->product_category_id = $value['product_category_id'];
                        $relativeHistory->size = $value['size'];
                        $relativeHistory->count = $value['count'];
                        $relativeHistory->type = $value['type'];
                        $relativeHistory->price = $value['price'];
                        $relativeHistory->cr_date = date('Y-m-d H:i:s');
                        $relativeHistory->save(false);
                    }
                }
            }
            if ($myTotalDebt) {
                $myTotalDebt->total_debt = $myTotalDebt->total_debt + ($given_sum_dollar_new -  ($sum_dollar_new - $discount_amount_new));
                $myTotalDebt->update_by = Yii::$app->user->identity->id;
                $myTotalDebt->cr_date = date('Y-m-d H:i:s');
                $myTotalDebt->save(false);
            }   
            $elegantHistoryUpdate = new ElegantHistoryUpdate();
            $elegantHistoryUpdate->title = $consignor->name . " dan olingan mahsulotlar ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada o'zgartirildi...";
            $elegantHistoryUpdate->comment = $updateReason;
            $elegantHistoryUpdate->status = 1;
            $elegantHistoryUpdate->type = 1;
            $elegantHistoryUpdate->save(false);

            // $model->comment = $updateReason;
            $model->given_sum_dollar = $given_sum_dollar_new;
            $model->sum_dollar = $sum_dollar_new;
            $model->exchange_rate = $exchange_rate_new;
            $model->discount_amount = $discount_amount_new;
            $model->my_total_debt = $given_sum_dollar_new -  ($sum_dollar_new -  $discount_amount_new);
            $model->old_my_total_debt = $my_total_debt_new - ($given_sum_dollar_old -  ($sum_dollar_old - $discount_amount_old));
            $model->cr_date_time = date('Y-m-d H:i:s');
            $model->cr_date = date('Y-m-d',strtotime($datees_new));
            $model->consignor_id = $consignor->id;
            $model->status = 1;
            $model->save(false);
            return $this->redirect(['index']);
        }
        return $this->render('update', ['model' => $model, 'my_total_debt' => $myTotalDebt->total_debt]);
        
    }

    /**
     * Delete an existing Sklad model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;

        $model = $this->findModel($id); 
        $warehouseHistories = WarehouseHistory::find()->where(['sklad_id' => $id])->all();
        foreach ($warehouseHistories as $value) {
            $warehouse = Warehouse::find()->andWhere(['brand_id' => $value->brand_id])
                                ->andWhere(['product_category_id' => $value->product_category_id])
                                ->andWhere(['type' => $value->type])
                                ->andWhere(['size' => $value->size])->one();
            $warehouse->count = $warehouse->count - $value->count;
            $warehouse->save(false);
            $value->delete();
        }
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $model->consignor_id])->one();
        $myTotalDebt->total_debt = $myTotalDebt->total_debt - $model->my_total_debt;
        $myTotalDebt->save(false);

        $this->findModel($id)->delete();

        $deleteReason = Yii::$app->request->post('delete_reason');
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $model->consignor0->name . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi import qilingan mahsulotlar o'chirildi...";
        $elegantHistoryUpdate->comment = $deleteReason;
        $elegantHistoryUpdate->status = 2;
        $elegantHistoryUpdate->type = 1;
        $elegantHistoryUpdate->save(false);

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
     * Delete multiple existing Sklad model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
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
     * Finds the Sklad model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sklad the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sklad::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
