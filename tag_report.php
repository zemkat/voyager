<?php
#
#       tag_report.php - generate reports of tag usage in Voyager
#
#       (c) 2014 Kathryn Lybarger. CC-BY-SA
#

# Address of spreadsheet library
require_once "../lib/Spreadsheet.php";

# Login information for Voyager
require_once "passwd.php";

$fp = fopen ("tag-report.csv", "w");
$fpe = fopen ("tag-report-error.txt", "w");
$fpss = fopen("tag-report.xml","w");

#  You should not have to edit below this line
#---------------------------------------------------------------------------#

$ss = new Spreadsheet();
$ss->styles[1]->BackgroundColor = '#006411';
$percent = new Style('Percent','Percent',array('NumberFormat' => 'Percent'));
array_push($ss->styles, $percent);

$sheet1 = $ss->sheets[0];
$sheet1->appendColumn(new Column(array('width' => 50)));
$sheet1->appendColumn(new Column(array('width' => 60)));
$sheet1->appendColumn(new Column(array('width' => 80)));
$sheet1->appendColumn(new Column(array('width' => 70, 'style' => 'Percent')));
$sheet1->addTitleRow(array(
        'Tag',
        'Total Uses',
        'Total Records',
		'Percentage'
));

$conn = oci_connect($user, $pass, $host);

$query = "SELECT BIB_ID, RECORD_SEGMENT FROM BIB_DATA WHERE SEQNUM='1'";

$stid = oci_parse($conn, $query);
oci_execute($stid);

$total_uses = array(); 
for ($j=0;$j<1000;$j++) { 
	$total_uses[sprintf("%03d",$j)] = 0; 
}

$total_records = array();
for ($j=0;$j<1000;$j++) { 
	$total_records[sprintf("%03d",$j)] = 0; 
}

$count = 0;
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	$count++;

	$base_address = substr($row['RECORD_SEGMENT'],12,5);
	$dirsegs = $row['RECORD_SEGMENT'];
	$chunk_size = 990;
	if ($base_address >= $chunk_size) { # check border, get extra if need be
		$max_seg = ceil($base_address/$chunk_size);

		for ($j=2;$j<=$max_seg;$j++) {
			$query2 = "SELECT RECORD_SEGMENT FROM BIB_DATA WHERE SEQNUM='$j' AND BIB_ID = '" . $row['BIB_ID'] . "'";
			$stid2 = oci_parse($conn, $query2);
			oci_execute($stid2);
			$row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS);
			$dirsegs .= $row2['RECORD_SEGMENT'];
		}
	}
	$directory = substr($dirsegs,24,$base_address-1-24);
	$dir_length = strlen($directory);
	$fields = $dir_length/12;

	$this_record = array();
	for ($j=0;$j<$fields;$j++) {
		$tag = substr($directory,$j*12,3);
		if (preg_match("/^\d\d\d$/",$tag)) {
			$total_uses[$tag]++;
			$this_record[$tag] = true;
		} else {
			fwrite($fpe,"ERROR: " . $row['BIB_ID'] . " has tag " . $tag . "\n");
		}
	}
	foreach ($this_record as $k => $v) {
		$total_records[$k]++;
	}
}

for ($j=1;$j<1000;$j++) {
	$tag = sprintf("%03d",$j);
	if (($total_uses[$tag]) > 0) {
		$percent = $total_records[$tag]/$count;
		#$percent = round($percent*10000)/100;

		fwrite($fp,$tag . "," . $total_uses[$tag] . "," . $total_records[$tag] . "," . $total_records[$tag]/$count . "\n");
		$row = new Row();
		$row->populate(array($tag, $total_uses[$tag], $total_records[$tag],
			$percent));
		$row->cells[1]->data->type = "Number";
		$row->cells[2]->data->type = "Number";
		$row->cells[3]->data->type = "Number";
		$sheet1->appendRow($row);
	}
}

fwrite($fpss, $ss->asXML() );
fclose($fpss);

fclose($fp);
fclose($fpe);
