<?php

namespace app\controllers;

use Yii;
use app\models\Warehouse;
use app\models\WarehouseSearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\models\Brands;
use app\models\ProductCategory;
use app\models\WarehouseHistory;
use app\models\WarehouseHistoryUpdate;
use app\models\Orders;
use app\models\Sklad;
use app\models\Prices;
use app\models\Consignor;
use app\models\MyTotalDebt;
use app\models\MyTotalDebtHistory;
use app\models\ElegantHistoryUpdate;
use app\models\TypeSklad;
use app\models\BrandsSize;

/**
 * WarehouseController implements the CRUD actions for Warehouse model.
 */
class WarehouseController extends Controller
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
     * Lists all Warehouse models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSizesByCategory()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $brandId    = Yii::$app->request->post('brand_id');
        $categoryId = Yii::$app->request->post('product_category_id');

        if (!$brandId || !$categoryId) {
            return ['sizes' => []];
        }

        $rows = BrandsSize::find()
            ->select(['size', 'type'])
            ->where([
                'brand_id'            => (int)$brandId,
                'product_category_id' => (int)$categoryId,
            ])
            ->orderBy(['size' => SORT_ASC])
            ->asArray()
            ->all();

        $sizes = [];
        foreach ($rows as $row) {
            $sizes[] = [
                'size' => (string)$row['size'],
                'type' => (int)$row['type'],    // 1,2,3,4 ...
            ];
        }

        return ['sizes' => $sizes];
    }



    public function actionAccept(){
        // Requestni chop etish
        
        $request = Yii::$app->request;
        $consignor_id = $request->post('customer_name');
        $my_total_debts = $request->post('jami_qarzi');
        $dates = $request->post('order_date');
        $exchange_rates = $request->post('dollar_kurs');

        $discount_amounts = $request->post('chegirma_summa');
        $sum_dollars = $request->post('summa_dollor');
        $comments = $request->post('comment');
        $tasdiq_check = $request->post('tasdiq_check');
        
        $count = $request->post('count');
        $all_sum = $request->post('all_sum');
        $product_details = $request->post('product_details');
        $importProducts = json_decode($product_details, true);

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
            $brand_list = Brands::find()->where(['name' => $value['marka']])->one();
            $product_category_list = ProductCategory::find()->where(['name' => $value['name']])->andWhere(['<>', 'sup_status', 0])->one();
            $type_sklad_list = TypeSklad::find()->where(['name' => $value['joy']])->one();


            $warehouse = Warehouse::find()
                            ->andWhere(['brand_id' => $brand_list->id])
                            ->andWhere(['product_category_id' => $product_category_list->id])
                            ->andWhere(['type' => $sklad->getTypeNameView($value['tip'])])
                            ->andWhere(['size' => (float)$value['size']])->one();
            
            if (!$warehouse) {
                $relative = new Warehouse();
                $relative->brand_id = $brand_list->id;
                $relative->product_category_id = $product_category_list->id;
                $relative->size = $value['size'];
                $relative->count = $value['count'];
                $relative->price = $value['price'];
                $relative->type = $sklad->getTypeNameView($value['tip']);

                $relative->all_my_total_debt = 0;
                $relative->all_sum_dollar = 0;
                $relative->all_discount_amount = 0;
                // $relative->order_account_statuse = $value['order_account_statuses'];

                $relative->cr_date = date('Y-m-d',strtotime($dates));
                $relative->save(false);
                $given_sum_dollars = $given_sum_dollars + (float)$value['price'] * (float)$value['count'];

                $relativeHistory = new WarehouseHistory();
                $relativeHistory->sklad_id = $sklad->id;
                $relativeHistory->brand_id = $brand_list->id;
                $relativeHistory->product_category_id = $product_category_list->id;
                $relativeHistory->size = $value['size'];
                $relativeHistory->type = $sklad->getTypeNameView($value['tip']);
                $relativeHistory->price = $value['price'];
                $relativeHistory->count = $value['count'];
                $relativeHistory->cr_date = date('Y-m-d H:i:s');
                $relativeHistory->cr_date_time = date('Y-m-d H:i:s');
                $relativeHistory->save(false);
                $error = $relativeHistory->errors;

            }else {
                
                $warehouse->all_my_total_debt = 0;
                $warehouse->all_sum_dollar =0;
                $warehouse->all_discount_amount = 0;
                $warehouse->count = $warehouse->count + $value['count'];
                $warehouse->save(false);
                $given_sum_dollars = $given_sum_dollars + (float)$value['price'] * (float)$value['count'];

                $relativeHistory = new WarehouseHistory();
                $relativeHistory->sklad_id = $sklad->id;
                $relativeHistory->brand_id = $brand_list->id;
                $relativeHistory->product_category_id = $product_category_list->id;
                $relativeHistory->size = $value['size'];
                $relativeHistory->count = $value['count'];
                $relativeHistory->type = $sklad->getTypeNameView($value['tip']);
                $relativeHistory->price = $value['price'];
                $relativeHistory->cr_date = date('Y-m-d H:i:s');
                $relativeHistory->save(false);
                $error = $relativeHistory->errors;
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

        return $this->redirect(['/sklad/index']);
    }

    public function actionQarztul(){
        // Requestni chop etish
        
        $request = Yii::$app->request;
        $clients_id = $request->post('customer_name');
        $qarz_client_summ = $request->post('qarz_client_summ');
        $qarz_tul_date = $request->post('qarz_tul_date');
        $dollar_kurs = $request->post('tul_qarz_dollar_kurs');

        $tul_qarz_sikidka = $request->post('tul_qarz_sikidka');
        $tul_qarz_sum_dollar = $request->post('tul_qarz_sum_dollar');
        $all_tulangan_summa_dollar = round($tul_qarz_sum_dollar + $tul_qarz_sikidka,2);

        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $clients_id])->one();
        $myTotalDebt->total_debt = $myTotalDebt->total_debt - $all_tulangan_summa_dollar;
        $myTotalDebt->save(false);
        
        $myTotalDebtHistory = new MyTotalDebtHistory();  
        $myTotalDebtHistory->my_total_debt_id = $myTotalDebt->id;
        $myTotalDebtHistory->exchange_rate = $dollar_kurs;
        $myTotalDebtHistory->cr_date = $qarz_tul_date;
        $myTotalDebtHistory->all_summ_dollar = $all_tulangan_summa_dollar;
        $myTotalDebtHistory->discount_amount = $tul_qarz_sikidka;
        $myTotalDebtHistory->total_debt = $tul_qarz_sum_dollar;
        $myTotalDebtHistory->save(false);

        return $this->redirect(['/my-total-debt/index']);
    }

    public function actionImportProduct()
    {    
        // $warehouse = Warehouse::find()->select(['brand_id'])->groupBy(['brand_id'])->orderBy(['product_category.sorting' => SORT_ASC])->all();
            
        $warehouse = Warehouse::find()
        ->alias('p')
        ->select(["p.*", "pc.sorting"])
        ->leftJoin("brands pc", "p.brand_id = pc.id")
        ->andWhere(['pc.sup_status' => 1])
        // ->andWhere(['p.type' => [2, 3]])
        ->orderBy(['pc.sorting' => SORT_ASC])
        ->groupBy(['p.brand_id'])->all();
        // echo "<pre>";
        // print_r($warehouse);
        // echo "<pre>";
        // $warehouses = Warehouse::find()->all();
        return $this->render('import_product', ['warehouse' => $warehouse]);
    }

    public function actionQarz(){
        $request = Yii::$app->request;
        $client_id = $request->get('client_id');
        // echo '<pre>';
        // print_r($client_id);
        // // print_r($order_account);
        // echo '</pre>';
        $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $client_id])->one();
        // echo '<pre>';
        // print_r($client_id);
        // // print_r($order_account);
        // echo '</pre>';
        $qarz_sum = 0;
        if ($myTotalDebt) {
            $qarz_sum = $myTotalDebt->total_debt;
        }
        return $qarz_sum;
    }

    public function actionConsignor($id)
    {
        $models = Consignor::find()->where(['id' => $id])->all();
        foreach ($models as $value) {
           echo "<option value = '".$value->id."'>".$value->name."</option>" ;
        }        
    }

    public function actionAllList()
    {    
        $warehouse = Warehouse::find()
        ->alias('w')
        ->select(["w.*", "pc.sorting"])
        ->leftJoin("brands pc", "w.brand_id = pc.id")
        // ->where(['w.type' => [2, 3]])
        ->orderBy(['pc.sorting' => SORT_ASC])
        ->groupBy(['w.brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('all_list', ['warehouse' => $warehouse]);
    }

    public function actionOrders()
    {    
        // $warehouse = Warehouse::find()->select(['brand_id'])->groupBy(['brand_id'])->orderBy(['product_category.sorting' => SORT_ASC])->all();
            
        $warehouse = Warehouse::find()
        ->alias('w')
        ->select(["w.*", "pc.sorting"])
        ->leftJoin("brands pc", "w.brand_id = pc.id")
        ->orderBy(['pc.sorting' => SORT_ASC])
        ->groupBy(['w.brand_id'])->all();
        // echo "<pre>";
        // print_r($warehouse);
        // die;
        // $warehouses = Warehouse::find()->all();
        return $this->render('orders', ['warehouse' => $warehouse]);
    }

    public function actionImport()
    {    
        $warehouse = Warehouse::find()->select(['cr_date'])->groupBy(['cr_date'])->all();
        return $this->render('import', ['warehouse' => $warehouse]);
    }

    public function actionImportView( $cr_date)
    {    
        $warehouse = Warehouse::find()->where(['cr_date' => $cr_date])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('import_view', ['warehouse' => $warehouse, 'cr_date' => $cr_date]);
    }

    public function actionOrdersAjax(){

        $brand = Yii::$app->request->post('brand');

        Yii::$app->response->format = Response::FORMAT_JSON;


        $warehouses = Warehouse::find()->where(['brand_id' => $brand])->all();
        
        $warehouse = $warehouses[0] ?? null;

        $html =  '<tr>
                <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red">' . $warehouse->brand->name . '</b></td>
                <td style="background-color:#a0d9ea;"></td>
            </tr>';

            $i = 1; $allCount = 0;

            foreach ($warehouses as $model1) { 
                $html .= '<tr class="handle" id="' . $warehouse->brand->name . '_' . $i . '">
                    <td>' . $i . '</td>
                    <td>' . $model1->brand->name . '</td>
                    <td>' . $model1->product_category_id ? $model1->productCategory->name : '' . '</td>
                    <td> ' . $model1->size . '</td><td>' . $model1->count . '</td><td style="display:none;">' . $model1->id . ' </td>
                </tr>';
            // $i = $i +1 ; $allCount = $allCount + $model1->count; $allMarkCount = $allMarkCount + $model1->count; 
        } 

        
        return ['html' =>  $html];
    }

    public function actionExport()
    {    
        $warehouse = Warehouse::find()->select(['cr_date'])->groupBy(['cr_date'])->all();
        return $this->render('export', ['warehouse' => $warehouse]);
    }

    public function actionExportView($cr_date)
    {    
        $warehouse = Warehouse::find()->where(['cr_date' => $cr_date])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('export_view', ['warehouse' => $warehouse, 'cr_date' => $cr_date]);
    }

    public function actionBrand($id)
    {
        $datas = Brands::find()->where(['id' => $id])->one();
        $productCategory = ProductCategory::find()->where(['brand_id' => $datas->id])->all();
        foreach ($productCategory as $value) {
            echo "<option value = '".$value->id."'>".$value->name."</option>" ;
        }
    }

    /**
     * Displays a single Warehouse model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Ombor",
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('O\'zgartirish',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Warehouse model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Warehouse();
        
        if ($model->load(Yii::$app->request->post()) ) {
            $post = Yii::$app->request->post();
            $consignor_id = $post['Warehouse']['consignor_id'];
            $contact = $post['Warehouse']['allValue'];
            
            $consignor = Consignor::find()->where(['id' => $consignor_id])->one();
            $sklad = new Sklad();
            $sklad->created_by = Yii::$app->user->identity->id;
            $sklad->cr_date_time = date('Y-m-d H:i:s');
            $sklad->cr_date = date('Y-m-d H:i:s');
            if (!isset($consignor)){                    
                $good = new Consignor();
                $good->name = $consignor_id;
                $good->save();
                $sklad->consignor_id = $good->id;                        
            }else{
                $sklad->consignor_id = $consignor_id;   
            }
            // $sklad->consignor_id = $consignor->id;
            $sklad->save(false);

            foreach ($contact as $value) {
                $warehouse = Warehouse::find()
                                ->andWhere(['brand_id' => (int)$value['brand_id']])
                                ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                                ->andWhere(['size' => (float)$value['size']])->one();
                
                if (!$warehouse) {
                    $relative = new Warehouse();
                    $relative->brand_id = $value['brand_id'];
                    $relative->product_category_id = $value['product_category_id'];
                    $relative->size = $value['size'];
                    $relative->count = $value['count'];
                    $relative->cr_date = date('Y-m-d H:i:s');
                    $relative->save(false);
                    $error = $relative->errors;

                    $relativeHistory = new WarehouseHistory();
                    $relativeHistory->sklad_id = $sklad->id;
                    $relativeHistory->brand_id = $value['brand_id'];
                    $relativeHistory->product_category_id = $value['product_category_id'];
                    $relativeHistory->size = $value['size'];
                    $relativeHistory->count = $value['count'];
                    $relativeHistory->cr_date = date('Y-m-d H:i:s');
                    $relativeHistory->cr_date_time = date('Y-m-d H:i:s');
                    $relativeHistory->save(false);
                    $error = $relativeHistory->errors;

                }else {
                    $warehouse->count = $warehouse->count + $value['count'];
                    $warehouse->save(false);

                    $relativeHistory = new WarehouseHistory();
                    $relativeHistory->sklad_id = $sklad->id;
                    $relativeHistory->brand_id = $value['brand_id'];
                    $relativeHistory->product_category_id = $value['product_category_id'];
                    $relativeHistory->size = $value['size'];
                    $relativeHistory->count = $value['count'];
                    $relativeHistory->cr_date = date('Y-m-d H:i:s');
                    $relativeHistory->save(false);
                    $error = $relativeHistory->errors;
                }
                    
            }
            // $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);   
        $modelOld = $this->findModel($id);    

        if ($model->cr_date != null) {
            $model->cr_date = \Yii::$app->formatter->asDate($model->cr_date, 'php:d.m.Y');
        }

        $updateData = Yii::$app->request->post('Warehouse');
        $updateReason = isset($updateData['comment']) ? $updateData['comment'] : null;
        $updateCount = isset($updateData['count']) ? $updateData['count'] : null;
        $updateworkerprice = isset($updateData['worker_price']) ? $updateData['worker_price'] : 0;
        $updateType = isset($updateData['type']) ? $updateData['type'] : null;
        $statusCount = isset($updateData['status_count']) ? $updateData['status_count'] : null;

        // YANGI QO‘SHILGANLAR: brand, category, size
        $updateBrandId = isset($updateData['brand_id']) ? (int)$updateData['brand_id'] : null;
        $updateCategoryId = isset($updateData['product_category_id']) ? (int)$updateData['product_category_id'] : null;
        $updateSize = isset($updateData['size']) ? (float)$updateData['size'] : null;

        $countChange = "";
        
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isGet) {
                return [
                    'title'=> '<div style="text-align:center"><b style="font-size:18px;color:red;text-align:center">'
                            . $model->brand->name.
                            '</b> <b style="font-size:16px;;text-align:center">nomli modelni o\'zgartirmoqchimisiz ?</b></div>',
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            } elseif ($updateReason) {

                // *** MUHIM QISM: formdan kelgan brand/category/size ni modelga yozamiz ***
                if ($updateBrandId) {
                    $model->brand_id = $updateBrandId;
                }
                if ($updateCategoryId) {
                    $model->product_category_id = $updateCategoryId;
                }
                if ($updateSize !== null) {
                    $model->size = $updateSize;
                }
                // agar type ni ham o‘zgartirmoqchi bo‘lsangiz (enabled qilsangiz):
                if ($updateType !== null) {
                    $model->type = (int)$updateType;
                }

                $model->count = $updateCount;
                $model->worker_price = $updateworkerprice;
                $model->status_count = $statusCount;
                $model->update_by = Yii::$app->user->identity->id;
                $model->save(false);

                // Tarixga eski ma’lumotlarni yozish
                $relativeHistory = new WarehouseHistoryUpdate();
                $relativeHistory->brand_id = $modelOld->brand_id;
                $relativeHistory->product_category_id = $modelOld->product_category_id;
                $relativeHistory->size = $modelOld->size;
                $relativeHistory->type = $modelOld->type;
                $relativeHistory->count = $modelOld->count;
                $relativeHistory->count_update = $model->count;
                $relativeHistory->created_by = $modelOld->created_by;
                $relativeHistory->update_by = Yii::$app->user->identity->id;
                $relativeHistory->cr_date = date('Y-m-d H:i:s');
                $relativeHistory->save(false);

                if ($modelOld->count != $updateCount) {
                    $countChange = $model->brand->name.' modelli mahsulot soni '
                                . $modelOld->count .' tadan ' . $updateCount. ' taga o\'zgardi.';
                }

                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->brand->name
                    . " nomli modelning ma'lumotlari "
                    . \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ')
                    . " sanada o'zgartirildi. ". $countChange;
                $elegantHistoryUpdate->comment = $updateReason;
                $elegantHistoryUpdate->status = 1;
                $elegantHistoryUpdate->type = 1;
                $elegantHistoryUpdate->save(false);

                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];    
            } else {
                return [
                    'title'=> '<div style="text-align:center"><b style="font-size:18px;color:red;text-align:center">'
                            . $model->brand->name.
                            '</b> <b style="font-size:16px;;text-align:center">nomli modelni o\'zgartirmoqchimisiz ?</b> </div><br/> <p style="font-size:14px;color:red;text-align:center">Izohni to\'ldiring...</p>',
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        } else {
            // non-ajax holat – bu yerda eski kodingiz yetarli
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }


    public function actionOneDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);   
        // $updateReason = Yii::$app->request->post('Warehouse')['comment'];
        $postData = Yii::$app->request->post('Warehouse');
        $updateReason = isset($postData['comment']) ? $postData['comment'] : "No Data";
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> 'Haqiqatdan ham <b style="font-size:18px;color:red">'.$model->brand->name.'</b> modelli mahsulotni oʻchirib tashlamoqchimisiz?',
                    'content'=>$this->renderAjax('delete_form', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('O\'chirish',['class'=>'btn btn-danger','type'=>"submit"])
                ];         
            }else if($updateReason){
                $allPrices = Prices::find()->where(['warehouse_id' => $id])->all();
                foreach ($allPrices as $price) {
                    $price->delete();
                } 
                
                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->brand->name . " modeldagi mahsulotlar ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada o'chirildi...";
                $elegantHistoryUpdate->comment = $updateReason;
                $elegantHistoryUpdate->status = 2;
                $elegantHistoryUpdate->type = 1;
                $elegantHistoryUpdate->save(false);
                $this->findModel($id)->delete();
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];    
            }else{
                 return [
                    'title'=> 'Haqiqatdan ham <b style="font-size:18px;color:red">'.$model->brand->name.'</b> modelli mahsulotni oʻchirib tashlamoqchimisiz?<br/> <p style="font-size:14px;color:red;text-align:center">Izohni to\'ldiring...</p>',
                    'content'=>$this->renderAjax('delete_form', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('O\'chirish',['class'=>'btn btn-danger','type'=>"submit"])
                ];        
            }
        }else{
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('delete_form', [
                    'model' => $model,
                ]);
            }
        }
    }
    /**
     * Delete an existing Warehouse model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete2($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        // $allPrices = Prices::find()->where(['warehouse_id' => $id])->all();
        // foreach ($allPrices as $price) {
        //     $price->delete();
        // } 
        

        // $deleteReason = Yii::$app->request->post('delete_reason');
        // $elegantHistoryUpdate = new ElegantHistoryUpdate();
        // $elegantHistoryUpdate->title = $model->brand->name . " nomliu marka ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada o'chirildi...";
        // $elegantHistoryUpdate->comment = $deleteReason;
        // $elegantHistoryUpdate->status = 2;
        // $elegantHistoryUpdate->type = 1;
        // $elegantHistoryUpdate->save(false);
        $this->findModel($id)->delete();
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
     * Delete multiple existing Warehouse model.
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
     * Finds the Warehouse model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Warehouse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Warehouse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
