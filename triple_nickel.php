<?php
#
#       triple_nickel.php - this script should be run daily by a cron job
#
#       Usage: php triple_nickel.php <tag>
#
#       (c) 2014 Kathryn Lybarger. CC-BY-SA
#

# Location of Spreadsheet library
require_once "../lib/Spreadsheet.php";

# To connect to Voyager server
require_once "passwd.php";

# Create this directory
$filedir = "files";

# You shouldn't have to change anything below here
#-------------------------------------------------------------------#

function seqnum_of_address($addr) {
	return floor($addr/990) + 1;
}

function fielddata_from_triplet($segment,$trip_addr) {
	# this function expects to receive the WHOLE leader/directory
	global $bibid;
	global $conn;
	$base_addr = substr($segment,12,5);
	$length = substr($segment,$trip_addr+3,4) - 3;
	$offset = substr($segment,$trip_addr+7,5) + $base_addr + 2;
	
	$data_offset = $offset % 990;

	# how many blobs?
	$data_seqnum = seqnum_of_address($offset);
	$data_seqnumpp = seqnum_of_address($offset + $length);

	$query3 = "SELECT RECORD_SEGMENT FROM BIB_DATA WHERE BIB_ID='$bibid' AND (SEQNUM>='$data_seqnum' AND SEQNUM <='$data_seqnumpp') ORDER BY SEQNUM";
	$stid3 = oci_parse($conn, $query3);
	oci_execute($stid3);
	$data_segment = "";
	while ($row3 = oci_fetch_array($stid3, OCI_ASSOC+OCI_RETURN_NULLS)) {
		$data_segment .= $row3['RECORD_SEGMENT'];
	}

	return substr($data_segment,$data_offset,$length);
}

if (isset($argv[1]) and preg_match("/^\d\d\d$/",$argv[1])) {
	$tag = $argv[1];
} else {
	print "Usage: php TripleNickel.php <tag>\n";
	exit;
}

$today = date("Ymd");
$fp = fopen("$filedir/all-$tag-$today-full.txt","w");

# FIRST find only the ones whose directory fully fits in the first blob
$query = "SELECT BIB_MASTER.BIB_ID,BIB_DATA.RECORD_SEGMENT FROM BIB_DATA JOIN BIB_MASTER ON BIB_MASTER.BIB_ID=BIB_DATA.BIB_ID WHERE BIB_DATA.SEQNUM='1' AND BIB_MASTER.SUPPRESS_IN_OPAC = 'N' AND
(SUBSTR(BIB_DATA.RECORD_SEGMENT,13,5) < '00983') AND
(
";

# build query
# from 25 to 973
for ($j=0;$j<=79;$j++) { # 79 fields sounds like a lot
	$num = 25+$j*12;
	$query .= "(SUBSTR(BIB_DATA.RECORD_SEGMENT," . $num . ", 3) = '$tag') OR\n";
}

$query = preg_replace("/ OR$/", "", $query) . ")";

$conn = oci_connect($user, $pass, $host );
$stid = oci_parse($conn, $query);
oci_execute($stid);

while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	$bibid = $row['BIB_ID'];
	$base_addr = substr($row['RECORD_SEGMENT'],12,5);
	for ($j=0;24+$j*12+3<=$base_addr-2;$j++) {
		$num = 24+$j*12;
		if (substr($row['RECORD_SEGMENT'],$num,3) == $tag) {
			$fd = fielddata_from_triplet($row['RECORD_SEGMENT'],
				$num);
			fwrite($fp, $row['BIB_ID'] . "\t" . htmlspecialchars(strtr($fd,"\x1F","$")) . "\n");
		}
	}
}

# now find records bigger than that
$query = "SELECT BIB_MASTER.BIB_ID, SUBSTR(BIB_DATA.RECORD_SEGMENT,13,5) AS BASE_ADDR FROM BIB_DATA JOIN BIB_MASTER ON BIB_DATA.BIB_ID = BIB_MASTER.BIB_ID WHERE BIB_DATA.SEQNUM='1' AND BIB_MASTER.SUPPRESS_IN_OPAC='N' AND SUBSTR(BIB_DATA.RECORD_SEGMENT,13,5) >= '00990'";
$stid = oci_parse($conn, $query);
oci_execute($stid);

# loop through all "big" records
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
	$bibid = $row['BIB_ID'];
	$base_addr = $row['BASE_ADDR'];
	$endseg = seqnum_of_address($base_addr);
	$query2 = "SELECT RECORD_SEGMENT FROM BIB_DATA WHERE BIB_ID='$bibid' AND SEQNUM <= '$endseg' ORDER BY SEQNUM";
	$stid2 = oci_parse($conn, $query2);
	oci_execute($stid2);
	$dir_segment = "";
	while ($row2 = oci_fetch_array($stid2, OCI_ASSOC+OCI_RETURN_NULLS)) {
		$dir_segment .= $row2['RECORD_SEGMENT'];
	}

	for ($j=0;24+$j*12+3<=$base_addr-2;$j++) {
		$num = 24+$j*12;
		if (substr($dir_segment,$num,3) == $tag) {
			$fd = fielddata_from_triplet($dir_segment,
				$num);
			fwrite($fp, $bibid . "\t" . htmlspecialchars(strtr($fd,"\x1F","$")) . "\n");
		}
	}
}

fclose($fp);

`sed -e "s/.*\t//" $filedir/all-$tag-$today-full.txt | sort -f | uniq -ci | sort -r > $filedir/$tag-$today-grouped.txt`;

$ss = new Spreadsheet();
$ss->styles[1]->BackgroundColor = '#0431b4';

$sheet1 = $ss->sheets[0];
$sheet1->name = "BibId";
$sheet1->appendColumn(new Column(array('width' => 70)));
$sheet1->appendColumn(new Column(array('width' => 100)));
$sheet1->addTitleRow(array(
        'BIB ID',
        'Value'
));

$fp = fopen("$filedir/all-$tag-$today-full.txt","r");
while ($line = fgets($fp)) {
	preg_match("/^(\d+)\t(.*)/",$line,$m);
	$row = new Row();
	$row->populate(array($m[1],$m[2]));
	$sheet1->appendRow($row);
}
fclose($fp);

$ss->sheets[1] = new Sheet("Freq");
$sheet2 = $ss->sheets[1];
$sheet2->appendColumn(new Column(array('width' => 70)));
$sheet2->appendColumn(new Column(array('width' => 100)));
$sheet2->addTitleRow(array(
        'Frequency',
        'Value'
));

$fp = fopen("$filedir/$tag-$today-grouped.txt","r");
while ($line = fgets($fp)) {
	preg_match("/^ *(\d+) (.*)/",$line,$m);
	$row = new Row();
	$row->populate(array($m[1],$m[2]));
	$sheet2->appendRow($row);
}
fclose($fp);

$fp = fopen("$filedir/$tag-report-$today.xml","w");
fwrite($fp,$ss->asXML());
fclose($fp);
