<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\type_exercises\modules\suggestion_constructor\models\SuggestionConstructor */

$this->title = 'Update Suggestion Constructor: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Suggestion Constructors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="suggestion-constructor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
