<?php

namespace app\controllers;

use app\models\Users;
use Ratchet\App;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Warehouse;
use yii\helpers\Html;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
    	if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }

    	$usersCount = Users::find()->count();

        $end = date("Y-m-d", strtotime("+1 days", strtotime(date("Y-m-d"))));
        $begin = date("Y-m-d", strtotime("-7 days", strtotime($end)));

        return $this->render('index',[

            'usersCount' =>$usersCount,
        ]);
    }
    

    public function actionProductList()
    {
        return $this->render('product_list');
    }
    
    public function actionExport()
    {
        return $this->render('export');
    }

    public function actionImport()
    {
        return $this->render('import');
    }
    
    public function actionOrders()
    {
        return $this->render('orders');
    }
    // public function actionCalculator()
    // {
    //     $model = new ProductCategory();  
    //     return $this->render('calculator', ['model' => $model]);
    // }
    public function actionCalculator()
    {
        $model = new Warehouse();
        
        if ($model->load(Yii::$app->request->post()) ) {
            $post = Yii::$app->request->post();
            $contact = $post['Warehouse']['allValue'];
            
            foreach ($contact as $value) {
                // echo '<pre>';
                // print_r($value['count']);
                // echo '</pre>';
            }
           
        }

        return $this->render('calculator', [
            'model' => $model,
        ]);
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['login']);
    }

    public function actionAvtorizatsiya()
    {
      if(isset(Yii::$app->user->identity->id))
      {
        return $this->render('error');
      }        
       else
        {
            Yii::$app->user->logout();
            $this->redirect(['login']);
        }

    }

    public function actionCount($id)
    {
        echo "<option value = '".$id."</option>";
        // echo "<pre>";
        // print_r($id);
        // echo "</pre>";
        // $datas = Regions::find()->where(['id' => $id])->one();
        // $district = Districts::find()->where(['region_id' => $datas->id])->all();
        // foreach ($district as $value) {
        //     echo "<option value = '".$value->id."'>".$value->name."</option>" ;
        // }
    }

    public function actionSetThemeValues()
    {
        $session = Yii::$app->session;
        if (isset($_POST['sd_position'])) $session['sd_position'] = $_POST['sd_position'];

        if (isset($_POST['header_styling'])) $session['header_styling'] = $_POST['header_styling'];

        if (isset($_POST['sd_styling'])) $session['sd_styling'] = $_POST['sd_styling'];

        if (isset($_POST['cn_gradiyent'])) $session['cn_gradiyent'] = $_POST['cn_gradiyent'];

        if (isset($_POST['cn_style'])) $session['cn_style'] = $_POST['cn_style'];

        if (isset($_POST['boxed'])) $session['boxed'] = $_POST['boxed'];

    }

    public function actionSdPosition()
    {
        $session = Yii::$app->session;
        if($session['sd_position'] != null) return $session['sd_position'];
        else return 1;
    }

    public function actionHeaderStyling()
    {
        $session = Yii::$app->session;
        if($session['header_styling'] != null) return $session['header_styling'];
        else return 1;
    } 

    public function actionSdStyling()
    {
        $session = Yii::$app->session;
        if($session['sd_styling'] != null) return $session['sd_styling'];
        else return 1;
    } 

    public function actionCnGradiyent()
    {
        $session = Yii::$app->session;
        if($session['cn_gradiyent'] != null) return $session['cn_gradiyent'];
        else return 1;
    } 

    public function actionCnStyle()
    {
        $session = Yii::$app->session;
        if($session['cn_style'] != null) return $session['cn_style'];
        else return 1;
    } 
    public function actionBoxed()
    {
        $session = Yii::$app->session;
        if($session['boxed'] != null) return $session['boxed'];
        else return 1;
    } 

}
