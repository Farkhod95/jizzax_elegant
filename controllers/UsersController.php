<?php

namespace app\controllers;

use Faker\Factory;
use Yii;
use app\models\Users;
use app\models\searchs\UsersSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UploadedFile;
use app\models\Orders;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
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
                    // [
                    //     'actions' => ['index'],
                    //     'allow' => true,
                    //     'roles' => ['@'],
                    // ],
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
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function init()
    {
        parent::init();

        if (!Yii::$app->user->isGuest) {
            // Code to Set the last seen time for the user. For eg,
            $user = Users::findOne(Yii::$app->user->id);
            $user->last_seen = date('Y-m-d H:i:s');
            $user->save(false, ["last_seen"]);
        }
    }

    public function actionFaker(){
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
           $model = new Users();
           $model->surname = $faker->firstName;
           $model->name = $faker->name;
           $model->middle_name = $faker->lastName;
           $model->email = $faker->email;
           $model->login = $faker->email;
           $model->phone = $faker->phone;
        }

    }


    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     */
    public function actionProfile()
    {
        $id = Yii::$app->user->identity->id;
        return $this->render('profile', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Creates a new Users model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Users();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load($request->post()) && $model->save()){
                $model->image = UploadedFile::getInstance($model, 'image');
                if(isset($model->image )) $model->upload();
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
            }else{
                return [
                    'title'=> "Yaratish",
                    'size' => 'large',
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])

                ];
            }
        }

    }


    /**
     * Updates an existing Users model.
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
            if($model->load($request->post()) && $model->save()){
                $model->image = UploadedFile::getInstance($model, 'image');
                if(isset($model->image )) $model->upload();
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
            }else{
                return [
                    'title'=> "O'zgartirish",
                    'size' => 'large',
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }
    }


    /**
     * change profile
     * @return array|string|Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionChange()
    {

        $id = Yii::$app->user->identity->id;
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load($request->post()) && $model->save()){
                $model->image = UploadedFile::getInstance($model, 'image');
                if(isset($model->image )) $model->upload();
                return ['forceClose'=>true,'forceReload'=>'#profile-pjax'];
            }else{
                return [
                    'title'=> "O'zgartirish",
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
     * Delete an existing Users model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        if(file_exists('uploads/'.Users::DIR_IMAGE.'/'.$model->avatar )&&$model->avatar != null)
        {
            unlink('uploads/'.Users::DIR_IMAGE.'/'.$model->avatar );
        }
        $orders = Orders::find()->where(['created_by' => $id])->all();

        foreach ($orders as $order) {
            $order->created_by = NULL;
            $order->save();
        }
        $model->delete();

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
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
