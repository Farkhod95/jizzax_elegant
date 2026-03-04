<?php

namespace app\controllers;

use Yii;
use app\models\OrderAccount;
use app\models\OrderAccountSearch;
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
/**
 * OrderAccountController implements the CRUD actions for OrderAccount model.
 */
class OrderAccountController extends Controller
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
     * Lists all OrderAccount models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new OrderAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionQarzs($id){
        $request = Yii::$app->request;
        // $client_id = $request->get('client_id');
        $order_account = OrderAccount::find()->where(['client_id' => $id])->one();
        // echo '<pre>';
        // print_r($client_id);
        // // print_r($order_account);
        // echo '</pre>';
        $qarz_sum = 0;
        if ($order_account) {
            $qarz_sum = $order_account->total_debt;
        }
        return $qarz_sum;
    }
    public function actionQarz(){
        $request = Yii::$app->request;
        $client_id = $request->get('client_id');
        $order_account = OrderAccount::find()->where(['client_id' => $client_id])->one();
        // echo '<pre>';
        // print_r($client_id);
        // // print_r($order_account);
        // echo '</pre>';
        $qarz_sum = 0;
        if ($order_account) {
            $qarz_sum = $order_account->total_debt;
        }
        return $qarz_sum;
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
        $tul_qarz_sum_som = $request->post('tul_qarz_sum_som');
        $tul_qarz_summ_cart = $request->post('tul_qarz_summ_cart');
        $tul_qarz_sum_transfer = $request->post('tul_qarz_sum_transfer');
        $tul_qarz_zdacha_dollar = $request->post('tul_qarz_zdacha_dollar');
        $tul_qarz_zdacha_sum = $request->post('tul_qarz_zdacha_sum');

        $all_tulangan_summa_dollar = round($tul_qarz_sum_dollar + $tul_qarz_sikidka,2);
        // echo '<pre>';
        // print_r($all_tulangan_summa_dollar);
        // echo '</pre>';
        $client = Client::find()->where(['id' => $clients_id])->one();
        $exchangeRate = ExchangeRate::find()->where(['id' => 1])->one();
        $dollar_kurs =  $exchangeRate->dollar;
      
        // $orderAccountHistory = OrderAccountHistory::find()->where(['client_id' => $clients_id])->all();
        $orderAccountHistory = OrderAccountHistory::find()
            ->where(['client_id' => $clients_id])
            ->andWhere(['or',
                ['!=', 'is_worker', 1],
                ['is', 'is_worker', null]
            ])
            ->all();

        foreach ($orderAccountHistory as $value) {
            $value->is_debt = 1;
            $value->save(false);
        }
        $orderAccount = OrderAccount::find()->where(['client_id' => $clients_id])->one();
        
        $model = new DebtRepayment();  
        $model->client_id = $clients_id;
        $model->order_account_id = $orderAccount->id;
        $model->date = $qarz_tul_date;
        $model->total_debt_old = $qarz_client_summ;
        $model->exchange_rate = $dollar_kurs;

        $model->discount_amount = $tul_qarz_sikidka;

        $model->summ_dollar = $tul_qarz_sum_dollar;
        $model->sum_som = $tul_qarz_sum_som;
        $model->summ_cart = $tul_qarz_summ_cart;
        $model->sum_transfers = $tul_qarz_sum_transfer;
        $model->zdacha_dollar = $tul_qarz_zdacha_dollar;
        $model->zdacha_sum = $tul_qarz_zdacha_sum;

        $model->all_summ_dollar = $all_tulangan_summa_dollar;

        $orderAccount->total_debt = $orderAccount->total_debt - $all_tulangan_summa_dollar;
        $orderAccount->date_last_debt_payment = $qarz_tul_date;
        $orderAccount->save();

        $tul_qarz_sikidkatext = $tul_qarz_sikidka !=0? (", ".$tul_qarz_sikidka." $ chegirma qilib berildi"): "";
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $client->fio . " ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada qarz to'ladi...";
        $elegantHistoryUpdate->comment = $all_tulangan_summa_dollar. " $ qarz to'ladi". $tul_qarz_sikidkatext;
        $elegantHistoryUpdate->status = 3;
        $elegantHistoryUpdate->type = 2;
        $elegantHistoryUpdate->save(false);

        $model->total_debt = $orderAccount->total_debt;
        $model->save(false);
        
        return $this->redirect(['/debt-repayment/index']);
    }

    public function actionAccept()
    {
        // Requestni chop etish
        
        $request = Yii::$app->request;
        $clients_id = $request->post('customer_name');
        $total_debts = $request->post('jami_qarzi');
        $dates = $request->post('order_date');
        $exchange_rates = $request->post('dollar_kurs');

        $discount_amounts = $request->post('chegirma_summa');
        $sum_dollars = $request->post('summa_dollor');
        $dollar_sumda = $request->post('dollar_sumda');
        $sum_soms = $request->post('summa_som');
        $sum_carts = $request->post('summa_karta');
        $sum_transferss = $request->post('summa_transfer');
        $zdacha_dollar = $request->post('zdacha_dollar');
        $zdacha_sum = $request->post('zdacha_sum');
        $comment = $request->post('comment');
        $driver_info = $request->post('driver_info');
        $fastOrder = $request->post('fast_order');
        // echo '<pre>';
        // print_r($fastOrder);
        // echo '</pre>';
        $tasdiq_check = $request->post('tasdiq_check');
        
        $count = $request->post('count');
        $all_sum = $request->post('all_sum');
        $product_details = $request->post('product_details');
        $contact = json_decode($product_details, true);

        $all_pay_summ = round($sum_dollars, 2);
        // $contact = $post['OrderAccount']['allValue'];
        
        $client = Client::find()->where(['id' => $clients_id])->one();
        if (!isset($client)){                    
            $client_cr = new Client();
            $client_cr->fio = $clients_id;
            $client_cr->save();
            $client_id = $client_cr->id;                        
        }else{
            $client_id = $client->id;   
        }
        // echo '<pre>';
        // print_r(date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s'))));
        // echo '</pre>';
        $orderAccount = OrderAccount::find()->where(['client_id' => $client_id])->one();
            
        if ($orderAccount) {
            // $orderAccount->total_debt = $total_debts;
            $orderAccount->last_order_date = date('Y-m-d',strtotime($dates));
            $orderAccount->exchange_rate = $exchange_rates;
            $orderAccount->number_of_orders = $orderAccount->number_of_orders + 1;
            $orderAccount->all_summ_dollar = round($orderAccount->all_summ_dollar + $all_pay_summ, 2);

            $orderAccount->discount_amount = $orderAccount->discount_amount + (float)$discount_amounts;
            $orderAccount->sum_dollar = $orderAccount->sum_dollar + (float)$sum_dollars;
            $orderAccount->dollar_sumda = $orderAccount->dollar_sumda + (float)$dollar_sumda;
            $orderAccount->sum_som = $orderAccount->sum_som + (float)$sum_soms;
            $orderAccount->sum_cart = $orderAccount->sum_cart + (float)$sum_carts;
            $orderAccount->sum_transfers = $orderAccount->sum_transfers + (float)$sum_transferss;
            $orderAccount_id =$orderAccount->id;
            $orderAccount->save(false);

            $orderAccountCrHistory = new OrderAccountHistory();
            $orderAccountCrHistory->client_id = $client_id;
            // $orderAccountCrHistory->total_debt = $total_debts;
            $orderAccountCrHistory->date = date('Y-m-d',strtotime($dates));
            $orderAccountCrHistory->exchange_rate = $exchange_rates;
            $orderAccountCrHistory->number_of_orders = 1;
            $orderAccountCrHistory->all_summ_dollar = $all_pay_summ;
            $orderAccountCrHistory->total_debt_old = $total_debts;

            $orderAccountCrHistory->discount_amount = $discount_amounts;
            $orderAccountCrHistory->sum_dollar = $sum_dollars;
            $orderAccountCrHistory->dollar_sumda = $dollar_sumda;
            $orderAccountCrHistory->sum_som = $sum_soms;
            $orderAccountCrHistory->order_account_status = $tasdiq_check;
            $orderAccountCrHistory->sum_cart = $sum_carts;
            $orderAccountCrHistory->sum_transfers = $sum_transferss;
            $orderAccountCrHistory->zdacha_dollar = $zdacha_dollar;
            $orderAccountCrHistory->zdacha_sum = $zdacha_sum;
            $orderAccountCrHistory->cr_date = date('Y-m-d',strtotime($dates));
            $orderAccountCrHistory->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
            $orderAccountCrHistory->update_status = 1; // update_status= 1 bo'lsa yangi yaratilgan yoki o'zgarish tasdiqlangan bo'ladi
            $orderAccountCrHistory->save(false);
            $orderAccountHistory_id = $orderAccountCrHistory->id;
        }else{
            $orderAccountCr = new OrderAccount();
            $orderAccountCr->client_id = $client_id;
            $orderAccountCr->last_order_date = date('Y-m-d',strtotime($dates));
            // $orderAccountCr->total_debt = $total_debts;
            $orderAccountCr->date = date('Y-m-d',strtotime($dates));
            $orderAccountCr->exchange_rate = $exchange_rates;
            $orderAccountCr->all_summ_dollar = $all_pay_summ;
            $orderAccountCr->number_of_orders = 1;

            $orderAccountCr->discount_amount = $discount_amounts;
            $orderAccountCr->sum_dollar = $sum_dollars;
            $orderAccountCr->dollar_sumda = $dollar_sumda;
            $orderAccountCr->sum_som = $sum_soms;
            $orderAccountCr->sum_cart = $sum_carts;
            $orderAccountCr->sum_transfers = $sum_transferss;
            $orderAccountCr->cr_date = date('Y-m-d',strtotime($dates));
            $orderAccountCr->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
            $orderAccountCr->save(false);
            $orderAccount_id = $orderAccountCr->id;

            $orderAccountCrHistory = new OrderAccountHistory();
            $orderAccountCrHistory->client_id = $client_id;
            // $orderAccountCrHistory->total_debt = $total_debts;
            $orderAccountCrHistory->date = date('Y-m-d',strtotime($dates));
            $orderAccountCrHistory->exchange_rate = $exchange_rates;
            $orderAccountCrHistory->all_summ_dollar = $all_pay_summ;
            $orderAccountCrHistory->number_of_orders = 1;
            $orderAccountCrHistory->total_debt_old = $total_debts;

            $orderAccountCrHistory->discount_amount = $discount_amounts;
            $orderAccountCrHistory->sum_dollar = $sum_dollars;
            $orderAccountCrHistory->dollar_sumda = $dollar_sumda;
            $orderAccountCrHistory->sum_som = $sum_soms;
            $orderAccountCrHistory->order_account_status = $tasdiq_check;
            $orderAccountCrHistory->sum_cart = $sum_carts;
            $orderAccountCrHistory->sum_transfers = $sum_transferss;
            $orderAccountCrHistory->zdacha_dollar = $zdacha_dollar;
            $orderAccountCrHistory->zdacha_sum = $zdacha_sum;
            $orderAccountCrHistory->cr_date = date('Y-m-d',strtotime($dates));
            $orderAccountCrHistory->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
            $orderAccountCrHistory->update_status = 1; // update_status= 1 bo'lsa yangi yaratilgan yoki o'zgarish tasdiqlangan bo'ladi
            $orderAccountCrHistory->save(false);
            $orderAccountHistory_id = $orderAccountCrHistory->id;
        }
        $all_profit = 0;
        $all_summ   = 0;
        $status_order_dukon = 0;
        $status_order_sklad = 0;

        // YANGI: hech bo‘lmasa bitta mahsulot narxi ombordagidan katta/kiyinchalik flag
        $hasLargePrice = false;

        foreach ($contact as $value) {
            $brand_list = Brands::find()->where(['id' => $value['brand_id']])->one();
            $product_category_list = ProductCategory::find()->where(['id' => $value['product_category_id']])->one();
            $type_sklad_list = TypeSklad::find()->where(['name' => $value['joy']])->one();

            if ($value['joy'] == "Ombor") {
                $status_order_sklad = 1;
            } elseif ($value['joy'] == "Dokon") {
                $status_order_dukon = 1;
            }

            $typeId = $orderAccount->getTypeNameView($value['tip']);
            $size   = (float)$value['size'];
            $price  = (float)$value['price'];
            $count  = (int)$value['count'];

            $productAccount = ProductAccount::find()
                ->andWhere(['order_account_id' => $orderAccount_id])
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
                $relative->order_account_id         = $orderAccount_id;
                $relative->order_account_history_id = $orderAccountHistory_id;
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
                $relative->save(false);
            } else {
                $productAccount->count      = $productAccount->count + $count;
                $productAccount->price      = $price;
                $productAccount->real_price = $price_real;
                $productAccount->is_debtor                = $hasLargePrice ? 1 : 0;
                $productAccount->profit     = round($productAccount->profit + $lineProfit, 2);
                $productAccount->save(false);
            }

            // History yozuvi
            $relativeHistory = new ProductAccountHistory();
            $relativeHistory->order_account_id         = $orderAccount_id;
            $relativeHistory->order_account_history_id = $orderAccountHistory_id;
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
            $relativeHistory->save(false);

            // Ombor soni
            if ($warehouseValue && $type_sklad_list->id == 1) {
                $warehouseValue->count = $warehouseValue->count - $count;
                $warehouseValue->save(false);
            }

            // Umumiy summalar
            $all_profit += $lineProfit;
            $all_summ   += $lineAmount;
        }

        // echo '<pre>';
        // print_r($rrrrrrrrr);
        // echo '</pre>';
        $orderAccountProfHistory = OrderAccountHistory::find()->where(['id' => $orderAccountHistory_id])->one();
        $orderAccountProfHistory->status_order_dukon = $status_order_dukon;
        $orderAccountProfHistory->status_order_sklad = $status_order_sklad;
        $orderAccountProfHistory->order_commit = $comment;
        $orderAccountProfHistory->driver_info = $driver_info;
        $orderAccountProfHistory->fast_order = $fastOrder;
        // YANGI: flagni yozish
        $orderAccountProfHistory->large_price = $hasLargePrice ? 1 : 0;

        $orderAccountProfHistory->save();

        if ($tasdiq_check == 1) {
            
            $orderAccountProf = OrderAccount::find()->where(['id' => $orderAccount_id])->one();
            // $orderAccountProf->all_profit_dollar = round($orderAccountProf->all_profit_dollar + array_sum($all_profit_array), 2);
            if ($client->is_profit_loss == 1) {
                $orderAccountProf->all_profit_dollar = 0;
            }else{
                $orderAccountProf->all_profit_dollar = round($orderAccountProf->all_profit_dollar + $all_profit, 2);
            }
            
            $orderAccountProf->all_product_sum = round($orderAccountProf->all_product_sum + $all_summ, 2);
            $orderAccountProf->total_debt_old = $total_debts;
            $orderAccountProf->total_debt = round($total_debts + ($all_summ - $all_pay_summ -$discount_amounts), 2);
            $orderAccountProf->save();

            $orderAccountProfHistory = OrderAccountHistory::find()->where(['id' => $orderAccountHistory_id])->one();
            if ($client->is_profit_loss == 1) {
                $orderAccountProfHistory->all_profit_dollar = 0;
            }else{
                $orderAccountProfHistory->all_profit_dollar = round($all_profit, 2);
            }
            
            // $orderAccountProfHistory->all_profit_dollar = round($orderAccountProfHistory->all_profit_dollar + array_sum($all_profit_array), 2);
            $orderAccountProfHistory->total_debt_today = round(($all_summ - $all_pay_summ -$discount_amounts), 2);
            $orderAccountProfHistory->total_debt = round($total_debts + ($all_summ - $all_pay_summ -$discount_amounts), 2);
            $orderAccountProfHistory->all_product_sum = $all_summ;
            $orderAccountProfHistory->save();

            $keshbekClient = Client::find()->where(['id' => $clients_id])->one();
            if ($keshbekClient->keshbek) {
                $keshbekHistory = new KeshbekHistory();
                $keshbekHistory->order_account_history_id = $orderAccountHistory_id;
                $keshbekHistory->client_id = $keshbekClient->id;
                $keshbekHistory->keshbek = $keshbekClient->keshbek;
                $keshbekHistory->keshbek_sum = round(($all_summ * $keshbekClient->keshbek)/100, 2);
                $keshbekHistory->cr_date = date('Y-m-d',strtotime($dates));;
                $keshbekHistory->save();
            }
            
        }
        return $this->redirect(['/order-account-history/index']);
    }

    public function actionClient($id='')
    {
        // echo '<pre>';
        // print_r($id);
        // echo '</pre>';
        // print_r($id);
        // die();
        $orderAccount = OrderAccount::find()->where(['client_id' => $id])->one();
        if ($orderAccount) {
            echo $orderAccount->total_debt;
        }else{
            echo 22;
        }
    }
    /**
     * Displays a single OrderAccount model.
     * @param integer $id
     * @return mixed
     */
    public function actionOrders()
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
        return $this->render('orders', ['warehouse' => $warehouse]);
    }

    public function actionTopClient()
    {    
        $orderAccount = OrderAccount::find()
            ->where(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->orderBy([ 'all_profit_dollar' => SORT_DESC, ])->all();
        return $this->render('top_client', ['orderAccount' => $orderAccount,]);
    }

    public function actionDebtors()
    {    
        $orderAccount = OrderAccount::find()
            ->where(['>', 'total_debt', 0])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->orderBy(['total_debt' => SORT_DESC])
            ->all();

        return $this->render('debtor', ['orderAccount' => $orderAccount,]);
    }

    public function actionProducts($id)
    {    
        $warehouse = ProductAccount::find()->where(['order_account_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('products', ['warehouse' => $warehouse, 'order_id' => $id]);
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
    
    public function actionPrint2($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlText2($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("ClientOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }
    
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Mijoz buyurmasi",
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
     * Creates a new OrderAccount model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderAccount();
        
        if ($model->load(Yii::$app->request->post()) ) {
            $post = Yii::$app->request->post();
            $clients_id = $post['OrderAccount']['clients_id'];
            $total_debts = $post['OrderAccount']['total_debts'];
            $dates = $post['OrderAccount']['dates'];
            $exchange_rates = $post['OrderAccount']['exchange_rates'];

            $discount_amounts = $post['OrderAccount']['discount_amounts'];
            $sum_dollars = $post['OrderAccount']['sum_dollars'];
            $dollar_sumdas = $post['OrderAccount']['dollar_sumdas'];
            $sum_soms = $post['OrderAccount']['sum_soms'];
            $sum_carts = $post['OrderAccount']['sum_carts'];
            $sum_transferss = $post['OrderAccount']['sum_transferss'];
            $order_account_statuses = $post['OrderAccount']['order_account_statuses'];

            // $dollar_sum_soms = $dollar_sumdas;
            // $dollar_sum_carts = $sum_carts/$exchange_rates;
            // $dollar_transferss = $sum_transferss/$exchange_rates;
            // echo '<pre>';
            // print_r($order_account_statuses);
            // echo '</pre>';
            $all_pay_summ = round($sum_dollars, 2);
            $allValues = $post['OrderAccount']['allValue'];
            
            $client = Client::find()->where(['id' => $clients_id])->one();
            if (!isset($client)){                    
                $client_cr = new Client();
                $client_cr->fio = $clients_id;
                $client_cr->save();
                $client_id = $client_cr->id;                        
            }else{
                $client_id = $client->id;   
            }
            
            $orderAccount = OrderAccount::find()->where(['client_id' => $client_id])->one();
             
            if ($orderAccount) {
                // $orderAccount->total_debt = $total_debts;
                $orderAccount->last_order_date = date('Y-m-d',strtotime($dates));
                $orderAccount->exchange_rate = $exchange_rates;
                $orderAccount->number_of_orders = $orderAccount->number_of_orders + 1;
                $orderAccount->all_summ_dollar = $orderAccount->all_summ_dollar + $all_pay_summ;

                $orderAccount->discount_amount = $orderAccount->discount_amount + $discount_amounts;
                $orderAccount->sum_dollar = $orderAccount->sum_dollar + $sum_dollars;
                $orderAccount->dollar_sumda = $orderAccount->dollar_sumda + $dollar_sumdas;
                $orderAccount->sum_som = $orderAccount->sum_som + $sum_soms;
                $orderAccount->sum_cart = $orderAccount->sum_cart + $sum_carts;
                $orderAccount->sum_transfers = $orderAccount->sum_transfers + $sum_transferss;
                $orderAccount_id =$orderAccount->id;
                $orderAccount->save(false);

                $orderAccountCrHistory = new OrderAccountHistory();
                $orderAccountCrHistory->client_id = $client_id;
                // $orderAccountCrHistory->total_debt = $total_debts;
                $orderAccountCrHistory->date = date('Y-m-d',strtotime($dates));
                $orderAccountCrHistory->exchange_rate = $exchange_rates;
                $orderAccountCrHistory->number_of_orders = 1;
                $orderAccountCrHistory->all_summ_dollar = $all_pay_summ;

                $orderAccountCrHistory->discount_amount = $discount_amounts;
                $orderAccountCrHistory->sum_dollar = $sum_dollars;
                $orderAccountCrHistory->dollar_sumda = $dollar_sumdas;
                $orderAccountCrHistory->sum_som = $sum_soms;
                $orderAccountCrHistory->sum_cart = $sum_carts;
                $orderAccountCrHistory->sum_transfers = $sum_transferss;

                $orderAccountCrHistory->order_account_status = $order_account_statuses;
                $orderAccountCrHistory->cr_date = date('Y-m-d',strtotime($dates));
                $orderAccountCrHistory->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
                $orderAccountCrHistory->update_status = 1; // update_status= 1 bo'lsa yangi yaratilgan yoki o'zgarish tasdiqlangan bo'ladi
                $orderAccountCrHistory->save(false);
                $orderAccountHistory_id = $orderAccountCrHistory->id;
            }else{
                $orderAccountCr = new OrderAccount();
                $orderAccountCr->client_id = $client_id;
                $orderAccountCr->last_order_date = date('Y-m-d',strtotime($dates));
                // $orderAccountCr->total_debt = $total_debts;
                $orderAccountCr->date = date('Y-m-d',strtotime($dates));
                $orderAccountCr->exchange_rate = $exchange_rates;
                $orderAccountCr->all_summ_dollar = $all_pay_summ;
                $orderAccountCr->number_of_orders = 1;

                $orderAccountCr->discount_amount = $discount_amounts;
                $orderAccountCr->sum_dollar = $sum_dollars;
                $orderAccountCr->dollar_sumda = $dollar_sumdas;
                $orderAccountCr->sum_som = $sum_soms;
                $orderAccountCr->sum_cart = $sum_carts;
                $orderAccountCr->sum_transfers = $sum_transferss;
                $orderAccountCr->cr_date = date('Y-m-d',strtotime($dates));
                $orderAccountCr->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
                $orderAccountCr->save(false);
                $orderAccount_id = $orderAccountCr->id;

                $orderAccountCrHistory = new OrderAccountHistory();
                $orderAccountCrHistory->client_id = $client_id;
                // $orderAccountCrHistory->total_debt = $total_debts;
                $orderAccountCrHistory->date = date('Y-m-d',strtotime($dates));
                $orderAccountCrHistory->exchange_rate = $exchange_rates;
                $orderAccountCrHistory->all_summ_dollar = $all_pay_summ;
                $orderAccountCrHistory->number_of_orders = 1;

                $orderAccountCrHistory->discount_amount = $discount_amounts;
                $orderAccountCrHistory->sum_dollar = $sum_dollars;
                $orderAccountCrHistory->dollar_sumda = $dollar_sumdas;
                $orderAccountCrHistory->sum_som = $sum_soms;
                $orderAccountCrHistory->sum_cart = $sum_carts;
                $orderAccountCrHistory->sum_transfers = $sum_transferss;

                $orderAccountCrHistory->order_account_status = $order_account_statuses;
                $orderAccountCrHistory->cr_date = date('Y-m-d',strtotime($dates));
                $orderAccountCrHistory->cr_date_time = date('Y-m-d H:i:s',strtotime($dates.' '.date('H:i:s')));
                $orderAccountCrHistory->update_status = 1; // update_status= 1 bo'lsa yangi yaratilgan yoki o'zgarish tasdiqlangan bo'ladi
                $orderAccountCrHistory->save(false);
                $orderAccountHistory_id = $orderAccountCrHistory->id;
            }
            $all_profit = 0;
            $all_profit_array = array();
            $all_summ = 0;
            $status_order_dukon = 0;
            $status_order_sklad = 0;
            if ($allValues) {
                foreach ($allValues as $value) {
                    $productAccount = ProductAccount::find()
                                    ->andWhere(['order_account_id' => $orderAccount_id])
                                    ->andWhere(['brand_id' => (int)$value['brand_id']])
                                    ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                                    ->andWhere(['type' => (int)$value['type']])
                                    ->andWhere(['type_sklad_id' => (int)$value['type_sklad_id']])
                                    ->andWhere(['size' => (float)$value['size']])->one();

                    // $priceProduct = PriceProduct::find()
                    //                 ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                    //                 ->andWhere(['brand_id' => (int)$value['brand_id']])
                    //                 ->andWhere(['size' => (float)$value['size']])
                    //                 ->andWhere(['type' => (int)$value['type']])->one();

                    if ($value['type_sklad_id'] == 1) {
                        $status_order_sklad = 1;
                    }elseif ($value['type_sklad_id'] == 2) {
                        $status_order_dukon = 1;
                    }


                    $warehouseValue = Warehouse::find()
                            ->andWhere(['product_category_id' => (int)$value['product_category_id']])
                            ->andWhere(['brand_id' => (int)$value['brand_id']])
                            ->andWhere(['size' => (float)$value['size']])
                            ->andWhere(['type' => (int)$value['type']])->one();
                    if ($warehouseValue) { 
                        // $real_prices_product = Prices::find()->where(['warehouse_id' => $warehouseValue->id])->one();
                        // if ($real_prices_product) {
                        //     $price_real = (int)$real_prices_product->price;
                        // }else{
                        //     $price_real = 0;
                        // }
                        $price_real = (float)$warehouseValue->price;
                    }else{
                        $price_real = 0;
                    }
                    
                    if (!$productAccount) {
                        $relative = new ProductAccount();
                        $relative->order_account_id = $orderAccount_id;
                        $relative->order_account_history_id = $orderAccountHistory_id;
                        $relative->brand_id = $value['brand_id'];
                        $relative->product_category_id = $value['product_category_id'];
                        $relative->size = $value['size'];
                        $relative->count = $value['count'];

                        $relative->type = $value['type'];
                        $relative->price = $value['price'];
                        $relative->real_price = $price_real;
                        $relative->type_sklad_id = $value['type_sklad_id'];
                        $relative->profit = round($price_real != 0 ? ($value['price'] * $value['count'] - $price_real * $value['count']):0, 2);
                        $relative->cr_date = date('Y-m-d',strtotime($dates));
                        $relative->save();


                        $relativeHistory = new ProductAccountHistory();
                        $relativeHistory->order_account_id = $orderAccount_id;
                        $relativeHistory->order_account_history_id = $orderAccountHistory_id;
                        $relativeHistory->brand_id = $value['brand_id'];
                        $relativeHistory->product_category_id = $value['product_category_id'];
                        $relativeHistory->size = $value['size'];
                        $relativeHistory->count = $value['count'];
                        $relativeHistory->given_count = $value['count'];

                        $relativeHistory->type = $value['type'];
                        $relativeHistory->price = $value['price'];
                        $relativeHistory->real_price = $price_real;
                        $relativeHistory->type_sklad_id = $value['type_sklad_id'];
                        $relativeHistory->profit = round($price_real != 0 ? ($value['price'] * $value['count'] - $price_real * $value['count']):0, 2);
                        $relativeHistory->cr_date = date('Y-m-d',strtotime($dates));
                        $relativeHistory->save();

                        $all_profit = round($all_profit + $price_real != 0 ? ($value['price'] * $value['count'] - $price_real * $value['count']): 0, 2);
                        $all_profit_array[] = $all_profit;
                        $all_summ = round($all_summ + $value['price'] * $value['count'], 2);
                    }else {
                        $productAccount->count = $productAccount->count + $value['count'];
                        $productAccount->price = $value['price'];
                        $productAccount->real_price = $price_real;
                        $productAccount->profit = round($productAccount->profit +  $price_real != 0 ? ($value['price']*$value['count'] - $price_real * $value['count']):0, 2);
                        $productAccount->save();

                        $relativeHistory = new ProductAccountHistory();
                        $relativeHistory->order_account_id = $orderAccount_id;
                        $relativeHistory->order_account_history_id = $orderAccountHistory_id;
                        $relativeHistory->brand_id = $value['brand_id'];
                        $relativeHistory->product_category_id = $value['product_category_id'];
                        $relativeHistory->size = $value['size'];
                        $relativeHistory->count = $value['count'];
                        $relativeHistory->given_count = $value['count'];

                        $relativeHistory->type = $value['type'];
                        $relativeHistory->price = $value['price'];
                        $relativeHistory->real_price = $price_real;
                        $relativeHistory->type_sklad_id = $value['type_sklad_id'];
                        $relativeHistory->profit = round($price_real != 0 ? ($value['price']*$value['count'] - $price_real * $value['count']): 0, 2);
                        $relativeHistory->cr_date = date('Y-m-d',strtotime($dates));
                        $relativeHistory->save();
                        

                        $all_profit = round($all_profit + $price_real != 0 ? ( $value['price'] * $value['count'] - $price_real * $value['count']):0, 2);
                        $all_profit_array[] = $all_profit;
                        $all_summ = round($all_summ + $value['price'] * $value['count'], 2);
                    }
                    if ($warehouseValue) {
                        if ($type_sklad_list->id == 1) {
                            $warehouseValue->count = $warehouseValue->count - $value['count'];
                            $warehouseValue->save(false);
                        }
                    }
                        
                }
            }
            $orderAccountProfHistory = OrderAccountHistory::find()->where(['id' => $orderAccountHistory_id])->one();
            $orderAccountProfHistory->status_order_dukon = $status_order_dukon;
            $orderAccountProfHistory->status_order_sklad = $status_order_sklad;
            $orderAccountProfHistory->save();
            if ($order_account_statuses == 1) {
                $orderAccountProf = OrderAccount::find()->where(['id' => $orderAccount_id])->one();
                $orderAccountProf->all_profit_dollar = $orderAccountProf->all_profit_dollar + array_sum($all_profit_array);
                $orderAccountProf->all_product_sum = $orderAccountProf->all_product_sum + $all_summ;
                $orderAccountProf->total_debt_old = $total_debts;
                $orderAccountProf->total_debt = round($total_debts + ($all_summ - $all_pay_summ -$discount_amounts), 2);
                $orderAccountProf->save();

                $orderAccountProfHistory = OrderAccountHistory::find()->where(['id' => $orderAccountHistory_id])->one();
                $orderAccountProfHistory->all_profit_dollar = $orderAccountProfHistory->all_profit_dollar + array_sum($all_profit_array);
                $orderAccountProfHistory->total_debt_today = round(($all_summ - $all_pay_summ -$discount_amounts), 2);
                $orderAccountProfHistory->total_debt = round($total_debts + ($all_summ - $all_pay_summ -$discount_amounts), 2);
                $orderAccountProfHistory->all_product_sum = $all_summ;
                $orderAccountProfHistory->save();

                $keshbekClient = Client::find()->where(['id' => $clients_id])->one();
                if ($keshbekClient->keshbek) {
                    $keshbekHistory = new KeshbekHistory();
                    $keshbekHistory->order_account_history_id = $orderAccountHistory_id;
                    $keshbekHistory->client_id = $keshbekClient->id;
                    $keshbekHistory->keshbek = $keshbekClient->keshbek;
                    $keshbekHistory->keshbek_sum = round(($all_summ * $keshbekClient->keshbek)/100, 2);
                    $keshbekHistory->cr_date = date('Y-m-d',strtotime($dates));;
                    $keshbekHistory->save();
                }
                
            }
            // echo '<pre>';
            // print_r($all_summ);
            // print_r($all_pay_summ);
            // print_r($discount_amounts);
            // print_r($orderAccountProf->total_debt);
            // print_r($orderAccountProfHistory->all_product_sum);
            // echo '</pre>';
            return $this->redirect(['/order-account-history/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing OrderAccount model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Yangilash",
                    'size' => 'large',
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];  
            }else{
                 return [
                    'title'=> "Yangilash",
                    'size' => 'large',
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing OrderAccount model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionOneDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);


        $valueReason = Yii::$app->request->post('OrderAccount')['comment'];
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> '<div style="text-align:center"><b style="font-size:16px;color:#707478">Haqiqatdan ham</b> <b style="font-size:18px;color:red">'.$model->client->fio.'</b> <b style="font-size:16px;color:#707478">ning barcha buyurtmasini oʻchirib tashlamoqchimisiz?</b></div>',
                    'content'=>$this->renderAjax('delete_form', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('O\'chirish',['class'=>'btn btn-danger','type'=>"submit"])
                ];         
            }else if($valueReason){
                
                $productAccountHistory = ProductAccountHistory::find()->where(['order_account_id' => $id])->all();
                foreach ($productAccountHistory as $value) {
                    ProductAccountHistory::find()->where(['id' => $value['id']])->one()->delete();
                }
                $productAccount = ProductAccount::find()->where(['order_account_id' => $id])->all();
                foreach ($productAccount as $value) {
                    ProductAccount::find()->where(['id' => $value['id']])->one()->delete();
                }
        
                $debtRepayment = DebtRepayment::find()
                    ->where(['order_account_id' => $id])
                    ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                    ->all();

                foreach ($debtRepayment as $value) {
                    DebtRepayment::find()->where(['id' => $value['id']])->one()->delete();
                }

                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->client->fio . " ning barcha buyurtmasi o'chirildi...";
                $elegantHistoryUpdate->comment = $valueReason;
                $elegantHistoryUpdate->status = 2;
                $elegantHistoryUpdate->type = 2;
                $elegantHistoryUpdate->save(false);
                $this->findModel($id)->delete();

                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];    
            }else{
                 return [
                    'title'=> '<div style="text-align:center"><b style="font-size:16px;color:#707478">Haqiqatdan ham</b> <b style="font-size:18px;color:red">'.$model->client->fio.'</b> <b style="font-size:16px;color:#707478">ning barcha buyurtmasini oʻchirib tashlamoqchimisiz?</b></div><br/> <p style="font-size:14px;color:red;text-align:center">Izohni to\'ldiring...</p>',
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

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id); 

        $productAccountHistory = ProductAccountHistory::find()->where(['order_account_id' => $id])->all();
        foreach ($productAccountHistory as $value) {
            ProductAccountHistory::find()->where(['id' => $value['id']])->one()->delete();
        }
        $productAccount = ProductAccount::find()->where(['order_account_id' => $id])->all();
        foreach ($productAccount as $value) {
            ProductAccount::find()->where(['id' => $value['id']])->one()->delete();
        }

        $debtRepayment = DebtRepayment::find()
            ->where(['order_account_id' => $id])
            ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
            ->all();

        foreach ($debtRepayment as $value) {
            DebtRepayment::find()->where(['id' => $value['id']])->one()->delete();
        }

        

        $deleteReason = Yii::$app->request->post('delete_reason');
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $model->client->fio . " ning barcha buyurtmasi o'chirildi...";
        $elegantHistoryUpdate->comment = $deleteReason;
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
     * Delete multiple existing OrderAccount model.
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
     * Finds the OrderAccount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderAccount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderAccount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
