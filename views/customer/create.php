<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'Create Customer';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-create">

    <?= $this->render('_form', [
        'model' => $model,
		'modelLinkCustPackage'=>$modelLinkCustPackage,
		'stateList'=>$stateList,
		'countryList'=>$countryList,
		'packageList'=>$packageList,
		'speedList'=>$speedList,
		'currencyList'=>$currencyList
    ]) ?>

</div>
