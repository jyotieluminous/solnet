<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Customer */

$this->title = 'View Service Report';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://adminlte.io/themes/AdminLTE/bower_components/Ionicons/css/ionicons.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<!-- FLOT CHARTS -->
<script src="https://adminlte.io/themes/AdminLTE/bower_components/Flot/jquery.flot.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="https://adminlte.io/themes/AdminLTE/bower_components/Flot/jquery.flot.resize.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="https://adminlte.io/themes/AdminLTE/bower_components/Flot/jquery.flot.pie.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="https://adminlte.io/themes/AdminLTE/bower_components/Flot/jquery.flot.categories.js"></script>
<script type="text/javascript" src="https://www.jqueryflottutorial.com/js/flot/jquery.flot.time.js"></script>
<script type="text/javascript" src="https://www.jqueryflottutorial.com/js/flot/jquery.numberformatter-1.2.3.min.js"></script>
<script type="text/javascript" src="https://www.jqueryflottutorial.com/js/flot/jquery.flot.symbol.js"></script>


<style type="text/css">
.info-box-content {
    padding: 5px 10px;
    margin-left: 0px!important;
}

.info-box-text{
	color: black;
	font-weight: 800;
}

.chart-legend {
  width: 40%;
  margin: 20px auto;
}
.legendLabel {
  padding-right: 10px;
}

</style>
<div class="customer-view">
    
