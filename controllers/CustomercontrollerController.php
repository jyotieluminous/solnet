<?php

namespace app\controllers;

class CustomercontrollerController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
