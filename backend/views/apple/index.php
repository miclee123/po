<?php

/**
 * @var View $this
 *
 * @var Apple[] $apples
 */

use backend\models\Apple;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

echo Html::beginForm('', 'post', []) .
    Html::submitButton('Создать новые яблоки', ['class' => 'btn btn-primary']) .
    Html::hiddenInput('action', 'generate') .
    Html::endForm() .
    Html::tag('br') .
    Html::tag('br');
Pjax::begin();
?>
<div id="apple-background">
    <?php foreach ($apples as $apple) {
        echo Html::tag('div', $apple->renderApple(), [
            'class' => 'apple-item-wrap'
        ]);
    } ?>
</div>
<?php Pjax::end();