<?
require('init.php');
require('session.php');

if(!isset($loggedInUser) || $loggedInUser->get('email') != 'alexinmann@gmail.com'){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

$proxies = api::getProxyInformation();
$totalRequests = 0;
$totalLimit = 0;

require('header.php');
?>
<div class="col-md-12">
	<?require('showMessages.php');?>
	<h3>Proxy Information</h3>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>ENV</th>
					<th>Count</th>
					<th>Limit</th>
					<th>Requests/Day</th>
					<th>Expected Date of Exhaustion</th>
					<th class="text-right">IP</th>
				</tr>
			</thead>
			<tbody>
				<?foreach ($proxies as $proxy) {?>
					<tr>
						<?$totalRequests += $proxy->count;
						$totalLimit += $proxy->limit;
						$requestsPerDay = $proxy->count/date('d');?>
						<td><?=$proxy->env;?></td>
						<td><?=$proxy->count;?></td>
						<td><?=$proxy->limit;?></td>
						<td><?=number_format($requestsPerDay, 2);?></td>
						<td>
							<?if($requestsPerDay != 0){
								$daysUntilExhaustion = $proxy->limit / $requestsPerDay;
								print date('F j, Y g:m:s A', strtotime(date('01-m-Y h:m:s')) + $daysUntilExhaustion*DAY);
							}?>
						</td>
						<td class="text-right"><?=$proxy->ip;?></td>
					</tr>
				<?}?>
				<tr>
					<th class="text-right">Total</th>
					<?$requestsPerDay = $totalRequests/date('d');?>
					<td><?=$totalRequests;?></td>
					<td><?=$totalLimit;?></td>
					<td><?=number_format($requestsPerDay, 2);?></td>
					<td>
						<?if($requestsPerDay != 0){
							$daysUntilExhaustion = $totalLimit / $requestsPerDay;
							print date('F j, Y g:m:s A', strtotime(date('01-m-Y h:m:s')) + $daysUntilExhaustion*DAY);
						}?>
					</td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?
require('footer.php');