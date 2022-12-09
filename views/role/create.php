<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SurveyTemplateCategories */

$this->title = 'Create Role';
$this->params['breadcrumbs'][] = ['label' => 'Manage roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section id="page-content">

    <?= $this->render('_form', [
        'model' => $model,
		'authModel' => $authModel,
		'arrModules' => $arrModules,
    ]) ?>