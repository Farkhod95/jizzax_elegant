<?php
/** @var array $items */
/** @var \app\models\ProductCategory $pc */
use yii\helpers\Html;
use app\models\ProductCategory;

if (empty($items)) {
    echo '<div class="text-muted" style="padding:10px 0;">Hozircha o‘lcham kiritilmagan.</div>';
    return;
}
?>
<table class="table table-sm table-striped table-bordered mb-0">
    <thead>
        <tr>
            <th style="width:60px; text-align:center;">№</th>
            <th style="width:180px;">O‘lcham</th>
            <th>Tip</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $i => $row): ?>
        <?php
            $size = $row['size'] ?? '—';
            $typeRaw = $row['type'] ?? null;
            // Agar getTypeView() static bo‘lmasa: $pc->getTypeView((int)$typeRaw) deb chaqiring
            $typeLabel = ($typeRaw === null || $typeRaw === '')
                ? '—'
                : (is_numeric($typeRaw) ? ProductCategory::getTypeView((int)$typeRaw) : (string)$typeRaw);
        ?>
        <tr>
            <td style="text-align:center;"><?= $i + 1 ?></td>
            <td><b style="font-size:16px;color:#e11d48;"><?= Html::encode($size) ?></b></td>
            <td>
                <?php if ($typeLabel !== '—'): ?>
                    <span class="badge" style="font-size:14px;background:#eef2ff;color:#1f2937;border:1px solid #c7d2fe;">
                        <?= Html::encode($typeLabel) ?>
                    </span>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<style>
#ajaxCrudModal .modal-dialog { max-width: 640px; }
.table>thead th { background:#f8fafc; }
</style>
