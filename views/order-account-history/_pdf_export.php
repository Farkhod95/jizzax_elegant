<?php
use yii\helpers\Html;
?>

<table border="1" width="100%" cellpadding="5" cellspacing="0">
    <thead>
        <tr style="background:#ccc;">
            <th>#</th>
            <th>Mijoz</th>
            <th>Hodim</th> <!-- ✅ -->
            <th>To‘lanadigan summa ($)</th>
            
            <th>To‘langan summa ($)</th>
            <th>To‘langan qarz ($)</th>
            <th>Foyda ($)</th> <!-- ✅ -->
            <th>Umumiy qarz ($)</th>
            <th>Sana</th>
            <th>Holat</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grouped = [];
        foreach ($rows as $row) {
            $key = date('Y-m-d', strtotime($row['datetime']));
            $grouped[$key][] = $row;
        }

        foreach ($grouped as $date => $groupRows):
            $counter = 1;

            $sum_all_product = 0;
            $sum_profit = 0;

            $sum_paid = 0;
            $sum_paid_debt = 0;
            $sum_total_debt = 0;

            $sum_paid_som = 0;
            $sum_paid_cart = 0;
            $sum_paid_transfers = 0;

            $sum_paid_debt_som = 0;
            $sum_paid_debt_cart = 0;
            $sum_paid_debt_transfers = 0;
        ?>
            <tr>
                <td colspan="10" style="background-color:#e9f7fc;">
                    <b><?= date('d.m.Y', strtotime($date)) ?></b>
                </td>
            </tr>

            <?php foreach ($groupRows as $row): ?>
                <tr>
                    <td><?= $counter++ ?></td>
                    <td><b><?= Html::encode($row['client_name'] ?? '') ?></b></td>

                    <td><b><?= Html::encode($row['created_by_name'] ?? '-') ?></b></td> <!-- ✅ -->

                    <td><b><?= number_format(($row['all_product_sum'] ?? 0), 2) ?></b></td>

                    

                    <td style="font-weight: bold; font-size:12px; min-width:220px;">
                        <?php if (($row['action'] ?? '') === 'Buyurtma qilgan') { ?>
                            <div><b><?= number_format($row['all_summ_dollar'] ?? 0, 2) ?></b> </div>
                            <!-- <div><span style="color:rgb(184, 27, 22);"><b>$:</b> <?= number_format($row['sum_transfers'] ?? 0, 2) ?></span></div>
                            <div><span style="color:#333;"><b>N:</b> <?= number_format($row['sum_som'] ?? 0, 2) ?></span></div>
                            <div><span style="color:#007bff;"><b>K:</b> <?= number_format($row['sum_cart'] ?? 0, 2) ?></span></div> -->
                        <?php } ?>
                    </td>

                    <td style="font-weight: bold; font-size:12px; min-width:220px;">
                        <?php if (($row['action'] ?? '') === 'Qarz to‘lagan') { ?>
                            <div><b><?= number_format($row['paid_debt'] ?? 0, 2) ?></b> </div>
                            <!-- <div><span style="color:rgb(184, 27, 22);"><b>$:</b> <?= number_format($row['debt_sum_transfers'] ?? 0, 2) ?></span></div>
                            <div><span style="color:#333;"><b>N:</b> <?= number_format($row['debt_sum_som'] ?? 0, 2) ?></span></div>
                            <div><span style="color:#007bff;"><b>K:</b> <?= number_format($row['debt_summ_cart'] ?? 0, 2) ?></span></div> -->
                        <?php } ?>
                    </td>
                    <td><b><?= number_format(($row['all_profit_dollar'] ?? 0), 2) ?></b></td> <!-- ✅ -->
                    <td><b><?= number_format(($row['total_debt'] ?? 0), 2) ?></b></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['datetime'])) ?></td>

                    <td style="color: <?= (($row['action'] ?? '') === 'Qarz to‘lagan') ? 'red' : 'green' ?>">
                        <b><?= Html::encode($row['action'] ?? '') ?></b>
                    </td>
                </tr>

                <?php
                // Kunlik yig‘indilar
                $sum_all_product += ($row['all_product_sum'] ?? 0);
                $sum_profit      += ($row['all_profit_dollar'] ?? 0);

                $sum_paid += ($row['all_summ_dollar'] ?? 0);
                $sum_paid_som += ($row['sum_som'] ?? 0);
                $sum_paid_cart += ($row['sum_cart'] ?? 0);
                $sum_paid_transfers += ($row['sum_transfers'] ?? 0);

                $sum_paid_debt += ($row['paid_debt'] ?? 0);
                $sum_paid_debt_som += ($row['debt_sum_som'] ?? 0);
                $sum_paid_debt_cart += ($row['debt_summ_cart'] ?? 0);
                $sum_paid_debt_transfers += ($row['debt_sum_transfers'] ?? 0);

                $sum_total_debt += ($row['total_debt'] ?? 0);
                ?>
            <?php endforeach; ?>

            <!-- Jami qatori -->
            <tr style="background-color:rgb(214, 183, 183); font-weight:bold;">
                <td colspan="3" style="text-align:right;">Umumiy summa:</td>
                <td><?= number_format($sum_all_product, 2) ?></td>
                <td><?= number_format($sum_profit, 2) ?></td>

                <td style="font-weight: bold; font-size:12px;">
                    <div><span style="color: green;"><b>Jami($):</b> <?= number_format($sum_paid ?? 0, 2) ?></span></div>
                    <div><span style="color:rgb(184, 27, 22);"><b>$:</b> <?= number_format($sum_paid_transfers ?? 0, 2) ?></span></div>
                    <div><span style="color:#333;"><b>N:</b> <?= number_format($sum_paid_som ?? 0, 2) ?></span></div>
                    <div><span style="color:#007bff;"><b>K:</b> <?= number_format($sum_paid_cart ?? 0, 2) ?></span></div>
                </td>

                <td style="font-weight: bold; font-size:12px;">
                    <div><span style="color: green;"><b>Jami($):</b> <?= number_format($sum_paid_debt ?? 0, 2) ?></span></div>
                    <div><span style="color:rgb(184, 27, 22);"><b>$:</b> <?= number_format($sum_paid_debt_transfers ?? 0, 2) ?></span></div>
                    <div><span style="color:#333;"><b>N:</b> <?= number_format($sum_paid_debt_som ?? 0, 2) ?></span></div>
                    <div><span style="color:#007bff;"><b>K:</b> <?= number_format($sum_paid_debt_cart ?? 0, 2) ?></span></div>
                </td>

                <td><?= number_format($sum_total_debt, 2) ?></td>
                <td colspan="2"></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
