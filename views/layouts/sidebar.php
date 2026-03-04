<?php 
use app\widgets\Menu;
use app\models\Users;
$model = Users::findOne(Yii::$app->user->identity->id);
 ?>

<div id="sidebar" class="sidebar sidebar-transparent">
    <div data-scrollbar="true" data-height="100%" >
        <ul class="nav">
            <li class="nav-profile">
                <a href="javascript:;" data-toggle="nav-profile" >
                    <div class="cover with-shadow"></div>
                    <div class="image">
                        <img src="<?= $model != null ? $model->getAvatar() : ''?>" alt="" />
                    </div>
                    <div class="info">
                        <?= $model != null ? $model->getFio() : '' ?>
                        <small><?= $model != null ? $model->getRoleDescription() : '' ?></small>
                    </div>
                </a>
            </li>
            
        </ul>
        <?= Menu::widget(
            [
                'options' => ['class' => 'nav'],
                'items' => [
                    // [
                    //     'label' => 'Bosh Sahifa', 
                    //     'icon' => 'dashboard', 
                    //     'url' => ['/site/index'],
                    //     'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 3 || $model->permission == 5 ? true : false,
                    // ],
                    [
                        'label' => 'Ombor mahsulotlari', 
                        'icon' => 'th', 
                        'url' => ['/warehouse/all-list'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 3 || $model->permission == 4 || $model->permission == 5 || $model->permission == 6? true : false,
                    ],
                    [
                        'label' => 'Buyurtma qilish', 
                        'icon' => 'shopping-cart', 
                        'url' => ['/order-account/orders'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6 ? true : false,
                    ],

                    // [
                    //     'label' => 'Ombor', 
                    //     'icon' => 'cubes', 
                    //     'url' => ['/warehouse/index'],
                    //     'visible' => $model->permission == 1 || $model->permission == 5|| $model->permission == 2 ? true : false,
                    // ],
                    // [
                    //     'label' => 'Omborxona hisobi', 
                    //     'icon' => 'tags', 
                    //     'url' => ['/sklad/index'],
                    //     'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 ? true : false,
                    // ],
                    [
                        'label' => 'Mijoz buyurtmalar tarixi', 
                        'icon' => 'list-ul', 
                        'url' => ['/order-account-history/index'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6? true : false,
                    ],
                    [
                        'label' => 'Mijozdan qarzdorlik', 
                        'icon' => 'balance-scale', 
                        'url' => ['/order-account-history/index-deptor'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6? true : false,
                    ],
                    [
                        'label' => 'Buyurtmalar va qarzlar', 
                        'icon' => 'pie-chart', 
                        'url' => ['/order-account-history/order-and-debt'],
                        'visible' => $model->permission == 1  ? true : false,
                    ],
                     [
                        'label' => 'Vozvrat', 
                        'icon' => 'history', 
                        'url' => ['/vozvrat-order/vozvrat'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6 ? true : false,
                    ],
                     [
                        'label' => 'Vozvrat buyurmalar tarixi', 
                        'icon' => 'list-alt', 
                        'url' => ['/vozvrat-order/index'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6 ? true : false,
                    ],
                    [
                        'label' => 'Mijoz umumiy buyurtmasi', 
                        'icon' => 'shopping-basket', 
                        'url' => ['/order-account/index'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6? true : false,
                    ],
                    
                    // [
                    //     'label' => 'Buyurtmalar tarixi', 
                    //     'icon' => 'shopping-basket', 
                    //     'url' => ['/orders/index'],
                    //     'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 ? true : false,
                    // ],
                    [
                        'label' => 'Tovarlar va Narxlar', 
                        'icon' => 'cubes', 
                        'url' => ['/warehouse/index'],
                        'visible' => $model->permission == 1 || $model->permission == 5|| $model->permission == 2 ? true : false,
                    ],
                    [
                        'label' => 'Omborxona hisobi', 
                        'icon' => 'university', 
                        'url' => ['/sklad/index'],
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 5 || $model->permission == 6 ? true : false,
                    ],
                    
                    [
                        'label' => 'Mening qarzlarim', 
                        'icon' => 'bookmark', 
                        'url' => ['/my-total-debt/index'],
                        'visible' => $model->permission == 1  ? true : false,
                    ],
                    
                    
                    
                    
                    // [
                    //     'label' => 'Kalkulyator', 
                    //     'icon' => 'calculator', 
                    //     'url' => ['/site/calculator'],
                    //     'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 3 || $model->permission == 4 || $model->permission == 5 ? true : false,
                    // ],                    
                    [
                        "label" => "Tizim boshqaruvi",
                        "url" => "#",
                        "icon" => "bars",
                        'visible' => $model->permission == 1 || $model->permission == 6 ? true : false,
                        "items" => [
                   
                      
                            [
                                'label' => 'Mahsulot toifalari', 
                                'icon' => 'product-hunt', 
                                'url' => ['/product-category/index'],
                                'visible' => $model->permission == 1 || $model->permission == 6? true : false,
                            ],
                            [
                                'label' => 'Modellar', 
                                'icon' => 'bookmark', 
                                'url' => ['/brands/index'],
                                'visible' => $model->permission == 1  || $model->permission == 6? true : false,
                            ],
                            [
                                'label' => 'Mahsulot o\'lchami', 
                                'icon' => 'list-ol', 
                                'url' => ['/brands-size/index'],
                                'visible' => $model->permission == 1  || $model->permission == 6? true : false,
                            ],
                        
                            [
                                'label' => 'O\'zgarishlar hisobi', 
                                'icon' => 'warning', 
                                'url' => ['/elegant-history-update/index'],
                                'visible' => $model->permission == 1 ? true : false,
                            ],
                    
                         
                            [
                                'label' => 'Foydalanuvchilar', 
                                'icon' => 'users', 
                                'url' => ['/users/index'],
                                'visible' => $model->permission == 1 ? true : false,
                            ],
                            [
                                'label' => 'Foyda va zarar', 
                                'icon' => 'bar-chart', 
                                'url' => ['/loss-of-profit/index'],
                                'visible' => $model->permission == 1  ? true : false,
                            ],
                            [
                                'label' => 'Xarajatlar', 
                                'icon' => 'align-left', 
                                'url' => ['/expenses/index'],
                                'visible' => $model->permission == 1  ? true : false,
                            ],
                            [
                                'label' => 'Yuk chiquvchi joy', 
                                'icon' => 'th-large', 
                                'url' => ['/type-sklad/index'],
                                'visible' => $model->permission == 1 ? true : false,
                            ],
                            [
                                'label' => 'Xarajat turi', 
                                'icon' => 'bars', 
                                'url' => ['/type-expense/index'],
                                'visible' => $model->permission == 1  ? true : false,
                            ],
                            [
                                'label' => 'Biz haqimizda', 
                                'icon' => 'home', 
                                'url' => ['/about/index'],
                                'visible' => $model->permission == 1  ? true : false,
                            ],
                            
                        ],
                    ],
                    [
                        "label" => "Sozlamalar",
                        "url" => "#",
                        "icon" => "cogs",
                        'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 6 ? true : false,
                        "items" => [
                            // [
                            //     'label' => 'Tekshirishlar tarixi', 
                            //     'icon' => 'check', 
                            //     'url' => ['/check/index'],
                            //     'visible' => $model->permission == 1  ? true : false,
                            // ],
                            
                            [
                                'label' => 'Mijozlar', 
                                'icon' => 'user', 
                                'url' => ['/client/index'],
                                'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 6? true : false,
                            ],
                            // [
                            //     'label' => 'Mijoz Keshbeklari', 
                            //     'icon' => 'percent', 
                            //     'url' => ['/keshbek-history/client-keshbek'],
                            //     'visible' => $model->permission == 1  || $model->permission == 6? true : false,
                            // ],
                            [
                                'label' => 'Sotilgan tovarlar tarixi', 
                                'icon' => 'history', 
                                'url' => ['/order-account-history/export'],
                                'visible' => $model->permission == 1 || $model->permission == 2  || $model->permission == 6? true : false,
                            ],
                            [
                                'label' => 'To\'langan qarzlar', 
                                'icon' => 'pie-chart', 
                                'url' => ['/debt-repayment/index'],
                                'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 6   ? true : false,
                            ],
                            
                            [
                                'label' => 'Klientlar tarixi', 
                                'icon' => 'history', 
                                'url' => ['/order-account-history/client-history'],
                                'visible' => $model->permission == 1 || $model->permission == 2  || $model->permission == 6? true : false,
                            ],
                            // Bu menyuda dateto dan date fromga cha bo'lgan oraliqdan qaysi kilent qancha maxsulot olganini ko'rsatadi
                            // [
                            //     'label' => 'Klientlar tarixi', 
                            //     'icon' => 'history', 
                            //     'url' => ['/order-account-history/client'],
                            //     'visible' => $model->permission == 1 || $model->permission == 2  || $model->permission == 6? true : false,
                            // ],
                            [
                                'label' => 'Kunlik sotilgan tovarlar', 
                                'icon' => 'history', 
                                'url' => ['/order-account-history/day-orders'],
                                'visible' => $model->permission == 1 || $model->permission == 2  ? true : false,
                            ],
                            // [
                            //     'label' => 'Kunlik sotilgan tovarlar', 
                            //     'icon' => 'history', 
                            //     'url' => ['/order-account-history/client-product-history-old'],
                            //     'visible' => $model->permission == 1 || $model->permission == 2  ? true : false,
                            // ],
                            // [
                            //     'label' => 'Kunlik sotilgan tovarlar', 
                            //     'icon' => 'history', 
                            //     'url' => ['/warehouse-history/product'],
                            //     'visible' => $model->permission == 1 || $model->permission == 2  ? true : false,
                            // ],
                            // [
                            //     'label' => 'Import tovarlar tarixi', 
                            //     'icon' => 'history', 
                            //     'url' => ['/warehouse-history/import'],
                            //     'visible' => $model->permission == 1 || $model->permission == 2  ? true : false,
                            // ],
                            [
                                'label' => 'Top mijozlar', 
                                'icon' => 'history', 
                                'url' => ['/order-account/top-client'],
                                'visible' => $model->permission == 1  ? true : false,
                            ],
                            [
                                'label' => 'Qarzdorlar ro\'yxati', 
                                'icon' => 'history', 
                                'url' => ['/order-account/debtors'],
                                'visible' => $model->permission == 1 || $model->permission == 2  || $model->permission == 6? true : false,
                            ],
                            [
                                'label' => 'Mijoz Buyurtma Karzinka', 
                                'icon' => 'trash-o', 
                                'url' => ['/order-account-history/trash-o'],
                                'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 6? true : false,
                            ],
                            [
                                'label' => 'To\'langan Qarz Karzinka', 
                                'icon' => 'trash-o', 
                                'url' => ['/debt-repayment/trash-o'],
                                'visible' => $model->permission == 1 || $model->permission == 2 || $model->permission == 6? true : false,
                            ],
                           
                            
                        ],
                    ],
                    
                ],
            ]
        ) ?>
        <li style="list-style: none;"><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
    </div>
</div>
<div class="sidebar-bg"></div>