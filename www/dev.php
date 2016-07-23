<?
require('init.php');
require('session.php');

if(!isset($loggedInUser) || !$loggedInUser->isAdmin()){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

$proxies = API::getProxyInformation();
$totalRequests = 0;
$totalLimit = 0;

$apiKeys = APIKey::getKeys();
$daysInMonth = (strtotime(date('d-m-Y h:i:s'))-strtotime(date('01-m-Y')))/DAY;

$query = $_POST['query'];
if(strlen($query)>0){
	if(strpos($query, ";") !== false){
		$query = substr($query, 0, strpos($query, ";")+1);
	}
	$queryLower = strtolower($query);
	if(strpos($queryLower, 'drop') === false &&
	   strpos($queryLower, 'truncate') === false &&
	   strpos($queryLower, 'update') === false &&
	   strpos($queryLower, 'create') === false &&
	   strpos($queryLower, 'procedure') === false &&
	   strpos($queryLower, 'call') === false &&
	   strpos($queryLower, 'insert') === false &&
	   strpos($queryLower, 'delete') === false &&
	   strpos($queryLower, 'select') !== false){
		global $db;
		if($db->multi_query($query) === true){
			$results = $db->store_result();
			while ($db->more_results()){
				$db->next_result();
			}
			$queryResults = array();
			if($results->num_rows){
				while($resultObj = $results->fetch_object()){
					$queryResults[] = $resultObj;
				}
			}
		}else{
			$_SESSION['curError'] = 'The database encountered an error. ' . $db->error;
		}
	}else{
		$queryResults = array();
		$invalid = true;
	}
}

require('header.php');
?>
<div class="col-md-12">
	<?require('showMessages.php');?>
	<div class="col-md-12">
		<h3>Proxy Information</h3>
		<div class="table-responsive">
			<form id="updateProxyForm" action="/processUpdateProxyCount.php" method="POST">
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
								$requestsPerDay = $proxy->count/$daysInMonth;?>
								<td><?=$proxy->env;?></td>
								<td id="<?=$proxy->env;?>" style="cursor: pointer;" onclick="updateProxyCount('<?=$proxy->env;?>');"><?=$proxy->count;?></td>
								<td><?=$proxy->limit;?></td>
								<td><?=number_format($requestsPerDay, 2);?></td>
								<td>
									<?if($requestsPerDay != 0){
										$daysUntilExhaustion = $proxy->limit / $requestsPerDay;
										print date('F j, Y g:i:s A', strtotime(date('01-m-Y')) + $daysUntilExhaustion*DAY);
									}?>
								</td>
								<td class="text-right"><?=$proxy->ip;?></td>
							</tr>
						<?}?>
						<tr>
							<th class="text-right">Total</th>
							<?$requestsPerDay = $totalRequests/$daysInMonth;?>
							<td><?=$totalRequests;?></td>
							<td><?=$totalLimit;?></td>
							<td><?=number_format($requestsPerDay, 2);?></td>
							<td>
								<?if($requestsPerDay != 0){
									$daysUntilExhaustion = $totalLimit / $requestsPerDay;
									print date('F j, Y g:i:s A', strtotime(date('01-m-Y')) + $daysUntilExhaustion*DAY);
								}?>
							</td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<h4>Add ENV</h4>
		<div class="col-md-12">
			<form class="form-inline" action="/processAddEnv.php" method="POST">
				<div class="form-group">
					<label for="env">ENV Variable Name: </label>
					<input type="text" class="form-control" id="env" name="env" placeholder="FIXIE_URL">
				</div>
				<div class="form-group">
					<label for="limit">Monthly Limit: </label>
					<input type="text" class="form-control" id="limit" name="limit" placeholder="500">
				</div>
				<div class="form-group">
					<label for="ip">IP: </label>
					<input type="text" class="form-control" id="ip" name="ip" placeholder="0.0.0.0">
				</div>
				<button type="submit" class="btn btn-primary text-right">Add</button>
			</form>
		</div>
	</div>
	<div class="col-md-12">
		<hr>
		<h3>API Keys</h3>
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
		<div class="col-md-12">
			<form class="form-inline" action="/processAddApiKey.php" method="POST">
				<div class="form-group">
					<label for="ips">IPs: </label>
					<input type="text" class="form-control" id="ips" name="ips" placeholder="0.0.0.0">
				</div>
				<div class="form-group">
					<label for="key">Key: </label>
					<input type="text" class="form-control" id="key" name="key" placeholder="eyJ0eXAiOiJKV1QiLCJh...69SRf18_wG4i147Ge0hQ">
				</div>
				<button type="submit" class="btn btn-primary text-right">Save</button>
			</form>
		</div>
	</div>
	<div class="col-md-12">
		<hr>
		<h3>MySQL</h3>
		<div class="col-md-12">
			<form class="form-horizontal" action="/dev.php" method="POST">
				<div class="form-group col-md-11">
					<input type="text" class="form-control" id="query" name="query" placeholder="select * from clan;" value="<?=$query;?>">
				</div>
				<div class="text-right col-md-1">
					<button type="submit" class="btn btn-primary">Query</button>
				</div>
			</form>
		</div>
		<?if(isset($queryResults)){?>
			<div class="col-md-12">
				<?if(count($queryResults)>0){?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<?$keys = array_keys(get_object_vars($queryResults[0]));
									foreach($keys as $key){?>
										<th><?=$key;?></th>
									<?}?>
								</tr>
							</thead>
							<tbody>
								<?foreach ($queryResults as $result) {?>
									<tr>
										<?$values = array_values(get_object_vars($result));
										foreach($values as $value){?>
											<td><?=htmlspecialchars(cpr($value));?></td>
										<?}?>
									</tr>
								<?}?>
							</tbody>
						</table>
					</div>
				<?}else{
					if($invalid){?>
						<div class="alert alert-info" role="alert">
							This query is not allowed on this page. A single SELECT statement is the only query allowed.
						</div>
					<?}else{?>
						<div class="alert alert-info" role="alert">
							There were no results for your query.
						</div>
					<?}
				}?>
			</div>
		<?}?>
	</div>
	<div class="col-md-12">
		<hr>
		<h3>Add Blog Post</h3>
		<form class="form-horizontal" action="/processAddBlogPost.php" method="POST">
			<div class="col-sm-12">
				<div class="form-group">
					<label class="col-sm-4 col-md-2 control-lable" for="name">Post Name:</label>
					<div class="col-sm-8 col-md-10">
						<input type="text" class="form-control" id="name" name="name" placeholder="New Update">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 col-md-2 control-lable" for="content">Post Content:</label>
					<div class="col-sm-8 col-md-10">
						<textarea rows="4" class="form-control" id="content" name="content" placeholder="I've added X feature. Yay :)"></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<br>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	var formAvailable = false;
	function updateProxyCount(id){
		var value = $('#' + id).html();
		$('#' + id).css('padding', '6px').html('<input class="inline-input form-control" name="'+id+'" value="'+value+'">');
		$('#' + id).prop('onclick',null).off('click');
		formAvailable = true;
	}
	$(document).keypress(function(e) {
	    if(e.which == 13 && formAvailable) {
	        $('#updateProxyForm').submit();
	    }
	});
</script>
<?
require('footer.php');