<?php 
ini_set("log_errors", 1);

$fileXML = '../log/test_log_paralel.xml';

$date_log = date ("d-m-Y", filemtime($fileXML));
//echo $date_log;

$xmlObj = simplexml_load_file($fileXML);
$jsonObj = json_encode($xmlObj);
$arrLogs = json_decode($jsonObj,TRUE);

// get correct time
$totalTime = 0;
/*
foreach($arrLogs['testsuite']['testsuite'] as $testSuites1){

	if(!isset($testSuites1['testcase'][0])){
		$testSuites1['testcase'][0] = $testSuites1['testcase'];
		unset($testSuites1['testcase']['@attributes']);
	}
	foreach ($testSuites1['testcase'] as $times) {
		
		$totalTime = $totalTime + $times['@attributes']['time'];
		echo $times['@attributes']['time'];
		echo '<br>';
	}
}
//echo $totalTime;*/
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Log Result</title>
		<style>

		*{font-family: arial;font-size: 9pt}
		h1{font-size: 18px}
		table{background-color: #DDD}
		th{font-size: 12pt}
		td{padding:5px;}
		.subTest{font-weight: bold;}
		.subTestCase{font-weight: bold;}
		tr{background-color: #FFF}
		 .black{
			background-color: #000;
			color:#FFF;
			font-size: 11pt;
			font-weight: bold;
		}
		.red{
			background-color: #fc3c3c;
		}
		.yellow{
			background-color: #ffcd44;
		}
		.green{
			background-color: #00db36;
		}
		</style>
	</head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script>
		function filterResult(opt){
			$('tr.red').show();
			$('tr.yellow').show();
			$('tr.green').show();
			if(opt.value=='green'){
				$('tr.red').hide();
				$('tr.yellow').hide();
			}
			if(opt.value=='yellow'){
				$('tr.red').hide();
				$('tr.green').hide();
			}
			if(opt.value=='red'){
				$('tr.green').hide();
				$('tr.yellow').hide();
			}
			
		}
	</script>
	<body>
	<h1>Log Result</h1>
	<p>
		Filter : 
		<select name="filter"  onchange="filterResult(this)">
			<option value="all">All result</option>
			<option value="green">Success Only</option>
			<option value="yellow">Warning Only</option>
			<option value="red">Error Only</option>
		</select>
	</p>
	<table width="100%" cellpadding="10" cellspacing="3">
		<tr class="mainTest">
			<th width="20">No</th>
			<th>Name</th>
			<th>File</th>
			<th>Test Count</th>
			<th>Asserts</th>
			<th>Success</th>
			<th>Failures</th>
			<th>Errors</th>
			<th>Time</th>
		</tr>
		<?php 
			$totalSuccess = $arrLogs['testsuite']['@attributes']['tests']-($arrLogs['testsuite']['@attributes']['failures']+$arrLogs['testsuite']['@attributes']['errors']);	
			$percentSuccess = $totalSuccess/$arrLogs['testsuite']['@attributes']['tests']*100;
			$percentFail = $arrLogs['testsuite']['@attributes']['failures']/$arrLogs['testsuite']['@attributes']['tests']*100;
			$percentError = $arrLogs['testsuite']['@attributes']['errors']/$arrLogs['testsuite']['@attributes']['tests']*100;
		?>
		<tr class="mainTest" >
			<td>#</td>
			<td><?php echo $arrLogs['testsuite']['@attributes']['name']?></td>
			<td>-</td>
			<td><?php echo $arrLogs['testsuite']['@attributes']['tests']?></td>
			<td><?php echo $arrLogs['testsuite']['@attributes']['assertions']?></td>
			<td class="green"><?php echo $totalSuccess?> (<?php echo number_format($percentSuccess,2,'.',',')?>%)</td>
			<td class="yellow"><?php echo $arrLogs['testsuite']['@attributes']['failures']?>  (<?php echo number_format($percentFail,2,'.',',')?>%)</td>
			<td class="red"><?php echo $arrLogs['testsuite']['@attributes']['errors']?> (<?php echo number_format($percentError,2,'.',',')?>%)</td>
			<td><?php echo gmdate('H:i:s',$arrLogs['testsuite']['@attributes']['time'])?></td>
		</tr>
		
	<?php $a=0;$no=1;foreach($arrLogs['testsuite']['testsuite'] as $rowLogs1):?>
		<?php 
			$testSummarySuites1 =  $rowLogs1['@attributes'];
			$testSuites1 =  $rowLogs1;
		?>
		<?php 
			$percentError = '0%';
			$percentSuccess = '0%';
			$percentFail = '0%';
			$totalSuccess = 0;
			if ($testSummarySuites1['tests'] > 0) {
				$totalSuccess = $testSummarySuites1['tests']-($testSummarySuites1['failures']+$testSummarySuites1['errors']);	
				$percentSuccess = $totalSuccess/$testSummarySuites1['tests']*100;
				$percentFail = $testSummarySuites1['failures']/$testSummarySuites1['tests']*100;
				$percentError = $testSummarySuites1['errors']/$testSummarySuites1['tests']*100;
			} 
		?>
		<tr class="subTest">
			<td><?php echo $no?></td>
			<td><?php echo $testSummarySuites1['name']?></td>
			<td>-</td>
			<td><?php echo $testSummarySuites1['tests']?></td>
			<td><?php echo $testSummarySuites1['assertions']?></td>
			<td class="green"><?php echo $totalSuccess?> (<?php echo number_format($percentSuccess,2,'.',',')?>%)</td>
			<td class="yellow"><?php echo $testSummarySuites1['failures']?> (<?php echo number_format($percentFail,2,'.',',')?>%)</td>
			<td class="red"><?php echo $testSummarySuites1['errors']?> (<?php echo number_format($percentError,2,'.',',')?>%)</td>
			<td><?php echo gmdate('H:i:s',$testSummarySuites1['time'])?></td>
		</tr>
		
			<?php 
				$subNo = 1;
				if(!isset($testSuites1['testcase'][0])){
					$testSuites1['testcase'][0] = $testSuites1['testcase'];
					unset($testSuites1['testcase']['@attributes']);
				}
				if (isset($testSuites1['testcase'])):
				foreach ($testSuites1['testcase'] as $rowLogs2): 
					$testSummarySuites2 = $rowLogs2['@attributes'];
					$totalSuccess = '';
					$percentSuccess = '0%';
					$percentFail = '0%';
					$percentFail = '0%';
					$percentError = '0%';
					
					if ($testSummarySuites2['tests'] > 0) { 
						$totalSuccess = $testSummarySuites2['tests']-($testSummarySuites2['failures']+$testSummarySuites2['errors']);	
						$percentSuccess = $totalSuccess/$testSummarySuites2['tests']*100;
						$percentFail = $testSummarySuites2['failures']/$testSummarySuites2['tests']*100;
						$percentError = $testSummarySuites2['errors']/$testSummarySuites2['tests']*100;
					}

					// init test description
					$testDescription = '';
					if(isset($rowLogs2['testsuite']['testcase']['@attributes'])){
						$rowLogs2Testcase[0] = $rowLogs2['testsuite']['testcase']['@attributes'];

						$rowLogs2Testcase = $rowLogs2['testsuite']['testcase'];

						// test description for single test
						$testDescription = explode(":", $rowLogs2['testsuite']['testcase']['system-out']);
						$testDescription = trim(str_replace("...", "", $testDescription[1]));
					}else{
						$rowLogs2Testcase = $rowLogs2['testsuite']['testcase'];
				}

				$class = 'green';
				if(isset($rowLogs2['failure'])){
					$class='yellow';
				}elseif(isset($rowLogs2['error'])){
					$class = 'red';
				}

				$testDescription = explode(":", $testCases['system-out']);
				$testDescription = trim(str_replace("...", "", $testDescription[1]));
				$totalTime +=$testSummarySuites2['time'];
			?>
			<tr class="subTestCase <?php echo $class?>">
				<td><?php echo $no.'.'.$subNo?></td>
				<td>- <?php echo $testSummarySuites2['name']?></td>
				<td><?php if (isset($testSummarySuites2['file'])) echo $testSummarySuites2['file'];?></td>
				<td  colspan="5"></td>
				<td><?php echo gmdate('H:i:s',$testSummarySuites2['time'])?></td>
			</tr>
			<?php if(isset($rowLogs2['failure']) || isset($rowLogs2['error'])):?>
				<tr class="<?php echo $class?>">
					<td colspan="9">
						<?php print_r($rowLogs2['failure'])?> <?php print_r($rowLogs2['error'])?>
						<br/>
						<?php echo $testDescription?>
					</td>
				</tr>
			<?php endif; ?>
			
			<?php
				$subNo++;
				endforeach;
				endif;
			?>
			<tr>
				<td colspan="9"></td>
			</tr>
	<?php $a++; $no++; endforeach; ?>
		</table>
	
	
	</body>
</html>
<?php 
echo $totalTime;
/*echo count($arrLogs['testsuite']);
echo '<pre>';
print_r($arrLogs);
*/
?>