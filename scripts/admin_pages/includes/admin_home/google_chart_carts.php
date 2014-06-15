<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
$key='google_chart_carts';
$compiledWidget['key']='google_chart';
$compiledWidget['defaultCol']=2;
$compiledWidget['title']=$this->pi_getLL('admin_label_shoppingcart');
$dates=array();
$data=array();
$data[]=array(
	'Datum',
	'Winkelwagens'
);
for ($i=12; $i>=0; $i--) {
	$time=strtotime(date('Y-m-01').' -'.$i.' MONTH');
	$dates[strftime("%B %Y", $time)]=date("Y-m", $time);
}
foreach ($dates as $key=>$value) {
	$start_time=strtotime($value."-01 00:00:00");
	$end_time=strtotime($value."-01 23:59:59 +1 MONTH -1 DAY");
	$where=array();
	$where[]='(f.is_checkout=0 or f.is_checkout=1)';
	$str="SELECT count(1) as total from tx_multishop_cart_contents f WHERE (".implode(" AND ", $where).") and (f.crdate BETWEEN ".$start_time." and ".$end_time.")";
	$qry=$GLOBALS['TYPO3_DB']->sql_query($str);
	$row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($qry);
	$data[]=array(
		date("m-Y", $start_time),
		(int)$row['total']
	);
}
$compiledWidget['class']='googleChart-wrapper';
$compiledWidget['content']='
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart'.$compiledWidget['key'].');
      function drawChart'.$compiledWidget['key'].'() {
        var data = google.visualization.arrayToDataTable(
		'.json_encode($data, ENT_NOQUOTES).'
		);
        var options = {
          title: \'\',
		  height:100,
          hAxis: {title: \'\', titleTextStyle: {color: \'red\'}},
          vAxis: {title: \''.$this->pi_getLL('admin_label_shoppingcart').'\', titleTextStyle: {color: \'red\'}},
		  legend: {position: \'none\'},
			chartArea: {
				left: 60,
				top:20,
				width: "98%",
				height: "50"
			}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById(\'chart_div_carts\'));
        chart.draw(data, options);
      }
    </script>
<div id="chart_div_carts" style=""></div>
';
$compiledWidget['additionalHeaderData']['key']='google_chart';
$compiledWidget['additionalHeaderData']['content']='<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
?>