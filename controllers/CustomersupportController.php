<?php

namespace app\controllers;
use Yii;
use app\models\Customer;
use app\models\CustomerSearch;
use app\models\Customerinvoice;
use app\models\CustomerinvoiceSearch;
use app\models\CustomerpaymentSearch;
use app\models\Customerpayment;
use app\models\Currency;
use app\models\State;
use kartik\mpdf\Pdf;
use mPDF;
use yii\helpers\ArrayHelper;
use app\models\Package;
use app\models\User;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\web\Response;
class CustomersupportController extends \yii\web\Controller
{
    
	public function behaviors()
    {
		$behaviors['access'] = [
			'class' => AccessControl::className(),
                        'only' => ['index','customersupportpayment','paycustomersupport'],
			'rules' => [
                        [
                        'allow' => true,
                        'roles' => ['@'],
                                        'matchCallback' => function($rules, $action){
                                               $action = Yii::$app->controller->action->id;
                                                $controller = Yii::$app->controller->id;
                                                $route = "$controller/$action";
                                                $post = Yii::$app->request->post();
                                                if(\Yii::$app->user->can($route)){

                                                        return true;
                                                }
                                        }
                    ],
                 ],
		];
		return $behaviors;
    }

    public function actionIndex()
    {
    	$searchModel = new CustomerSearch();
		//$searchModel->linkcustomerpackage->is_current_package= 'yes';
		$queryParams = Yii::$app->request->queryParams;
		
		$queryParams['CustomerSearch']['customer_type']='Broadband';
		
        $dataProvider = $searchModel->searchCustomerSupport($queryParams);
    	return $this->render('customer_support', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionPaycustomersupport($id)
	{
		$model = Customerinvoice::findOne($id);
		
		$arrStatus = array('unpaid','partial');
        $arrSolnetId    = Customerinvoice::find()->joinWith('customer')->select(['fk_customer_id','solnet_customer_id'])->where(['is_deleted'=>'0','is_invoice_activated'=>'yes'])->andWhere(['IN',['tblcustomerinvoice.status'],$arrStatus])->asArray()->all();
        
        $SolnetIdListData   = ArrayHelper::map($arrSolnetId,'fk_customer_id','solnet_customer_id');


		if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {

       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       return ActiveForm::validate($model);
      }
		$searchModel = new CustomerpaymentSearch();
		$queryParams = Yii::$app->request->getQueryParams();
		//$queryParams["CustomerpaymentSearch"]["tblcustomerpayment.fk_customer_id"]   = $model->fk_customer_id;
		$queryParams["CustomerpaymentSearch"]["fk_invoice_id"]   = $model->customer_invoice_id;
		$dataProvider = $searchModel->search($queryParams);
		$payModel = new Customerpayment();
		$intPendingAmt =  $model->pending_amount;
		$intCustId =  $model->fk_customer_id;
		$intCurrencyId = $model->linkcustomepackage->fk_currency_id;
		$intinvoiceId =  $model->customer_invoice_id;
		$strInvoiceNumber = $model->invoice_number;
		$model->scenario = 'pay';
		$arrPostData = Yii::$app->request->post();
		
		/*************To fetch currency from table************/
		$arrCurrency 	= Currency::find()->where(['status'=>'active'])->all();
		$currencyListData	= ArrayHelper::map($arrCurrency,'currency_id','currency');
		/*************To fetch state from table************/

		if($model->load(Yii::$app->request->post()))
		{

			if(!empty($arrPostData)) {
				
				$paymentModel = new Customerpayment();
				$intTotalPaid = $arrPostData['Customerinvoice']['payment_amount'];
				$paymentModel->fk_customer_id = $intCustId;
				$paymentModel->fk_invoice_id = $intinvoiceId;
				$paymentModel->discount = 0;
				$paymentModel->deduct_tax = 0;
				$paymentModel->bank_admin = 0;
				
				$paymentModel->payment_method = $arrPostData['Customerpayment']['payment_method'];
				$paymentModel->cheque_no =  $arrPostData['Customerpayment']['cheque_no'];
				$paymentModel->amount_paid = $intTotalPaid;
				//$paymentModel->reciept_no = $arrPostData['Customerpayment']['reciept_no'];
				$paymentModel->fk_currency_id = $intCurrencyId;
				$paymentModel->comment = $arrPostData['Customerpayment']['comment'];
				$paymentModel->created_at = date('Y-m-d h:i:s');
				$paymentModel->payment_date =date("Y-m-d",  strtotime($arrPostData['Customerpayment']['payment_date']));
				$paymentModel->is_payment_by_cs = 'yes';
				$paymentModel->cs_user_id = Yii::$app->user->identity->user_id;
					//save payment details
				if($paymentModel->save())
				{
					$model->po_wo_number = 0;
					//save invoice details
					if($model->save())
					{
						$connection = Yii::$app->getDb();
						
						$command = $connection->createCommand(
								'UPDATE tblcustomer SET po_wo_number = "0" WHERE customer_id ='.$intCustId);
						$command->execute();
					}

					$session = Yii::$app->session;
					$session->setFlash('success_paid',INVOICE_PAID_SUCCESSFULL);
					/************Log Activity*********/
					$logArray = array();
					$logArray['fk_user_id'] = yii::$app->user->identity->user_id;
					$logArray['module'] = 'Pay Invoice';
					$logArray['action'] = 'update';
					$logArray['message'] = ' "'.yii::$app->user->identity->name.'" has paid the invoice of "'.$model->customer->name.'".';
					$logArray['created'] = date('Y-m-d H:i:s');
					Yii::$app->customcomponents->logActivity($logArray);
					/************Log Activity*********/
					return $this->redirect(['customersupport/paycustomersupport','id'=>$id]);
				}
				else{
					
					if($paymentModel->getErrors())
					{
						foreach ($paymentModel->getErrors() as $key => $value) 
						{
							$arrErrors[] = $value[0];
						}
						$message = implode("<br>",$arrErrors);
						$session = Yii::$app->session;
						$session->setFlash('error','Error <br>'.$message);
						if(isset($arrPostData['Customerinvoice']['check']) && $arrPostData['Customerinvoice']['check']==1)
						
						{
							return $this->render('pay_customer_support', [
							'model' => $model,
							'pay'=>$paymentModel,
							'bank'=>$bank,
							'dataProvider'=>$dataProvider,
							'searchModel'=>$searchModel,
							'currencyList'=>$currencyListData,
							'data'=>$SolnetIdListData
							]);
						}
						else
						{
							return $this->render('pay_customer_support', [
							'model' => $model,
							'pay'=>$paymentModel,
							
							'dataProvider'=>$dataProvider,
							'searchModel'=>$searchModel,
							'currencyList'=>$currencyListData,
							'data'=>$SolnetIdListData
							]);
						}
					}
					
				}
					//}

				}


		}else{
			
			return $this->render('pay_customer_support', [
					'model' => $model,
					'pay'=>$payModel,
					
					'dataProvider'=>$dataProvider,
					'searchModel'=>$searchModel,
					'currencyList'=>$currencyListData,
					'data'=>$SolnetIdListData
			]);
		}
	}

	public function actionCustomersupportpayment()
    {
        $searchModel = new CustomerpaymentSearch();
        $queryParams = Yii::$app->request->queryParams;
       
        
        $queryParams['CustomerpaymentSearch']['is_payment_by_cs']='yes';
        $dataProvider = $searchModel->searchCustomersupport($queryParams);

        //$dataProvider = $searchModel->searchCustomersupport(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
