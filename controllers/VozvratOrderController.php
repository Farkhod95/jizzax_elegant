<?php

namespace app\controllers;

use Yii;
use app\models\VozvratOrder;
use app\models\VozvratOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\Brands;
use app\models\ProductCategory;
use app\models\Client;
use app\models\ProductAccount;
use app\models\OrderAccountHistory;
use app\models\PriceProduct;
use app\models\ProductAccountHistory;
use app\models\DebtRepayment;
use app\models\Warehouse;
use app\models\TypeSklad;
use app\models\ElegantHistoryUpdate;
use app\models\Prices;
use app\models\KeshbekHistory;
use app\models\ExchangeRate;
use app\models\OrderAccount;
/**
 * VozvratOrderController implements the CRUD actions for VozvratOrder model.
 */
class VozvratOrderController extends Controller
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
     * Lists all VozvratOrder models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new VozvratOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single VozvratOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "VozvratOrder #".$id,
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
        $mpdf->Output("VozvratOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }


    public function actionPrintForClient($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlTextForClient($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("VozvratOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }

    public function actionProducts($id)
    {    
        $warehouse = ProductAccountHistory::find()->where(['vozvrat_order_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('products', ['warehouse' => $warehouse, 'order_id' => $id]);
    }

    public function actionVozvrat()
    {    
        // $warehouse = Warehouse::find()->select(['brand_id'])->groupBy(['brand_id'])->orderBy(['product_category.sorting' => SORT_ASC])->all();
            
        $warehouse = Warehouse::find()
        ->alias('p')
        ->select(["p.*", "pc.sorting"])
        ->leftJoin("brands pc", "p.brand_id = pc.id")
        ->where(['pc.sup_status' => 1])
        ->orderBy(['pc.sorting' => SORT_ASC])
        ->groupBy(['p.brand_id'])->all();
        // echo "<pre>";
        // print_r($warehouse);
        // echo "<pre>";
        // $warehouses = Warehouse::find()->all();
        return $this->render('vozvrat', ['warehouse' => $warehouse]);
    }


    public function actionAccept()
    {
        // Requestni chop etish
        
        $request = Yii::$app->request;
        $client_id = $request->post('customer_name');
        $total_debts = $request->post('jami_qarzi');
        $dates = $request->post('order_date');
        $all_summ_dollar = $request->post('all_summ_dollar');
        $summ_dollar = $request->post('summ_dollar');
        $sum_soms = $request->post('summa_som');
        $sum_carts = $request->post('summa_karta');
        $comment = $request->post('comment');
        $tasdiq_check = $request->post('tasdiq_check');
        
        $count = $request->post('count');
        $total_product_sum = $request->post('total_product_sum');
        $product_details = $request->post('product_details');
        $contact = json_decode($product_details, true);
        
        $client = Client::findOne((int)$client_id);
        if ($client === null) {
            Yii::$app->session->setFlash('error', 'Siz kiritgan mijoz topilmadi.');
            return $this->redirect(['index']);
        }
        $exchangeRate = ExchangeRate::find()->where(['id' => 1])->one();
        $exchange_rates = $exchangeRate->dollar;

        $orderAccount = OrderAccount::find()->where(['client_id' => $client_id])->one();
        if ($orderAccount === null) {
            $orderAccount = new OrderAccount();
            $orderAccount->client_id = $client_id;
            $orderAccount->last_order_date = date('Y-m-d',strtotime($dates));
            $orderAccount->total_debt = 0;
            $orderAccount->date = date('Y-m-d',strtotime($dates));
            $orderAccount->exchange_rate = $exchange_rates;
            $orderAccount->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
            $orderAccount->save(false);
        }
         
        $vozvratOrder = new VozvratOrder();
        $vozvratOrder->client_id = $client_id;
        $vozvratOrder->date = date('Y-m-d',strtotime($dates));
        $vozvratOrder->exchange_rate = $exchange_rates;
        $vozvratOrder->product_summ_dollar = $total_product_sum;
        $vozvratOrder->all_summ_dollar = $all_summ_dollar;
        $vozvratOrder->old_total_debt = $total_debts;
        $vozvratOrder->sum_dollar = $summ_dollar;
        $vozvratOrder->sum_som = $sum_soms;
        $vozvratOrder->sum_cart = $sum_carts;
        $vozvratOrder->confirmation = $tasdiq_check;
        $vozvratOrder->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
        $vozvratOrder->save(false);
        $vozvratOrder_id = $vozvratOrder->id;

        $all_profit = 0;
        $all_summ   = 0;

        // YANGI: hech bo‘lmasa bitta mahsulot narxi ombordagidan katta/kiyinchalik flag
        $hasLargePrice = false;

        foreach ($contact as $value) {
            $brand_list = Brands::find()->where(['id' => $value['brand_id']])->one();
            $product_category_list = ProductCategory::find()->where(['id' => $value['product_category_id']])->one();
            $type_sklad_list = TypeSklad::find()->where(['name' => $value['joy']])->one();

            $typeId = $vozvratOrder->getTypeNameView($value['tip']);
            $size   = (float)$value['size'];
            $price  = (float)$value['price'];
            $count  = (int)$value['count'];

            $productAccount = ProductAccount::find()
                ->andWhere(['vozvrat_order_id' => $vozvratOrder_id])
                ->andWhere(['brand_id' => (int)$brand_list->id])
                ->andWhere(['product_category_id' => (int)$product_category_list->id])
                ->andWhere(['type' => $typeId])
                ->andWhere(['type_sklad_id' => (int)$type_sklad_list->id])
                ->andWhere(['size' => $size])
                ->one();

            $warehouseValue = Warehouse::find()
                ->andWhere(['product_category_id' => (int)$product_category_list->id])
                ->andWhere(['brand_id' => (int)$brand_list->id])
                ->andWhere(['size' => $size])
                ->andWhere(['type' => $typeId])
                ->one();

            // Ombordagi haqiqiy narx (Prices jadvalidan)
            $price_real = 0;
            if ($warehouseValue) {
                $real_prices_product = Prices::find()->where(['warehouse_id' => $warehouseValue->id])->one();
                if ($real_prices_product) {
                    $price_real = (float)$real_prices_product->price;
                }

                // flag – foydalanuvchi narxi ombor narxidan kichik/katta bo‘lsa
                if ($price_real > 0 && $price < $price_real) {
                // if ($price < $price_real) {
                    $hasLargePrice = true;
                }
            }

            // 🔥 Har bir qatordagi summa va foyda
            $lineAmount = $price * $count;
            $lineProfit = $price_real > 0 ? ($price - $price_real) * $count : 0;

            if (!$productAccount) {
                $relative = new ProductAccount();
                $relative->vozvrat_order_id         = $vozvratOrder_id;
                $relative->brand_id                 = $brand_list->id;
                $relative->product_category_id      = $product_category_list->id;
                $relative->size                     = $size;
                $relative->count                    = $count;
                $relative->type                     = $typeId;
                $relative->price                    = $price;
                $relative->real_price               = $price_real;
                $relative->type_sklad_id            = $type_sklad_list->id;
                $relative->profit                   = round($lineProfit, 2);
                $relative->cr_date                  = date('Y-m-d', strtotime($dates));
                $relative->is_debtor                = $hasLargePrice ? 1 : 0;
                $relative->warehouse_id             = $warehouseValue->id;
                $relative->save(false);
            } else {
                $productAccount->count      = $productAccount->count + $count;
                $productAccount->price      = $price;
                $productAccount->real_price = $price_real;
                $productAccount->is_debtor                = $hasLargePrice ? 1 : 0;
                $productAccount->profit     = round($productAccount->profit + $lineProfit, 2);
                $productAccount->warehouse_id             = $warehouseValue->id;
                $productAccount->save(false);
            }

            // History yozuvi
            $relativeHistory = new ProductAccountHistory();
            $relativeHistory->vozvrat_order_id         = $vozvratOrder_id;
            $relativeHistory->brand_id                 = $brand_list->id;
            $relativeHistory->product_category_id      = $product_category_list->id;
            $relativeHistory->size                     = $size;
            $relativeHistory->count                    = $count;
            $relativeHistory->given_count              = $count;
            $relativeHistory->type                     = $typeId;
            $relativeHistory->price                    = $price;
            $relativeHistory->real_price               = $price_real;
            $relativeHistory->is_debtor                = $hasLargePrice ? 1 : 0;
            $relativeHistory->type_sklad_id            = $type_sklad_list->id;
            $relativeHistory->profit                   = round($lineProfit, 2);
            $relativeHistory->cr_date                  = date('Y-m-d', strtotime($dates));
            $relativeHistory->warehouse_id             = $warehouseValue->id;
            $relativeHistory->save(false);

            // Ombor soni
            if ($warehouseValue && $type_sklad_list->id == 1) {
                $warehouseValue->count = $warehouseValue->count + $count;
                $warehouseValue->save(false);
            }

            // Umumiy summalar
            $all_profit += $lineProfit;
            $all_summ   += $lineAmount;
        }

        $vozvratOrders = VozvratOrder::find()->where(['id' => $vozvratOrder_id])->one();
        $vozvratOrders->comment = $comment;
        $vozvratOrders->large_price = $hasLargePrice ? 1 : 0;
        $vozvratOrders->save();

        if ($tasdiq_check == 1) {
            if ($all_summ_dollar != $total_product_sum) {
                $summ = $total_product_sum - $all_summ_dollar;
                $orderAccount->total_debt_old = $total_debts;
                $orderAccount->total_debt = round($total_debts - $summ, 2);
                $orderAccount->save();
            }
           

            $vozvratOrders = VozvratOrder::find()->where(['id' => $vozvratOrder_id])->one();
            $vozvratOrders->total_debt = round($total_debts - $all_summ, 2);
            $vozvratOrders->all_summ_dollar = $all_summ_dollar;
            $vozvratOrders->save();
            
        }
        return $this->redirect(['/vozvrat-order/index']);
    }

    /**
     * Creates a new VozvratOrder model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new VozvratOrder();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new VozvratOrder",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new VozvratOrder",
                    'content'=>'<span class="text-success">Create VozvratOrder success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new VozvratOrder",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing VozvratOrder model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id); 
        $orderAccount = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $client_total_debt = $orderAccount->total_debt;

        $client_id = $model->client_id;
        $date_old = $model->date;

        $exchange_rate_old = $model->exchange_rate;
        $sum_dollar_old = $model->all_summ_dollar;
        $discount_amounts_old = $model->discount_amount;
        
        $total_debt_old = $model->old_total_debt;

        $all_product_sum_old = $model->product_summ_dollar;
        $all_product_qolgan_sum_old = $all_product_sum_old;
        $client = Client::find()->where(['id' => $client_id])->one();

        if ($model->load(Yii::$app->request->post())) {
            $updateReason = Yii::$app->request->post('VozvratOrder')['comments'];
            $total_debts_new = Yii::$app->request->post('VozvratOrder')['old_total_debt']?? 0;
            $dates_new = Yii::$app->request->post('VozvratOrder')['date'];
            $exchange_rates_new = Yii::$app->request->post('VozvratOrder')['exchange_rate']?? 0;
            $sum_dollars_new = Yii::$app->request->post('VozvratOrder')['all_summ_dollar']?? 0;
            $tasdiq_check_new = isset(Yii::$app->request->post('VozvratOrder')['confirmation']) ? Yii::$app->request->post('VozvratOrder')['confirmation'] : 1;

            $productAccountHistory = ProductAccountHistory::find()->where(['vozvrat_order_id' => $id])->all();
            foreach ($productAccountHistory as $value) {
                $warehouseValue = Warehouse::find()->where(['id' => $value->warehouse_id])->one();
                if ($warehouseValue) {
                    if ($value->type_sklad_id == 1) {
                        $warehouseValue->count += $value->count;
                        $warehouseValue->save(false);                          
                    }
                }
                ProductAccountHistory::find()->where(['id' => $value['id']])->one()->delete();
            }
            
            $exchange_rates_new = is_numeric($exchange_rates_new) && $exchange_rates_new != 0 ? $exchange_rates_new : 1; // Avoid division by zero
            $sum_dollars_new = is_numeric($sum_dollars_new) ? $sum_dollars_new : 0;
            $all_pay_summ_new = round($sum_dollars_new, 2);
            
            // VozvratOrder malumotlarini yangilash 
            
            $model->client_id = $client_id;
            $model->date = date('Y-m-d',strtotime($dates_new));
            $model->exchange_rate = $exchange_rates_new;
            $model->all_summ_dollar = $all_pay_summ_new;

            $model->sum_dollar = $sum_dollars_new;
            $model->update_status = 2; // update_status= 2 bo'lsa o'zgarish bo'lgan lekin tasdiqlanmagan bo'ladi
            $model->save(false);
            
            $all_profit_new = 0;
            $all_profit_array_new = [];
            $all_product_summ_new = 0;
            $hasLargePriceNew = false;

            $allValues_new = Yii::$app->request->post('VozvratOrder')['allValue'] ?? null;

            if ($allValues_new) {
                foreach ($allValues_new as $value) {
                    $value_price = (float)$value['price'];
                    $value_count = (int)$value['count'];
                    $value_size  = (float)$value['size'];
                    $value_type  = (int)$value['type'];

                    $brand_list = Brands::find()->where(['id' => $value['brand_id']])->one();
                    $product_category_list = ProductCategory::find()
                        ->where(['id' => $value['product_category_id']])
                        ->andWhere(['<>', 'sup_status', 0])
                        ->one();
                    $type_sklad_list = TypeSklad::find()->where(['id' => $value['type_sklad_id']])->one();

                    $productAccount = ProductAccount::find()
                        ->andWhere(['order_account_id' => $orderAccount->id])
                        ->andWhere(['brand_id' => (int)$brand_list->id])
                        ->andWhere(['product_category_id' => (int)$product_category_list->id])
                        ->andWhere(['type' => $value_type])
                        ->andWhere(['type_sklad_id' => (int)$type_sklad_list->id])
                        ->andWhere(['size' => $value_size])
                        ->one();

                    $warehouseValue = Warehouse::find()
                        ->andWhere(['product_category_id' => (int)$product_category_list->id])
                        ->andWhere(['brand_id' => (int)$brand_list->id])
                        ->andWhere(['size' => $value_size])
                        ->andWhere(['type' => $value_type])
                        ->one();

                    // Ombordagi haqiqiy narx (Prices jadvalidan)
                    $price_real = 0;
                    if ($warehouseValue) {
                        $real_prices_product = Prices::find()->where(['warehouse_id' => $warehouseValue->id])->one();
                        if ($real_prices_product) {
                            $price_real = (float)$real_prices_product->price;
                        }
                    }

                    // Narx nisbatini belgilash (istaganizcha shart)
                    if ($price_real > 0 && $value_price < $price_real) {
                    // if ($value_price < $price_real) {
                        $hasLargePriceNew = true;
                    }

                    // 🔥 Har bir qatordagi foyda
                    $lineProfit = $price_real > 0
                        ? ($value_price - $price_real) * $value_count
                        : 0;

                    // Yangi ProductAccount
                    if (!$productAccount) {
                        $relative = new ProductAccount();
                        $relative->order_account_id         = $orderAccount->id;
                        $relative->vozvrat_order_id = $model->id;
                        $relative->brand_id                 = $brand_list->id;
                        $relative->product_category_id      = $product_category_list->id;
                        $relative->size                     = $value_size;
                        $relative->count                    = $value_count;
                        $relative->type                     = $value_type;
                        $relative->price                    = $value_price;
                        $relative->real_price               = $price_real;
                        $relative->type_sklad_id            = $type_sklad_list->id;
                        $relative->is_debtor                = $hasLargePriceNew ? 1 : 0;
                        $relative->profit                   = round($lineProfit, 2);
                        $relative->cr_date                  = date('Y-m-d', strtotime($dates_new));
                        $relative->warehouse_id             = $warehouseValue->id;
                        $relative->save(false);
                    } else {
                        // Mavjud yozuvga yangi miqdor va foyda qo‘shamiz
                        $productAccount->count      = $productAccount->count + $value_count;
                        $productAccount->price      = $value_price;
                        $productAccount->real_price = $price_real;
                        $productAccount->is_debtor  = $hasLargePriceNew ? 1 : 0;
                        $productAccount->profit     = round($productAccount->profit + $lineProfit, 2);
                        $productAccount->save(false);
                    }

                    // History yozuvi
                    $relativeHistory = new ProductAccountHistory();
                    $relativeHistory->order_account_id         = $orderAccount->id;
                    $relativeHistory->vozvrat_order_id = $model->id;
                    $relativeHistory->brand_id                 = $brand_list->id;
                    $relativeHistory->product_category_id      = $product_category_list->id;
                    $relativeHistory->size                     = $value_size;
                    $relativeHistory->count                    = $value_count;
                    $relativeHistory->given_count              = $value_count;
                    $relativeHistory->type                     = $value_type;
                    $relativeHistory->price                    = $value_price;
                    $relativeHistory->real_price               = $price_real;
                    $relativeHistory->type_sklad_id            = $type_sklad_list->id;
                    $relativeHistory->profit                   = round($lineProfit, 2);
                    $relativeHistory->is_debtor                = $hasLargePriceNew ? 1 : 0;
                    $relativeHistory->cr_date                  = date('Y-m-d', strtotime($dates_new));
                    $relativeHistory->save(false);

                    // Ombor sonini kamaytirish
                    if ($warehouseValue && $type_sklad_list->id == 1) {
                        $warehouseValue->count = $warehouseValue->count - $value_count;
                        $warehouseValue->save(false);
                    }

                    // Umumiy summalarni yig‘ish
                    $all_profit_new         += $lineProfit;
                    $all_profit_array_new[]  = $lineProfit;
                    $all_product_summ_new   += $value_price * $value_count;
                }
            }

            // YANGI: large_prise flagini yozish (1/0)
            $model->large_price = $hasLargePriceNew ? 1 : 0;

            $model->save(false);
            if ($model->confirmation == 1) {
                 // Eski ma'lumotlarni tozalash
                 if ($orderAccount->total_debt !=0) {
                    $orderAccount->total_debt = $client_total_debt - ($all_product_sum_old - $all_pay_summ_old);
                    $orderAccount->total_debt_old = $client_total_debt - ($all_product_sum_old - $all_pay_summ_old);
                    $orderAccount->save(false);
                 }
                
                // Yangi ma'lumotlarni qo'shish
                $orderAccount->last_order_date = date('Y-m-d',strtotime($dates_new));
                $orderAccount->exchange_rate = $exchange_rates_new;
                $orderAccount->number_of_orders = $orderAccount->number_of_orders + 1;
                $orderAccount->all_summ_dollar = round($orderAccount->all_summ_dollar + $all_pay_summ_new, 2);
                $orderAccount->sum_dollar = $orderAccount->sum_dollar + (float)$sum_dollars_new;
           
                $orderAccount->all_product_sum = round($orderAccount->all_product_sum + $all_product_summ_new, 2);
                $orderAccount->total_debt_old =$client_total_debt - ($all_product_sum_old - $all_pay_summ_old);
                $orderAccount->total_debt = $orderAccount->total_debt + ($all_product_summ_new - $all_pay_summ_new );
                $orderAccount->save(false);
       
                
                $model->total_debt_today = round(($all_product_summ_new - $all_pay_summ_new ), 2);
                $model->total_debt = $total_debt_old + ($all_product_summ_new - ($model->all_summ_dollar + $model->discount_amount));
                $model->total_debt_old = $total_debt_old;
                $model->all_product_sum = $all_product_summ_new;
                $model->confirmation = $tasdiq_check_new;
                $model->save(false);

                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->client->fio . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi buyurtmasi o'zgartirildi...";
                $elegantHistoryUpdate->comment = $updateReason . ' <br><b style="color:#e97171">' . 'Ostatka: '. $model->total_debt_old .'$, '. 'Mahsulot summasi: '. ($all_product_summ_new).'$, '. 'Qaytarilgan summa : '. $model->all_summ_dollar .'$, '. 'Qolgan qarz: '. $model->total_debt.'$ </b>';
                $elegantHistoryUpdate->status = 1;
                $elegantHistoryUpdate->type = 2;
                $elegantHistoryUpdate->vozvrat_order_id = $model->id;
                $elegantHistoryUpdate->save(false);

            }else{
                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->client->fio . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi vozvrat buyurtmasi o'zgartirildi...";
                $elegantHistoryUpdate->comment = $updateReason;
                $elegantHistoryUpdate->status = 1;
                $elegantHistoryUpdate->type = 2;
                $elegantHistoryUpdate->vozvrat_order_id = $model->id;
                $elegantHistoryUpdate->save(false);
            }
            Yii::$app->session->setFlash('success', 'Ma\'lumotlar muvaffaqiyatli yangilandi.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model, 'client_total_debt' => $model->old_total_debt ? $model->old_total_debt:0,  ]);
    }

    /**
     * Delete an existing VozvratOrder model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id); 
        if ($model->all_summ_dollar != $model->product_summ_dollar) {
            $summ = $model->product_summ_dollar - $model->all_summ_dollar;
            $orderAccount = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
            $orderAccount->total_debt = round($orderAccount->total_debt + $summ, 2);
            $orderAccount->save();
        }

        $productAccountHistory = ProductAccountHistory::find()->where(['vozvrat_order_id' => $id])->all();
        foreach ($productAccountHistory as $value) {
            ProductAccountHistory::find()->where(['id' => $value['id']])->one()->delete();
        }
        $productAccount = ProductAccount::find()->where(['vozvrat_order_id' => $id])->all();
        foreach ($productAccount as $value) {
            // echo "<pre>";
            // print_r($value['count']);
            // echo "<pre>";
            $warehouseValue = Warehouse::find()->andWhere(['id' => (int)$value->warehouse_id])->one();
            $warehouseValue->count = $warehouseValue->count - $value['count'];
            $warehouseValue->save(false);
            ProductAccount::find()->where(['id' => $value['id']])->one()->delete();
        } 

        $deleteReason = Yii::$app->request->post('delete_reason');
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $model->client->fio . " ning Vozvrat qilgan buyurtmasi o'chirildi...";
        $elegantHistoryUpdate->comment = $deleteReason; #"Vozvrat qilingan buyurtma o'chirildi. (product_summ_dollar: " . $model->product_summ_dollar .", comment:". $model->comment .")";
        $elegantHistoryUpdate->status = 2;
        $elegantHistoryUpdate->type = 2;
        $elegantHistoryUpdate->save(false);
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
     * Delete multiple existing VozvratOrder model.
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
     * Finds the VozvratOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VozvratOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VozvratOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