<div class="box box-default">
	<div class="box-body">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
	              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Service Report</a></li>
	              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Sales Statistic</a></li>
            	</ul>
            </div>
            <div class="tab-content">
	            <div class="tab-pane active" id="tab_1">
								<div class="tbllanguage-form View-Customer-sec">
									
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-3 form-group">
													<div class="info-box bg-aqua">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Active Subscribers</span>
											              <?php 
											              	if(!empty($customerActiveSubscriber)){
											              	$sumActiveSubscriberRev = 0;
										              		$sumActiveSubscriber = 0;
											              	foreach($customerActiveSubscriber as $keyActiveSub => $valueActiveSub)
											              	{
																$sumActiveSubscriberRev = $valueActiveSub['total_rev'] + $sumActiveSubscriberRev;
									              				$sumActiveSubscriber = $valueActiveSub['counters']+$sumActiveSubscriber;

											              ?>
											              <h4><?php echo $valueActiveSub['customer_type'] ?> = <strong><?php echo number_format($valueActiveSub['counters']) ?></strong></h4>
											              <?php } ?>
											              <h4>Total Rev. = <strong>IDR <?php echo number_format($sumActiveSubscriberRev,2) ?></strong></h4>
											              <h4>Total Pending Installation = <strong><?php echo number_format($sumActiveSubscriber) ?></strong></h4>
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											            </div>
										          	</div>
												</div>

												<div class="col-md-3 form-group">
													<div class="info-box bg-yellow">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Temporary Disconnected</span>
											              <?php 
											              	if(!empty($customerTemporaryDisconnected)){
											              	$sumTemporaryDisconnectedRev = 0;
										              		$sumTemporaryDisconnected = 0;
											              	foreach($customerTemporaryDisconnected as $keyTemp => $valueTemp)
											              	{ 
											              		$sumTemporaryDisconnectedRev = $valueTemp['total_rev'] + $sumTemporaryDisconnectedRev;
									              				$sumTemporaryDisconnected = $valueTemp['counters']+$sumTemporaryDisconnected;
											              ?>
											              <h4><?php echo $valueTemp['customer_type'] ?> = <strong><?php echo number_format($valueTemp['counters']) ?></strong></h4>
											              <?php } ?>
											              <h4>Total Rev. = <strong>IDR <?php echo number_format($sumTemporaryDisconnectedRev,2) ?></strong></h4>
											              <h4>Total Pending Installation = <strong><?php echo number_format($sumTemporaryDisconnected) ?></strong></h4>
											             
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											            </div>
										          	</div>
												</div>

												<div class="col-md-3 form-group">
													<div class="info-box bg-red">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Permanent Disconnected</span>
											              <?php 
											              	if(!empty($customerPermenantDisconnected)){

											              	$sumPermenantDisconnectedRev = 0;
										              		$sumPermenantDisconnected = 0; 
											              	foreach($customerPermenantDisconnected as $keyPermanent => $valuePermanent)
											              	{
											              		$sumPermenantDisconnectedRev = $valuePermanent['total_rev'] + $sumPermenantDisconnectedRev;
									              				$sumPermenantDisconnected = $valuePermanent['counters']+$sumPermenantDisconnected;
											              ?>
											              <h4><?php echo $valuePermanent['customer_type'] ?> = <strong><?php echo number_format($valuePermanent['counters']) ?></strong></h4>
											              <?php } ?>
											              <h4>Total Rev. = <strong>IDR <?php echo number_format($sumPermenantDisconnectedRev,2) ?></strong></h4>
											              <h4>Total Pending Installation = <strong><?php echo number_format($sumPermenantDisconnected) ?></strong></h4>
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											            </div>
										          	</div>
												</div>

												<div class="col-md-3 form-group">
													<div class="info-box bg-green">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Pending Installation  (<?php echo date('M Y') ?>) </span>
											              <?php 
											              		if(!empty($customerPendngInstallations)){


											              		$sumPendingInstRev = 0;
											              		$sumPendingInst = 0;
											              		foreach($customerPendngInstallations as $keyPendingInst => $valuePendingInst)
											              		{ 
											              			$sumPendingInstRev = $valuePendingInst['total_rev'] + $sumPendingInstRev;
											              			$sumPendingInst = $valuePendingInst['counters']+$sumPendingInst;
											              ?>
											              <h4><?php echo $valuePendingInst['customer_type'] ?> = <strong><?php echo number_format($valuePendingInst['counters']) ?></strong></h4>
											              <?php 
											          			} 
											              ?>
											              <h4>Total Rev. = <strong>IDR <?php echo number_format($sumPendingInstRev,2) ?></strong></h4>
											              <h4>Total Pending Installation = <strong><?php echo number_format($sumPendingInst) ?></strong></h4>
							
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											            </div>
										          	</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 form-group">
													<div class="info-box bg-green">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Pending Installation (All) </span>
											              <?php 
											              		if(!empty($customerPendngInstallationsAll)){
											              		$sumPendingInstAllRev = 0;
											              		$sumPendingInstAll = 0;
											              		foreach($customerPendngInstallationsAll as $keyPendingInstAll => $valuePendingInstAll)
											              		{ 
											              			$sumPendingInstAllRev = $valuePendingInstAll['total_rev'] + $sumPendingInstAllRev;
											              			$sumPendingInstAll = $valuePendingInstAll['counters']+$sumPendingInstAll;
											              ?>
											              <h4><?php echo $valuePendingInstAll['customer_type'] ?> = <strong><?php echo number_format($valuePendingInstAll['counters']) ?></strong></h4>
											              <?php 
											          			} 
											              ?>
											              <h4>Total Rev. = <strong>IDR <?php echo number_format($sumPendingInstAllRev,2) ?></strong></h4>
											              <h4>Total Pending Installation = <strong><?php echo number_format($sumPendingInstAll) ?></strong></h4>
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

												<div class="col-md-6 form-group">
													<div class="info-box bg-red">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">New Billing / Activated (<?php echo date('M Y') ?>) </span>
											              <?php 
											              	if(!empty($arrNewBillingThisMonth)){ 
											              	$IDRtotalNewBill = $SGDtotalNewBill = $USDtotalNewBill = 0;
											              ?>
											              <table class="table">
											              	<thead>
											              		<th></th>
											              		<th>IDR</th>
											              		<th>SGD</th>
											              		<th>USD</th>
											              	</thead>
											              	<tbody>
											              		<?php 
											              			
											              			foreach($arrNewBillingThisMonth as $keyNewBillingThisMonth => $valueNewBillingThisMonth)
											              			{ 
											              				
											              		?>
											              		<tr>
												              		<td><?php echo $keyNewBillingThisMonth ?></td>
												              		<td><?php 
												              				$IDR = array_column($valueNewBillingThisMonth, 'IDR');
												              				if (isset($IDR[0])) {
												              					echo $IDR[0];
												              					$IDRtotalNewBill = $IDRtotalNewBill + $IDR[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$SGD = array_column($valueNewBillingThisMonth, 'SGD');
												              				if (isset($SGD[0])) {
												              					echo $SGD[0];
												              					$SGDtotalNewBill = $SGDtotalNewBill + $SGD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$USD = array_column($valueNewBillingThisMonth, 'USD');
												              				if (isset($USD[0])) {
												              					echo $USD[0];
												              					$USDtotalNewBill = $USDtotalNewBill + $USD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
											              		</tr>
											              		<?php 
												          			} 
													            ?>
													            <tr>
													            	<td>Total Rev.</td>
													            	<td><?php echo $IDRtotalNewBill ?></td>
													            	<td><?php echo $SGDtotalNewBill ?></td>
													            	<td><?php echo $USDtotalNewBill ?></td>
													            </tr>
											              	</tbody>

											              </table>
												          
											             
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

											</div>

											<div class="row">
												<div class="col-md-6 form-group">
													<div class="info-box bg-yellow">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Recurring </span>
											              <?php 
											              	if(!empty($arrTotalRecurring)){ 
											              	
											              ?>
											              <table class="table">
											              	<thead>
											              		<th></th>
											              		<th></th>
											              	</thead>
											              	<tbody>
											              		<?php 
											              			
											              			foreach($arrTotalRecurring as $keyTotalRecurring => $valueTotalRecurring)
											              			{ 
											              				
											              		?>
											              		<tr>
												              		<td><?php echo $keyTotalRecurring ?></td>
												              		<td><?php echo $valueTotalRecurring ?></td>
												              		
											              		</tr>
											              		<?php 
												          			} 
													            ?>
													           
											              	</tbody>

											              </table>
												          
											             
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

												<div class="col-md-6 form-group">
													<div class="info-box bg-aqua">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Outstanding until this month </span>
											              <?php 
											              	if(!empty($arrOutstandingBill)){ 
											              	$IDRtotalOutstandingBill = $SGDtotalOutstandingBill = $USDtotalOutstandingBill = 0;
											              ?>
											              <table class="table">
											              	<thead>
											              		<th></th>
											              		<th>IDR</th>
											              		<th>SGD</th>
											              		<th>USD</th>
											              	</thead>
											              	<tbody>
											              		<?php 
											              			
											              			foreach($arrOutstandingBill as $keyOutstandingBill => $valueOutstandingBill)
											              			{ 
											              				if (!empty($keyOutstandingBill)) {
											              					
											              		?>
											              		<tr>
												              		<td><?php echo $keyOutstandingBill ?></td>
												              		<td><?php 
												              				$IDR = array_column($valueOutstandingBill, 'IDR');
												              				if (isset($IDR[0])) {
												              					echo $IDR[0];
												              					$IDRtotalOutstandingBill = $IDRtotalOutstandingBill + $IDR[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$SGD = array_column($valueOutstandingBill, 'SGD');
												              				if (isset($SGD[0])) {
												              					echo $SGD[0];
												              					$SGDtotalOutstandingBill = $SGDtotalOutstandingBill + $SGD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$USD = array_column($valueOutstandingBill, 'USD');
												              				if (isset($USD[0])) {
												              					echo $USD[0];
												              					$USDtotalOutstandingBill = $USDtotalOutstandingBill + $USD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
											              		</tr>
											              		<?php 
												          			} 
												          		}
													            ?>
													            <tr>
													            	<td>Total Rev.</td>
													            	<td><?php echo $IDRtotalOutstandingBill ?></td>
													            	<td><?php echo $SGDtotalOutstandingBill ?></td>
													            	<td><?php echo $USDtotalOutstandingBill ?></td>
													            </tr>
											              	</tbody>

											              </table>
												          
											             
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 form-group">
													<div class="info-box bg-aqua">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total collected (<?php echo date('M Y') ?>) </span>
											              <?php 
											              	if(!empty($arrCollecctionThisMonth)){ 
											              	$IDRtotal = $SGDtotal = $USDtotal = 0;
											              ?>
											              <table class="table">
											              	<thead>
											              		<th></th>
											              		<th>IDR</th>
											              		<th>SGD</th>
											              		<th>USD</th>
											              	</thead>
											              	<tbody>
											              		<?php 
											              			
											              			foreach($arrCollecctionThisMonth as $keyCollecctionThisMonthAll => $valueCollecctionThisMonthAll)
											              			{ 
											              				if (!empty($keyCollecctionThisMonthAll)) {
											              					
											              		?>
											              		<tr>
												              		<td><?php echo $keyCollecctionThisMonthAll ?></td>
												              		<td><?php 
												              				$IDR = array_column($valueCollecctionThisMonthAll, 'IDR');
												              				if (isset($IDR[0])) {
												              					echo $IDR[0];
												              					$IDRtotal = $IDRtotal + $IDR[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$SGD = array_column($valueCollecctionThisMonthAll, 'SGD');
												              				if (isset($SGD[0])) {
												              					echo $SGD[0];
												              					$SGDtotal = $SGDtotal + $SGD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$USD = array_column($valueCollecctionThisMonthAll, 'USD');
												              				if (isset($USD[0])) {
												              					echo $USD[0];
												              					$USDtotal = $USDtotal + $USD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
											              		</tr>
											              		<?php 
												          			} 
												          		}
													            ?>
													            <tr>
													            	<td>Total Rev.</td>
													            	<td><?php echo $IDRtotal ?></td>
													            	<td><?php echo $SGDtotal ?></td>
													            	<td><?php echo $USDtotal ?></td>
													            </tr>
											              	</tbody>

											              </table>
												          
											             
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

												<div class="col-md-6 form-group">
													<div class="info-box bg-yellow">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total collected (<?php echo date('Y') ?>) </span>
											              <?php 
											              	if(!empty($arrCollecctionAll)){ 
											              	$IDRtotal = $SGDtotal = $USDtotal = 0;
											              ?>
											              <table class="table">
											              	<thead>
											              		<th></th>
											              		<th>IDR</th>
											              		<th>SGD</th>
											              		<th>USD</th>
											              	</thead>
											              	<tbody>
											              		<?php 
											              			
											              			foreach($arrCollecctionAll as $keyCollecctionAll => $valueCollecctionAll)
											              			{ 
											              				if (!empty($keyCollecctionAll)) {
											              					
											              				
											              		?>
											              		<tr>
												              		<td><?php echo $keyCollecctionAll ?></td>
												              		<td><?php 
												              				$IDR = array_column($valueCollecctionAll, 'IDR');
												              				if (isset($IDR[0])) {
												              					echo $IDR[0];
												              					$IDRtotal = $IDRtotal + $IDR[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$SGD = array_column($valueCollecctionAll, 'SGD');
												              				if (isset($SGD[0])) {
												              					echo $SGD[0];
												              					$SGDtotal = $SGDtotal + $SGD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
												              		<td>
												              			<?php 
												              				$USD = array_column($valueCollecctionAll, 'USD');
												              				if (isset($USD[0])) {
												              					echo $USD[0];
												              					$USDtotal = $USDtotal + $USD[0];
												              				}else{
												              					echo "0";
												              				}
												              			?>
												              		</td>
											              		</tr>
											              		<?php 
												          			} 
												          		}
													            ?>
													            <tr>
													            	<td>Total Rev.</td>
													            	<td><?php echo $IDRtotal ?></td>
													            	<td><?php echo $SGDtotal ?></td>
													            	<td><?php echo $USDtotal ?></td>
													            </tr>
											              	</tbody>

											              </table>
												          
											             
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-3 form-group">
													<div class="info-box bg-red">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Contract expired </span>
											              <?php 
											              		if(!empty($contractsExpired) && isset($contractsExpired[0])){
											              		
											              ?>
											              <h4> <strong><?php echo number_format($contractsExpired[0]['expired_contract']) ?></strong></h4>
											              
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

												<div class="col-md-3 form-group">
													<div class="info-box bg-green">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Contract Signed and (Returned) </span>
											              <?php 
											              		if(!empty($contracts) && isset($contracts[0])){
											              		
											              ?>
											              <h4> <strong><?php echo number_format($contracts[0]['returned_contract']) ?></strong></h4>
											              
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

												<div class="col-md-3 form-group">
													<div class="info-box bg-aqua">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Contract (Sent) out for signing </span>
											              <?php 
											              		if(!empty($contracts) && isset($contracts[0])){
											              		
											              ?>
											              <h4> <strong><?php echo number_format($contracts[0]['sent_contract']) ?></strong></h4>
											              
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>

												<div class="col-md-3 form-group">
													<div class="info-box bg-yellow">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">Total Contract (Not Sent) out </span>
											              <?php 
											              		if(!empty($contracts) && isset($contracts[0])){
											              		
											              ?>
											              <h4> <strong><?php echo number_format($contracts[0]['not_sent_contract']) ?></strong></h4>
											              
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-3 form-group">
													<div class="info-box bg-green">
											            
											            <div class="info-box-content">
											              <span class="info-box-text">No Contract </span>
											              <?php 
											              		if(!empty($contracts) && isset($contracts[0])){
											              		
											              ?>
											              <h4> <strong><?php echo number_format($contracts[0]['no_contract']) ?></strong></h4>
											              
											              <?php }else{ ?>
											              	-
											              <?php } ?>
											             
											            </div>
										          	</div>
												</div>
											</div>

											
										</div>
									</div>				
								</div>
							</div>

							<div class="tab-pane" id="tab_2">

								<div class="tbllanguage-form View-Customer-sec">
									
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-md-6 form-group">
													<h3>Monthly Sales Statistics</h3>
													<div id="graph-monthly-statistics" style="width:100%;height:400px;margin:0 auto"></div>
													<div id="monthly-statistics-bar-legend" class="chart-legend"></div>
												</div>

												<div class="col-md-6 form-group">
													<h3>Yearly Sales Statistics</h3>
													<div id="graph-yearly-statistics" style="width:100%;height:400px;margin:0 auto"></div>
													<div id="yearly-statistics-bar-legend" class="chart-legend"></div>
												</div>
											</div>

											<div class="row">
												<div class="col-md-6 form-group">
													<h3>Monthly Bill Collection Statistics</h3>
													<div id="graph-monthly-bill-collection-statistics" style="width:100%;height:400px;margin:0 auto"></div>
													<div id="monthly-bill-collection-statistics-bar-legend" class="chart-legend"></div>
												</div>

												<!-- <div class="col-md-6 form-group">
													<h3>Annual Bill Collection Statistics</h3>
													<div id="graph-yearly-bill-collection-statistics" style="width:100%;height:400px;margin:0 auto"></div>
													<div id="yearly-bill-collection-statistics-bar-legend" class="chart-legend"></div>
												</div> -->
											</div>
										</div>
									</div>
								</div>
							</div>
	</div>
	
</div>
</div>
<div class="box box-default">

</div>


<script type="text/javascript">
		var ticksUsedOriginal = {'1':"Jan", '2':"Feb", '3':"Mar", '4':"Apr", '5':"May", '6':"Jun", '7':"Jul", '8':"Aug", '9':"Sep", '10':"Oct", '11':"Nov", '12':"Dec"};
		
	 	

		/**		Monthly statistics Start	 **/ 
	 		var datasetMonthlyAll = <?php echo json_encode($getMonthlySaleStatistics) ?> 

	 		var datasetMonthly = datasetMonthlyAll.monthData;
	 		var ticksUsed = Object.entries(datasetMonthlyAll.monthTicks);

	 		var datasetMonthlyIDR = (typeof datasetMonthly.IDR === "undefined")?[]:Object.entries(datasetMonthly.IDR);
	 		var datasetMonthlyUSD = (typeof datasetMonthly.USD === "undefined")?[]:Object.entries(datasetMonthly.USD);
	 		var datasetMonthlySGD = (typeof datasetMonthly.SGD === "undefined")?[]:Object.entries(datasetMonthly.SGD);
        
      var datasetMonthlyStaticstics  = [ 
        		{ label: "IDR", data: datasetMonthlyIDR, color: "#FF0000" },
			  	{ label: "SGD", data: datasetMonthlyUSD, color: "#5c9b3c" },
			  	{ label: "USD", data: datasetMonthlySGD, color: "#0022FF" }
			]

 
      var optionsMonthlyStaticstics  = {
          series: {
              lines: {
                  show: true
              },
              points: {
                  radius: 3,
                  fill: true,
                  show: true
              }
          },
          xaxis: {
              mode: "time",
              tickSize: [1, "month"],
              tickLength: 0,
              axisLabel: "2012",
              axisLabelUseCanvas: true,
              axisLabelFontSizePixels: 12,
              axisLabelFontFamily: 'Verdana, Arial',
              axisLabelPadding: 10,
              ticks:ticksUsed
          },
          yaxes: [
          	{
                axisLabel: "Revenue",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3,
                
                
            },{
                position: "right",
                axisLabel: "Change(%)",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3
            }
        ],
          legend: {
              show: true,
		    noColumns: 3,
		    container: "#monthly-statistics-bar-legend"
          },
          grid: {
              hoverable: true,
              borderWidth: 2,
              borderColor: "#633200",
              backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
          },
         
      };

      $("#graph-monthly-statistics").bind("plothover", function (event, pos, item) {
          if (item) {
              if (previousPoint != item.dataIndex) {

                  previousPoint = item.dataIndex;

                  $("#tooltip").remove();
                  var x = ticksUsedOriginal[item.datapoint[0]],
                  y = item.datapoint[1].toFixed(2);

                  showTooltip(item.pageX, item.pageY,
                      "Month=" + x + ", Revenue=" + y);
              }
          } else {
              $("#tooltip").remove();
              previousPoint = null;            
          }
      });
    /**		Monthly statistics End	 **/ 

    /**		Yearly statistics start	 **/ 
        var datasetYearlyAll = <?php echo json_encode($getYearlySaleStatistics) ?> 

        var datasetYearly = datasetYearlyAll.yearData;
        var datasetYearlyTicks = Object.entries(datasetYearlyAll.yearTicks);
		 		
		 		var datasetYearlyIDR = (typeof datasetYearly.IDR === "undefined")?[]:Object.entries(datasetYearly.IDR);
		 		var datasetYearlyUSD = (typeof datasetYearly.USD === "undefined")?[]:Object.entries(datasetYearly.USD);
		 		var datasetYearlySGD = (typeof datasetYearly.SGD === "undefined")?[]:Object.entries(datasetYearly.SGD);
		        
        var datasetYearlyStaticstics  = [ 
		        		{ label: "IDR", data: datasetYearlyIDR, color: "#FF0000" },
					  		{ label: "SGD", data: datasetYearlyUSD, color: "#5c9b3c" },
					  		{ label: "USD", data: datasetYearlySGD, color: "#0022FF" }
				]

		 
        var optionsYearlyStaticstics  = {
            series: {
                lines: {
                    show: true
                },
                points: {
                    radius: 3,
                    fill: true,
                    show: true
                }
            },
            xaxis: {
                mode: "time",
                tickSize: [1, "month"],
                tickLength: 0,
                axisLabel: "2012",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 10,
                ticks:datasetYearlyTicks
            },
            yaxes: [
            	{
	                axisLabel: "Revenue",
	                axisLabelUseCanvas: true,
	                axisLabelFontSizePixels: 12,
	                axisLabelFontFamily: 'Verdana, Arial',
	                axisLabelPadding: 3,
	                
	                
	            },{
	                position: "right",
	                axisLabel: "Change(%)",
	                axisLabelUseCanvas: true,
	                axisLabelFontSizePixels: 12,
	                axisLabelFontFamily: 'Verdana, Arial',
	                axisLabelPadding: 3
	            }
	        ],
            legend: {
                show: true,
			    noColumns: 3,
			    container: "#yearly-statistics-bar-legend"
            },
            grid: {
                hoverable: true,
                borderWidth: 2,
                borderColor: "#633200",
                backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
            },
           
        };

        $("#graph-yearly-statistics").bind("plothover", function (event, pos, item) {
            if (item) {
                if (previousPoint != item.dataIndex) {

                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = ticksUsedOriginal[item.datapoint[0]],
                    y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY,
                        "Month=" + x + ", Revenue=" + y);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
        });
    /**		Yearly statistics End	 **/ 

    /**		Monthly Billing statistics start	 **/ 
        var datasetMonthlyBillingAll = <?php echo json_encode($getMonthlyBillingSaleStatistics) ?> 
        
        var datasetMonthlyBilling = datasetMonthlyBillingAll.monthData;
        var datasetMonthlyBillingTicks = Object.entries(datasetMonthlyBillingAll.monthTicks);
		 		
		 		var datasetMonthlyBillingIDR = (typeof datasetMonthlyBilling.IDR === "undefined")?[]:Object.entries(datasetMonthlyBilling.IDR);
		 		var datasetMonthlyBillingUSD = (typeof datasetMonthlyBilling.USD === "undefined")?[]:Object.entries(datasetMonthlyBilling.USD);
		 		var datasetMonthlyBillingSGD = (typeof datasetMonthlyBilling.SGD === "undefined")?[]:Object.entries(datasetMonthlyBilling.SGD);
		        
        var datasetMonthlyBillingStaticstics  = [ 
		        		{ label: "IDR", data: datasetMonthlyBillingIDR, color: "#FF0000" },
					  		{ label: "SGD", data: datasetMonthlyBillingUSD, color: "#5c9b3c" },
					  		{ label: "USD", data: datasetMonthlyBillingSGD, color: "#0022FF" }
				]

		 
        var optionsMonthlyBillingStaticstics  = {
            series: {
                lines: {
                    show: true
                },
                points: {
                    radius: 3,
                    fill: true,
                    show: true
                }
            },
            xaxis: {
                mode: "time",
                tickSize: [1, "month"],
                tickLength: 0,
                axisLabel: "2012",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 10,
                ticks:datasetMonthlyBillingTicks
            },
            yaxes: [
            	{
	                axisLabel: "Revenue",
	                axisLabelUseCanvas: true,
	                axisLabelFontSizePixels: 12,
	                axisLabelFontFamily: 'Verdana, Arial',
	                axisLabelPadding: 3,
	                
	                
	            },{
	                position: "right",
	                axisLabel: "Change(%)",
	                axisLabelUseCanvas: true,
	                axisLabelFontSizePixels: 12,
	                axisLabelFontFamily: 'Verdana, Arial',
	                axisLabelPadding: 3
	            }
	        ],
            legend: {
                show: true,
						    noColumns: 3,
						    container: "#monthly-bill-collection-statistics-bar-legend"
            },
            grid: {
                hoverable: true,
                borderWidth: 2,
                borderColor: "#633200",
                backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
            },
           
        };

        $("#graph-monthly-bill-collection-statistics").bind("plothover", function (event, pos, item) {
            if (item) {
                if (previousPoint != item.dataIndex) {

                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = ticksUsedOriginal[item.datapoint[0]],
                    y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY,
                        "Month=" + x + ", Revenue=" + y);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
        });
    /**		Monthly Billing statistics End	 **/

    /**		Annual Billing statistics start	 **/ 
        var datasetYearlyAll = <?php echo json_encode($getYearlySaleStatistics) ?> 

        var datasetYearly = datasetYearlyAll.yearData;
        var datasetYearlyTicks = Object.entries(datasetYearlyAll.yearTicks);
		 		
		 		var datasetYearlyIDR = (typeof datasetYearly.IDR === "undefined")?[]:Object.entries(datasetYearly.IDR);
		 		var datasetYearlyUSD = (typeof datasetYearly.USD === "undefined")?[]:Object.entries(datasetYearly.USD);
		 		var datasetYearlySGD = (typeof datasetYearly.SGD === "undefined")?[]:Object.entries(datasetYearly.SGD);
		        
        var datasetYearlyStaticstics  = [ 
		        		{ label: "IDR", data: datasetYearlyIDR, color: "#FF0000" },
					  		{ label: "SGD", data: datasetYearlyUSD, color: "#5c9b3c" },
					  		{ label: "USD", data: datasetYearlySGD, color: "#0022FF" }
				]

		 
        var optionsYearlyStaticstics  = {
            series: {
                lines: {
                    show: true
                },
                points: {
                    radius: 3,
                    fill: true,
                    show: true
                }
            },
            xaxis: {
                mode: "time",
                tickSize: [1, "month"],
                tickLength: 0,
                axisLabel: "2012",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 10,
                ticks:datasetYearlyTicks
            },
            yaxes: [
            	{
	                axisLabel: "Revenue",
	                axisLabelUseCanvas: true,
	                axisLabelFontSizePixels: 12,
	                axisLabelFontFamily: 'Verdana, Arial',
	                axisLabelPadding: 3,
	                
	                
	            },{
	                position: "right",
	                axisLabel: "Change(%)",
	                axisLabelUseCanvas: true,
	                axisLabelFontSizePixels: 12,
	                axisLabelFontFamily: 'Verdana, Arial',
	                axisLabelPadding: 3
	            }
	        ],
            legend: {
                show: true,
			    noColumns: 3,
			    container: "#yearly-statistics-bar-legend"
            },
            grid: {
                hoverable: true,
                borderWidth: 2,
                borderColor: "#633200",
                backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
            },
           
        };

        $("#graph-yearly-statistics").bind("plothover", function (event, pos, item) {
            if (item) {
                if (previousPoint != item.dataIndex) {

                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = ticksUsedOriginal[item.datapoint[0]],
                    y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY,
                        "Month=" + x + ", Revenue=" + y);
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
        });
    /**		Annual Billing statistics End	 **/ 

		function showTooltip(x, y, contents) {
    $("<div id='tooltip'>" + contents + "</div>").css({
            position: "absolute",
            display: "none",
            top: y + 5,
            left: x + 5,
            border: "1px solid #fdd",
            padding: "2px",
            "background-color": "#fee",
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

	  $(document).ready(function() {
		  $('#EquipmentsTable').DataTable();

		  $.plot($("#graph-monthly-statistics"), datasetMonthlyStaticstics, optionsMonthlyStaticstics);    
		  $.plot($("#graph-yearly-statistics"), datasetYearlyStaticstics, optionsYearlyStaticstics);    
		  $.plot($("#graph-monthly-bill-collection-statistics"), datasetMonthlyBillingStaticstics, optionsMonthlyBillingStaticstics);    
		  $.plot($("#graph-yearly-bill-collection-statistics"), datasetYearlyStaticstics, optionsYearlyStaticstics);    
		} );
	

    $('.monthly_search_statistics').on('change',function(){
     	var search_year = $(this).val();
     	window.location.href= location.protocol + '//' + location.host + '/servicereport/index?monthly_stat_search_year='+search_year;
    })

	


</script>