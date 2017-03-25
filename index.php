<html>
<head>
	<title>Jomsocial Automation Interface</title>
	<style>
		.command{
			width:45%;
			height:300px;
			color:#1bcc00;
			background-color: #000;
			font-size: 11px;
			margin:2px;
			padding:5px;
			overflow: auto;
			float: left;
		}
	</style>
</head>
<body>
	<?php 
		$dailyMotionRSSFeedXML = file_get_contents("http://www.dailymotion.com/rss/en");
					$xml = new SimpleXMLElement($dailyMotionRSSFeedXML);
					$key = rand(1,15);
					$videoURL = (string) $xml->channel->item[$key]->link;
					$videoURL = urldecode($videoURL);
		echo $videoURL;
	?>
</body>
</html>