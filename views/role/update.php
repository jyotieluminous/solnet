<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SurveyTemplateCategories */

$this->title = 'Edit Role : '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Manage Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<section id="page-content">

    <?= $this->render('_form', [
        'model' => $model,
		'authModel' => $authModel,
		'arrModules' => $arrModules
    ]) ?>