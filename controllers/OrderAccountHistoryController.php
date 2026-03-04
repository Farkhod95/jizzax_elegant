<?php

namespace app\controllers;

use Yii;
use app\models\OrderAccountHistory;
use app\models\OrderAccountHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\filters\AccessControl;
use app\models\ProductAccountHistory;
use app\models\ProductAccount;
use app\models\OrderAccount;
use app\models\Orders;
use app\models\OrderProducts;
use app\models\Client;
use app\models\Brands;
use yii\helpers\Url;
use app\models\DebtRepayment;
use app\models\ExchangeRate;
use app\models\ProductCategory;
use app\models\TypeSklad;
use app\models\PriceProduct;
use app\models\ElegantHistoryUpdate;
use app\models\Prices;
use app\models\Warehouse;
use app\models\KeshbekHistory;
use yii\db\Expression;
use yii\data\ArrayDataProvider;

/**
 * OrderAccountHistoryController implements the CRUD actions for OrderAccountHistory model.
 */
class OrderAccountHistoryController extends Controller
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
     * Lists all OrderAccountHistory models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new OrderAccountHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexDeptor()
    {    
        $searchModel = new OrderAccountHistorySearch();
        $dataProvider = $searchModel->searchDeptor(Yii::$app->request->queryParams);

        return $this->render('index_deptor', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionClientList($q = '', $type = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = \app\models\Client::find()
            ->select(['id', 'fio']);

        if (!empty($type)) {
            $query->andWhere(['type' => (int)$type]);
        }

        // q bo‘lsa qidiradi, bo‘lmasa ham 50 ta qaytaradi
        if ($q !== null && $q !== '') {
            $query->andWhere(['like', 'fio', $q]);
        }

        $rows = $query
            ->orderBy(['id' => SORT_ASC])
            ->limit(200)
            ->asArray()
            ->all();

        $results = [];
        foreach ($rows as $r) {
            $results[] = ['id' => $r['id'], 'text' => $r['fio']];
        }

        return ['results' => $results];
    }

    /**
     * [YANGI] Brend bo‘yicha kategoriyalar ro‘yxati
     * GET: brand_id
     * Return: { ok: true, categories: [{id, text}, ...] }
     */
    public function actionCategoriesByBrand($brand_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $rows = ProductCategory::find()
            ->select(['id', 'name'])
            ->where(['brand_id' => (int)$brand_id, 'sup_status' => 1])
            ->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
            ->asArray()
            ->all();

        // PHP 7.4- bo‘lsa fn() emas, oddiy function()
        $categories = array_map(function ($r) {
            return ['id' => (int)$r['id'], 'text' => $r['name']];
        }, $rows);

        return ['ok' => true, 'categories' => $categories];
    }

    public function actionSizesTypesByCategory($brand_id, $category_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $brand_id    = (int)$brand_id;
        $category_id = (int)$category_id;

        if (!$brand_id || !$category_id) {
            return ['ok' => true, 'sizes' => [], 'types' => []];
        }

        // Asosiy so'rov: brand+category bo'yicha
        $sizes = \app\models\BrandsSize::find()
            ->select('size')
            ->where(['brand_id' => $brand_id, 'product_category_id' => $category_id])
            ->distinct()
            ->orderBy(['size' => SORT_ASC])
            ->column();

        // Agar topilmasa — faqat kategoriya bo‘yicha fallback (xohlasangiz o'chiring)
        if (empty($sizes)) {
            $sizes = \app\models\BrandsSize::find()
                ->select('size')
                ->where(['product_category_id' => $category_id])
                ->distinct()
                ->orderBy(['size' => SORT_ASC])
                ->column();
        }

        $typeIds = \app\models\BrandsSize::find()
            ->select('type')
            ->where(['brand_id' => $brand_id, 'product_category_id' => $category_id])
            ->distinct()
            ->column();

        $types = array_map(function ($tid) {
            $tid = (int)$tid;
            return ['id' => $tid, 'text' => \app\models\ProductCategory::getTypeView($tid)];
        }, $typeIds ?: []);

        return [
            'ok'    => true,
            'sizes' => array_values(array_unique(array_map('strval', $sizes ?: []))),
            'types' => $types,
        ];
    }

    public function actionTypesBySize($brand_id, $category_id, $size)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $brand_id    = (int)$brand_id;
        $category_id = (int)$category_id;

        // size ni normalize qilamiz (vergul -> nuqta)
        $sizeStr = str_replace(',', '.', trim((string)$size));
        $sizeNum = (float)$sizeStr;

        if (!$brand_id || !$category_id || $sizeStr === '') {
            return ['ok' => true, 'types' => []];
        }

        // DOUBLE tenglikdagi muammolarni chetlash uchun epsilon bilan BETWEEN ishlatamiz
        $eps = 0.000001;

        $typeIds = \app\models\BrandsSize::find()
            ->select('type')
            ->where([
                'brand_id'            => $brand_id,
                'product_category_id' => $category_id,
            ])
            ->andWhere(['between', 'size', $sizeNum - $eps, $sizeNum + $eps])
            ->distinct()
            ->column();

        $types = array_map(function ($tid) {
            $tid = (int)$tid;
            return ['id' => $tid, 'text' => \app\models\ProductCategory::getTypeView($tid)];
        }, $typeIds ?: []);

        return ['ok' => true, 'types' => $types];
    }
    
    public function actionTrashO()
    {    
        $searchModel = new OrderAccountHistorySearch();
        $dataProvider = $searchModel->searchTrash(Yii::$app->request->queryParams);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionOrderAndDebt()
    {
        $selectedFio = Yii::$app->request->get('customer_name');
        $startDate = Yii::$app->request->get('start_date');
        $endDate = Yii::$app->request->get('end_date');

        $clients = \app\models\Client::find()->orderBy(['id' => SORT_ASC])->all();

        // 1. Buyurtmalar (OrderAccountHistory)
        $orderQuery = \app\models\OrderAccountHistory::find()
            ->select([
                'id',
                'sum_som',
                'sum_cart',
                'sum_transfers',
                'cr_date_time AS datetime',
                'client_id',

                'created_by',              // ✅ qo‘shildi
                'all_profit_dollar',       // ✅ qo‘shildi

                'all_product_sum',
                'all_summ_dollar',
                'total_debt',

                new \yii\db\Expression('0 AS debt_sum_som'),
                new \yii\db\Expression('0 AS debt_summ_cart'),
                new \yii\db\Expression('0 AS debt_sum_transfers'),

                new \yii\db\Expression('0 AS sum_paid_som'),
                new \yii\db\Expression('0 AS sum_paid_cart'),
                new \yii\db\Expression('0 AS sum_paid_transfers'),
                new \yii\db\Expression('0 AS paid_debt'),

                new \yii\db\Expression("'Buyurtma qilgan' AS action")
            ])
            ->andWhere(['<>', 'is_delete', 1])
            ->andWhere(['or',
                ['!=', 'is_worker', 1],
                ['is', 'is_worker', null]
            ]);

        if ($startDate && $endDate) {
            // Agar cr_date_time datetime bo‘lsa, date input bilan to‘liq qamrab olish uchun:
            $orderQuery->andWhere(['between', 'cr_date_time', $startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $orders = $orderQuery->asArray()->all();

        // 2. Qarz to‘lovlari (DebtRepayment)
        $repaymentQuery = \app\models\DebtRepayment::find()
            ->select([
                'id',
                'sum_som AS debt_sum_som',
                'summ_cart AS debt_summ_cart',
                'sum_transfers AS debt_sum_transfers',
                'cr_date_time AS datetime',
                'client_id',

                'created_by', // ✅ agar DebtRepayment jadvalida bo‘lsa; bo‘lmasa pastdagi 0 expression bilan almashtiring

                new \yii\db\Expression('0 AS sum_som'),
                new \yii\db\Expression('0 AS sum_cart'),
                new \yii\db\Expression('0 AS sum_transfers'),
                new \yii\db\Expression('0 AS all_product_sum'),
                new \yii\db\Expression('0 AS all_summ_dollar'),

                new \yii\db\Expression('0 AS all_profit_dollar'), // ✅ repaymentda foyda yo‘q

                'total_debt',
                'summ_dollar AS paid_debt',
                new \yii\db\Expression("'Qarz to‘lagan' AS action")
            ])
            ->where(['<>', 'is_delete', 1])
            ->andWhere(['or',
                ['!=', 'is_worker', 1],
                ['is', 'is_worker', null]
            ]);

        if ($startDate && $endDate) {
            $repaymentQuery->andWhere(['between', 'cr_date_time', $startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $repayments = $repaymentQuery->asArray()->all();

        // 3. Birlash
        $rows = array_merge($orders, $repayments);

        // 4. Saralash
        usort($rows, function ($a, $b) {
            return strtotime($b['datetime']) <=> strtotime($a['datetime']);
        });

        // 5. client_id lar asosida mijozlarni olish
        $clientIds = array_unique(array_column($rows, 'client_id'));

        $clientsMap = \app\models\Client::find()
            ->where(['id' => $clientIds])
            ->indexBy('id')
            ->all();

        foreach ($rows as &$row) {
            $client = $clientsMap[$row['client_id']] ?? null;
            $row['client_name'] = $client ? $client->fio : 'Nomaʼlum mijoz';
        }
        unset($row);

        // ✅ 6. created_by userlarni olish va map qilish (name + surname)
        $createdByIds = array_unique(array_filter(array_column($rows, 'created_by')));

        $usersMap = \app\models\Users::find()
            ->where(['id' => $createdByIds])
            ->indexBy('id')
            ->all();

        foreach ($rows as &$row) {
            $u = (!empty($row['created_by']) && isset($usersMap[$row['created_by']]))
                ? $usersMap[$row['created_by']]
                : null;

            $row['created_by_name'] = $u
                ? trim(($u->name ?? '') . ' ' . ($u->surname ?? ''))
                : '-';
        }
        unset($row);

        // 7. Mijoz ismi bo‘yicha filter
        if (!empty($selectedFio)) {
            $rows = array_filter($rows, function ($row) use ($selectedFio) {
                return $row['client_name'] === $selectedFio;
            });
        }

        // 8. Provider
        $provider = new \yii\data\ArrayDataProvider([
            'allModels' => array_values($rows),
            'pagination' => ['pageSize' => 100],
        ]);

        return $this->render('order_and_debt', [
            'provider' => $provider,
            'clients' => $clients,
            'selectedFio' => $selectedFio,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    // public function actionOrderAndDebt()
    // {
    //     $selectedFio = Yii::$app->request->get('customer_name');
    //     $startDate = Yii::$app->request->get('start_date');
    //     $endDate = Yii::$app->request->get('end_date');

    //     $clients = \app\models\Client::find()->orderBy(['id' => SORT_ASC])->all();

    //     $orders = \app\models\OrderAccountHistory::find()
    //         ->select([
    //             'id',
    //             'date AS datetime',
    //             'client_id',
    //             'all_product_sum',
    //             'all_summ_dollar',
    //             'total_debt',
    //             new \yii\db\Expression('0 AS paid_debt'),
    //             new \yii\db\Expression("'Buyurtma qilgan' AS action")
    //         ])
    //         ->asArray()
    //         ->all();

    //     $repayments = \app\models\DebtRepayment::find()
    //         ->select([
    //             'id',
    //             'date AS datetime',
    //             'client_id',
    //             new \yii\db\Expression('0 AS all_product_sum'),
    //             new \yii\db\Expression('0 AS all_summ_dollar'),
    //             'total_debt',
    //             'summ_dollar AS paid_debt',
    //             new \yii\db\Expression("'Qarz to‘lagan' AS action")
    //         ])
    //         ->asArray()
    //         ->all();

    //     // Sana bo‘yicha filter
    //     if ($startDate && $endDate) {
    //         $orders = array_filter($orders, function($row) use ($startDate, $endDate) {
    //             return $row['datetime'] >= $startDate && $row['datetime'] <= $endDate;
    //         });
        
    //         $repayments = array_filter($repayments, function($row) use ($startDate, $endDate) {
    //             return $row['datetime'] >= $startDate && $row['datetime'] <= $endDate;
    //         });
    //     }
        

    //     $rows = array_merge($orders, $repayments);

    //     usort($rows, function ($a, $b) {
    //         return strtotime($b['datetime']) <=> strtotime($a['datetime']);
    //     });

    //     foreach ($rows as &$row) {
    //         $client = \app\models\Client::findOne($row['client_id']);
    //         $row['client_name'] = $client ? $client->fio : 'Nomaʼlum mijoz';
    //     }

    //     if (!empty($selectedFio)) {
    //         $rows = array_filter($rows, function ($row) use ($selectedFio) {
    //             return $row['client_name'] === $selectedFio;
    //         });
    //     }

    //     $provider = new \yii\data\ArrayDataProvider([
    //         'allModels' => array_values($rows),
    //         'pagination' => ['pageSize' => 100],
    //     ]);

    //     return $this->render('order_and_debt', [
    //         'provider' => $provider,
    //         'clients' => $clients,
    //         'selectedFio' => $selectedFio,
    //         'startDate' => $startDate,
    //         'endDate' => $endDate,
    //     ]);
    // }

    public function actionExportPdf($customer_name = null, $start_date = null, $end_date = null)
    {
        // 1) Orders (OrderAccountHistory)
        $orderQuery = \app\models\OrderAccountHistory::find()
            ->select([
                'id',
                'cr_date_time AS datetime',
                'client_id',
                'created_by',              // ✅ qo‘shildi
                'all_profit_dollar',       // ✅ qo‘shildi

                'all_product_sum',
                'all_summ_dollar',
                'total_debt',
                'sum_som',
                'sum_cart',
                'sum_transfers',

                new \yii\db\Expression('0 AS debt_sum_som'),
                new \yii\db\Expression('0 AS debt_summ_cart'),
                new \yii\db\Expression('0 AS debt_sum_transfers'),
                new \yii\db\Expression('0 AS paid_debt'),

                new \yii\db\Expression("'Buyurtma qilgan' AS action")
            ])
            ->where(['or',
                ['!=', 'is_worker', 1],
                ['is', 'is_worker', null]
            ])
            ->andWhere(['<>', 'is_delete', 1]);

        // 2) Repayments (DebtRepayment)
        $repaymentQuery = \app\models\DebtRepayment::find()
            ->select([
                'id',
                'cr_date_time AS datetime',
                'client_id',

                // ✅ Agar DebtRepayment jadvalida created_by bo‘lsa, shuni qoldiring:
                'created_by',
                // ✅ Agar yo‘q bo‘lsa, mana buni ishlating:
                // new \yii\db\Expression('0 AS created_by'),

                new \yii\db\Expression('0 AS all_profit_dollar'), // ✅ repaymentda foyda yo‘q

                new \yii\db\Expression('0 AS all_product_sum'),
                new \yii\db\Expression('0 AS all_summ_dollar'),
                'total_debt',

                new \yii\db\Expression('0 AS sum_som'),
                new \yii\db\Expression('0 AS sum_cart'),
                new \yii\db\Expression('0 AS sum_transfers'),

                'sum_som AS debt_sum_som',
                'summ_cart AS debt_summ_cart',
                'sum_transfers AS debt_sum_transfers',
                'summ_dollar AS paid_debt',

                new \yii\db\Expression("'Qarz to‘lagan' AS action")
            ])
            ->where(['or',
                ['!=', 'is_worker', 1],
                ['is', 'is_worker', null]
            ])
            ->andWhere(['<>', 'is_delete', 1]);

        // ✅ Sana filtri: query darajasida (to‘g‘ri)
        if ($start_date && $end_date) {
            $from = $start_date . ' 00:00:00';
            $to   = $end_date . ' 23:59:59';

            $orderQuery->andWhere(['between', 'cr_date_time', $from, $to]);
            $repaymentQuery->andWhere(['between', 'cr_date_time', $from, $to]);
        }

        $orders = $orderQuery->asArray()->all();
        $repayments = $repaymentQuery->asArray()->all();

        // 3) Merge
        $rows = array_merge($orders, $repayments);

        // 4) client_name uchun clients map
        $clientIds = array_unique(array_filter(array_column($rows, 'client_id')));
        $clientsMap = \app\models\Client::find()
            ->where(['id' => $clientIds])
            ->indexBy('id')
            ->all();

        foreach ($rows as &$row) {
            $client = $clientsMap[$row['client_id']] ?? null;
            $row['client_name'] = $client ? $client->fio : 'Nomaʼlum mijoz';
        }
        unset($row);

        // 5) created_by_name uchun users map
        $createdByIds = array_unique(array_filter(array_column($rows, 'created_by')));
        $usersMap = \app\models\Users::find()
            ->where(['id' => $createdByIds])
            ->indexBy('id')
            ->all();

        foreach ($rows as &$row) {
            $u = (!empty($row['created_by']) && isset($usersMap[$row['created_by']]))
                ? $usersMap[$row['created_by']]
                : null;

            $row['created_by_name'] = $u
                ? trim(($u->name ?? '') . ' ' . ($u->surname ?? ''))
                : '-';
        }
        unset($row);

        // 6) Mijoz bo‘yicha filter
        if (!empty($customer_name)) {
            $rows = array_filter($rows, function ($r) use ($customer_name) {
                return isset($r['client_name']) && $r['client_name'] === $customer_name;
            });
            $rows = array_values($rows);
        }

        // 7) Sort
        usort($rows, function ($a, $b) {
            return strtotime($b['datetime']) <=> strtotime($a['datetime']);
        });

        // 8) PDF HTML
        ini_set('pcre.backtrack_limit', '10000000');

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => 'L',
        ]);

        $html = $this->renderPartial('_pdf_export', ['rows' => $rows]);

        // katta html bo‘lsa bo‘laklab yozamiz
        foreach (str_split($html, 50000) as $chunk) {
            $mpdf->WriteHTML($chunk);
        }

        $mpdf->Output('Buyurtmalar_va_Qarzlar.pdf', 'I');
    }

    public function actionClientHistory()
    {    
        return $this->render('client_order_history');
    }

    public function actionClientProductHistory()
    {    
        $request = Yii::$app->request;
        $client_id = (int)$request->post('client_id');
        $start_date = $request->post('start_date');
        $end_date = $request->post('end_date');
        // echo '<pre style="margin-left:300px; margin-top:200px">';
        // print_r($client_id);
        // echo '</pre>';
        $client = Client::findOne($client_id);
        if (!$client) {
            Yii::$app->session->setFlash('error', 'Mijoz topilmadi');
            return $this->redirect(['client-history']);
        }
        // $orders = OrderAccountHistory::find()->andWhere(['client_id' => $client_id->id ])->andWhere(['between', 'cr_date', $start_date, $end_date])->select(['cr_date', 'id'])->orderBy(['cr_date'=>SORT_DESC])->all();
        $orders = OrderAccountHistory::find()
            ->where(['client_id' => $client->id])
            ->andWhere(['between', 'cr_date', $start_date, $end_date])
            ->andWhere(['or',
                ['!=', 'is_worker', 1],
                ['is', 'is_worker', null]
            ])
            ->select(['cr_date', 'id'])
            ->orderBy(['cr_date' => SORT_DESC])
            ->all();
        $debtRepayments = DebtRepayment::find()
                ->where(['client_id' => $client->id])
                ->andWhere(['between', 'date', $start_date, $end_date])
                ->andWhere(['or',
                    ['!=', 'is_worker', 1],
                    ['is', 'is_worker', null]
                ])
                ->select(['all_summ_dollar', 'date', 'id'])
                ->orderBy(['date' => SORT_DESC])
                ->all();


        return $this->render('client_history_export', ['orders' => $orders, 'debtRepayments' => $debtRepayments, 'customer_fio' => $client->fio, 'client_id' => $client->id, 'start_date' => $start_date, 'end_date' => $end_date]);
    }
    public function actionClientExportView($order_id, $cr_date, $customer_fio, $client_id, $link="client-product-history", $start_date, $end_date)
    {    
        $orderProducts = ProductAccountHistory::find()->where(['order_account_history_id' => $order_id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('export_view_client', ['orderProducts' => $orderProducts, 'cr_date' => $cr_date, 'customer_fio' => $customer_fio, 'client_id' => $client_id, 'order_id' => $order_id, 'link' => $link, 'start_date' => $start_date, 'end_date' => $end_date]);
    }

    public function actionDayOrders()
    {
        $request = Yii::$app->request;

        $start_date = $request->post('start_date') ?: date('Y-m-d');
        $end_date   = $request->post('end_date')   ?: date('Y-m-d');

        return $this->render('day_orders', [
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ]);
    }

    public function actionDayOrdersHistory()
    {
        $request = Yii::$app->request;
        $start_date = $request->post('start_date') ?: date('Y-m-d');
        $end_date   = $request->post('end_date')   ?: date('Y-m-d');

        // datetime oralig'i (cr_date_time uchun)
        $startDt = $start_date . ' 00:00:00';
        $endDt   = $end_date   . ' 23:59:59';

        // Kurs: odatda oxirgisi kerak bo'ladi (id DESC)
        $exchangeRate = ExchangeRate::find()->orderBy(['id' => SORT_DESC])->one();
        if (!$exchangeRate || (float)$exchangeRate->dollar <= 0) {
            // fallback (siz xohlasangiz exception qiling)
            $exchangeRate = (object)['dollar' => 1];
        }

        /**
         * 1) OrderAccountHistory: faqat kerakli joylarni SUM() bilan DBdan olamiz
         *    E'tibor: sizda oldin ->where(['between','date', ...]) edi.
         *    Tezlik + aniqlik uchun cr_date_time oralig'ini ishlatamiz.
         */
        $ordersQuery = OrderAccountHistory::find()
            ->andWhere(['between', 'cr_date_time', $startDt, $endDt])
            ->andWhere(['or', ['is_delete' => null], ['<>', 'is_delete', 1]])
            ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]]);

        // DB aggregate (foreach yo'q)
        $orderAgg = $ordersQuery->select([
            'all_product_sum'   => 'COALESCE(SUM(all_product_sum),0)',
            'all_summ_dollar'   => 'COALESCE(SUM(all_summ_dollar),0)',
            'discount_amount'   => 'COALESCE(SUM(discount_amount),0)',
            'sum_dollar'        => 'COALESCE(SUM(sum_dollar),0)',
            'sum_som'           => 'COALESCE(SUM(sum_som),0)',
            'dollar_sumda'      => 'COALESCE(SUM(dollar_sumda),0)',
            'sum_cart'          => 'COALESCE(SUM(sum_cart),0)',
            'sum_transfers'     => 'COALESCE(SUM(sum_transfers),0)',
            'zdacha_sum'        => 'COALESCE(SUM(zdacha_sum),0)',
            'zdacha_dollar'     => 'COALESCE(SUM(zdacha_dollar),0)',
        ])->asArray()->one();

        $all_product_sum  = (float)$orderAgg['all_product_sum'];
        $all_summ_dollar  = (float)$orderAgg['all_summ_dollar'];
        $discount_amount  = (float)$orderAgg['discount_amount'];
        $sum_dollar       = (float)$orderAgg['sum_dollar'];
        $sum_som          = (float)$orderAgg['sum_som'];
        $dollar_sumda     = (float)$orderAgg['dollar_sumda'];
        $sum_cart         = (float)$orderAgg['sum_cart'];
        $sum_transfers    = (float)$orderAgg['sum_transfers'];
        $zdacha_sum       = (float)$orderAgg['zdacha_sum'];
        $zdacha_dollar    = (float)$orderAgg['zdacha_dollar'];

        // Sizning logikangiz
        $total_debt = $all_product_sum - $all_summ_dollar;

        /**
         * 2) Sotilgan mahsulotlar ro'yxati (sizda viewda kommentga olingan)
         *    Agar kerak bo'lsa - bitta query bilan order_id larni olib,
         *    keyin ProductAccountHistory dan grouped (N+1 emas).
         */
        $orderIds = $ordersQuery->select('id')->column();

        $array_Products = [];
        if (!empty($orderIds)) {
            // brand nomini tez olish uchun
            $brandsModel = new Brands();

            // har bir order bo'yicha brand lar ro'yxati
            $rows = ProductAccountHistory::find()
                ->select([
                    'order_account_history_id',
                    'brand_id',
                    'cr_date' => 'MIN(cr_date)', // siz groupBy(['cr_date']) qilgansiz, bu yerda soddalashtirdik
                ])
                ->where(['order_account_history_id' => $orderIds])
                ->groupBy(['order_account_history_id', 'brand_id'])
                ->asArray()
                ->all();

            foreach ($rows as $r) {
                $array_Products[] = [
                    'brand_id' => (int)$r['brand_id'],
                    'brand_name' => $brandsModel->getBrands($r['brand_id']),
                    'order_account_history_id' => (int)$r['order_account_history_id'],
                    'cr_date' => $r['cr_date'],
                ];
            }
        }

        /**
         * 3) DebtRepayment: faqat cr_date_time oralig'i bo'yicha
         *    N+1 bo'lmasin: client ni eager-load qilamiz
         */
        $debtQuery = DebtRepayment::find()
            ->with(['client'])
            ->andWhere(['between', 'date', $startDt, $endDt])
            ->andWhere(['or', ['is_delete' => null], ['<>', 'is_delete', 1]])
            ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
            ->orderBy(['date' => SORT_DESC]); // date emas, real datetime

        $debtRepayments = $debtQuery->all();

        // Debt aggregate DBdan
        $debtAgg = (clone $debtQuery)->select([
            'debt_sum_dollars'     => 'COALESCE(SUM(summ_dollar),0)',
            'debt_sum_soms'        => 'COALESCE(SUM(sum_som),0)',
            'debt_sum_carts'       => 'COALESCE(SUM(summ_cart),0)',
            'debt_sum_transferss'  => 'COALESCE(SUM(sum_transfers),0)',
            'all_summ_dollars'  => 'COALESCE(SUM(all_summ_dollar),0)',
            'all_summ_zdacha_sum'  => 'COALESCE(SUM(zdacha_sum),0)',
            'all_summ_zdacha_dollar'  => 'COALESCE(SUM(zdacha_dollar),0)',
        ])->asArray()->one();

        $debt_sum_dollars    = (float)$debtAgg['debt_sum_dollars'];
        $debt_sum_soms       = (float)$debtAgg['debt_sum_soms'];
        $debt_sum_carts      = (float)$debtAgg['debt_sum_carts'];
        $debt_sum_transferss = (float)$debtAgg['debt_sum_transferss'];
        $debt_sum_all_dollars = (float)$debtAgg['all_summ_dollars'];
        $debt_sum_all_zdacha_sum = (float)$debtAgg['all_summ_zdacha_sum'];
        $debt_sum_all_zdacha_dollar = (float)$debtAgg['all_summ_zdacha_dollar'];

        $debt_sum_soms_all       = $debt_sum_soms  - $debt_sum_all_zdacha_sum;
        // $rep_total_debt = $debt_sum_dollars
        //     + ($debt_sum_soms / (float)$exchangeRate->dollar)
        //     + ($debt_sum_carts / (float)$exchangeRate->dollar)
        //     + ($debt_sum_transferss / (float)$exchangeRate->dollar)
        //     - ($debt_sum_all_zdacha_dollar / (float)$exchangeRate->dollar);
        return $this->render('day_orders_history', [
            'array_Products'      => $array_Products,
            'start_date'          => $start_date,
            'end_date'            => $end_date,

            'all_product_sum'     => $all_product_sum,
            'all_summ_dollar'     => $all_summ_dollar,
            'discount_amount'     => $discount_amount,
            'total_debt'          => $total_debt,

            'sum_dollar'          => $sum_dollar,
            'sum_som'             => $sum_som,
            'dollar_sumda'        => $dollar_sumda,
            'sum_cart'            => $sum_cart,
            'sum_transfers'       => $sum_transfers,
            'zdacha_sum'          => $zdacha_sum,
            'zdacha_dollar'       => $zdacha_dollar,

            'debtRepayments'      => $debtRepayments,
            'exchangeRate'        => $exchangeRate,

            'debt_sum_dollars'    => $debt_sum_dollars,
            'debt_sum_soms'       => $debt_sum_soms,
            'debt_sum_carts'      => $debt_sum_carts,
            'debt_sum_transferss' => $debt_sum_transferss,
            'debt_sum_all_zdacha_sum' => $debt_sum_all_zdacha_sum,
            'debt_sum_all_zdacha_dollar' => $debt_sum_all_zdacha_dollar,

            'debt_sum_soms_all'       => $debt_sum_soms_all,

            'rep_total_debt'      => $debt_sum_all_dollars,
            
        ]);
    }

    
    public function actionClientOrders($customer_fio, $start_date, $end_date)
    {    
        $client_id = Client::find()->where(['fio' => $customer_fio])->one();
        // $orders = OrderAccountHistory::find()->andWhere(['client_id' => $client_id->id ])->andWhere(['between', 'cr_date', $start_date, $end_date])->all();
        $orders = OrderAccountHistory::find()
            ->where(['client_id' => $client_id->id])
            ->andWhere(['between', 'cr_date', $start_date, $end_date])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->all();

        $brands = new Brands();
        
        $array_Products = [];
        foreach($orders as $val) {
            $orderProducts = ProductAccountHistory::find()->where(['order_account_history_id' => $val['id']])->select(['brand_id'])->groupBy(['cr_date'])->all();
            foreach($orderProducts as $value) {
                $array_Products []= [
                    'brand_id' => $value['brand_id'],
                    'brand_name' => $brands->getBrands($value['brand_id']),
                    'order_account_history_id' => $val['id'],
                    'cr_date' => $val['cr_date_time'],
                ];
            }
            
        }
        
        
        return $this->render('client_orders', ['array_Products' => $array_Products, 'customer_fio' => $customer_fio]);
    }
    public function actionClient($start_date = '', $end_date = '')
    {    

        function group_by($key, $data) {
            $result = array();
        
            foreach($data as $val) {
                if(array_key_exists($key, $val)){
                    if(array_key_exists($val[$key], $result)){
                      $result[$val[$key]]['count'] += $val['count'];
                    }else{
                      $result[$val[$key]] = $val;
                    }
                }else{
                    $result[""][] = $val;
                }
            }
        
            return $result;
        }

        $request = Yii::$app->request;
        $start_date = $request->post('start_date');
        $end_date = $request->post('end_date');

        // echo '<pre style="margin-left:300px; margin-top:200px">';
        // print_r($start_date);
        // print_r($end_date);
        // echo '</pre>';
        if (!$start_date) {
            $start_date = date('Y-m-d');
        }
        if (!$end_date) {
            $end_date = date('Y-m-d');
        }

        // $orders = OrderAccountHistory::find()->where(['between', 'cr_date', $start_date, $end_date ])->all();
        $orders = OrderAccountHistory::find()
            ->where(['between', 'cr_date', $start_date, $end_date])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->all();

        // echo '<pre style="margin-left:300px; margin-top:200px">';
        // print_r($orders);
        // echo '</pre>';
        $ordersArray = array();
        foreach ($orders as $model) {
            $orderProducts = ProductAccount::find()->where(['order_account_history_id' => $model->id])->all();
            $orderCount = 0;
            foreach ($orderProducts as $orderProduct) {
                $orderCount = $orderCount + $orderProduct->count;
            }
            $ordersArray [] = [
                'fio' => $model->client->fio, 
                'count' => $orderCount, 
                'date' => date("d.m.Y", strtotime($model->cr_date)),
            ];
        }
        $html_td = '';
        $index = 1;
       
        $data = group_by("fio", $ordersArray);

        // usort($data, fn($a, $b) => $b['count'] <=> $a['count']);
        $countAll = 0;
        foreach ($data as $model) {
            $html_td .='<tr class="handle" >
                    <td style="background-color:#efdfdf;"><b>' . $index . '</b></td>
                    <td style="background-color:#efdfdf;"><b><a href='. Url::toRoute(['order-account-history/client-orders', 'customer_fio' => $model['fio'], 'start_date' => $start_date, 'end_date' => $end_date]).' class="alert-link" style="font-size: 14px;">'. $model['fio'] . '</a></b></td>
                    <td style="background-color:#efdfdf;"><b>' . $model['count'] . '</b></td>
                </tr>
                ';
            $index = $index + 1;
            $countAll = $countAll + $model['count'];
        }
        
        
        $html =  '<table class="table">
        <thead>
            <tr>
                <th  style="background-color:#90e6e6;" ><b>#</b></th>
                <th  style="background-color:#90e6e6;" nowrap><b>FIO</b></th>
                <th  style="background-color:#90e6e6;" nowrap><b>Karobka soni</b></th>
            </tr>
        </thead>
        <tbody data-count="0" data-increment="0">
            '.$html_td.'
            <tr>
                <td colspan="2" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                <td style="background-color:#2d353c;"><b style="color:white">'. $countAll . '</b></td>
            </tr>
        </tbody>
    </table>';
    
        return $this->render('client_history', ['html' => $html, 'start_date' => $start_date, 'end_date' => $end_date ]);
    }
    /**
     * Displays a single OrderAccountHistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $type)
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
                'type' => $type,
            ]);
        }
    }
    
    public function actionExport()
    {    
        // $orders = OrderAccountHistory::find()->select(['cr_date'])->groupBy(['cr_date'])->orderBy(['cr_date'=>SORT_DESC])->all();
        $orders = OrderAccountHistory::find()
            ->select(['cr_date'])
            ->where(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->groupBy(['cr_date'])
            ->orderBy(['cr_date' => SORT_DESC])
            ->all();

        return $this->render('export', ['orders' => $orders]);
    }

    public function actionExportOrder($cr_date)
    {    
        // $orders = OrderAccountHistory::find()->where(['cr_date' => $cr_date])->orderBy(['cr_date'=>SORT_DESC])->all();
        $orders = OrderAccountHistory::find()
            ->where(['cr_date' => $cr_date])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->orderBy(['cr_date' => SORT_DESC])
            ->all();

        $sum_dollars = 0;
        $sum_soms = 0;
        $dollar_sumdas = 0;
        $dollar_sumda = 0;
        $sum_carts = 0;
        $sum_transferss = 0;
        foreach ($orders as $value) {
            $sum_dollars = $sum_dollars + $value['sum_dollar'];
            $sum_soms = $sum_soms + $value['sum_som'];
            $dollar_sumdas = $dollar_sumdas + $value['dollar_sumda'];
            $dollar_sumda = $dollar_sumda + $value['dollar_sumda'];
            $sum_carts= $sum_carts + $value['sum_cart'];
            $sum_transferss = $sum_transferss + $value['sum_transfers'];
        }

        $exchangeRate = ExchangeRate::find()->orderBy(['id' => SORT_ASC])->one();
        $debtRepayments = DebtRepayment::find()
            ->where(['date' => $cr_date])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
            ->orderBy(['date' => SORT_DESC])
            ->all();

        $debt_sum_dollars = 0;
        $debt_sum_soms = 0;
        $debt_sum_carts = 0;
        $debt_sum_transferss = 0;
        $rep_total_debt = 0;
        foreach ($debtRepayments as $value) {
            $debt_sum_dollars = $debt_sum_dollars + $value['summ_dollar'];
            $debt_sum_soms = $debt_sum_soms + $value['sum_som'];
            $debt_sum_carts= $debt_sum_carts + $value['summ_cart'];
            $debt_sum_transferss = $debt_sum_transferss + $value['sum_transfers'];
            $rep_total_debt = $debt_sum_dollars + $debt_sum_soms/$exchangeRate->dollar + $debt_sum_carts/$exchangeRate->dollar + $debt_sum_transferss/$exchangeRate->dollar;
        }

        return $this->render('export_order', [
            'orders' => $orders, 
            'cr_date' => $cr_date, 

            'sum_dollars' => $sum_dollars, 
            'sum_soms' => $sum_soms, 
            'dollar_sumdas' => $dollar_sumdas, 
            'dollar_sumda' => $dollar_sumda, 
            'sum_carts' => $sum_carts, 
            'sum_transferss' => $sum_transferss, 

            'debtRepayments' => $debtRepayments, 

            'debt_sum_dollars' => $debt_sum_dollars, 
            'debt_sum_soms' => $debt_sum_soms, 
            'debt_sum_carts' => $debt_sum_carts, 
            'debt_sum_transferss' => $debt_sum_transferss, 
            'rep_total_debt' => $rep_total_debt, 

            'exchangeRate' => $exchangeRate
        ]);
    }
    
    public function actionExportView($order_id, $cr_date, $customer_fio, $link="client-history")
    {    
        $orderProducts = ProductAccountHistory::find()->where(['order_account_history_id' => $order_id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        return $this->render('export_view', ['orderProducts' => $orderProducts, 'cr_date' => $cr_date, 'customer_fio' => $customer_fio, 'order_id' => $order_id, 'link' => $link]);
    }
    
    public function actionProducts($id, $type)
    {    
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('products', ['warehouse' => $warehouse, 'order_id' => $id, 'type'=> $type]);
    }

    public function actionProductsTrash($id)
    {    
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        // $warehouses = Warehouse::find()->all();
        return $this->render('products_trash', ['warehouse' => $warehouse, 'order_id' => $id]);
    }

    public function actionPrintSklad($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlTextSklad($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("ClientOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }

    public function actionPrintHistoryClient($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlTextHistoryClient($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("ClientOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
    }

    public function actionPrintDateClient($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
        $mpdf->WriteHTML($model->getInvoiceHtmlTextDateClient($model, $id));
        
        //call watermark content and image
        $mpdf->SetWatermarkText('');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;

        //save the file put which location you need folder/filname
        $mpdf->Output("ClientOrderList.pdf", 'F');
        //out put in browser below output function
        $mpdf->Output();
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

    public function actionPrintProfit($id)
    {
        $model = $this->findModel($id);
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L',]);
        $mpdf->WriteHTML($model->getProfitInvoiceHtmlText($model, $id));
        
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
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',]);
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
    /**
     * Creates a new OrderAccountHistory model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new OrderAccountHistory();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new OrderAccountHistory",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new OrderAccountHistory",
                    'content'=>'<span class="text-success">Create OrderAccountHistory success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new OrderAccountHistory",
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
     * Updates an existing OrderAccountHistory model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $type)
    {
        $model = $this->findModel($id); 
        $orderAccount = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $client_total_debt = $orderAccount->total_debt;

        $client_id = $model->client_id;
        $order_account_status_old = $model->order_account_status;
        $date_old = $model->date;

        $exchange_rate_old = $model->exchange_rate;
        $sum_dollar_old = $model->sum_dollar;
        $discount_amounts_old = $model->discount_amount;
        
        $total_debt_old = $model->total_debt_old;
        $all_profit_dollar_old = $model->all_profit_dollar;

        $all_product_sum_old = $model->all_product_sum;
        $all_pay_summ_old = round($sum_dollar_old + $discount_amounts_old, 2);
        $all_product_qolgan_sum_old = $all_product_sum_old - $all_pay_summ_old;
        $client = Client::find()->where(['id' => $client_id])->one();
        if ($model->load(Yii::$app->request->post())) {
            $updateReason = Yii::$app->request->post('OrderAccountHistory')['comment'];
            $driverInfo = Yii::$app->request->post('OrderAccountHistory')['driver_info'];
            $fastOrder = Yii::$app->request->post('OrderAccountHistory')['fast_order'];
            $total_debts_new = Yii::$app->request->post('OrderAccountHistory')['total_debt_old']?? 0;
            $dates_new = Yii::$app->request->post('OrderAccountHistory')['date'];
            $exchange_rates_new = Yii::$app->request->post('OrderAccountHistory')['exchange_rate']?? 0;
            $discount_amounts_new = Yii::$app->request->post('OrderAccountHistory')['discount_amount']?? 0;
            $sum_dollars_new = Yii::$app->request->post('OrderAccountHistory')['sum_dollar']?? 0;
            $tasdiq_check_new = isset(Yii::$app->request->post('OrderAccountHistory')['order_account_status']) ? Yii::$app->request->post('OrderAccountHistory')['order_account_status'] : 1;

            $productAccountHistory = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->all();
            foreach ($productAccountHistory as $value) {
                $warehouseValue = Warehouse::find()
                                    ->andWhere(['product_category_id' => $value->product_category_id])
                                    ->andWhere(['brand_id' => $value->brand_id])
                                    ->andWhere(['size' => $value->size])
                                    ->andWhere(['type' => $value->type])->one();
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
            $discount_amounts_new = is_numeric($discount_amounts_new) ? $discount_amounts_new : 0;
            $all_pay_summ_new = round($sum_dollars_new, 2);
            
            // OrderAccountHistory malumotlarini yangilash 
            
            $model->client_id = $client_id;
            $model->date = date('Y-m-d',strtotime($dates_new));
            $model->exchange_rate = $exchange_rates_new;
            $model->all_summ_dollar = $all_pay_summ_new;
            $model->number_of_orders = 1;

            $model->discount_amount = $discount_amounts_new;
            $model->sum_dollar = $sum_dollars_new;
            $model->cr_date = date('Y-m-d',strtotime($dates_new));
            // $model->cr_date_time = date('Y-m-d H:i:s',strtotime($dates_new.' '.date('H:i:s')));
            $model->update_status = 2; // update_status= 2 bo'lsa o'zgarish bo'lgan lekin tasdiqlanmagan bo'ladi
            $model->save(false);
            
            $all_profit_new = 0;
            $all_profit_array_new = [];
            $all_product_summ_new = 0;
            $status_order_dukon = 0;
            $status_order_sklad = 0;
            $hasLargePriceNew = false;

            $allValues_new = Yii::$app->request->post('OrderAccountHistory')['allValue'] ?? null;

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

                    if ($value['type_sklad_id'] == 1) {
                        $status_order_sklad = 1;
                    } elseif ($value['type_sklad_id'] == 2) {
                        $status_order_dukon = 1;
                    }

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
                        $relative->order_account_history_id = $model->id;
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
                    $relativeHistory->order_account_history_id = $model->id;
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


            $model->status_order_dukon = $status_order_dukon;
            $model->status_order_sklad = $status_order_sklad;

            // YANGI: large_prise flagini yozish (1/0)
            $model->large_price = $hasLargePriceNew ? 1 : 0;
            $model->driver_info = $driverInfo;
            $model->fast_order = $fastOrder;
            $model->save(false);
            if ($model->order_account_status == 1) {
                 // Eski ma'lumotlarni tozalash
                //  if ($orderAccount->total_debt !=0) {
                $orderAccount->number_of_orders = $orderAccount->number_of_orders - 1;
                $orderAccount->all_summ_dollar = round($orderAccount->all_summ_dollar - $all_pay_summ_old, 2);
                $orderAccount->discount_amount = $orderAccount->discount_amount - (float)$discount_amounts_old;
                $orderAccount->sum_dollar = $orderAccount->sum_dollar - (float)$sum_dollar_old;
                $orderAccount->all_product_sum = $orderAccount->all_product_sum - $all_product_sum_old;
                $orderAccount->total_debt = $client_total_debt - ($all_product_sum_old - $all_pay_summ_old);
                $orderAccount->total_debt_old = $client_total_debt - ($all_product_sum_old - $all_pay_summ_old);
                if ($client->is_profit_loss == 1) {
                    $orderAccount->all_profit_dollar = 0;
                }else{
                    $orderAccount->all_profit_dollar = $orderAccount->all_profit_dollar - $all_profit_dollar_old;
                }
                
                $orderAccount->save(false);
                //  }
                
                // Yangi ma'lumotlarni qo'shish
                $orderAccount->last_order_date = date('Y-m-d',strtotime($dates_new));
                $orderAccount->exchange_rate = $exchange_rates_new;
                $orderAccount->number_of_orders = $orderAccount->number_of_orders + 1;
                $orderAccount->all_summ_dollar = round($orderAccount->all_summ_dollar + $all_pay_summ_new, 2);
                $orderAccount->discount_amount = $orderAccount->discount_amount + (float)$discount_amounts_new;
                $orderAccount->sum_dollar = $orderAccount->sum_dollar + (float)$sum_dollars_new;
                if ($client->is_profit_loss == 1) {
                    $orderAccount->all_profit_dollar = 0;
                }else{
                    $orderAccount->all_profit_dollar = round($orderAccount->all_profit_dollar + array_sum($all_profit_array_new), 2);
                }
                $orderAccount->all_product_sum = round($orderAccount->all_product_sum + $all_product_summ_new, 2);
                $orderAccount->total_debt_old =$client_total_debt - ($all_product_sum_old - $all_pay_summ_old);
                // $orderAccount->total_debt = round($orderAccount->total_debt + ($all_product_summ_new - ($all_pay_summ_new + $discount_amounts_new)), 2);
                $orderAccount->total_debt = $orderAccount->total_debt + ($all_product_summ_new - ($all_pay_summ_new + $discount_amounts_new));
                // $orderAccount->total_debt = $total_debt_old + ($all_product_summ_new - ($all_pay_summ_new + $discount_amounts_new));
                // $orderAccount->total_debt = $orderAccount->total_debt_old + ($all_product_summ_new - ($all_pay_summ_new + $discount_amounts_new));
                $orderAccount->save(false);
                if ($client->is_profit_loss == 1) {
                    $model->all_profit_dollar = 0;
                }else{
                    $model->all_profit_dollar = round(array_sum($all_profit_array_new), 2);
                }
                
                $model->total_debt_today = round(($all_product_summ_new - ($all_pay_summ_new + $discount_amounts_new)), 2);
                $model->total_debt = $total_debt_old + ($all_product_summ_new - ($model->all_summ_dollar + $model->discount_amount));
                $model->total_debt_old = $total_debt_old;
                $model->all_product_sum = $all_product_summ_new;
                $model->order_account_status = $tasdiq_check_new;
                $model->save(false);

                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->client->fio . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi buyurtmasi o'zgartirildi...";
                $elegantHistoryUpdate->comment = $updateReason . ' <br><b style="color:#e97171">' . 'Ostatka: '. $model->total_debt_old .'$, '. 'Mahsulot summasi: '. ($all_product_summ_new).'$, '. 'To\'langan summa: '. ($model->all_summ_dollar + $model->discount_amount) .'$, '. 'Qolgan qarz: '. $model->total_debt.'$ </b>';
                $elegantHistoryUpdate->status = 1;
                $elegantHistoryUpdate->type = 2;
                $elegantHistoryUpdate->order_account_history_id = $model->id;
                $elegantHistoryUpdate->save(false);

                $previousDebt = $model->total_debt;

                // // Har bir sana uchun bir marta DebtRepayment summasini hisoblash
                // $debtRepaymentsByDate = DebtRepayment::find()
                //     ->where(['client_id' => $model->client_id])
                //     ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                //     ->groupBy('date')
                //     ->select(['date', 'SUM(all_summ_dollar) AS total_repayment'])
                //     ->asArray()
                //     ->all();


                // $debtRepaymentsMap = array_column($debtRepaymentsByDate, 'total_repayment', 'date'); // ['2024-11-18' => 1000]

                // // Buyurtmalarni qayta hisoblash
                // $nextOrders = OrderAccountHistory::find()
                //     ->where(['client_id' => $model->client_id])
                //     ->andWhere([
                //         'or',
                //         ['>', 'date', $model->date],
                //         ['and', ['date' => $model->date], ['>', 'id', $model->id]]
                //     ])->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                //     ->orderBy(['date' => SORT_ASC, 'id' => SORT_ASC])
                //     ->all();

                
                
                // foreach ($nextOrders as $nextOrder) {
                //     // echo "<pre>";
                //     // print_r($nextOrder->client_id);
                //     // print_r($nextOrder->date);
                //     // echo "<pre>";
                //     if (isset($debtRepaymentsMap[$nextOrder->date])) {
                //         $value = $debtRepaymentsMap[$nextOrder->date]; // Qiymatni olish
                //         unset($debtRepaymentsMap[$nextOrder->date]); // Elementni o‘chirish
                //         // echo "O'chirilgan sana: $nextOrder->date, qiymat: $value\n";
                //     } else {
                //         $value = 0;
                //         // echo "Sana $nextOrder->date mavjud emas.\n";
                //     }

                //     $nextOrder->total_debt_old = $previousDebt;
                //     $nextOrder->total_debt_today = $nextOrder->all_product_sum - $nextOrder->all_summ_dollar - $nextOrder->discount_amount;
                //     $nextOrder->total_debt = $nextOrder->total_debt_old + $nextOrder->total_debt_today - $value;

                //     // Yangilangan total_debt ni keyingi buyurtma uchun saqlang
                //     $previousDebt = $nextOrder->total_debt;
                //     $nextOrder->save();
                // }
                $nextOrders = OrderAccountHistory::find()
                    ->where(['client_id' => $model->client_id])
                    ->andWhere([
                        'or',
                        ['>', 'date', $model->date],
                        ['and', ['date' => $model->date], ['>', 'id', $model->id]]
                    ])
                    ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                    ->orderBy(['date' => SORT_ASC, 'id' => SORT_ASC])
                    ->all();

                foreach ($nextOrders as $nextOrder) {

                    // Shu buyurtma vaqtidan KEYIN yaratilgan DebtRepayment lar yig'indisi
                    $summaDebt = (float) DebtRepayment::find()
                        ->where(['client_id' => $model->client_id])
                        ->andWhere(['or', ['!=', 'is_worker', 1], ['is', 'is_worker', null]])
                        ->andWhere(['>', 'cr_date_time', $nextOrder->cr_date_time]) // qat'iy > (katta bo'lgan)
                        ->sum('all_summ_dollar');

                    // hech narsa topilmasa null qaytadi — 0 ga tenglashtiramiz
                    $summaDebt = $summaDebt ?: 0.0;

                    $nextOrder->total_debt_old = $previousDebt;
                    $nextOrder->total_debt_today = round(
                        (float)$nextOrder->all_product_sum - ((float)$nextOrder->all_summ_dollar + (float)$nextOrder->discount_amount),
                        2
                    );
                    $nextOrder->total_debt = round(
                        $nextOrder->total_debt_old + $nextOrder->total_debt_today - $summaDebt,
                        2
                    );

                    // Keyingi buyurtma uchun yuguruvchi qarz
                    $previousDebt = $nextOrder->total_debt;
                    $nextOrder->save(false);
                }


                $client = Client::findOne($client_id);

                if ($client && $client->keshbek) {

                    $keshbekClient = KeshbekHistory::find()
                        ->where(['order_account_history_id' => $model->id])
                        ->one();

                    // Agar oldin keshbek yozuvi bo'lmasa – yangisini yaratamiz
                    if ($keshbekClient === null) {
                        $keshbekClient = new KeshbekHistory();
                        $keshbekClient->client_id = $client_id;
                        $keshbekClient->order_account_history_id = $model->id;
                        $keshbekClient->keshbek = $client->keshbek; // foiz
                    }

                    $keshbekClient->keshbek_sum = round(($all_product_summ_new * $keshbekClient->keshbek) / 100, 2);
                    $keshbekClient->cr_date = date('Y-m-d', strtotime($dates_new));
                    $keshbekClient->save(false);
                }
            }else{
                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $model->client->fio . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi buyurtmasi o'zgartirildi...";
                $elegantHistoryUpdate->comment = $updateReason;
                $elegantHistoryUpdate->status = 1;
                $elegantHistoryUpdate->type = 2;
                $elegantHistoryUpdate->order_account_history_id = $model->id;
                $elegantHistoryUpdate->save(false);
            }
            Yii::$app->session->setFlash('success', 'Ma\'lumotlar muvaffaqiyatli yangilandi.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model, 'client_total_debt' => $model->total_debt_old?$model->total_debt_old:0, 'order_account_status_old'=> $order_account_status_old, 'type' => $type]);
    }


    public function actionUpdate1($id)
    {
        $model = $this->findModel($id);
        $client_id = $model->client_id;
        
        $allValues = $post['OrderAccountHistory']['allValue'];
        
        $orderAccount = OrderAccount::find()->where(['client_id' => $client_id])->one();
        $client_total_debt = $orderAccount->total_debt;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->save();
            $orderAccount->total_debt = $model->total_debt_old;
            $orderAccount->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'client_total_debt' => $client_total_debt
        ]);
    }

    public function actionUpdateAjax($id)
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
                    'title'=> "Update OrderAccountHistory #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "OrderAccountHistory #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update OrderAccountHistory #".$id,
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
     * Delete an existing OrderAccountHistory model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionUpdateStatus($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $latestRecord = ElegantHistoryUpdate::find()
                                            ->where(['order_account_history_id' => $model->id])
                                            ->orderBy(['id' => SORT_DESC])  // id bo'yicha kamayish tartibida saralash
                                            ->one();
        $commnet = " ";                                    
        if ($latestRecord) {
            $commnet = $latestRecord->comment;
        }

        // Safely retrieve 'comment' from the POST data if it exists
        $updateData = Yii::$app->request->post('OrderAccountHistory');
        $updateReason = isset($updateData['comment']) ? $updateData['comment'] : null;

        
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> 'Tasdiqlaysizmi ?',
                    'content'=>$this->renderAjax('check', [
                        'model' => $model,
                        'check' => "check_1",
                        'commnet' => $commnet,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Tasdiqlash',['class'=>'btn btn-success','type'=>"submit"])
                ];         
            }else if($updateReason){
                $model->update_status = 1;
                $model->save(false);

                // // ===================================================================>
                
                // // $priceProducts = PriceProduct::find()->all();
                // // foreach ($priceProducts as $priceProduct) {
                // //     // Warehouse jadvaliga yangi yozuv qo'shish
                // //     $warehouse = new Warehouse();
                // //     // $warehouse->id = $priceProduct->id;
                // //     $warehouse->brand_id = $priceProduct->brand_id;
                // //     $warehouse->product_category_id = $priceProduct->product_category_id;
                // //     $warehouse->size = $priceProduct->size;
                // //     $warehouse->price = $priceProduct->real_price;
                // //     $warehouse->count = 0;
                // //     $warehouse->cr_date = $priceProduct->cr_date;
                // //     $warehouse->type = $priceProduct->type;
                    
                // //     // Qo'shimcha ustunlarga qiymat berish
                // //     $warehouse->created_by = 1; // Misol uchun, admin deb belgilayapmiz
                // //     $warehouse->update_by = 1;
                // //     $warehouse->consignor_id = 16; // Yuk jo'natuvchi ID sini 1 deb belgilayapmiz
                // //     $warehouse->all_sum_dollar = 0; // Boshlang'ich qiymat
                // //     $warehouse->all_discount_amount = 0; // Boshlang'ich qiymat
                // //     $warehouse->all_my_total_debt = 0; // Boshlang'ich qiymat
                // //     $warehouse->comment = 'PriceProduct dan kiritildi';

                // //     // Warehouse jadvaliga saqlash
                // //     $warehouse->save(false);
                // // }
                
                // // ===================================================================>

                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];    
            }
        }
    }

    public function actionOrderCheckDokon($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id); 
        $model->status_order_dukon = 2;
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

    public function actionOrderCheckSklad($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id); 
        $model->status_order_sklad = 2;
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
    
    public function actionOneDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        $orderAccountHistory = OrderAccountHistory::find()->where(['id' => $id])->one();
        $orderAccount = OrderAccount::find()->where(['client_id' => $orderAccountHistory->client_id])->one();

        $postData = Yii::$app->request->post('OrderAccountHistory');
        $valueReason = isset($postData['comment']) ? $postData['comment'] : "No Data";
        $client = Client::find()->where(['id' => $orderAccountHistory->client_id])->one();
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> '<div style="text-align:center"><b style="font-size:16px;color:#707478">Haqiqatdan ham</b> <b style="font-size:18px;color:red">'.$model->client->fio.'</b> <b style="font-size:16px;color:#707478">ning buyurtmasini oʻchirib tashlamoqchimisiz?</b></div>',
                    'content'=>$this->renderAjax('delete_form', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Yopish',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('O\'chirish',['class'=>'btn btn-danger','type'=>"submit"])
                ];         
            }else if($valueReason){
                if($orderAccount){
                    $orderAccount->number_of_orders = $orderAccount->number_of_orders - 1;
                    $orderAccount->all_summ_dollar = $orderAccount->all_summ_dollar - $orderAccountHistory->all_summ_dollar;
        
                    $orderAccount->discount_amount = $orderAccount->discount_amount - $orderAccountHistory->discount_amount;
                    $orderAccount->sum_dollar = $orderAccount->sum_dollar - $orderAccountHistory->sum_dollar;
                    $orderAccount->dollar_sumda = $orderAccount->dollar_sumda - $orderAccountHistory->dollar_sumda;
                    $orderAccount->sum_som = $orderAccount->sum_som - $orderAccountHistory->sum_som;
                    $orderAccount->sum_cart = $orderAccount->sum_cart - $orderAccountHistory->sum_cart;
                    $orderAccount->sum_transfers = $orderAccount->sum_transfers - $orderAccountHistory->sum_transfers;
        
                    $orderAccount->all_product_sum = $orderAccount->all_product_sum - $orderAccountHistory->all_product_sum;
                    $orderAccount->total_debt = $orderAccount->total_debt - $orderAccountHistory->total_debt_today;
                    if ($client->is_profit_loss == 1) {
                        $orderAccount->all_profit_dollar = 0;
                    }else{
                        $orderAccount->all_profit_dollar = $orderAccount->all_profit_dollar - $orderAccountHistory->all_profit_dollar;
                    }
                    $orderAccount->save(false);
                }
        
                $productAccountHistory = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->all();
                foreach ($productAccountHistory as $value) {
                    $warehouseValue = Warehouse::find()
                                    ->andWhere(['product_category_id' => $value->product_category_id])
                                    ->andWhere(['brand_id' => $value->brand_id])
                                    ->andWhere(['size' => $value->size])
                                    ->andWhere(['type' => $value->type])->one();
                    if($warehouseValue){
                        $warehouseValue->count = $warehouseValue->count + $value->count;
                        $warehouseValue->save(false);
                    }
                    ProductAccountHistory::find()->where(['id' => $value->id])->one()->delete();
                }
                $productAccount = ProductAccount::find()->where(['order_account_history_id' => $id])->all();
                foreach ($productAccount as $value) {
                    ProductAccount::find()->where(['id' => $value->id])->one()->delete();
                }
                $elegantHistoryUpdates = ElegantHistoryUpdate::find()->where(['order_account_history_id' => $id])->all();
                foreach ($elegantHistoryUpdates as $update) {
                    $update->order_account_history_id = null;
                    $update->save(false);
                }
        
                $elegantHistoryUpdate = new ElegantHistoryUpdate();
                $elegantHistoryUpdate->title = $orderAccountHistory->client->fio . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi buyurtmasi o'chirildi...";
                $elegantHistoryUpdate->comment = $valueReason;
                $elegantHistoryUpdate->status = 2;
                $elegantHistoryUpdate->type = 2;
                $elegantHistoryUpdate->save(false);

                $keshbek = KeshbekHistory::find()->where(['order_account_history_id' => $id])->one();
                if ($keshbek !== null) {
                    $keshbek->delete();
                }


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

    // public function actionTrash($id)
    // {
    //     $request = Yii::$app->request;
    //     $model = $this->findModel($id);
    //     $model->is_delete = 1;
    //     $model->save(false);
        
    //     // $this->findModel($id)->delete();
    //     if($request->isAjax){
    //         /*
    //         *   Process for ajax request
    //         */
    //         Yii::$app->response->format = Response::FORMAT_JSON;
    //         return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
    //     }else{
    //         /*
    //         *   Process for non-ajax request
    //         */
    //         return $this->redirect(['index']);
    //     }


    // }


    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $orderAccountHistory = OrderAccountHistory::find()->where(['id' => $id])->one();

        $orderAccount = OrderAccount::find()->where(['client_id' => $orderAccountHistory->client_id])->one();
        $client = Client::find()->where(['id' => $orderAccountHistory->client_id])->one();
        if($orderAccount){
            $orderAccount->number_of_orders = $orderAccount->number_of_orders - 1;
            $orderAccount->all_summ_dollar = $orderAccount->all_summ_dollar - $orderAccountHistory->all_summ_dollar;

            $orderAccount->discount_amount = $orderAccount->discount_amount - $orderAccountHistory->discount_amount;
            $orderAccount->sum_dollar = $orderAccount->sum_dollar - $orderAccountHistory->sum_dollar;
            $orderAccount->dollar_sumda = $orderAccount->dollar_sumda - $orderAccountHistory->dollar_sumda;
            $orderAccount->sum_som = $orderAccount->sum_som - $orderAccountHistory->sum_som;
            $orderAccount->sum_cart = $orderAccount->sum_cart - $orderAccountHistory->sum_cart;
            $orderAccount->sum_transfers = $orderAccount->sum_transfers - $orderAccountHistory->sum_transfers;

            $orderAccount->all_product_sum = $orderAccount->all_product_sum - $orderAccountHistory->all_product_sum;
            $orderAccount->total_debt = $orderAccount->total_debt - $orderAccountHistory->total_debt_today;
             if ($client->is_profit_loss == 1) {
                $orderAccount->all_profit_dollar = 0;
             }else{
                $orderAccount->all_profit_dollar = $orderAccount->all_profit_dollar - $orderAccountHistory->all_profit_dollar;
             }
            $orderAccount->save(false);
        }

        $productAccountHistory = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->all();
        foreach ($productAccountHistory as $value) {
            ProductAccountHistory::find()->where(['id' => $value['id']])->one()->delete();
        }
        $productAccount = ProductAccount::find()->where(['order_account_history_id' => $id])->all();
        foreach ($productAccount as $value) {
            ProductAccount::find()->where(['id' => $value['id']])->one()->delete();
        }
        $elegantHistoryUpdates = ElegantHistoryUpdate::find()->where(['order_account_history_id' => $id])->all();
        foreach ($elegantHistoryUpdates as $update) {
            $update->order_account_history_id = null;
            $update->save(false);
        }

        $deleteReason = Yii::$app->request->post('delete_reason');
        $elegantHistoryUpdate = new ElegantHistoryUpdate();
        $elegantHistoryUpdate->title = $orderAccountHistory->client->fio . " ning ". \Yii::$app->formatter->asDatetime(date('Y-m-d'), 'php:d.m.Y ') ." sanadagi buyurtmasi o'chirildi...";
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
     * Delete multiple existing OrderAccountHistory model.
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
     * Finds the OrderAccountHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderAccountHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderAccountHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
