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
										<div id="flot-placeholder" style="width:100%;height:400px;margin:0 auto"></div>
									</div>
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

	 	
 
        
        var dataset = [ 
        		{ label: "Foo", data: [ [10, 1], [17, -14], [30, 5] ] },
			  	{ label: "Bar", data: [ [11, 13], [19, 11], [30, -7] ] },
			  	{ label: "Bar", data: [ [11, 13], [19, 11], [30, -7] ] }
			]
 
        var options = {
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
                axisLabelPadding: 10
            },
            yaxes: [{
                axisLabel: "Gold Price(USD)",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3,
                
                
            }, {
                position: "right",
                axisLabel: "Change(%)",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3
            }
          ],
            legend: {
                noColumns: 0,
                labelBoxBorderColor: "#000000",
                position: "nw"
            },
            grid: {
                hoverable: true,
                borderWidth: 2,
                borderColor: "#633200",
                backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
            },
            colors: ["#FF0000", "#0022FF"]
        };

        $(document).ready(function() {
		    $('#EquipmentsTable').DataTable();

		    $.plot($("#flot-placeholder"), dataset, options);    
		    
		} );
 		
 
        var previousPoint = null, previousLabel = null;
        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
 
        $.fn.UseTooltip = function () {
            $(this).bind("plothover", function (event, pos, item) {
                if (item) {
                    if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                        previousPoint = item.dataIndex;
                        previousLabel = item.series.label;
                        $("#tooltip").remove();
 
                        var x = item.datapoint[0];
                        var y = item.datapoint[1];
 
                        var color = item.series.color;
                        var month = new Date(x).getMonth();
 
                        //console.log(item);
 
                        if (item.seriesIndex == 0) {
                            showTooltip(item.pageX,
                            item.pageY,
                            color,
                            "<strong>" + item.series.label + "</strong><br>" + monthNames[month] + " : <strong>" + y + "</strong>(USD)");
                        } else {
                            showTooltip(item.pageX,
                            item.pageY,
                            color,
                            "<strong>" + item.series.label + "</strong><br>" + monthNames[month] + " : <strong>" + y + "</strong>(%)");
                        }
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        };
 
        function showTooltip(x, y, color, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y - 40,
                left: x - 120,
                border: '2px solid ' + color,
                padding: '3px',
                'font-size': '9px',
                'border-radius': '5px',
                'background-color': '#fff',
                'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
                opacity: 0.9
            }).appendTo("body").fadeIn(200);
        }

	


</script>

<!-- <div class="row">
											<div class="col-md-6">
												<select  class="form-control monthly_search_statistics"  name="search_month" id="search_year"> -->
						                        <?php 
						                            // for ($intId=0; $intId <=4 ; $intId++){ 
						                        
						                        ?>

						                            <option value="<?php //echo date("Y",strtotime("-$intId year")) ?>" <?php //echo (date("Y",strtotime("-$intId year")) == date('Y') ? 'selected' : '') ?>>
						                                <?php //echo date("Y",strtotime("-$intId year")) ?>
						                            </option>
						                        <?php
						                            // }
						                        ?>
						                    	<!-- </select>
											</div>
										</div> -->