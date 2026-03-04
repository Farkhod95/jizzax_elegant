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
            $dollar_sum_carts = $model->summ_cart/$model->exchange_rate;
            $dollar_transferss = $model->sum_transfers/$model->exchange_rate;
            $summa_all = $model->summ_dollar + $dollar_sum_soms + $dollar_sum_carts + $dollar_transferss;
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
        $debtRepayment = DebtRepayment::find()->where(['id' => $id])->one();
        $orderAccount = OrderAccount::find()->where(['id' => $debtRepayment->order_account_id])->one();
        $orderAccount->total_debt = $orderAccount->total_debt + $debtRepayment->all_summ_dollar;
        $orderAccount->save();
        $this->findModel($id)->delete();

        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $debtRepayment->client->fio . " ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada to'lagan qarzi o'chirildi...";
        $elegantHistoryUpdate->comment = $debtRepayment->all_summ_dollar. " $ qarz to'lagani o'chirildi";
        $elegantHistoryUpdate->status = 2;
        $elegantHistoryUpdate->type = 2;
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
