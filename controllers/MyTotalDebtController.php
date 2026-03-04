<?php

namespace app\controllers;

use Yii;
use app\models\MyTotalDebt;
use app\models\MyTotalDebtSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\ElegantHistoryUpdate;
use app\models\MyTotalDebtHistory;
/**
 * MyTotalDebtController implements the CRUD actions for MyTotalDebt model.
 */
class MyTotalDebtController extends Controller
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
     * Lists all MyTotalDebt models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new MyTotalDebtSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single MyTotalDebt model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "MyTotalDebt #".$id,
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

    /**
     * Creates a new MyTotalDebt model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new MyTotalDebt();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Qo'shish",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('O\'zgartirish',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post())){
                $modelOld = MyTotalDebt::find()->where(['consignor_id' => $model->consignor_id])->one();
                if ($modelOld) {
                    $modelOld->total_debt = $modelOld->total_debt + $model->total_debt;
                    $modelOld->save();
                }else {
                    $model->save();
                }
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];       
            }else{           
                return [
                    'title'=> "Create new MyTotalDebt",
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
     * Updates an existing MyTotalDebt model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       
        $total_debt_old = $model->total_debt;

        $updateData = Yii::$app->request->post('MyTotalDebt');
        $valueReason = isset($updateData['total_debts']) ? $updateData['total_debts'] : null;
        $chegirma = isset($updateData['chegirma']) ? $updateData['chegirma'] : 0;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> 'Hozirgi qarzingiz <b style="font-size:16px;color:red">'. $model->total_debt. ' $</b>',
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Bekor qilish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('To\'lash',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                $myTotalDebtHistory = new MyTotalDebtHistory();
                $myTotalDebtHistory->all_summ_dollar = $valueReason;
                $myTotalDebtHistory->total_debt = $total_debt_old;
                $myTotalDebtHistory->cr_date = $model->cr_date;
                $myTotalDebtHistory->discount_amount = $chegirma;
                $myTotalDebtHistory->my_total_debt_id = $model->id;
                $myTotalDebtHistory->exchange_rate = $total_debt_old - ($valueReason + $chegirma);
                $myTotalDebtHistory->save(false);

                $model->total_debt = $total_debt_old - ($valueReason + $chegirma);
                $model->save(false);

                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];   
            }else{
                 return [
                    'title'=> "Update MyTotalDebt #".$id,
                    'content'=>$this->renderAjax('update', [
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
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing MyTotalDebt model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionOneDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $consignor_name = "";
        if ($model->consignor_id) {
            $consignor_name = $model->consignor->name;
        }
        $valueReason = Yii::$app->request->post('MyTotalDebt')['comment'];
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> '<div style="text-align:center"><b style="font-size:16px;color:red">'.$consignor_name.'</b> dan qarzingiz qolmadimi?</div>',
                    'content'=>$this->renderAjax('delete_form', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('O\'chirish',['class'=>'btn btn-danger','type'=>"submit"])
                ];         
            }else if($valueReason){
                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->consignor->name . " dan qarzingiz qolmadi, ". $model->total_debt ." $ ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada o'chirildi...";
                $elegantHistoryUpdate->comment = $valueReason;
                $elegantHistoryUpdate->status = 2;
                $elegantHistoryUpdate->type = 1;
                $elegantHistoryUpdate->save(false);
                MyTotalDebtHistory::deleteAll(['my_total_debt_id' => $id]);

                $this->findModel($id)->delete();
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];    
            }else{
                 return [
                    'title'=> '<div style="text-align:center"><b style="font-size:16px;color:#707478">Haqiqatdan ham</b> <b style="font-size:18px;color:red">'.$model->client->fio.'</b> <b style="font-size:16px;color:#707478">ning buyurtmasini oʻchirib tashlamoqchimisiz?</b></div><br/> <p style="font-size:14px;color:red;text-align:center">Izohni to\'ldiring...</p>',
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
        
        $deleteReason = Yii::$app->request->post('delete_reason');
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $model->consignor->name . " dan qarzingiz qolmadi, ". $model->total_debt ." $ ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada o'chirildi...";
        $elegantHistoryUpdate->comment = $deleteReason;
        $elegantHistoryUpdate->status = 2;
        $elegantHistoryUpdate->type = 1;
        $elegantHistoryUpdate->save(false);
        MyTotalDebtHistory::deleteAll(['my_total_debt_id' => $id]);

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
     * Delete multiple existing MyTotalDebt model.
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
     * Finds the MyTotalDebt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MyTotalDebt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MyTotalDebt::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
