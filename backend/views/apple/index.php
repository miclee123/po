<?php

/**
 * @var View $this
 *
 * @var Apple[] $apples
 */

use backend\models\Apple;
use yii\helpers\Html;
use yii\web\View;

echo Html::beginForm('', 'post', []);
echo Html::submitButton('Создать новые яблоки', ['class' => 'btn btn-primary']);
echo Html::hiddenInput('action', 'generate');
echo Html::endForm();
echo Html::tag('br');
echo Html::tag('br');
Apple::nicePrint($apples);
?>
<div id="apple-background">

</div>