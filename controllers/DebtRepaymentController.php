<?php

namespace app\controllers;

use Yii;
use app\models\DebtRepayment;
use app\models\DebtRepaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\OrderAccount;
use app\models\OrderAccountHistory;
use app\models\ElegantHistoryUpdate;
/**
 * DebtRepaymentController implements the CRUD actions for DebtRepayment model.
 */
class DebtRepaymentController extends Controller
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
     * Lists all DebtRepayment models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new DebtRepaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function getHistoryCalcTime(OrderAccountHistory $model, $endOfDay = true)
    {
        if (!empty($model->cr_date_time)) {
            return date('Y-m-d H:i:s', strtotime($model->cr_date_time));
        }

        $date = !empty($model->date) ? $model->date : $model->cr_date;
        return date('Y-m-d', strtotime($date)) . ($endOfDay ? ' 23:59:59' : ' 00:00:00');
    }

    private function getDebtRepaymentCalcTime(DebtRepayment $model)
    {
        if (!empty($model->cr_date_time)) {
            return date('Y-m-d H:i:s', strtotime($model->cr_date_time));
        }

        return date('Y-m-d', strtotime($model->date)) . ' 23:59:59';
    }

    private function getDebtRepaymentTotalBetween($clientId, $fromTime, $toTime = null)
    {
        $timeExpression = "COALESCE(cr_date_time, CONCAT(`date`, ' 23:59:59'))";
        $query = DebtRepayment::find()
            ->where(['client_id' => $clientId])
            ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
            ->andWhere(['or', ['is_delete' => null], ['<>', 'is_delete', 1]])
            ->andWhere($timeExpression . ' > :fromTime', [':fromTime' => $fromTime]);

        if ($toTime !== null) {
            $query->andWhere($timeExpression . ' <= :toTime', [':toTime' => $toTime]);
        }

        return (float)$query
            ->select(new \yii\db\Expression('COALESCE(SUM(COALESCE(all_summ_dollar, 0) + COALESCE(discount_amount, 0)), 0)'))
            ->scalar();
    }

    private function recalculateClientOrderDebtsFrom($clientId, $previousDebt, $previousTime)
    {
        $nextOrders = OrderAccountHistory::find()
            ->where(['client_id' => $clientId])
            ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
            ->andWhere(['or', ['is_delete' => null], ['<>', 'is_delete', 1]])
            ->andWhere("COALESCE(cr_date_time, CONCAT(`date`, ' 23:59:59')) > :previousTime", [':previousTime' => $previousTime])
            ->orderBy(['date' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        foreach ($nextOrders as $nextOrder) {
            $nextOrderTime = $this->getHistoryCalcTime($nextOrder, true);
            $repaymentTotal = $this->getDebtRepaymentTotalBetween($clientId, $previousTime, $nextOrderTime);

            $nextOrder->total_debt_old = $previousDebt;
            $nextOrder->total_debt_today = round(
                (float)$nextOrder->all_product_sum - ((float)$nextOrder->all_summ_dollar + (float)$nextOrder->discount_amount),
                2
            );
            $nextOrder->total_debt = round(
                (float)$nextOrder->total_debt_old + (float)$nextOrder->total_debt_today - $repaymentTotal,
                2
            );
            $nextOrder->save(false);

            $previousDebt = (float)$nextOrder->total_debt;
            $previousTime = $nextOrderTime;
        }

        $afterLastRepayment = $this->getDebtRepaymentTotalBetween($clientId, $previousTime);
        $orderAccount = OrderAccount::find()->where(['client_id' => $clientId])->one();
        if ($orderAccount) {
            $orderAccount->total_debt = round($previousDebt - $afterLastRepayment, 2);
            $lastPaymentDate = DebtRepayment::find()
                ->where(['client_id' => $clientId])
                ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                ->andWhere(['or', ['is_delete' => null], ['<>', 'is_delete', 1]])
                ->max('date');
            $orderAccount->date_last_debt_payment = $lastPaymentDate ?: null;
            $orderAccount->save(false);
        }
    }
    public function actionTrashO()
    {    
        $searchModel = new DebtRepaymentSearch(['is_delete' => 1]);
        $dataProvider = $searchModel->searchTrash(Yii::$app->request->queryParams);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintDebt($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlTextSklad($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("DebtRepaymentList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }

    /**
     * Displays a single DebtRepayment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "To'langan qarz",
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

    public function actionClientDebt($client_id, $customer_fio)
    {            
        $array_Products = [];
        $orderProducts = DebtRepayment::find()
            ->where(['client_id' => $client_id])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->select(['date'])
            ->groupBy(['date'])
            ->all();

        foreach($orderProducts as $value) {
            $array_Products []= [
                'client_id' => $client_id,
                'date' => $value['date'],
            ];
        }
            
        
        
        return $this->render('client_debt', ['array_Products' => $array_Products, 'customer_fio' => $customer_fio]);
    }
    /**
     * Creates a new DebtRepayment model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($account_id)
    {
        $request = Yii::$app->request;
        $model = new DebtRepayment();  
        $orderAccount = OrderAccount::find()->where(['id' => $account_id])->one();
        // echo '<pre>';
        // print_r(number_format($orderAccount->total_debt, 0, '.', ''));
        // print_r("ddd");
        // // print_r((float)$wwww);
        // echo '</pre>';
        if ($model->load($request->post()) && $model->save()) {
            $model->client_id = $orderAccount->client_id;
            $model->order_account_id = $account_id;
            $model->total_debt = $orderAccount->total_debt;
            $model->save();
            $dollar_sum_soms = $model->sum_som/$model->exchange_rate;
            $dollar_sum_otkazma = $model->sum_otkazma/$model->exchange_rate;
            $dollar_sum_carts = $model->summ_cart/$model->exchange_rate;
            $dollar_transferss = $model->sum_transfers/$model->exchange_rate;
            $summa_all = $model->summ_dollar + $dollar_sum_soms + $dollar_sum_otkazma + $dollar_sum_carts + $dollar_transferss;
            $model->all_summ_dollar = $summa_all;
            $model->save();
            $orderAccount->total_debt = $orderAccount->total_debt - $summa_all - $model->discount_amount;
            $orderAccount->date_last_debt_payment = $model->date;
            $orderAccount->save();
            return $this->redirect(['/order-account/index']); 
        } else {
            return $this->render('create', [
                'model' => $model,
                'total_debt' => number_format($orderAccount->total_debt, 0, '.', ''),
                'orderAccount' => $orderAccount,
            ]);
        }
       
    }

    /**
     * Updates an existing TypeSklad model.
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
     * Delete an existing DebtRepayment model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionReturn($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->is_delete = 0;
        $model->save(false);

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

    public function actionTrash($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->is_delete = 1;
        $model->save(false);

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

    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $debtRepayment = $this->findModel($id);
        $orderAccount = OrderAccount::find()->where(['id' => $debtRepayment->order_account_id])->one();
        if (!$orderAccount) {
            throw new NotFoundHttpException('Mijoz qarz hisobi topilmadi.');
        }
        if (!$debtRepayment->client) {
            throw new NotFoundHttpException('Mijoz topilmadi.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $clientId = (int)$debtRepayment->client_id;
            $repaymentTime = $this->getDebtRepaymentCalcTime($debtRepayment);
            $previousOrder = OrderAccountHistory::find()
                ->where(['client_id' => $clientId])
                ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                ->andWhere(['or', ['is_delete' => null], ['<>', 'is_delete', 1]])
                ->andWhere("COALESCE(cr_date_time, CONCAT(`date`, ' 23:59:59')) <= :repaymentTime", [':repaymentTime' => $repaymentTime])
                ->orderBy(['date' => SORT_DESC, 'id' => SORT_DESC])
                ->one();

            $previousDebt = $previousOrder ? (float)$previousOrder->total_debt : 0.0;
            $previousTime = $previousOrder ? $this->getHistoryCalcTime($previousOrder, true) : '1970-01-01 00:00:00';

            $deletedAmount = round((float)$debtRepayment->all_summ_dollar + (float)$debtRepayment->discount_amount, 2);
            $elegantHistoryUpdate = new ElegantHistoryUpdate();
            $elegantHistoryUpdate->title = $debtRepayment->client->fio . " ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada to'lagan qarzi o'chirildi...";
            $elegantHistoryUpdate->comment = $deletedAmount . " $ qarz to'lagani o'chirildi";
            $elegantHistoryUpdate->status = 2;
            $elegantHistoryUpdate->type = 2;
            $elegantHistoryUpdate->save(false);

            $debtRepayment->delete();
            $this->recalculateClientOrderDebtsFrom($clientId, $previousDebt, $previousTime);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            if($request->isAjax){
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'title'=> '<div style="text-align:center"><b style="font-size:16px;color:red">O\'chirishda xatolik</b></div>',
                    'content'=> '<div class="alert alert-danger">'.Html::encode($e->getMessage()).'</div>',
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                ];
            }
            throw $e;
        }

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }
     /**
     * Delete multiple existing DebtRepayment model.
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
     * Finds the DebtRepayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DebtRepayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DebtRepayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
