<?php

namespace app\controllers;

use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\Regions;
use app\models\Districts;
use app\models\ExchangeRate;
use app\models\OrderAccount;
use yii\db\Transaction;
/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

   public function actionQuickCreate()
{
    $request = Yii::$app->request;
    $model = new Client();

    if (!$request->isAjax) {
        throw new \yii\web\BadRequestHttpException('Faqat AJAX so‘rov uchun.');
    }

    Yii::$app->response->format = Response::FORMAT_JSON;

    if ($request->isGet) {
        return [
            'title' => "Yangi mijoz qo'shish",
            'content' => $this->renderAjax('_quick_create', [
                'model' => $model,
            ]),
        ];
    }

    if ($model->load($request->post())) {
        $model->type = 1;
        $model->total_debt = 0;

        if ($model->validate()) {
            $model->save(false);

            $exchangeRate = ExchangeRate::find()->orderBy(['id' => SORT_ASC])->one();

            $orderAccountCr = new OrderAccount();
            $orderAccountCr->client_id = $model->id;
            $orderAccountCr->last_order_date = date('Y-m-d');
            $orderAccountCr->total_debt = 0;
            $orderAccountCr->date = date('Y-m-d');
            $orderAccountCr->exchange_rate = $exchangeRate ? $exchangeRate->dollar : 0;
            $orderAccountCr->number_of_orders = 1;
            $orderAccountCr->cr_date = date('Y-m-d');
            $orderAccountCr->cr_date_time = date('Y-m-d H:i:s');
            $orderAccountCr->save(false);

            return [
                'status' => 'success',
                'id' => $model->id,
                'fio' => $model->fio,
            ];
        }
    }

    return [
        'status' => 'error',
        'content' => $this->renderAjax('_quick_create', [
            'model' => $model,
        ]),
    ];
}

    public function actionClientKeshbekHisob($id)
    {
        $client = Client::findOne($id);

        // 1. POST bo‘lsa: hisobla va sessionga yoz
        if (Yii::$app->request->isPost) {
            $startDate = Yii::$app->request->post('start_date');
            $endDate = Yii::$app->request->post('end_date');

            $query = \app\models\OrderAccountHistory::find()
                ->where(['client_id' => $id])
                ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ]);


            if ($startDate && $endDate) {
                $query->andWhere(['between', 'cr_date', $startDate, $endDate]);
            }

            Yii::$app->session->setFlash('totalProductSum', $query->sum('all_product_sum'));
            Yii::$app->session->setFlash('totalSummDollar', $query->sum('all_summ_dollar'));
            Yii::$app->session->setFlash('startDate', $startDate);
            Yii::$app->session->setFlash('endDate', $endDate);

            return $this->redirect(['client/client-keshbek-hisob', 'id' => $id]);
        }

        // 2. GET bo‘lsa: sessiondan o‘qib ol
        $totalProductSum = Yii::$app->session->getFlash('totalProductSum');
        $totalSummDollar = Yii::$app->session->getFlash('totalSummDollar');
        $startDate = Yii::$app->session->getFlash('startDate');
        $endDate = Yii::$app->session->getFlash('endDate');

        return $this->render('client_keshbek_hisob', [
            'client_id' => $id,
            'clients' => $client,
            'totalProductSum' => $totalProductSum,
            'totalSummDollar' => $totalSummDollar,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Client #".$id,
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
    public function actionDistricts($id)
    {
        $datas = Regions::find()->where(['id' => $id])->one();
        $district = Districts::find()->where(['region_id' => $datas->id])->all();
        foreach ($district as $value) {
            echo "<option value = '".$value->id."'>".$value->name."</option>" ;
        }
    }
    /**
     * Creates a new Client model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateOne()
    {
        $request = Yii::$app->request;
        $model = new Client();

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($request->isGet) {
                return [
                    'title'   => "Mijoz qo'shish",
                    'content' => $this->renderAjax('create_one', [
                        'model' => $model,
                    ]),
                    'footer'  =>
                        Html::button('Yopish', [
                            'class' => 'btn btn-default pull-left',
                            'data-dismiss' => 'modal',
                        ]) .
                        Html::button('Saqlash', [
                            'class' => 'btn btn-primary',
                            'type'  => 'submit',
                        ]),
                ];
            }

            if ($model->load($request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
                try {
                    $model->save(false);

                    $exchangeRate = ExchangeRate::find()->orderBy(['id' => SORT_ASC])->one();

                    $orderAccountCr = new OrderAccount();
                    $orderAccountCr->client_id = $model->id;
                    $orderAccountCr->last_order_date = date('Y-m-d');
                    $orderAccountCr->total_debt = $model->total_debt ?: 0;
                    $orderAccountCr->date = date('Y-m-d');
                    $orderAccountCr->exchange_rate = $exchangeRate ? $exchangeRate->dollar : 0;
                    $orderAccountCr->number_of_orders = 1;
                    $orderAccountCr->cr_date = date('Y-m-d');
                    $orderAccountCr->cr_date_time = date('Y-m-d H:i:s');
                    $orderAccountCr->save(false);

                    $transaction->commit();

                    return [
                        'forceClose' => true,
                        'message'    => 'Mijoz muvaffaqiyatli qo‘shildi',
                    ];
                } catch (\Throwable $e) {
                    $transaction->rollBack();

                    return [
                        'title'   => "Mijoz qo'shish",
                        'content' => '<div class="alert alert-danger">' . $e->getMessage() . '</div>' .
                            $this->renderAjax('create_one', [
                                'model' => $model,
                            ]),
                        'footer'  =>
                            Html::button('Yopish', [
                                'class' => 'btn btn-default pull-left',
                                'data-dismiss' => 'modal',
                            ]) .
                            Html::button('Saqlash', [
                                'class' => 'btn btn-primary',
                                'type'  => 'submit',
                            ]),
                    ];
                }
            }

            return [
                'title'   => "Mijoz qo'shish",
                'content' => $this->renderAjax('create_one', [
                    'model' => $model,
                ]),
                'footer'  =>
                    Html::button('Yopish', [
                        'class' => 'btn btn-default pull-left',
                        'data-dismiss' => 'modal',
                    ]) .
                    Html::button('Saqlash', [
                        'class' => 'btn btn-primary',
                        'type'  => 'submit',
                    ]),
            ];
        }

        if ($model->load($request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create_one', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Client();  

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
                                Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->validate() && $model->save(false)){
                $model->save(false);

                $exchangeRate = ExchangeRate::find()->orderBy(['id' => SORT_ASC])->one();
                $orderAccountCr = new OrderAccount();
                $orderAccountCr->client_id = $model->id;
                $orderAccountCr->last_order_date = date('Y-m-d');
                $orderAccountCr->total_debt = $model->total_debt;
                $orderAccountCr->date = date('Y-m-d');
                $orderAccountCr->exchange_rate = $exchangeRate->dollar;
                $orderAccountCr->number_of_orders = 1;
                $orderAccountCr->cr_date = date('Y-m-d');
                $orderAccountCr->cr_date_time = date('Y-m-d H:i:s');
                $orderAccountCr->save(false);
                
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Qo'shish",
                    'content'=>'<span class="text-success">Model muvaffaqiyatini yaratildi</span>',
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Koʻproq yaratish',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Qo'shish",
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
            if ($model->load($request->post()) && $model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing Client model.
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
            }else if($model->load($request->post()) && $model->save(false)){
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
            if ($model->load($request->post()) && $model->save(false)) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Client model.
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
     * Delete multiple existing Client model.
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
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
