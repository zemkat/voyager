<?php
#
#	quick_query.php - query a Voyager server, output as TSV or HTML table
#
#	(c)2013 Kathryn Lybarger. CC-BY-SA
#

$db_host = 'HOST.hosted.exlibrisgroup.com'; # host
$db_port = '1521';		# port
$ro_login = 'XXXX';		# read-only login
$ro_passwd = 'XXXX';	# read-only password
$html_table = 1;		# set to 0 for TSV output

$query = 'SELECT * FROM VERSIONS'; #query

#-----------you should not have to edit below this line--------------

$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = $db_host)(PORT = $db_port)))(CONNECT_DATA=(SID=VGER)))";
$conn = oci_connect($ro_login, $ro_passwd, $db);

$stid = oci_parse($conn, $query);
oci_execute($stid);


if ($html_table) {
	# print HTML table
	print "<table border='1'>\n"; 
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		print "<tr>\n"; 
    	foreach ($row as $item) {
        		print "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    	}
		print "</tr>\n"; 
	}
	print "</table>\n"; 
} else {
	# print TSV
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    	foreach ($row as $item) {
				print "$item\t";
		}
		print "\n";
	}
}
