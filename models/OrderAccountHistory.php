<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use app\models\KeshbekHistory;
use Yii;

/**
 * This is the model class for table "order_account_history".
 *
 * @property int $id
 * @property int $client_id client
 * @property string $date
 * @property double $exchange_rate dollar exchange rate
 * @property double $discount_amount сумма скидки
 * @property double $all_summ_dollar all_summ_dollar
 * @property double $all_profit_dollar all_profit_dollar
 * @property double $total_debt total debt
 * @property string $date_last_debt_payment
 * @property double $number_of_orders number_of_orders
 * @property string $last_order_date
 * @property double $sum_som sum_som
 * @property double $sum_dollar summ_dollar
 * @property double $sum_cart summ_cart
 * @property double $sum_transfers summ_transfers
 * @property int $created_by create user
 * @property string $cr_date
 *
 * @property Client $client
 * @property Users $createdBy
 * @property ProductAccount[] $productAccounts
 */
class OrderAccountHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_account_history';
    }

    /**
     * {@inheritdoc}
     */
    var $all_total_debt_sum = null;
    var $clients_id = null;
    var $sum_all_pro = null;
    var $comment = '';
    var $status_order ='';
    public $day_seq;

    public function rules()
    {
        return [
            [['client_id', 'created_by', 'update_status', 'status_order_dukon', 'status_order_sklad', 'is_debt', 'is_delete', 'is_worker', 'large_price', 'fast_order', 'is_debtor'], 'integer'],
            [['order_account_status'], 'boolean'],
            [['date', 'date_last_debt_payment', 'last_order_date', 'cr_date', 'cr_date_time', 'order_commit', 'driver_info', 'day_seq'], 'safe'],
            [['exchange_rate', 'all_product_sum', 'discount_amount', 'all_summ_dollar', 'all_profit_dollar', 'total_debt', 'total_debt_today','dollar_sumda', 'all_total_debt_sum', 'total_debt_old', 'number_of_orders', 'sum_som', 'sum_dollar', 'sum_cart', 'sum_transfers', 'zdacha_sum', 'zdacha_dollar'], 'number'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['created_by' => 'id']],
            // [['comment'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Mijoz',
            'is_worker' => 'Ishchilar zakazi',
            'clients_id' => 'Mijoz',
            'date' => 'Sana',
            'is_debt' => 'Qarz to\'laganmi?',
            'is_delete' => 'O\'chirilsinmi?',
            'all_product_sum' => 'To\'lanadigan summa ($)',
            'exchange_rate' => 'Dollar kursi',
            'discount_amount' => 'Jami chegirma ($)',
            'all_summ_dollar' => 'To\'langan summa ($)',
            'all_profit_dollar' => 'Jami foyda ($)',
            'total_debt' => 'Bugungi umumiy qarz ($)',
            'total_debt_today' => 'Bugungi qarz ($)',
            'all_total_debt_sum' => 'Umumiy qolgan qarz ($)',
            'total_debt_old' => 'Eski Qarz ($)',
            'date_last_debt_payment' => 'Oxirgi qarzni to\'lash sanasi',
            'number_of_orders' => 'Buyurtmalar soni',
            'last_order_date' => 'Oxirgi buyurtma sanasi',
            'sum_som' => 'Summa so\'mda',
            'sum_dollar' => 'Summa dollarda ($)',
            'sum_cart' => 'Summa kartada',
            'dollar_sumda' => 'So\'m dollarda',
            'sum_transfers' => 'Summa transferda',
            'created_by' => 'Kim buyurtma oldi',
            'cr_date' => 'Yaratilgan vaqt',
            'cr_date_time' => 'Yaratilgan vaqt',
            'sum_all_pro' => '',
            'order_account_status' => '',
            'order_commit' => 'Buyurtma uchun izoh',
            'comment' => 'Izoh',
            'update_status' => 'O\'zgarish holati',
            'status_order_dukon' => 'Do\'konda buyurtma xolati',
            'status_order_sklad' => 'Skladda buyurtma xolati',
            'status_order' => 'Buyurtma holati',
            'large_price' => 'Narx faqri',
            'driver_info' => 'Haydovchi ma\'lumotlari',
            'fast_order' => '',
            'is_debtor' => 'Mahsulotdan qarzdorlik',
            'zdacha_sum' => 'Qaytim so\'mda',
            'zdacha_dollar' => 'Qaytim',
        ];
    }

    public function beforeSave($insert)
    {   
        // $this->cr_date = date('Y-m-d H:i:s');
        // $this->cr_date_time = date('Y-m-d H:i:s');
        if ($this->isNewRecord){
            $this->created_by = Yii::$app->user->identity->id;
            $this->is_debt = 0;
            $this->is_delete = 0;
            $this->is_worker = 0;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getStatusOrderDukon()
    {
        return ArrayHelper::map([
            ['id' => '1', 'status_order_dukon' => 'Tayyorlanmoqda',],
            ['id' => '2', 'status_order_dukon' => 'Tayyor',],
        ],
        'id', 'status_order_dukon');
    }

    public function getStatusOrderDukonView($id)
    {
        if($id == 1) return 'Tayyorlanmoqda';
        if($id == 2) return 'Tayyor';
    }

    public function getStatusOrderSklad()
    {
        return ArrayHelper::map([
            ['id' => '1', 'status_order_sklad' => 'Tayyorlanmoqda',],
            ['id' => '2', 'status_order_sklad' => 'Tayyor',],
        ],
        'id', 'status_order_sklad');
    }

    public function getStatusOrderSkladView($id)
    {
        if($id == 1) return 'Tayyorlanmoqda';
        if($id == 2) return 'Tayyor';
    }

    public function getOrderAccountStatus()
    {
        return ArrayHelper::map([
            ['id' => '1', 'order_account_status' => 'Tasdiqlangan',],
            ['id' => '2', 'order_account_status' => 'Tasdiqlanmagan',],
        ],
        'id', 'order_account_status');
    }

    public function getOrderAccountStatusView($id)
    {
        if($id == 1) return 'Tasdiqlangan';
        if($id == 2) return 'Tasdiqlanmagan';
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    public function getClients()
    {
        return ArrayHelper::map(Client::find()->all(), 'id', 'fio');
    }

    public function getTypeSklads()
    {
        return ArrayHelper::map(TypeSklad::find()->all(), 'id', 'name');
    }

    public function getType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Dona',],
            ['id' => '2', 'type' => 'Karobka',],
            ['id' => '3', 'type' => 'Komplekt',],
            ['id' => '4', 'type' => 'Pochka',],
        ],
        'id', 'type');
    }

    public function getBrands()
    {
        return ArrayHelper::map(Brands::find()->where(['sup_status' => 1])->all(), 'id', 'name');
    }

    public function getProductCategories()
    {
        return ArrayHelper::map(ProductCategory::find()->where(['sup_status' => 1])->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductAccounts()
    {
        return $this->hasMany(ProductAccount::className(), ['order_account_history_id' => 'id']);
    }

    public function getInvoiceHtmlTextSklad($model, $id)
    {
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $orderAccount2 = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $about = About::find()->where(['id' => 1])->one();
        $table = '';
        $allCount = 0;
        $allSumm = 0;
        $tulan_sum_som = $model->sum_som?'To\'langan summa so\'mda:': '';
        $tulan_sum_karta = $model->sum_cart?'To\'langan summa kartada:': '';
        $tulan_sum_transfer = $model->sum_transfers?'To\'langan summa transferda:': '';
        $discount_amount_sum = $model->discount_amount?'Jami chegirma ($):': '';
        $discount_amount_sum_val = $model->discount_amount?$model->discount_amount.' $,': '';
        $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;font-size:12px">
                    <tr style="">
                        <td style="height: 20px;text-align: center; width: 10px; border: 1px solid #000;"><b>№</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>MODEL</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>NOMI</b></td> 
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>TIP</b></td>
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>SONI</b></td>   
                        
                    </tr>';
        $i =1;
        foreach ($warehouse as $model1) { 
            
            foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['order_account_history_id' => $id])->andWhere(['brand_id' => $model1->brand_id])->all() as $model1){ 
                $table .= '<tr style="">
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $i . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->brand->name . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->productCategory->name.($model1->size!='0'?'&nbsp; (&nbsp;'. $model1->size.'&nbsp;)' : '') . '</b></td>

                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>&nbsp;'. $model1->getTypeView($model1->type) . '</b></td>
                            <td style="height: 15px; text-align: center; border: 1px solid #000;"><b>'. $model1->count . '</b></td>
                        </tr>';
                $allCount = $allCount + $model1->count;
                $allSumm = $allSumm + $model1->count * $model1->price;
                $i=$i+1;
            }
            // $table .= '<tr style="">
            //                 <td colspan="6" style="height: 20px; border: 1px solid #000;">&nbsp;</td>
                            
            //             </tr>';
        }
        $table .= '
                    <tr style="">
                        <td colspan="2" style=";height: 20px; border: 1px solid #000;">&nbsp;<b>Jami</b></td>
                        <td style="height: 15px; text-align: center; border: 1px solid #000;"><b></b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;"></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'.$allCount.'</b></td>
                    </tr>
        </table>';

        $text = '   
        
        <table style="width: 100%; text-align: right; font-size:12px">  
             <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px;">{cr_date_time}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px; color:red;">{customer_fio}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><hr></td>
            </tr>
            <tr>
                <td rowspan="5" style="text-align: left; width: 150px!important;"><img src="../views/image/logo4.jpg" style="height:100px; border-radius:20%;"></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 200px;">Do\'kon:</th>
                <td  ><b >{t_p},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;font-size:14px; color:green;">Dollar kursi:</th>
                <td ><b style="text-align: left;font-size:14px; color:green;">{exchange_rate} so\'m</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left;">Firma:</th>
                <td ><b >{postavshik},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left; width: 150px;">Manzil:</th>
                <td ><b >{customer_region}, {customer_district}</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 150px;">Telefon nomer:</th>
                <td ><b >{phone},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;">Telefon:</th>
                <td   ><b >{customer_phone}</b></td>
            </tr>
        </table>
        '.$table.'<br>
       
      ';
        $text = str_replace ("{exchange_rate}", $model->exchange_rate , $text);
        $text = str_replace ("{total_debt_old}", Yii::$app->formatter->asDecimal($model->total_debt_old, 2), $text);
        $text = str_replace ("{all_total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2), $text);
        $text = str_replace ("{total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2) , $text);
        $text = str_replace ("{discount_amount}", $model->discount_amount?$model->discount_amount:'' , $text);
        $text = str_replace ("{sum_dollar}", $model->sum_dollar?Yii::$app->formatter->asDecimal($model->sum_dollar,2):'' , $text);
        $text = str_replace ("{all_summ_dollar}", Yii::$app->formatter->asDecimal($model->all_summ_dollar,2) , $text);
        $text = str_replace ("{sum_som}", $model->sum_som?Yii::$app->formatter->asDecimal($model->sum_som,2):' ' , $text);
        $text = str_replace ("{sum_transfers}", $model->sum_transfers?Yii::$app->formatter->asDecimal($model->sum_transfers,2):' ' , $text);
        $text = str_replace ("{sum_cart}", $model->sum_cart?Yii::$app->formatter->asDecimal($model->sum_cart, 2):' ' , $text);
        $text = str_replace ("{all_product_sum}", Yii::$app->formatter->asDecimal($model->all_product_sum, 2) , $text);
        $text = str_replace ("{date}", $model->date , $text);
        $text = str_replace ("{cr_date_time}", date('d.m.Y H:i', strtotime($model->cr_date_time)) , $text);
        

        $text = str_replace ("{order_number}", $model->date , $text);
        $text = str_replace ("{customer_fio}", $model->client->fio , $text);
        $text = str_replace ("{customer_phone}", $model->client->phone , $text);
        $text = str_replace ("{customer_region}", $model->client->region_id?$model->client->region->name:'' , $text);
        $text = str_replace ("{customer_district}", $model->client->district_id?$model->client->district->name:'' , $text);

        $text = str_replace ("{nomer_nakladnoy}", $about->nomer_nakladnoy , $text);
        $text = str_replace ("{postavshik}", $about->postavshik , $text);
        $text = str_replace ("{phone}", $about->phone , $text);
        $text = str_replace ("{dostavshik}", $about->dostavshik , $text);
        $text = str_replace ("{t_p}", $about->t_p , $text);

        $text = str_replace ("{cr_date}", date(' d/m/Y H:i:s') , $text);
        return $text;
    }
    public function getInvoiceHtmlTextDateClient($model, $id)
    {
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $orderAccount2 = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $about = About::find()->where(['id' => 1])->one();
        $table = '';
        $allCount = 0;
        $allSumm = 0;
        $tulan_sum_som = $model->sum_som?'To\'langan summa so\'mda:': '';
        $tulan_sum_karta = $model->sum_cart?'To\'langan summa kartada:': '';
        $tulan_sum_transfer = $model->sum_transfers?'To\'langan summa transferda:': '';
        $discount_amount_sum = $model->discount_amount?'Jami chegirma ($):': '';
        $discount_amount_sum_val = $model->discount_amount?$model->discount_amount.' $,': '';
        $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;font-size:12px">
                    <tr style="">
                        <td style="height: 20px;text-align: center; width: 10px; border: 1px solid #000;"><b>№</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>MODEL</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>NOMI</b></td> 
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>SONI</b></td>   
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>TIP</b></td>                    
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>NARXI ($)</b></td>
                        <td style="height: 15px;text-align: center; width: 12%; border: 1px solid #000;"><b>UMUMIY NARXI ($)</b></td>
                    </tr>';
        $i =1;
        foreach ($warehouse as $model1) { 
            
            foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['order_account_history_id' => $id])->andWhere(['brand_id' => $model1->brand_id])->all() as $model1){ 
                $table .= '<tr style="">
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $i . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->brand->name . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->productCategory->name.($model1->size!='0'?'&nbsp; (&nbsp;'. $model1->size.'&nbsp;)' : '') . '</b></td>
                            <td style="height: 15px; text-align: center; border: 1px solid #000;"><b>'. $model1->count . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>&nbsp;'. $model1->getTypeView($model1->type) . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>'. $model1->price . '</b></td>
                            <td style="height: 15px; text-align: left;  border: 1px solid #000;"><b> = '. $model1->count * $model1->price . '</b></td>
                        </tr>';
                $allCount = $allCount + $model1->count;
                $allSumm = $allSumm + $model1->count * $model1->price;
                $i=$i+1;
            }
            // $table .= '<tr style="">
            //                 <td colspan="6" style="height: 20px; border: 1px solid #000;">&nbsp;</td>
                            
            //             </tr>';
        }
        $table .= '
                    <tr style="">
                        <td colspan="4" style=";height: 20px; border: 1px solid #000;">&nbsp;<b>Jami</b></td>

                        <td style="height: 15px; text-align: center; border: 1px solid #000;"><b></b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;-</td>
                        <td style=";height: 15px; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'. $allSumm . '</b></td>
                    </tr>
        </table>';

        $text = '   
        
        <table style="width: 100%; text-align: right; font-size:12px">  
             <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px;">{cr_date_time}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px; color:red;">{customer_fio}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><hr></td>
            </tr>
            <tr>
                <td rowspan="5" style="text-align: left; width: 150px!important;"><img src="../views/image/logo4.jpg" style="height:100px; border-radius:20%;"></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 200px;">Do\'kon:</th>
                <td  ><b >{t_p},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;font-size:14px; color:green;">Dollar kursi:</th>
                <td ><b style="text-align: left;font-size:14px; color:green;">{exchange_rate} so\'m</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left;">Firma:</th>
                <td ><b >{postavshik},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left; width: 150px;">Manzil:</th>
                <td ><b >{customer_region}, {customer_district}</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 150px;">Telefon nomer:</th>
                <td ><b >{phone},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;">Telefon:</th>
                <td   ><b >{customer_phone}</b></td>
            </tr>
            <tr>                            
        </table>
        '.$table.'<br>
        <table style="width: 100%; text-align: right; font-size:12px;">   
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 250px;color:#f59c1">Ostatka  ($):</th>
                    <td ><b style="font-size:16px;color:#f59c1">{total_debt_old} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0">To\'langan summa dollarda ($):</th>
                    <td ><b style="color:#474ba0">{sum_dollar} $</b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px; color:#f59c1">Olingan tavarlar summasi ($):</th>
                    <td ><b style="color:#f59c1">{all_product_sum} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left;color:#474ba0"></th>
                    <td  ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">Jami to\'langan summa ($):</th>
                    <td ><b style="color:#f59c1" >{all_summ_dollar} $,</b></td>
        
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0"></th>
                    <td ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">'.$discount_amount_sum.'</th>
                    <td ><b style="color:#f59c1" >'.$discount_amount_sum_val.'</b></td>
                </tr>
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 150px;color:red">Qolgan qarz ($): </th>
                    <td ><b style="font-size:16px;color:red" >{all_total_debt} $,</b></td>
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left;color:#474ba0"></th>
                    <td><b style="color:#474ba0"></b></td>
                </tr>
            </table> 
      ';
        $text = str_replace ("{exchange_rate}", $model->exchange_rate , $text);
        $text = str_replace ("{total_debt_old}", Yii::$app->formatter->asDecimal($model->total_debt_old, 2), $text);
        $text = str_replace ("{all_total_debt}", $model->total_debt?Yii::$app->formatter->asDecimal($model->total_debt,2): 0, $text);
        $text = str_replace ("{total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2) , $text);
        $text = str_replace ("{discount_amount}", $model->discount_amount?$model->discount_amount:'' , $text);
        $text = str_replace ("{sum_dollar}", $model->sum_dollar?Yii::$app->formatter->asDecimal($model->sum_dollar,2):'0' , $text);
        $text = str_replace ("{all_summ_dollar}", $model->all_summ_dollar?Yii::$app->formatter->asDecimal($model->all_summ_dollar,2): '0' , $text);
        $text = str_replace ("{sum_som}", $model->sum_som?Yii::$app->formatter->asDecimal($model->sum_som,2):' ' , $text);
        $text = str_replace ("{sum_transfers}", $model->sum_transfers?Yii::$app->formatter->asDecimal($model->sum_transfers,2):'0' , $text);
        $text = str_replace ("{sum_cart}", $model->sum_cart?Yii::$app->formatter->asDecimal($model->sum_cart, 2):'0' , $text);
        $text = str_replace ("{all_product_sum}", $model->all_product_sum?Yii::$app->formatter->asDecimal($model->all_product_sum, 2): 0 , $text);
        $text = str_replace ("{date}", $model->date , $text);
        $text = str_replace ("{cr_date_time}", date('d.m.Y H:i', strtotime($model->cr_date_time)) , $text);

        $text = str_replace ("{order_number}", $model->date , $text);
        $text = str_replace ("{customer_fio}", $model->client->fio , $text);
        $text = str_replace ("{customer_phone}", $model->client->phone , $text);
        $text = str_replace ("{customer_region}", $model->client->region_id?$model->client->region->name:'' , $text);
        $text = str_replace ("{customer_district}", $model->client->district_id?$model->client->district->name:'' , $text);

        $text = str_replace ("{nomer_nakladnoy}", $about->nomer_nakladnoy , $text);
        $text = str_replace ("{postavshik}", $about->postavshik , $text);
        $text = str_replace ("{phone}", $about->phone , $text);
        $text = str_replace ("{dostavshik}", $about->dostavshik , $text);
        $text = str_replace ("{t_p}", $about->t_p , $text);

        $text = str_replace ("{cr_date}", date(' d/m/Y H:i:s') , $text);
        return $text;
    }

    public function getInvoiceHtmlTextHistoryClient($model, $id)
    {
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $orderAccount2 = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $about = About::find()->where(['id' => 1])->one();
        $table = '';
        $allCount = 0;
        $allSumm = 0;
        $tulan_sum_som = $model->sum_som?'To\'langan summa so\'mda:': '';
        $tulan_sum_karta = $model->sum_cart?'To\'langan summa kartada:': '';
        $tulan_sum_transfer = $model->sum_transfers?'To\'langan summa transferda:': '';
        $discount_amount_sum = $model->discount_amount?'Jami chegirma ($):': '';
        $discount_amount_sum_val = $model->discount_amount?$model->discount_amount.' $,': '';
        $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;font-size:12px">
                    <tr style="">
                        <td style="height: 20px;text-align: center; width: 10px; border: 1px solid #000;"><b>№</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>MODEL</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>NOMI</b></td> 
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>SONI</b></td>   
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>TIP</b></td>                    
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>NARXI ($)</b></td>
                        <td style="height: 15px;text-align: center; width: 12%; border: 1px solid #000;"><b>UMUMIY NARXI ($)</b></td>
                    </tr>';
        $i =1;
        foreach ($warehouse as $model1) { 
            
            foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['order_account_history_id' => $id])->andWhere(['brand_id' => $model1->brand_id])->all() as $model1){ 
                $table .= '<tr style="">
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $i . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->brand->name . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->productCategory->name.($model1->size!='0'?'&nbsp; (&nbsp;'. $model1->size.'&nbsp;)' : '') . '</b></td>
                            <td style="height: 15px; text-align: center; border: 1px solid #000;"><b>'. $model1->count . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>&nbsp;'. $model1->getTypeView($model1->type) . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>'. $model1->price . '</b></td>
                            <td style="height: 15px; text-align: left;  border: 1px solid #000;"><b> = '. $model1->count * $model1->price . '</b></td>
                        </tr>';
                $allCount = $allCount + $model1->count;
                $allSumm = $allSumm + $model1->count * $model1->price;
                $i=$i+1;
            }
            // $table .= '<tr style="">
            //                 <td colspan="6" style="height: 20px; border: 1px solid #000;">&nbsp;</td>
                            
            //             </tr>';
        }
        $table .= '
                    <tr style="">
                        <td colspan="4" style=";height: 20px; border: 1px solid #000;">&nbsp;<b>Jami</b></td>

                        <td style="height: 15px; text-align: center; border: 1px solid #000;"><b></b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;-</td>
                        <td style=";height: 15px; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'. $allSumm . '</b></td>
                    </tr>
        </table>';

        $text = '   
        
        <table style="width: 100%; text-align: right; font-size:12px">  
             <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px;">{cr_date_time}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px; color:red;">{customer_fio}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><hr></td>
            </tr>
            <tr>
                <td rowspan="5" style="text-align: left; width: 150px!important;"><img src="../views/image/logo4.jpg" style="height:100px; border-radius:20%;"></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 200px;">Do\'kon:</th>
                <td  ><b >{t_p},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;font-size:14px; color:green;">Dollar kursi:</th>
                <td ><b style="text-align: left;font-size:14px; color:green;">{exchange_rate} so\'m</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left;">Firma:</th>
                <td ><b >{postavshik},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left; width: 150px;">Manzil:</th>
                <td ><b >{customer_region}, {customer_district}</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 150px;">Telefon nomer:</th>
                <td ><b >{phone},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;">Telefon:</th>
                <td   ><b >{customer_phone}</b></td>
            </tr>
            <tr>                            
        </table>
        '.$table.'<br>
        <table style="width: 100%; text-align: right; font-size:12px;">   
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 250px;color:#f59c1">Ostatka  ($):</th>
                    <td ><b style="font-size:16px;color:#f59c1">{total_debt_old} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0">To\'langan summa dollarda ($):</th>
                    <td ><b style="color:#474ba0">{sum_dollar} $</b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px; color:#f59c1">Olingan tavarlar summasi ($):</th>
                    <td ><b style="color:#f59c1">{all_product_sum} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left;color:#474ba0"></th>
                    <td  ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">Jami to\'langan summa ($):</th>
                    <td ><b style="color:#f59c1" >{all_summ_dollar} $,</b></td>
        
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0"></th>
                    <td ><b style="color:#474ba0"> </b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">'.$discount_amount_sum.'</th>
                    <td ><b style="color:#f59c1" >'.$discount_amount_sum_val.'</b></td>
                </tr>
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 150px;color:red">Qolgan qarz ($): </th>
                    <td ><b style="font-size:16px;color:red" >{all_total_debt} $,</b></td>
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left;color:#474ba0"></th>
                    <td><b style="color:#474ba0"></b></td>
                </tr>
            </table> 
      ';
        $text = str_replace ("{exchange_rate}", $model->exchange_rate , $text);
        $text = str_replace ("{total_debt_old}", Yii::$app->formatter->asDecimal($model->total_debt_old, 2), $text);
        $text = str_replace ("{all_total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2), $text);
        $text = str_replace ("{total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2) , $text);
        $text = str_replace ("{discount_amount}", $model->discount_amount?$model->discount_amount:'0' , $text);
        $text = str_replace ("{sum_dollar}", $model->sum_dollar?Yii::$app->formatter->asDecimal($model->sum_dollar,2):'0' , $text);
        $text = str_replace ("{all_summ_dollar}", Yii::$app->formatter->asDecimal($model->all_summ_dollar,2) , $text);
        $text = str_replace ("{sum_som}", $model->sum_som?Yii::$app->formatter->asDecimal($model->sum_som,2):'0' , $text);
        $text = str_replace ("{sum_transfers}", $model->sum_transfers?Yii::$app->formatter->asDecimal($model->sum_transfers,2):' ' , $text);
        $text = str_replace ("{sum_cart}", $model->sum_cart?Yii::$app->formatter->asDecimal($model->sum_cart, 2):' ' , $text);
        $text = str_replace ("{all_product_sum}", Yii::$app->formatter->asDecimal($model->all_product_sum, 2) , $text);
        $text = str_replace ("{date}", $model->date , $text);
        $text = str_replace ("{cr_date_time}", date('d.m.Y H:i', strtotime($model->cr_date_time)) , $text);

        $text = str_replace ("{order_number}", $model->date , $text);
        $text = str_replace ("{customer_fio}", $model->client->fio , $text);
        $text = str_replace ("{customer_phone}", $model->client->phone , $text);
        $text = str_replace ("{customer_region}", $model->client->region_id?$model->client->region->name:'' , $text);
        $text = str_replace ("{customer_district}", $model->client->district_id?$model->client->district->name:'' , $text);

        $text = str_replace ("{nomer_nakladnoy}", $about->nomer_nakladnoy , $text);
        $text = str_replace ("{postavshik}", $about->postavshik , $text);
        $text = str_replace ("{phone}", $about->phone , $text);
        $text = str_replace ("{dostavshik}", $about->dostavshik , $text);
        $text = str_replace ("{t_p}", $about->t_p , $text);

        $text = str_replace ("{cr_date}", date(' d/m/Y H:i:s') , $text);
        return $text;
    }

    public function getInvoiceHtmlText($model, $id)
    {
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $orderAccount2 = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $about = About::find()->where(['id' => 1])->one();
        $sum = (float) KeshbekHistory::find()->where(['client_id' => $model->client_id])->sum('keshbek_sum');
        $debtRepayments = DebtRepayment::find()
            ->where(['date' => $model->date])
            ->andWhere(['client_id' => $model->client_id])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
            ])
            ->all();

        $debtRepaymentall = 0;
        foreach ($debtRepayments as $value) { 
            $debtRepaymentall = $debtRepaymentall + $value->all_summ_dollar;
        }

        $table = '';
        $allCount = 0;
        $allSumm = 0;
        $tulan_sum_som = $model->sum_som?'To\'langan summa so\'mda:': '';
        $tulan_sum_karta = $model->sum_cart?'To\'langan summa kartada:': '';
        $tulan_sum_transfer = $model->sum_transfers?'To\'langan summa transferda:': '';
        $discount_amount_sum = $model->discount_amount?'Jami chegirma ($):': '';
        $discount_amount_sum_val = $model->discount_amount?$model->discount_amount.' $,': '';
        $debtRepaymentall_sum = $debtRepaymentall != 0 ?'To\'langan qarz ($):': '';
        $debt_repayment_all = $debtRepaymentall != 0 ?$debtRepaymentall.' $,': '';
        if($sum>0){
            $keshbek_tr = '
            <br/><br/><br/>
             <table style="width: 100%; text-align: right; font-size:14px;">   
                
                <tr> <td colspan="4" style="text-align: left;"><b style="font-size:16px;">Xaridingiz uchun rahmat!</b></td> </tr>
                <tr> <td colspan="4" style="text-align: left;"><b style="font-size:16px;color:green">Sizdagi Keshbek: '.Yii::$app->formatter->asDecimal($sum, 2).'$</b></td> </tr>
            </table> ';
        }else{
            $keshbek_tr = '';
        }
        

        $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;font-size:12px">
                    <tr style="">
                        <td style="height: 20px;text-align: center; width: 10px; border: 1px solid #000;"><b>№</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>MODEL</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>NOMI</b></td> 
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>SONI</b></td>   
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>TIP</b></td>                    
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>NARXI ($)</b></td>
                        <td style="height: 15px;text-align: center; width: 12%; border: 1px solid #000;"><b>UMUMIY NARXI ($)</b></td>
                    </tr>';
        $i =1;
        foreach ($warehouse as $model1) { 
            
            foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['order_account_history_id' => $id])->andWhere(['brand_id' => $model1->brand_id])->all() as $model1){ 
                $table .= '<tr style="">
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $i . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->brand->name . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->productCategory->name.($model1->size!='0'?'&nbsp; (&nbsp;'. $model1->size.'&nbsp;)' : '') . '</b></td>
                            <td style="height: 15px; text-align: center; border: 1px solid #000;"><b>'. $model1->count . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>&nbsp;'. $model1->getTypeView($model1->type) . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>'. $model1->price . '</b></td>
                            <td style="height: 15px; text-align: left;  border: 1px solid #000;"><b> = '. round($model1->count * $model1->price,2) . '</b></td>
                        </tr>';
                $allCount = $allCount + $model1->count;
                $allSumm = $allSumm + $model1->count * $model1->price;
                $i=$i+1;
            }
            // $table .= '<tr style="">
            //                 <td colspan="6" style="height: 20px; border: 1px solid #000;">&nbsp;</td>
                            
            //             </tr>';
        }
        $table .= '
                    <tr style="">
                        <td colspan="4" style=";height: 20px; border: 1px solid #000;">&nbsp;<b>Jami</b></td>

                        <td style="height: 15px; text-align: center; border: 1px solid #000;"><b></b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;-</td>
                        <td style=";height: 15px; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'. round($allSumm,2) . '</b></td>
                    </tr>
        </table>';

        $text = '   
          
        <table style="width: 100%; text-align: right; font-size:12px">  
             <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px;">{cr_date_time}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px; color:red;">{customer_fio}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><hr></td>
            </tr>
            <tr>
                <td rowspan="5" style="text-align: left; width: 150px!important;"><img src="../views/image/logo4.jpg" style="height:100px; border-radius:20%;"></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 200px;">Do\'kon:</th>
                <td  ><b >{t_p},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;font-size:14px; color:green;">Dollar kursi:</th>
                <td ><b style="text-align: left;font-size:14px; color:green;">{exchange_rate} so\'m</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left;">Firma:</th>
                <td ><b >{postavshik},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left; width: 150px;">Manzil:</th>
                <td ><b >{customer_region}, {customer_district}</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 150px;">Telefon nomer1:</th>
                <td ><b >{phone},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;">Telefon:</th>
                <td   ><b >{customer_phone}</b></td>
            </tr>
           
        </table>
        '.$table.'<br>
        <table style="width: 100%; text-align: right; font-size:12px;">   
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 250px;color:#f59c1">Ostatka  ($):</th>
                    <td ><b style="font-size:16px;color:#f59c1">{total_debt_old} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0">To\'langan summa dollarda ($):</th>
                    <td ><b style="color:#474ba0">{sum_dollar} $</b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px; color:#f59c1">Olingan tavarlar summasi ($):</th>
                    <td ><b style="color:#f59c1">{all_product_sum} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left;color:#474ba0"></th>
                    <td  ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">Jami to\'langan summa ($):</th>
                    <td ><b style="color:#f59c1" >{all_summ_dollar} $,</b></td>
        
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0"></th>
                    <td ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">'.$discount_amount_sum.'</th>
                    <td ><b style="color:#f59c1" >'.$discount_amount_sum_val.'</b></td>
                </tr>
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 150px;color:red">Qolgan qarz ($): </th>
                    <td ><b style="font-size:16px;color:red" >{all_total_debt} $,</b></td>
                    
                </tr>
            </table> 
            '.$keshbek_tr.'
      ';
        // <tr>
        //     <th nowrap style="text-align: left; width: 150px;color:#6c3d3d">'.$debtRepaymentall_sum.'</th>
        //     <td ><b style="color:#6c3d3d" >'.$debt_repayment_all.'</b></td>

        //     <td style="width: 10px;"></td>
        //     <th nowrap style="text-align: left;color:#474ba0">'.$tulan_sum_transfer.'</th>
        //     <td><b style="color:#474ba0">{sum_transfers}</b></td>
        // </tr>
        $text = str_replace ("{exchange_rate}", $model->exchange_rate , $text);
        $text = str_replace ("{total_debt_old}", Yii::$app->formatter->asDecimal($model->total_debt_old, 2), $text);
        $text = str_replace ("{all_total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2), $text);
        $text = str_replace ("{total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2) , $text);
        $text = str_replace ("{discount_amount}", $model->discount_amount?$model->discount_amount:'' , $text);
        $text = str_replace ("{sum_dollar}", $model->sum_dollar?Yii::$app->formatter->asDecimal($model->sum_dollar,2):'0' , $text);
        $text = str_replace ("{all_summ_dollar}", Yii::$app->formatter->asDecimal($model->all_summ_dollar,2) , $text);
        $text = str_replace ("{sum_som}", $model->sum_som?Yii::$app->formatter->asDecimal($model->sum_som,2):' ' , $text);
        $text = str_replace ("{sum_transfers}", $model->sum_transfers?Yii::$app->formatter->asDecimal($model->sum_transfers,2):'0' , $text);
        $text = str_replace ("{sum_cart}", $model->sum_cart?Yii::$app->formatter->asDecimal($model->sum_cart, 2):'0' , $text);
        $text = str_replace ("{all_product_sum}", $model->all_product_sum?Yii::$app->formatter->asDecimal($model->all_product_sum, 2):'0' , $text);
        $text = str_replace ("{date}", $model->date , $text);
        $text = str_replace ("{debt_repayment_all}", Yii::$app->formatter->asDecimal($debtRepaymentall, 2), $text);
        $text = str_replace ("{cr_date_time}", date('d.m.Y', strtotime($model->cr_date_time)) , $text);

        $text = str_replace ("{order_number}", $model->date , $text);
        $text = str_replace ("{customer_fio}", $model->client->fio , $text);
        $text = str_replace ("{customer_phone}", $model->client->phone , $text);
        $text = str_replace ("{customer_region}", $model->client->region_id?$model->client->region->name:'' , $text);
        $text = str_replace ("{customer_district}", $model->client->district_id?$model->client->district->name:'' , $text);

        $text = str_replace ("{nomer_nakladnoy}", $about->nomer_nakladnoy , $text);
        $text = str_replace ("{postavshik}", $about->postavshik , $text);
        $text = str_replace ("{phone}", $about->phone , $text);
        $text = str_replace ("{dostavshik}", $about->dostavshik , $text);
        $text = str_replace ("{t_p}", $about->t_p , $text);

        $text = str_replace ("{cr_date}", date(' d/m/Y H:i:s') , $text);
        return $text;
    }
    public function getInvoiceHtmlText2($model, $id)
    {
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $orderAccount2 = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $about = About::find()->where(['id' => 1])->one();
        $table = '';
        $allCount = 0;
        $allGivenCount = 0;
        $allSumm = 0;
        if ($model->driver_info) {
            $td_driver_info ='<td style="width: 65%; text-align: left; border-bottom: 1px solid #000;">{driver_info}</td>';
        }else{
            $td_driver_info ='<td style="width: 65%; text-align: left; border-bottom: 1px solid #000;"></td>';
        }
        
        if ($model->fast_order) {
            $td_fast_order ='<b style="color:green; ">Holati:</b> <b style="color:green; font-size:16px">Tezda tayyorlash kerak </b>';
        }else{
            $td_fast_order ='';
        }

        $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <tr style="background-color:#a0d9ea;" >
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>JOY</b></td>
                        <td style="height: 20px;text-align: center; width: 15%; border: 1px solid #000;"><b>MODEL</b></td>
                        <td style="height: 20px;text-align: center; width: 15%; border: 1px solid #000;"><b>NOMI</b></td>
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>TIP</b></td>
                        
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>SONI</b></td>
                    </tr>';
        foreach ($warehouse as $model1) { 
            foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['order_account_history_id' => $id])->andWhere(['brand_id' => $model1->brand_id])->all() as $model1){ 
                $type_color = $model1->type_sklad_id ==1?'<tr style="background-color:#67a38569">':'<tr style="">';
                if ($model1->count == $model1->given_count) {
                    $colorCount = 'blue';
                }else {
                    $colorCount = 'red';
                }
                $table .= $type_color.'
                            <td style="height: 20px; border: 1px solid #000;"><b>&nbsp;'. $model1->typeSklad->name . '</b></td>
                            <td style="height: 20px; border: 1px solid #000;"><b>&nbsp;'. $model1->brand->name . '</b></td>
                            <td style="height: 20px; border: 1px solid #000;"><b>&nbsp;'. $model1->productCategory->name.($model1->size!='0'?'&nbsp; (&nbsp;'. $model1->size.'&nbsp;)' : '') . '</b></td>
                            <td style="height: 20px; width: 80px; border: 1px solid #000;"><b>&nbsp;'. $model1->getTypeView($model1->type) . '</b></td>
                            
                            <td style="height: 20px; text-align: center; border: 1px solid #000;"><b>'. $model1->count . '</b></td>
                        </tr>';
                $allCount = $allCount + $model1->count;
                $allGivenCount = $allGivenCount + $model1->given_count;
                $allSumm = $allSumm + $model1->count * $model1->price;
            }
          
        }
        $table .= '
                    <tr style="">
                        <td colspan="4" style=";height: 20px; border: 1px solid #000;">&nbsp;<b>Jami</b></td>

                        <td style="height: 20px; text-align: center; border: 1px solid #000;"><b>'. $allCount . '</b></td>
                    </tr>
        </table>';

        $text = '   
          <br/>  <br/>
        <p style="width: 100%; margin-top: -10px; text-align: center;"><b>Buyurtma sanasi:</b> <b style="color:red; font-size:16px">{cr_date_time} </b><br> <b>Mijoz:</b><b style="color:red; font-size:16px">  {customer_fio}</b><br> <b>Mijoz telefon raqami:</b>  {customer_phone}<br>'.$td_fast_order.'</p>  
        '.$table.'
 
        <table style="margin-top: 30px; width: 100%;">
            <tr>
                <td style="width: 35%; padding-left: 0;">Shafyor: </td>
                '.$td_driver_info.'
            </tr>
        </table> 
      ';
        
        $text = str_replace ("{exchange_rate}", $model->exchange_rate , $text);
        $text = str_replace ("{all_total_debt}", Yii::$app->formatter->asDecimal($orderAccount2->total_debt, 2), $text);
        $text = str_replace ("{total_debt}", Yii::$app->formatter->asDecimal($model->total_debt, 2) , $text);
        $text = str_replace ("{discount_amount}", $model->discount_amount , $text);
        $text = str_replace ("{sum_dollar}", Yii::$app->formatter->asDecimal($model->sum_dollar??0,2) , $text);
        $text = str_replace ("{all_summ_dollar}", Yii::$app->formatter->asDecimal($model->all_summ_dollar, 2) , $text);
        $text = str_replace ("{sum_som}", Yii::$app->formatter->asDecimal($model->sum_som,2) , $text);
        $text = str_replace ("{sum_transfers}", Yii::$app->formatter->asDecimal($model->sum_transfers,2) , $text);
        $text = str_replace ("{sum_cart}", Yii::$app->formatter->asDecimal($model->sum_cart, 2) , $text);
        $text = str_replace ("{all_product_sum}", Yii::$app->formatter->asDecimal($model->all_product_sum, 2) , $text);
        $text = str_replace ("{date}", $model->date , $text);
        $text = str_replace ("{driver_info}", $model->driver_info , $text);
        $text = str_replace ("{cr_date_time}", date('d.m.Y H:i', strtotime($model->cr_date_time)) , $text);

        $text = str_replace ("{order_number}", $model->date , $text);
        $text = str_replace ("{customer_fio}", $model->client->fio , $text);
        $text = str_replace ("{customer_phone}", $model->client->phone , $text);
        $text = str_replace ("{customer_region}", $model->client->region_id?$model->client->region->name:'' , $text);
        $text = str_replace ("{customer_district}", $model->client->district_id?$model->client->district->name:'' , $text);

        $text = str_replace ("{nomer_nakladnoy}", $about->nomer_nakladnoy , $text);
        $text = str_replace ("{postavshik}", $about->postavshik , $text);
        $text = str_replace ("{phone}", $about->phone , $text);
        $text = str_replace ("{dostavshik}", $about->dostavshik , $text);
        $text = str_replace ("{t_p}", $about->t_p , $text);

        $text = str_replace ("{cr_date}", date(' d/m/Y H:i:s') , $text);
        return $text;
    }
    public function getProfitInvoiceHtmlText($model, $id)
    {
        $warehouse = ProductAccountHistory::find()->where(['order_account_history_id' => $id])->select(['brand_id'])->groupBy(['brand_id'])->all();
        $orderAccount2 = OrderAccount::find()->where(['client_id' => $model->client_id])->one();
        $about = About::find()->where(['id' => 1])->one();
        $debtRepayments = DebtRepayment::find()
            ->where(['date' => $model->date])
            ->andWhere(['client_id' => $model->client_id])
            ->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
            ])
            ->all();

        $debtRepaymentall = 0;
        foreach ($debtRepayments as $value) { 
            $debtRepaymentall = $debtRepaymentall + $value->all_summ_dollar;
        }

        $table = '';
        $allCount = 0;
        $allSumm = 0;
        $allRealSumm = 0;
        $allProfitSumm = 0;
        $tulan_sum_som = $model->sum_som?'To\'langan summa so\'mda:': '';
        $tulan_sum_karta = $model->sum_cart?'To\'langan summa kartada:': '';
        $tulan_sum_transfer = $model->sum_transfers?'To\'langan summa transferda:': '';
        $discount_amount_sum = $model->discount_amount?'Jami chegirma ($):': '';
        $discount_amount_sum_val = $model->discount_amount?$model->discount_amount.' $,': '';
        $debtRepaymentall_sum = $debtRepaymentall != 0 ?'To\'langan qarz ($):': '';
        $debt_repayment_all = $debtRepaymentall != 0 ?$debtRepaymentall.' $,': '';

        $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;font-size:12px">
                    <tr style="">
                        <td style="height: 20px;text-align: center; width: 10px; border: 1px solid #000;"><b>№</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>MODEL</b></td>
                        <td style="height: 15px;text-align: center; width: 15%; border: 1px solid #000;"><b>NOMI</b></td> 
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>SONI</b></td>   
                        <td style="height: 20px;text-align: center; width: 10%; border: 1px solid #000;"><b>TIP</b></td>                    
                        <td style="height: 15px;text-align: center; width: 10%; border: 1px solid #000;"><b>NARXI ($)</b></td>
                        <td style="height: 15px;text-align: center; width: 12%; border: 1px solid #000;"><b>JAMI</b></td>
                        <td style="height: 15px;text-align: center; width: 12%; border: 1px solid #000;"><b>ASL NARX</b></td>
                        <td style="height: 15px;text-align: center; width: 12%; border: 1px solid #000;"><b>FOYDA</b></td>
                    </tr>';
        $i =1;
        foreach ($warehouse as $model1) { 
            
            foreach ($orderProduct = ProductAccountHistory::find()->andWhere(['order_account_history_id' => $id])->andWhere(['brand_id' => $model1->brand_id])->all() as $model1){ 
                $colorProfit = ($model1->profit < 0) ? '#e70f0fff;font-size: 16px' : '';
                $largePrice = ($model1->price < $model1->real_price) ? '#e70f0fff;font-size: 16px' : '';   
                $priceIcon  = ($model1->price < $model1->real_price) ? '<img src="' . Yii::getAlias('@webroot') . '/images/Warning.png" style="width:25px;height:18px;vertical-align:middle;margin-left:1px;">': '';     
                $table .= '<tr style="">
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $i . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->brand->name . '</b></td>
                            <td style="height: 15px; border: 1px solid #000;"><b>&nbsp;'. $model1->productCategory->name.($model1->size!='0'?'&nbsp; (&nbsp;'. $model1->size.'&nbsp;)' : '') . '</b></td>
                            <td style="height: 15px; text-align: center; border: 1px solid #000;"><b>'. $model1->count . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>&nbsp;'. $model1->getTypeView($model1->type) . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;color:'.$largePrice.'"><b>'. $model1->price.$priceIcon .'</b></td>
                            <td style="height: 15px; text-align: left;  border: 1px solid #000;"><b> = '. round($model1->count * $model1->price,2) . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;"><b>  '. round($model1->real_price,2) . '</b></td>
                            <td style="height: 15px; text-align: center;  border: 1px solid #000;color:'.$colorProfit.'"><b>  '. round( $model1->profit,2) . '</b></td>
                        </tr>';
                $allCount = $allCount + $model1->count;
                $allSumm = $allSumm + $model1->count * $model1->price;
                $allRealSumm = $allRealSumm + $model1->count * $model1->real_price;
                $allProfitSumm = $allProfitSumm + $model1->profit;
                $i=$i+1;
            }
            // $table .= '<tr style="">
            //                 <td colspan="6" style="height: 20px; border: 1px solid #000;">&nbsp;</td>
                            
            //             </tr>';
        }
        $table .= '
                    <tr style="">
                        <td colspan="4" style=";height: 20px; border: 1px solid #000;">&nbsp;<b>Jami</b></td>

                        <td style="height: 15px; text-align: center; border: 1px solid #000;"><b></b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;-</td>
                        <td style=";height: 15px; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'. round($allSumm,2) . '</b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'. round($allRealSumm,2) . '</b></td>
                        <td style=";height: 15px;text-align: center; border: 1px solid #000;">&nbsp;&nbsp;&nbsp;<b>'. round($allProfitSumm,2) . '</b></td>
                    </tr>
        </table>';

        $text = '   
          
        <table style="width: 100%; text-align: right; font-size:12px">  
             <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px;">{cr_date_time}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><b style="font-size:20px; color:red;">{customer_fio}<br/></b></td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: center; width: 150px!important;"><hr></td>
            </tr>
            <tr>
                <td rowspan="5" style="text-align: left; width: 150px!important;"><img src="../views/image/logo4.jpg" style="height:100px; border-radius:20%;"></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 200px;">Do\'kon:</th>
                <td  ><b >{t_p},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;font-size:14px; color:green;">Dollar kursi:</th>
                <td ><b style="text-align: left;font-size:14px; color:green;">{exchange_rate} so\'m</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left;">Firma:</th>
                <td ><b >{postavshik},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left; width: 150px;">Manzil:</th>
                <td ><b >{customer_region}, {customer_district}</b></td>
            </tr>
            <tr>
                <th nowrap style="text-align: left; width: 150px;">Telefon nomer:</th>
                <td ><b >{phone},</b></td>
                <td style="width: 10px;"></td>
                <th nowrap style="text-align: left;">Telefon:</th>
                <td   ><b >{customer_phone}</b></td>
            </tr>
            <tr>                            
        </table>
        '.$table.'<br>
        <table style="width: 100%; text-align: right; font-size:12px;">   
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 250px;color:#f59c1">Ostatka  ($):</th>
                    <td ><b style="font-size:16px;color:#f59c1">{total_debt_old} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0">To\'langan summa dollarda ($):</th>
                    <td ><b style="color:#474ba0">{sum_dollar} $</b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px; color:#f59c1">Olingan tavarlar summasi ($):</th>
                    <td ><b style="color:#f59c1">{all_product_sum} $,</b></td>
                    
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left;color:#474ba0"></th>
                    <td  ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">Jami to\'langan summa ($):</th>
                    <td ><b style="color:#f59c1" >{all_summ_dollar} $,</b></td>
        
                    <td style="width: 10px;"></td>
                    <th nowrap style="text-align: left; color:#474ba0"></th>
                    <td ><b style="color:#474ba0"></b></td>
                </tr>
                <tr>
                    <th nowrap style="text-align: left; width: 150px;color:#f59c1">'.$discount_amount_sum.'</th>
                    <td ><b style="color:#f59c1" >'.$discount_amount_sum_val.'</b></td>
                </tr>
                <tr>
                    <th nowrap style="font-size:16px;text-align: left; width: 150px;color:red">Qolgan qarz ($): </th>
                    <td ><b style="font-size:16px;color:red" >{all_total_debt} $,</b></td>
                    
                </tr>
            </table> 
      ';
        // <tr>
        //     <th nowrap style="text-align: left; width: 150px;color:#6c3d3d">'.$debtRepaymentall_sum.'</th>
        //     <td ><b style="color:#6c3d3d" >'.$debt_repayment_all.'</b></td>

        //     <td style="width: 10px;"></td>
        //     <th nowrap style="text-align: left;color:#474ba0">'.$tulan_sum_transfer.'</th>
        //     <td><b style="color:#474ba0">{sum_transfers}</b></td>
        // </tr>
        $text = str_replace ("{exchange_rate}", $model->exchange_rate , $text);
        $text = str_replace ("{total_debt_old}", Yii::$app->formatter->asDecimal($model->total_debt_old, 2), $text);
        $text = str_replace ("{all_total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2), $text);
        $text = str_replace ("{total_debt}", Yii::$app->formatter->asDecimal($model->total_debt,2) , $text);
        $text = str_replace ("{discount_amount}", $model->discount_amount?$model->discount_amount:'' , $text);
        $text = str_replace ("{sum_dollar}", $model->sum_dollar?Yii::$app->formatter->asDecimal($model->sum_dollar,2):'0' , $text);
        $text = str_replace ("{all_summ_dollar}", Yii::$app->formatter->asDecimal($model->all_summ_dollar,2) , $text);
        $text = str_replace ("{sum_som}", $model->sum_som?Yii::$app->formatter->asDecimal($model->sum_som,2):' ' , $text);
        $text = str_replace ("{sum_transfers}", $model->sum_transfers?Yii::$app->formatter->asDecimal($model->sum_transfers,2):'0' , $text);
        $text = str_replace ("{sum_cart}", $model->sum_cart?Yii::$app->formatter->asDecimal($model->sum_cart, 2):'0' , $text);
        $text = str_replace ("{all_product_sum}", $model->all_product_sum?Yii::$app->formatter->asDecimal($model->all_product_sum, 2):'0' , $text);
        $text = str_replace ("{date}", $model->date , $text);
        $text = str_replace ("{debt_repayment_all}", Yii::$app->formatter->asDecimal($debtRepaymentall, 2), $text);
        $text = str_replace ("{cr_date_time}", date('d.m.Y H:i', strtotime($model->cr_date_time)) , $text);

        $text = str_replace ("{order_number}", $model->date , $text);
        $text = str_replace ("{customer_fio}", $model->client->fio , $text);
        $text = str_replace ("{customer_phone}", $model->client->phone , $text);
        $text = str_replace ("{customer_region}", $model->client->region_id?$model->client->region->name:'' , $text);
        $text = str_replace ("{customer_district}", $model->client->district_id?$model->client->district->name:'' , $text);

        $text = str_replace ("{nomer_nakladnoy}", $about->nomer_nakladnoy , $text);
        $text = str_replace ("{postavshik}", $about->postavshik , $text);
        $text = str_replace ("{phone}", $about->phone , $text);
        $text = str_replace ("{dostavshik}", $about->dostavshik , $text);
        $text = str_replace ("{t_p}", $about->t_p , $text);

        $text = str_replace ("{cr_date}", date(' d/m/Y H:i:s') , $text);
        return $text;
    }
}