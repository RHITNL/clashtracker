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

$apiKeys = apiKey::getKeys();

require('header.php');
?>
<div class="col-md-12">
	<?require('showMessages.php');?>
	<h3>Proxy Information</h3>
	<div class="col-md-12">
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
	<h3>API Keys</h3>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>IP</th>
						<th class="text-right">Key</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($apiKeys as $apiKey) {?>
						<tr>
							<td><?=$apiKey->get('ip');?></td>
							<?$key = $apiKey->get('apiKey');
							$key = substr($key, 0, 20) . '...' . substr($key, strlen($key)-20, 20);?>
							<td class="text-right"><?=$key;?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
		<h4>Add API Key</h4>
		<form class="form-inline" action="/processAddApiKey.php" method="POST">
			<div class="form-group">
				<label for="ips">IPs </label>
				<input type="text" class="form-control" id="ips" name="ips" placeholder="0.0.0.0">
			</div>
			<div class="form-group">
				<label for="key">Key</label>
				<input type="text" class="form-control" id="key" name="key" placeholder="eyJ0eXAiOiJKV1QiLCJh...69SRf18_wG4i147Ge0hQ">
			</div>
			<button type="submit" class="btn btn-primary text-right">Save</button>
		</form>
	</div>
</div>
<?
require('footer.php');