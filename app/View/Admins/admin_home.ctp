<!-- TODO List -->
<div class="col-md-6">
	<div class="title">
		<h2><i class="fa fa-briefcase"></i> <?=__d('admin', 'Work to do list:')?></h2>
	</div>
	<?=$this->Notice->show()?>
</div>
<!-- End of TODO list -->
<!-- Overall statistics -->
<div class="col-md-6">
	<div class="title">
		<h2><i class="fa fa-info-circle"></i> <?=__d('admin', 'Overall statistics:')?></h2>
	</div>
	<table class="table table-hover">
		<tbody>
			<tr>
				<th><?=__d('admin', 'Total Users:')?></th>
				<td><?=h($overallStats['total'])?></td>
				<th><?=__d('admin', 'Account Balances:')?></th>
				<td><?=h($this->Currency->format($overallStats['account_balances']))?></td>
			</tr>
			<tr>
				<th><?=__d('admin', 'Active Users:')?></th>
				<td><?=h($overallStats['active'])?></td>
				<th><?=__d('admin', 'Purchase Balances:')?></th>
				<td><?=h($this->Currency->format($overallStats['purchase_balances']))?></td>
			</tr>
			<tr>
				<th><?=__d('admin', 'Unverified Users:')?></th>
				<td><?=h($overallStats['unverified'])?></td>
				<th><?=__d('admin', 'Referrals For Rent:')?></th>
				<td><?=h($overallStats['not_rented'])?></td>
			</tr>
			<tr>
				<th><?=__d('admin', 'Suspended Users:')?></th>
				<td><?=h($overallStats['suspended'])?></td>
				<th><?=__d('admin', 'Referrals For Sale:')?></th>
				<td><?=h($overallStats['to_sale'])?></td>
			</tr>
		</tbody>
	</table>
</div>
<!-- End of Overall statistics -->
<!-- Bottom statistics -->
<div class="col-md-12">
	<div class="title">
		<h2><i class="fa fa-bar-chart"></i> <?=__d('admin', 'Statistics:')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#tabr1" id="overAllTitle"><?=__d('admin', 'Overall')?></a></li>
		<?php foreach($activeGateways as $gateway): ?>
			<li><a class="gatewaySelector" data-gateway="<?=h($gateway)?>" data-toggle="tab" href="#tabr2"><?=h(PaymentsInflector::humanize(PaymentsInflector::underscore($gateway)))?></a></li>
		<?php endforeach; ?>
		<?php if(Module::active('BotSystem')): ?>
			<li><a id="botSystemStatistics" data-toggle="tab" href="#tabr3"><?=__d('admin', 'BotSystem statistics')?></a></li>
		<?php endif; ?>
	</ul>
	<!-- Tab 1 -->
	<div class="tab-content">
		<div class="tab-pane fade in active" id="tabr1">
			<div class="col-md-3">
				<div class="title2"><h2><i class="fa fa-money"></i> <?=__d('admin', 'Total:')?></h2></div>
				<table class="table table-hover">
					<tbody>
						<tr>
							<th><?=__d('admin', 'Deposits:')?></th>
							<td><?=h($this->Currency->format($total['deposits']))?></td>
						</tr>
						<tr>
							<th><?=__d('admin', 'Paid:')?></th>
							<td><?=h($this->Currency->format($total['paid']))?></td>
						</tr>
						<tr>
							<th><?=__d('admin', 'New Cashouts:')?></th>
							<td><?=h($this->Currency->format($total['new']))?></td>
						</tr>
						<tr>
							<th><?=__d('admin', 'Overall Profit:')?></th>
							<td><?=h($this->Currency->format($total['profit']))?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-9">
				<div class="title2"><h2><i class="fa fa-check-square-o"></i> <?=__d('admin', 'Last 7 Days:')?></h2></div>
				<?=$this->Chart->show($chart, array(
							'id' => 'totalChart',
							'width' => '100%',
							'height' => '200px',
						)
					)
				?>
			</div>
		</div>
		<!-- End of Tab 1 -->
		<!-- Tab 2 -->
		<div class="tab-pane fade" id="tabr2">
			<div class="col-md-3">
				<div class="title2"><h2><i class="fa fa-money"></i> <?=__d('admin', 'Total')?></h2></div>
				<table class="table table-hover">
					<tbody>
						<tr>
							<th><?=__d('admin', 'Income:')?></th>
							<td id="gatewayDeposits"></td>
						</tr>
						<tr>
							<th><?=__d('admin', 'Outcome:')?></th>
							<td id="gatewayPaid"></td>
						</tr>
						<tr>
							<th><?=__d('admin', 'New Cashouts:')?></th>
							<td id="gatewayNew"></td>
						</tr>
						<tr>
							<th><?=__d('admin', 'Overall Profit:')?></th>
							<td id="gatewayProfit"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-9">
				<div class="title2"><h2><i class="fa fa-check-square-o"></i> <?=__d('admin', 'Last 7 Days:')?></h2></div>
				<?=
					$this->Chart->show($chart, array(
							'id' => 'gatewayChart',
							'width' => '100%',
							'height' => '200px',
						)
					)
				?>
			</div>
		</div>
		<!-- End of Tab 2 -->
		<?php if(Module::active('BotSystem')): $stats = $this->BotSystem->getOverallStats(); ?>
			<!-- Tab 3 -->
			<div class="tab-pane fade" id="tabr3">
				<div class="col-md-3">
					<div class="title2"><h2><i class="fa fa-money"></i> <?=__d('admin', 'Total')?></h2></div>
					<table class="table table-hover">
						<tbody>
							<tr>
								<th><?=__d('admin', 'Income:')?></th>
								<td><?=$this->Currency->format($stats['income'])?></td>
							</tr>
							<tr>
								<th><?=__d('admin', 'Outcome:')?></th>
								<td><?=$this->Currency->format($stats['outcome'])?></td>
							</tr>
							<tr>
								<th><?=__d('admin', 'Overall Profit:')?></th>
								<td><?=$this->Currency->format(bcsub($stats['income'], $stats['outcome']))?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-9">
					<div class="title2"><h2><i class="fa fa-check-square-o"></i> <?=__d('admin', 'Last 7 Days:')?></h2></div>
					<?=
						$this->BotSystem->statisticsChart(array(
							'width' => '100%',
							'height' => '200px',
						))
					?>
				</div>
			</div>
			<!-- End of Tab 3 -->
		<?php endif; ?>
	</div>
</div>
<?php
	$url = Router::url(array('controller' => 'admins', 'action' => 'statistics'));
	$this->Js->buffer("
		var totalChart = $('#totalChart').highcharts();
		var chart = $('#gatewayChart').highcharts();

		chart.setSize($(totalChart.container).width(), $(totalChart.container).height());
		chart.reflow();

		$('.gatewaySelector').on('click', function() {
			$.ajax({
				type: 'GET',
				url: '$url/' + $(this).data('gateway'),
				dataType: 'json',
				beforeSend: function() {
					chart.showLoading();
				},
				success: function(data) {
					chart.series[0].setData(data['chart']['Deposits']);
					chart.series[1].setData(data['chart']['Cashouts']);

					$('#gatewayDeposits').html(formatCurrency(data['total']['deposits']));
					$('#gatewayPaid').html(formatCurrency(data['total']['paid']));
					$('#gatewayNew').html(formatCurrency(data['total']['new']));
					$('#gatewayProfit').html(formatCurrency(data['total']['profit']));

					chart.hideLoading();
				}
			});
		});
	");
?>
