<?php
/*
############################################################################################
# eLuminous Technologies - Copyright (@) http://eluminoustechnologies.com
# This code is written by eLuminous Technologies, Its a sole property of
# eLuminous Technologies and cant be used / modified without license.
# Any changes/ alterations, illegal uses, unlawful distribution, copying is strictly
# prohibhited
############################################################################################
# Name : CustomerController.php
# Created on : 5th June 2017 by Suraj Malve.
# Update on  : 5th June 2017 by Suraj Malve.
# Purpose : Manage Customer.
############################################################################################
*/


namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\CustomerSearch;
use app\models\Linkcustomepackage;
use app\models\Customerinvoice;
use app\models\Package;
use app\models\Speed;
use app\models\Currency;
use app\models\Bank;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\State;
use app\models\Country;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use kartik\mpdf\Pdf;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\db\Expression;
/* EQUIPEMENT TASK */
use app\models\CustomerEquipments;
use app\models\Equipments;
use app\models\EquipmentsMacs;
use app\models\Settings;
use app\models\Customerlogs;
use app\models\TbluserStates;
use app\models\Bankdeposit;
use app\models\CustomerinvoiceSearch;

/* END EQUIPEMENT TASK */
//Yii::$app->cache->flush();
/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class ServicereportController extends Controller
{
    
    // public function behaviors()
    // {
    //     $behaviors['access'] = [
    //         'class' => AccessControl::className(),
    //         'only' => ['index','servicereport'],
    //         'rules' => [
    //                     [
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                     'matchCallback' => function($rules, $action){
    //                            $action = Yii::$app->controller->action->id;
    //                             $controller = Yii::$app->controller->id;
    //                             $route = "$controller/$action";
    //                             $post = Yii::$app->request->post();
    //                             if(\Yii::$app->user->can($route)){

    //                                     return true;
    //                             }
    //                     }
    //                 ],
    //              ],
    //     ];
    //     return $behaviors;
    // }

    public function actionIndex()
    {
    	
        $modelUserStates = new TbluserStates();
        $stateList = array();
        $todayDate = date('Y-m-d');
        $first_day_this_year = date('Y-01-01');
        $last_day_this_year = date('Y-12-31');
        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month  = date('Y-m-t');
        $arrAllCurrency = ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency');
        $postArray = Yii::$app->request->post();


        // Start Active sybscribers
        $queryActiveSubscriber = Customer::find();
        $queryActiveSubscriber->joinWith(['linkcustomerpackage.package','country','state','user','linkcustomerpackage.fk_speed_id']);
        $customerActiveSubscriber = $queryActiveSubscriber->andWhere([
                                                        'tblcustomer.status' => 'active',
                                                        'tblcustomer.is_deleted' => '0',
                                                        'installation_status' => 'Yes','is_invoice_activated' => 'Yes'
                                                    ])
                                                ->select('count(*) as counters, customer_type,SUM(package_price) as total_rev')
                                                ->groupBy(['customer_type'])
                                                ->createCommand()
                                                ->queryAll();
        // End Active sybscribers

        // Start temporary disconnedted sybscribers                                                
        $queryTemporaryDisconnected = Customer::find();
        $queryTemporaryDisconnected->joinWith(['linkcustomerpackage.package','country','state','user','linkcustomerpackage.fk_speed_id']);
        $customerTemporaryDisconnected = $queryTemporaryDisconnected->andWhere([
                                                        'tblcustomer.status' => 'inactive',
                                                        'tblcustomer.is_deleted' => '0',
                                                        'installation_status' => 'Yes','is_invoice_activated' => 'Yes'
                                                    ])
                                                ->select('count(*) as counters, customer_type,SUM(package_price) as total_rev')
                                                ->groupBy(['customer_type'])
                                                ->createCommand()
                                                ->queryAll();
        // End temporary disconnedted sybscribers                                                

        // Start permenant disconnedted sybscribers 
        $queryPermenantDisconnected = Linkcustomepackage::find();
        $queryPermenantDisconnected->joinWith(['customer as c','package','speed','currency','customer.user','customer.state']);

        $customerPermenantDisconnected = $queryPermenantDisconnected->andWhere([
                                            'is_current_package'=>'yes',
                                            'is_disconnected'=>'yes',
                                            'c.is_deleted'=>'0'
                                        ])
                                        ->select('count(*) as counters, c.customer_type,SUM(package_price) as total_rev')
                                        ->groupBy(['c.customer_type'])
                                        ->createCommand()
                                        ->queryAll();
        // End permenant disconnedted sybscribers        


        // Start pending installations
        

        $queryPendingInst = Linkcustomepackage::find();
        $queryPendingInst->joinWith(['customer as c','package','speed','currency','customer.user','customer.state']);

        $customerPendngInstallations = $queryPendingInst->andWhere([
                                            'is_current_package'=>'yes',
                                            'installation_status'=>'no',
                                            'c.is_deleted'=>'0'
                                        ])
                                        ->where(['between', 'DATE_FORMAT(linkcustomepackage.order_received_date,"%Y-%m-%d")', $first_day_this_month, $last_day_this_month ])
                                        ->select('count(*) as counters, c.customer_type,SUM(package_price) as total_rev')
                                        ->groupBy(['c.customer_type'])
                                        ->createCommand()
                                        ->queryAll();
        // End pending installations                 


        // Start pending installations
        $queryPendingInstAll = Linkcustomepackage::find();
        $queryPendingInstAll->joinWith(['customer as c','package','speed','currency','customer.user','customer.state']);

        $customerPendngInstallationsAll = $queryPendingInstAll->andWhere([
                                            'is_current_package'=>'yes',
                                            'installation_status'=>'no',
                                            'c.is_deleted'=>'0'
                                        ])
                                        ->select('count(*) as counters, c.customer_type,SUM(package_price) as total_rev')
                                        ->groupBy(['c.customer_type'])
                                        ->createCommand()
                                        ->queryAll();
        // End pending installations    


        // Start contracts
        $queryContracts = Linkcustomepackage::find();
        $queryContracts->joinWith(['customer as c','package','speed','currency','customer.user','customer.state']);
        $queryContracts->where(['is_current_package'=>'yes','c.is_invoice_activated'=>'yes','c.is_deleted'=>'0']);

        $contracts = $queryContracts->select('count(*) as counters')
                                        ->select(
                                                    "count(case when `contract_status`  = 'sent' then 1 end) AS sent_contract,
                                                    count(case when `contract_status`  = 'returned' then 1 end) AS returned_contract,
                                                    count(case when `contract_status`  = 'no_contract' then 1 end) AS no_contract,
                                                    count(case when `contract_status`  = 'not_sent' then 1 end) AS not_sent_contract,
                                                    count(case when DATE(`contract_end_date`)  < ".$todayDate." then 1 end) AS expired_contract"
                                                 )

                                        ->createCommand()
                                        ->queryAll();

        $queryContractsExpired = Linkcustomepackage::find();
        $queryContractsExpired->joinWith(['customer as c','package','speed','currency','customer.user','customer.state']);
        $queryContractsExpired->where(['is_current_package'=>'yes','c.is_invoice_activated'=>'yes','c.is_deleted'=>'0']);

        $contractsExpired = $queryContractsExpired->select('count(*) AS expired_contract')
                                        ->where(['<','DATE_FORMAT(contract_end_date,"%Y-%m-%d")',$todayDate])
                                        ->createCommand()
                                        ->queryAll();
        // End contracts

        //Start Collection
        $queryCollectionThisMonth = Bankdeposit::find()->where(['=','tblbankdeposit.is_deleted', '0']);
        $queryCollectionThisMonth->joinWith(['customer as c','customerinvoice','customer.state']);
        $collectionThisMonth = $queryCollectionThisMonth->select('sum(amount) AS total,fk_currency_id,customer_type')
                                        ->where(['between', 'DATE_FORMAT(tblbankdeposit.created_at,"%Y-%m-%d")', $first_day_this_month, $last_day_this_month ])
                                        ->groupBy(['fk_currency_id','c.customer_type'])
                                        ->createCommand()
                                        ->queryAll();


        $queryCollectionAll = Bankdeposit::find()->where(['=','tblbankdeposit.is_deleted', '0']);
        $queryCollectionAll->joinWith(['customer as c','customerinvoice','customer.state']);
        $collectionAll = $queryCollectionAll->select('sum(amount) AS total,fk_currency_id,customer_type')
                                        ->where(['between', 'DATE_FORMAT(tblbankdeposit.created_at,"%Y-%m-%d")', $first_day_this_year, $last_day_this_year ])
                                        ->groupBy(['fk_currency_id','c.customer_type'])
                                        ->createCommand()
                                        ->queryAll();

        $arrCollecctionThisMonth = $arrCollecctionAll = [];
        if (!empty($collectionThisMonth)) {
                foreach ($collectionThisMonth as $keyCollectionThisMonth => $valueCollectionThisMonth) {
                    $arrCollecctionThisMonth[$valueCollectionThisMonth['customer_type']][] = [
                                                                                           $arrAllCurrency[$valueCollectionThisMonth['fk_currency_id']] => $valueCollectionThisMonth['total']
                                                                                         ];
                }
        }

        if (!empty($collectionAll)) {
              foreach ($collectionAll as $keyCollectionAll => $valueCollectionAll) {
                    $arrCollecctionAll[$valueCollectionAll['customer_type']][] = [
                                                                                    $arrAllCurrency[$valueCollectionAll['fk_currency_id']] => $valueCollectionAll['total']
                                                                                ];
                }  
        }
        // End collections


        //Start New Billing 
        $queryNewBilling = Customerinvoice::find();
        $queryNewBilling->joinWith(['customer','linkcustomepackage','customer.user','customer.state','linkcustomepackage.package']);
        $newBillingThisMonth = $queryNewBilling->select('sum(total_invoice_amount) AS total_invoice_amount,fk_currency_id,customer_type')
                                        ->andWhere(['between', 'DATE_FORMAT(invoice_date,"%Y-%m-%d")', $first_day_this_month, $last_day_this_month ])
                                        ->groupBy(['fk_currency_id','customer_type'])
                                        ->createCommand()
                                        ->queryAll();

        $arrNewBillingThisMonth  = [];
        if (!empty($newBillingThisMonth)) {
                foreach ($newBillingThisMonth as $keyNewBillingThisMonth => $valueNewBillingThisMonth) {
                    $arrNewBillingThisMonth[$valueNewBillingThisMonth['customer_type']][] = [
                                                                                           $arrAllCurrency[$valueNewBillingThisMonth['fk_currency_id']] => $valueNewBillingThisMonth['total_invoice_amount']
                                                                                         ];
                }
        }
        // End New Billing

        // Start Recurring amount 
        $modelLinkPackage = new Linkcustomepackage();
        $invoiceSearch = new CustomerinvoiceSearch();
        $arrTotalRecurring = [];

        foreach ($arrAllCurrency as $keyCurr => $valueCurr) {
            $resultRecurring = $modelLinkPackage->getrecurring($keyCurr);    
            $resultRecurringService = $invoiceSearch->getTotalServicePrice($keyCurr);

            $amount = (array_sum((array_column($resultRecurring, 'Recurring')))) + $resultRecurringService;

            $arrTotalRecurring[$valueCurr] = $amount;
            
        }
        // End Recurring amount


        //Start Outstanding Billing 
        $arrStatus = ['partial','unpaid'];
        $queryOutstanding = Customerinvoice::find();
        $queryOutstanding->joinWith(['customer','linkcustomepackage','customer.user','customer.state','linkcustomepackage.package']);
        $outstanding = $queryOutstanding->select('sum(total_invoice_amount) AS total_invoice_amount,fk_currency_id,customer_type')
                                        ->andWhere(['in',['tblcustomerinvoice.status'],$arrStatus])
                                        ->andWhere(['<=', 'DATE_FORMAT(invoice_date,"%Y-%m-%d")', $todayDate ])
                                        ->groupBy(['fk_currency_id','customer_type'])
                                        ->createCommand()
                                        ->queryAll();

        $arrOutstandingBill  = [];
        if (!empty($outstanding)) {
                foreach ($outstanding as $keyOutstanding => $valueOutstanding) {
                    $arrOutstandingBill[$valueOutstanding['customer_type']][] = [
                                                                                   $arrAllCurrency[$valueOutstanding['fk_currency_id']] => $valueOutstanding['total_invoice_amount']
                                                                                 ];
                }
        }
        // End Outstanding Billing


        /*********** Graph statistics start ***********/

        //Start Sales Statistic
        
        $year = isset($postArray['monthly_stat_search_year'])?$postArray['monthly_stat_search_year']:date('Y');

        $startYear = date('Y',strtotime('-3 year'));
        $endYear = date('Y');
        
        $getMonthlySaleStatistics = $this->getSaleStatisticsMonthly($year);
        $getYearlySaleStatistics = $this->getSaleStatisticsYearly($startYear,$endYear);
        $getMonthlyBillingSaleStatistics = $this->getSaleStatisticsBillingMonthly($year);
        $getAnnualBillingSaleStatistics = [];
        // print_r($getMonthlyBillingSaleStatistics);die;
        $getAnnualBillingSaleStatistics = $this->getSaleStatisticsBillingYearly($startYear,$endYear);
        //End Sales Statistic

        /*********** Graph statistics End ***********/

        // echo $queryOutstanding->createCommand()->getRawSql();die;                                             
        // echo "<pre>";        print_R($getMonthlySaleStatistics);echo "</pre>";die;
        return $this->render('index', [
            'customerActiveSubscriber' => $customerActiveSubscriber,
            'customerTemporaryDisconnected' => $customerTemporaryDisconnected,
            'customerPermenantDisconnected' => $customerPermenantDisconnected,
            'customerPendngInstallations' =>  $customerPendngInstallations,
            'customerPendngInstallationsAll' => $customerPendngInstallationsAll,
            'contracts'   => $contracts,
            'contractsExpired'   => $contractsExpired,
            'arrCollecctionThisMonth'   => $arrCollecctionThisMonth,
            'arrCollecctionAll' => $arrCollecctionAll,
            'arrNewBillingThisMonth' => $arrNewBillingThisMonth,
            'arrAllCurrency' => $arrAllCurrency,
            'arrTotalRecurring' => $arrTotalRecurring,
            'arrOutstandingBill' => $arrOutstandingBill,
            'getMonthlySaleStatistics' => $getMonthlySaleStatistics,
            'getYearlySaleStatistics' => $getYearlySaleStatistics,
            'getMonthlyBillingSaleStatistics' => $getMonthlyBillingSaleStatistics,
            'getAnnualBillingSaleStatistics' => $getAnnualBillingSaleStatistics,
        ]);
    }
    

    public function getSaleStatisticsMonthly($year){
        $arrAllCurrency = ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency');
        $total = $arrMonthlyData = array();
        $model = new Customerinvoice();
        
        foreach($arrAllCurrency as $keyC => $valC){
            $arrTotaldata = $model->getTotalData($year,$keyC);

                if($arrTotaldata)
                {
                    $totalJan = $totalFeb = $totalMar = $totalApr = $totalMay = $totalJun = $totalJul = $totalAug = $totalSep = $totalOct = $totalNov = $totalDec = 0;

                    foreach ($arrTotaldata as $key => $value) {
                        
                        
                        if($value['month']=='Jan')
                        {
                            $totalJan = $totalJan+$value['c_revenue'];
                        }
                        if($value['month']=='Feb')
                        {
                            $totalFeb = $totalFeb+$value['c_revenue'];
                        }
                        if($value['month']=='Mar')
                        {
                            $totalMar = $totalMar+$value['c_revenue'];
                        }
                        if($value['month']=='Apr')
                        {
                            $totalApr = $totalApr+$value['c_revenue'];
                        }
                        if($value['month']=='May')
                        {
                            $totalMay = $totalMay+$value['c_revenue'];
                        }
                        if($value['month']=='Jun')
                        {
                            $totalJun = $totalJun+$value['c_revenue'];
                        }
                       if($value['month']=='Jul')
                        {
                            $totalJul = $totalJul+$value['c_revenue'];
                        }
                        if($value['month']=='Aug')
                        {
                            $totalAug = $totalAug+$value['c_revenue'];
                        }
                        if($value['month']=='Sept')
                        {
                            $totalSep = $totalSep+$value['c_revenue'];
                        }
                        if($value['month']=='Oct')
                        {
                            $totalOct = $totalOct+$value['c_revenue'];
                        }
                        if($value['month']=='Nov')
                        {
                            $totalNov = $totalNov+$value['c_revenue'];
                        }
                        if($value['month']=='Dec')
                        {
                            $totalDec = $totalDec+$value['c_revenue'];
                        }

                        
                        $total['totalJan'] = $totalJan;
                        $total['totalFeb'] = $totalFeb;
                        $total['totalMar'] = $totalMar;
                        $total['totalApr'] = $totalApr;
                        $total['totalMay'] = $totalMay;
                        $total['totalJun'] = $totalJun;
                        $total['totalJul'] = $totalJul;
                        $total['totalAug'] = $totalAug;
                        $total['totalSep'] = $totalSep;
                        $total['totalOct'] = $totalOct;
                        $total['totalNov'] = $totalNov;
                        $total['totalDec'] = $totalDec;
                        
                    }

                    $arrMonthlyData[$valC] = [
                                                '1' => $total['totalJan'],
                                                '2' => $total['totalFeb'],
                                                '3' => $total['totalMar'],
                                                '4' => $total['totalApr'],
                                                '5' => $total['totalMay'],
                                                '6' => $total['totalJun'],
                                                '7' => $total['totalJul'],
                                                '8' => $total['totalAug'],
                                                '9' => $total['totalSep'],
                                                '10' => $total['totalOct'],
                                                '11' => $total['totalNov'],
                                                '12' => $total['totalDec'],
                                            ];
                
                }
        }
             
        $arrGetAllMonths = array();
        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $arrGetAllMonths[date('n', $timestamp)] = date('M', $timestamp);
        }
        

        $response['monthTicks'] = $arrGetAllMonths;
        $response['monthData'] = $arrMonthlyData;   
        return $response;
        
    }

    public function getSaleStatisticsYearly($startYear,$endYear){
        $arrGetAllYears = [];

        for ($i=$startYear; $i <= $endYear; $i++) { 
            $arrGetAllYears[] =  $i;
        }

        
        $total = $arrMonthlyData = array();
        $arrAllCurrency = ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency');

        foreach($arrAllCurrency as $keyC => $valC){
            $currency = $keyC;

            $connection = \Yii::$app->db;
            $modelUserStates = new TbluserStates();
            $sessionData = Yii::$app->session;
            if(!empty($sessionData->get('user_state_id')))
            {
                if($sessionData->get('user_state_id')=="all")
                {
                    $stateList  = $modelUserStates->getUserStates();
                    $stateList  = implode("','",$stateList); 
                    $where      = 'fk_state_id IN (\''.$stateList.'\')' ; 
                }
                else
                {
                    $stateId    = $sessionData->get('user_state_id');
                    $where      = 'fk_state_id = '.$stateId;
                }       
            }
            else
            {
                $where = 'fk_state_id = null';
            }

            if($startYear == date('Y') ){
                 $where .=' and YEAR(linkcustomepackage.invoice_start_date) BETWEEN '.date("Y").' AND '.$endYear;
            }
            $sql = "SELECT Year(linkcustomepackage.invoice_start_date) as year,sum( linkcustomepackage.package_price) as c_revenue

            FROM tblcustomer
            LEFT JOIN linkcustomepackage on linkcustomepackage.fk_customer_id = tblcustomer.customer_id
            LEFT JOIN tblusers on tblusers.user_id = tblcustomer.fk_user_id
            LEFT JOIN tblstate on tblstate.state_id = tblcustomer.fk_state_id
            LEFT JOIN tblcurrency on tblcurrency.currency_id = linkcustomepackage.fk_currency_id
            WHERE invoice_start_date is not null  and  Year(linkcustomepackage.invoice_start_date) != 0
            and linkcustomepackage.package_price is not null
            and ".$where."
            and fk_currency_id = ".$currency."
            and Year(linkcustomepackage.invoice_start_date) BETWEEN ".$startYear." AND ".$endYear." 
            and tblcustomer.is_invoice_activated = 'yes'
            and linkcustomepackage.is_current_package = 'yes'
            and linkcustomepackage.is_disconnected = 'no'
            and tblcustomer.is_deleted = '0'
            and tblcustomer.status = 'active'
            GROUP by year
            order by tblcustomer.fk_user_id asc";
            
            $arrData = $connection->createCommand($sql)->queryAll();

            $ik=0;
            foreach ($arrGetAllYears as $keyData => $valueData) {

                $keytest = array_search($valueData, array_column($arrData, 'year'));
                $result = $arrData[$keytest]['c_revenue'];

                $arrMonthlyData[$valC][$ik]  = $result;
                $ik++;
            }

            
        }

        $response['yearTicks'] = $arrGetAllYears;
        $response['yearData'] = $arrMonthlyData;
        
        return $response;
        
    }

    public function getSaleStatisticsBillingMonthly($year){
        $arrData = $arrMonthlyData = $total = [];

        $first_day_this_year = date($year.'-01-01');
        $last_day_this_year = date($year.'-12-31');
        

        $arrAllCurrency = ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency');

        foreach($arrAllCurrency as $keyC => $valC){
            $currency = $keyC;
            $queryCollectionAll = Bankdeposit::find()->where(['=','tblbankdeposit.is_deleted', '0']);
            $queryCollectionAll->joinWith(['customer as c','customerinvoice','customer.state']);
            $arrData = $queryCollectionAll->select('sum(amount) AS total,fk_currency_id,MONTH(tblbankdeposit.created_at) as month')
                                            ->where(['between', 'DATE_FORMAT(tblbankdeposit.created_at,"%Y-%m-%d")', $first_day_this_year, $last_day_this_year ])
                                            ->where(['fk_currency_id' => $currency])
                                            ->groupBy(['month'])
                                            ->createCommand()
                                            ->queryAll();


            $totalJan = $totalFeb = $totalMar = $totalApr = $totalMay = $totalJun = $totalJul = $totalAug = $totalSep = $totalOct = $totalNov = $totalDec = 0;

            foreach ($arrData as $key => $value) {

                if($value['month']=='1')
                {
                    $totalJan = $value['total'];
                }
                if($value['month']=='2')
                {
                    $totalFeb = $value['total'];
                }
                if($value['month']=='3')
                {
                    $totalMar = $value['total'];
                }
                if($value['month']=='4')
                {
                    $totalApr = $value['total'];
                }
                if($value['month']=='5')
                {
                    $totalMay = $value['total'];
                }
                if($value['month']=='6')
                {
                    $totalJun = $value['total'];
                }
               if($value['month']=='7')
                {
                    $totalJul = $value['total'];
                }
                if($value['month']=='8')
                {
                    $totalAug = $value['total'];
                }
                if($value['month']=='9')
                {
                    $totalSep = $value['total'];
                }
                if($value['month']=='10')
                {
                    $totalOct = $value['total'];
                }
                if($value['month']=='11')
                {
                    $totalNov = $value['total'];
                }
                if($value['month']=='12')
                {
                    $totalDec = $value['total'];
                }

                
                        
            }
                $arrMonthlyData[$valC] = [
                                            '1' => $totalJan,
                                            '2' => $totalFeb,
                                            '3' => $totalMar,
                                            '4' => $totalApr,
                                            '5' => $totalMay,
                                            '6' => $totalJun,
                                            '7' => $totalJul,
                                            '8' => $totalAug,
                                            '9' => $totalSep,
                                            '10' => $totalOct,
                                            '11' => $totalNov,
                                            '12' => $totalDec,
                                        ];

        }
        

        $arrGetAllMonths = array();
        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $arrGetAllMonths[date('n', $timestamp)] = date('M', $timestamp);
        }
        

        $response['monthTicks'] = $arrGetAllMonths;
        $response['monthData'] = $arrMonthlyData;   
        return $response;
        
    }

    public function getSaleStatisticsBillingYearly($startYear,$endYear){
        $arrGetAllYears = $arrData = $arrMonthlyData = $total = [];

        for ($i=$startYear; $i <= $endYear; $i++) { 
            $arrGetAllYears[] =  $i;
        }


        $first_day_this_year = date($startYear.'-01-01');
        $last_day_this_year = date($endYear.'-12-31');
            // echo $first_day_this_year,$last_day_this_year;die;
        $total = $arrMonthlyData = array();
        $arrAllCurrency = ArrayHelper::map(Currency::find()->asArray()->all(), 'currency_id', 'currency');

        $arrGetAllMonths = array();
        $connection = \Yii::$app->db;

        foreach($arrAllCurrency as $keyC => $valC){

            $currency = $keyC;
            $queryCollectionAll = Bankdeposit::find()->where(['=','tblbankdeposit.is_deleted', '0']);
            $queryCollectionAll->joinWith(['customer as c','customerinvoice','customer.state']);
            $arrData = $queryCollectionAll->select('sum(amount) AS total,fk_currency_id,YEAR(tblbankdeposit.created_at) as year')
                                            ->where(['between', 'DATE_FORMAT(tblbankdeposit.created_at,"%Y")', $startYear, $endYear ])
                                            ->where(['fk_currency_id' => $currency])
                                            ->groupBy(['year','fk_currency_id'])
                                            ->createCommand()
                                            ->queryAll();

            // $sql = "SELECT Year(tblbankdeposit.created_at) as year,sum( tblbankdeposit.amount) as c_revenue,
            // MONTH(tblbankdeposit.created_at) as month

            // FROM tblbankdeposit
            // LEFT JOIN tblcustomer on tblbankdeposit.fk_customer_id = tblcustomer.customer_id
            // LEFT JOIN tblcustomerinvoice on tblbankdeposit.fk_invoice_id = tblcustomerinvoice.customer_invoice_id
            // LEFT JOIN tblstate on tblstate.state_id = tblcustomer.fk_state_id
            // WHERE tblbankdeposit.created_at is not null  and  Year(tblbankdeposit.created_at) != 0
            // and fk_currency_id = ".$currency."
            // and Year(tblbankdeposit.created_at) = ".$endYear."
            // GROUP by year,month";
            
            // // and Year(tblbankdeposit.created_at) BETWEEN ".$startYear." AND ".$endYear." 

            // $arrData = $queryCollectionAll->createCommand($sql)->queryAll();

        }

        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') - $i, 1);
            $arrGetAllMonths[date('n', $timestamp)] = date('M', $timestamp);
        }
        

        $response['monthTicks'] = $arrGetAllMonths;
        $response['monthData'] = $arrMonthlyData;   
        return $response;

    }


}
