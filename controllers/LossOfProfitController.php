<?php

namespace app\controllers;

use Yii;
use app\models\LossOfProfit;
use app\models\LossOfProfitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\OrderAccountHistory;
use app\models\OrderAccountHistorySearch;
use app\models\Expenses;
use app\models\ExpensesSearch;
/**
 * LossOfProfitController implements the CRUD actions for LossOfProfit model.
 */
class LossOfProfitController extends Controller
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
                            return \app\models\Users::isAdminRight(Yii::$app->user->identity->id);
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
     * Lists all LossOfProfit models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new LossOfProfitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single LossOfProfit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($start_date, $end_date)
    {           
        $searchModel = new OrderAccountHistorySearch();
        $dataProvider = $searchModel->search2(Yii::$app->request->queryParams, $start_date, $end_date);

        $searchModel1 = new ExpensesSearch();
        $dataProvider1 = $searchModel1->search2(Yii::$app->request->queryParams, $start_date, $end_date);

        return $this->render('order_history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'searchModel1' => $searchModel1,
            'dataProvider1' => $dataProvider1,
        ]);
    }

    /**
     * Creates a new LossOfProfit model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new LossOfProfit();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Foyda va Zararni hisoblash",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                $post = Yii::$app->request->post();
                $date_end = $post['LossOfProfit']['date_end'];
                $date_start = $post['LossOfProfit']['date_start'];
                // $orderAccount = OrderAccountHistory::find()->where(['between', 'date', date('Y-m-d',strtotime($date_start)), date('Y-m-d',strtotime($date_end))])->all();
                $orderAccount = OrderAccountHistory::find()
                        ->where(['between', 'date', date('Y-m-d', strtotime($date_start)), date('Y-m-d', strtotime($date_end))])
                        ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
                        ->all();

                $all_summ = 0;
                foreach($orderAccount as $value) {
                    $all_summ = $all_summ + $value['all_profit_dollar'];
                }
                
                $expenses = Expenses::find()->where(['between', 'date_cr', date('Y-m-d',strtotime($date_start)), date('Y-m-d',strtotime($date_end))])->all();
                $all_summ_xaraj = 0;
                foreach($expenses as $value) {
                    $all_summ_xaraj = $all_summ_xaraj + $value['summa'];
                }
                $model->date_end =date('Y-m-d',strtotime($date_end));
                $model->date_start =date('Y-m-d',strtotime($date_start));

                $all_summ_foyda = $all_summ - $all_summ_xaraj;
                if ((float)$all_summ_foyda < 0) {
                    $model->loss =$all_summ_foyda;
                    $model->profit = 0;
                }else{
                    $model->loss = 0;
                    $model->profit =$all_summ_foyda;
                }
                $model->save();

                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];        
            }else{           
                return [
                    'title'=> "Foyda va Zararni hisoblash",
                    'content'=>$this->renderAjax('create', [
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
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing LossOfProfit model.
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
     * Delete an existing LossOfProfit model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
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
     * Delete multiple existing LossOfProfit model.
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
     * Finds the LossOfProfit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LossOfProfit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LossOfProfit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
