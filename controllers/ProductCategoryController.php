<?php

namespace app\controllers;

use Yii;
use app\models\ProductCategory;
use app\models\BrandsSize;
use app\models\ProductCategorySearch;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\models\ElegantHistoryUpdate;
/**
 * ProductCategoryController implements the CRUD actions for ProductCategory model.
 */
class ProductCategoryController extends Controller
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
     * Lists all ProductCategory models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new ProductCategorySearch(['sup_status' => 1]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // Select2 uchun: brand bo‘yicha kategoriyalar (id/text)
    public function actionCategoriesByBrand($brand_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $list = ProductCategory::find()
            ->select(['id', 'name'])
            ->where(['brand_id' => (int)$brand_id, 'sup_status' => 1])
            ->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
            ->asArray()
            ->all();

        $results = [];
        foreach ($list as $row) {
            $results[] = ['id' => (string)$row['id'], 'text' => $row['name']];
        }
        return ['results' => $results];
    }

    public function actionSortingAdvice($brand_id, $value = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $brandId = (int)$brand_id;
        $value = $value !== null ? (int)$value : null;

        // Shu branddagi mavjud sortinglar
        $existing = ProductCategory::find()
            ->select('sorting')
            ->where(['brand_id' => $brandId])
            ->orderBy(['sorting' => SORT_ASC])
            ->column();

        // Eng yaqin bo'sh raqam (1 dan boshlab birinchi "turtib ketgan" joy)
        $next = 1;
        foreach ($existing as $s) {
            if ((int)$s === $next) {
                $next++;
            } elseif ((int)$s > $next) {
                break;
            }
        }

        // Kiritilgan qiymatgacha (yoki mavjud max gacha) bo'shlar ro'yxati
        $max = empty($existing) ? 0 : max($existing);
        $limit = $value ? max($value - 1, $max) : $max;

        $existsSet = array_flip($existing);
        $missing = [];
        for ($i = 1; $i <= $limit; $i++) {
            if (!isset($existsSet[$i])) {
                $missing[] = $i;
            }
        }

        return [
            'existing'  => $existing,
            'next_free' => $next,
            'missing'   => $missing, // ko‘p bo‘lsa JSda oxirgi 5 tasinigina ko‘rsatamiz
        ];
    }
    /**
     * Displays a single ProductCategory model.
     * @param integer $id
     * @return mixed
     */
    public function actionSizeList($id)
    {
        $pc = ProductCategory::findOne($id);
        if (!$pc) { throw new NotFoundHttpException('Topilmadi'); }

        $items = BrandsSize::find()
            ->select(['size', 'type'])                 // ⬅️ type qo‘shildi
            ->where(['product_category_id' => $id])
            ->orderBy(['type' => SORT_ASC, 'size' => SORT_ASC])
            ->asArray()->all();

        $payload = ['items' => $items, 'pc' => $pc];

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title'   => "O‘lchamlar — {$pc->name}",
                'content' => $this->renderAjax('_size_list_simple', $payload),
                'footer'  => Html::button('Yopish', ['class'=>'btn btn-default','data-dismiss'=>'modal']),
            ];
        }

        return $this->render('size-list-simple', $payload);
    }

    public function actionByBrand($brand_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = \app\models\ProductCategory::find()
            ->select(['id','name'])
            ->where(['brand_id' => (int)$brand_id]);

        // FAQAT aktivlarni ko‘rsatmoqchi bo‘lsangiz, quyidagisini qoldiring:
        $q->andWhere(['sup_status' => 1]);

        $rows = $q->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
                ->asArray()->all();

        // PHP 7.3 uchun clo­sure:
        $results = array_map(function($r){
            return ['id' => $r['id'], 'text' => $r['name']];
        }, $rows);

        return ['results' => $results];
    }

    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "ProductCategory #".$id,
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
     * Creates a new ProductCategory model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ProductCategory();

        if ($request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($request->isGet) {
                return [
                    'title'   => "Yaratish",
                    'content' => $this->renderAjax('create', ['model' => $model]),
                    'footer'  => Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]) .
                                Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"]),
                ];
            }

            // Hech qanday echo/print_r YO'Q!
            if ($model->load($request->post()) && $model->save()) {

                // Qo‘shimcha maydonlar/holat
                $model->sup_status = 1;
                $model->save(false);

                // MultipleInput dan kelgan qiymatlar
                $allValues = Yii::$app->request->post('ProductCategory')['allValue'] ?? [];
             
                if (!empty($allValues)) {
                    foreach ($allValues as $row) {
                        $sizeVal = (float)$row['size'];
                        // "9,99" kabilarni ham qabul qilish uchun
                        $sizeVal = str_replace(',', '.', trim((string)$sizeVal));
                        if (!is_numeric($sizeVal)) {
                            continue; // yoki xatoga tashlang
                        }
                        $bs = new BrandsSize();
                        $bs->size = (float)$sizeVal;
                        $bs->type = (float)$row['type'];
                        $bs->brand_id = $model->brand_id;
                        $bs->product_category_id = $model->id;
                        $bs->save();
                       
                    }
                }
                
                // Modalni yopamiz va jadvalni yangilaymiz
                return [
                    'forceClose'  => true,
                    'forceReload' => '#crud-datatable-pjax',
                ];
            }

            // Validatsiya xatolari bo‘lsa – formani qayta chiqaramiz
            return [
                'title'   => "Yaratish",
                'content' => $this->renderAjax('create', ['model' => $model]),
                'footer'  => Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]) .
                            Html::button('Saqlash',['class'=>'btn btn-primary','type'=>"submit"]),
            ];
        }

        // Non-AJAX holat
        if ($model->load($request->post()) && $model->save()) {
            $model->sup_status = 1;
            $model->save(false);
            return $this->redirect(['view','id'=>$model->id]);
        }
        return $this->render('create', ['model'=>$model]);
    }


    /**
     * Updates an existing ProductCategory model.
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
                $model->sup_status = 1;
                $model->save(false);
                $allValues_new = Yii::$app->request->post('ProductCategory')['allValue'];
                // echo "<pre>";
                // print_r($allValues_new);
                // echo "<pre>";
                if ($allValues_new) {
                    BrandsSize::deleteAll(['product_category_id' => (int)$model->id]);
                    foreach ($allValues_new as $row) {
                        $bs = new BrandsSize();
                        $bs->size = (float)$row['size'];
                        $bs->type = (float)$row['type'];
                        $bs->brand_id = $model->brand_id;
                        $bs->product_category_id = $model->id;
                        if (!$bs->save()) {
                            throw new \Exception('BrandsSize xatolik: '.json_encode($bs->errors));
                        }
                    }
                }
                        

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
     * Delete an existing ProductCategory model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        // $this->findModel($id)->delete();
        $model = $this->findModel($id); 
        $model->sup_status = 0;
        $model->sorting = NULL;
        $model->save(false);
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $model->name. " nomli Mahsulot toifasi ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanada o'chirildi...";
        $elegantHistoryUpdate->comment = "";
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
     * Delete multiple existing ProductCategory model.
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
     * Finds the ProductCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
