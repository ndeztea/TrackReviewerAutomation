<?php 
ini_set("log_errors", 1);

$fileXML = '../log/test_log.xml';

$date_log = date ("d-m-Y", filemtime($fileXML));
//echo $date_log;

$xmlObj = simplexml_load_file($fileXML);
$jsonObj = json_encode($xmlObj);
$arrLogs = json_decode($jsonObj,TRUE);
$testSuite100 = array();

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
		.subTestCase{font-weight: bold;}
		tr{background-color: #FFF}
		 .black{
			background-color: #000;
			color:#FFF;
			font-size: 11pt;
			font-weight: bold;
		}
		.red{
			background-color: #DE6565;
		}
		.yellow{
			background-color: #FFF1CA;
		}
		.green{
			background-color: #ACF5BE;
		}

		.black .red{
			color : #000;
		}
		.black .yellow{
			color : #000;
		}
		.black .green{
			color : #000;
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

		function filterResultTestCase(opt){
			
			if(opt=='all'){
				$('.testsuite').show();
				$('.subTestCase').show();
				$('.detailTestCase').show();
			}else if(opt=='testsuite'){
				$('.testsuite').show();
				$('.subTestCase').hide();
				$('.detailTestCase').hide();
			}else if(opt=='testCase'){
				$('.testsuite').show();
				$('.subTestCase').show();
				$('.detailTestCase').hide();
			}
		}

		function filterResult100(opt){
			filterTestCase = $('#filterTestCase').val();
			if(opt=='yes'){
				if(filterTestCase=='testsuite'){
					$('.testsuite.perfect').show();
				}else if(filterTestCase=='testCase'){
					$('.testsuite').show();
					$('.subTestCase.perfect').show();
				}
			}else if(opt=='no'){
				if(filterTestCase=='testsuite'){
					$('.testsuite.perfect').hide();
				}else if(filterTestCase=='testCase'){
					$('.subTestCase.perfect').hide();
				}
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
		Show Test Detail : <select name="filterTestCase" id="filterTestCase"  onchange="filterResultTestCase(this.value)">
			<option value="all">All</option>
			<option value="testsuite">Test Suites</option>
			<option value="testCase">Sub Test Cases</option>
		</select>
		Show 100% Test : <select name="filter100" id="filter100" onchange="filterResult100(this.value)">
			<option value="yes">Yes</option>
			<option value="no">No</option>
		</select>
	</p>
	<p>
		<a href="#testsuite">XML Test suite < 100%</a> | <a href="#allTC">XML All Test Case < 100%</a>
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
		<!--tr>
			<td colspan="9">&nbsp;</td>
		</tr-->
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

			// get testsuite only xml
			if($percentSuccess!=100){
				$testSuite100[$a]['name'] = $testSummarySuites1['name'];
				$arrTS = explode('_', $testSummarySuites1['name']);
				$testSuite100[$a]['dir'] = 'test/'.$arrTS[0].'/'.$arrTS[1];
			}
			//end
		?>
		<tr class="testsuite black <?php echo $percentSuccess==100?'perfect':'not-prefect'?>">
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

				if (isset($testSuites1['testsuite'])):
				foreach ($testSuites1['testsuite'] as $rowLogs2): 

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

				if($percentSuccess<100){
					if (isset($testSummarySuites2['file'])){
						$arr = explode('\\test\\', $testSummarySuites2['file']);
						if(!empty($arr[1])){
							$testSuite100[$a]['files'][] = 'test\\'.$arr[1];
						}
					}
					
				}
				
			?>
			<!--tr>
				<td colspan="9">&nbsp;</td>
			</tr-->
			<tr class="subTestCase <?php echo $percentSuccess==100?'perfect':'not-prefect'?>">
				<td><?php echo $no.'.'.$subNo?></td>
				<td>- <?php echo $testSummarySuites2['name']?></td>
				<td><?php if (isset($testSummarySuites2['file'])) echo $testSummarySuites2['file'];?></td>
				<td><?php echo $testSummarySuites2['tests']?></td>
				<td><?php echo $testSummarySuites2['assertions']?></td>
				<td class="green"><?php echo $totalSuccess?> (<?php echo number_format($percentSuccess,2,'.',',')?>%)</td>
				<td class="yellow"><?php echo $testSummarySuites2['failures']?> (<?php echo number_format($percentFail,2,'.',',')?>%)</td>
				<td class="red"><?php echo $testSummarySuites2['errors']?> (<?php echo number_format($percentError,2,'.',',')?>%)</td>
				<td><?php echo gmdate('H:i:s',$testSummarySuites2['time'])?></td>
			</tr>
			<?php if(!empty($rowLogs2Testcase)):?>
				<?php if(isset($rowLogs2Testcase[0])) { ?>
					<?php $subNo2=1; foreach($rowLogs2Testcase as $testCases):?>
					<?php 
						if(count($rowLogs2Testcase)>1){
							$testCase = $rowLogs2['testcase'];
							$summaryTestCase = $testCases['@attributes'];

							// test description for multiple test
							$testDescription = explode(":", $testCases['system-out']);
							$testDescription = trim(str_replace("...", "", $testDescription[1]));
						
						}else{
							$testCase = $testCases;
							$summaryTestCase = $testCases;
						}
						//print_r($testCases);
						$class = 'green';
						if(isset($testCases['failure'])){
							$class='yellow';
						}elseif(isset($testCases['error'])){
							$class = 'red';
						}
					?>
						<tr class="<?php echo $class?> detailTestCase <?php echo $percentSuccess==100?'perfect':'not-prefect'?>">
							<td><?php  echo $no.'.'.$subNo.'.'.$subNo2?> </td>
							<td><a href="../screenshoot/<?php echo $date_log ?>/<?php echo $summaryTestCase['name']?>" target="_blank">-- <?php echo $summaryTestCase['class']?>::<?php echo $summaryTestCase['name']?></a><p><?php echo $testDescription; ?></p></td>
							<td><?php echo $summaryTestCase['file']?> (<?php echo $summaryTestCase['line']?>)</td>
							<td><?php if (isset($summaryTestCase['tests'])) echo $summaryTestCase['tests']?></td>
							<td><?php echo $summaryTestCase['assertions']?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><?php echo gmdate('H:i:s',$summaryTestCase['time'])?></td>
						</tr>
						<?php if(isset($testCases['failure']) || isset($testCases['error'])):?>
						<tr class="<?php echo $class?> detailTestCase" >
							<td colspan="9"><?php print_r('<pre>'.$testCases['failure'].'</pre>')?> <?php print_r('<pre>'.$testCases['error'].'</pre>')?></td>
						</tr>
						<?php endif; ?>
					<?php $subNo2++; endforeach; ?>
				<?php } else { ?>
					<?php $subNo2=1; $testCases = $rowLogs2Testcase;?>
					<?php 
						if(count($rowLogs2Testcase)>0){

							$testCase = $rowLogs2Testcase;
							$summaryTestCase = $testCases['@attributes'];

							// test description for multiple test
							$testDescription = explode(":", $testCases['system-out']);
							$testDescription = trim(str_replace("...", "", $testDescription[1]));
						
						}else{
							$testCase = $testCases;
							$summaryTestCase = $testCases;
						}
						//print_r($rowLogs2Testcase);
						$class = 'green';
						if(isset($testCase['failure'])){
							$class='yellow';
						}elseif(isset($testCase['error'])){
							$class = 'red';
						}
					?>
						<tr class="<?php echo $class?> detailTestCase <?php echo $percentSuccess==100?'perfect':'not-prefect'?>"">
							<td><?php  echo $no.'.'.$subNo.'.'.$subNo2?></td>
							<td><a href="../screenshoot/<?php echo $date_log ?>/<?php echo $summaryTestCase['name']?>" target="_blank">-- <?php echo $summaryTestCase['class']?>::<?php echo $summaryTestCase['name']?></a><p><?php echo $testDescription; ?></p></td>
							<td><?php echo $summaryTestCase['file']?> (<?php echo $summaryTestCase['line']?>)</td>
							<td><?php if (isset($summaryTestCase['tests'])) echo $summaryTestCase['tests']?></td>
							<td><?php echo $summaryTestCase['assertions']?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><?php echo gmdate('H:i:s',$summaryTestCase['time'])?></td>
						</tr>
						<?php if(isset($testCase['failure']) || isset($testCase['error'])):?>
						<tr class="<?php echo $class?> detailTestCase">
							<td colspan="9"><?php print_r('<pre>'.$testCase['failure'].'</pre>')?> <?php print_r('<pre>'.$testCase['error'].'</pre>')?></td>
						</tr>
						<?php endif; ?>
					<?php $subNo2++; ?>
				<?php } ?>

			<?php endif?>


			<?php
				$subNo++;
				endforeach;
				endif;
			?>
	<?php $a++; $no++; endforeach; ?>
		</table>
		<hr>
		<a name="testsuite"></a>
		<strong>Test Suite Only < 100%</strong>
		<?php
		/*echo count($arrLogs['testsuite']);
		echo '<pre>';
		print_r($arrLogs);
		*/
		echo '<pre>';
		//var_dump($testSuite100);
		
		foreach ($testSuite100 as $testSuite) {
			echo '&lt;testsuite name="'.$testSuite['name'].'"&gt;<br>';
			echo '	&lt;directory&gt;'.$testSuite['dir'].'&lt;/directory&gt;<br>';
			echo '&lt;/testsuite&gt;<br>';
		}
		echo '</pre>';
		?>
		<hr>
		<a name="allTC"></a>
		<strong>All Test Case < 100%</strong>
		<?php
		/*echo count($arrLogs['testsuite']);
		echo '<pre>';
		print_r($arrLogs);
		*/
		echo '<pre>';
		foreach ($testSuite100 as $testSuite) {
			echo '&lt;testsuite name="'.$testSuite['name'].'"&gt;<br>';
			if(!empty($testSuite['files'])){
				foreach ($testSuite['files'] as $testFile) {
					echo '	&lt;file&gt;'.$testFile.'&lt;/file&gt;<br>';
				}
			}
			echo '&lt;/testsuite&gt;<br>';
		}
		echo '</pre>';
		?>

	
	</body>
</html>
<?php 
/*echo count($arrLogs['testsuite']);
echo '<pre>';
print_r($arrLogs);
*/
?>