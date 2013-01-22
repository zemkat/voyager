<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
<?php /*
   You probably don't need to change anything in this file; the
   username and password are stored in password.php and a sample
   is provided in passwd.php.txt for you. This work is CC-BY-SA
   (c) 2013 Kathryn Lybarger with special thanks to James Crowden,
   Janet Layman, and Jennifer Richmond from UK Libraries.
*/ ?>
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title>Voyager Get Items</title>
		<style type="text/css">
	body { background-color: #e5dccb; text-align: center; }
	.header td { text-align: left; }
	.header th { text-align: right; }
    .topRight { float: right; }
    label { font-weight: bold; }
    .itemListing th { background-color: #d4cbba; }
    .itemListing { width: 100%; margin-top: 1em; }
	.notes { min-width: 30ex; }
	@media print {
	.noPrint {
    	display:none;
	}
}
		</style>
    </head>
	<body>
<form class="noPrint topRight" action="" method="post"><fieldset>
	<label for="mfhd_id">MFHD ID: <input size="10" name="mfhd_id" id="mfhd_id"/></label>
</fieldset></form>

<?php

if (isset($_REQUEST['mfhd_id']) and $mfhd_id=intval($_REQUEST['mfhd_id'])) {

require_once "passwd.php";
$conn = oci_connect( $user, $pass, $host );

# HEADER

$query = "SELECT
	BIB_TEXT.BIB_ID,
	BIB_TEXT.TITLE_BRIEF,
	BIB_MFHD.MFHD_ID,
	MFHD_MASTER.DISPLAY_CALL_NO,
	LOCATION.LOCATION_CODE 
FROM 
	(((BIB_MFHD INNER JOIN BIB_TEXT ON BIB_MFHD.BIB_ID = BIB_TEXT.BIB_ID) INNER JOIN MFHD_MASTER ON BIB_MFHD.MFHD_ID = MFHD_MASTER.MFHD_ID) INNER JOIN LOCATION ON MFHD_MASTER.LOCATION_ID = LOCATION.LOCATION_ID) 
WHERE 
	MFHD_MASTER.MFHD_ID=$mfhd_id";

$stid = oci_parse($conn, $query);
oci_execute($stid);
$row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);

$title = htmlspecialchars($row['TITLE_BRIEF']);

print "
<table class='header' border='0'>
<tr><th>Bib record:</th>
    <td>$title (bib #{$row['BIB_ID']})</td>
</tr>
<tr><th>Call number:</th>
    <td>{$row['DISPLAY_CALL_NO']}</td>
</tr>
<tr><th>Holdings:</th>
    <td>{$row['LOCATION_CODE']} (mfhd #$mfhd_id)</td>
</tr>
</table>
";

$query = "SELECT 
	MFHD_ITEM.ITEM_ENUM,
	MFHD_ITEM.CHRON,
	ITEM_BARCODE.ITEM_BARCODE,
	LOCATION.LOCATION_CODE as PERM,
	LOCLOC.LOCATION_CODE as TEMP,
	ITEM_STATUS_TYPE.ITEM_STATUS_DESC
FROM 
(((((MFHD_ITEM INNER JOIN ITEM ON MFHD_ITEM.ITEM_ID = ITEM.ITEM_ID) INNER JOIN LOCATION ON ITEM.PERM_LOCATION = LOCATION.LOCATION_ID) LEFT JOIN LOCATION LOCLOC ON ITEM.TEMP_LOCATION = LOCLOC.LOCATION_ID) INNER JOIN ITEM_STATUS ON ITEM.ITEM_ID = ITEM_STATUS.ITEM_ID) INNER JOIN ITEM_STATUS_TYPE ON ITEM_STATUS.ITEM_STATUS = ITEM_STATUS_TYPE.ITEM_STATUS_TYPE) INNER JOIN ITEM_BARCODE ON ITEM.ITEM_ID = ITEM_BARCODE.ITEM_ID
WHERE (MFHD_ITEM.MFHD_ID=$mfhd_id) AND (ITEM_BARCODE.BARCODE_STATUS=1)
ORDER BY ITEM.ITEM_SEQUENCE_NUMBER
";

$stid = oci_parse($conn, $query);
oci_execute($stid);

echo "<table class='itemListing' border='1'>\n";
print "<tr>
<th>Enum</th>
<th>Chron</th>
<th>Barcode</th>
<th>Location</th>
<th>Status</th>
<th>NOTES</th>
</tr>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";

	$row['PERM'] = $row['TEMP'] ? "{$row['TEMP']} (T)" : "{$row['PERM']} (P)";
    unset( $row['TEMP'] );

    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
	print "    <td class='notes'>&nbsp;</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";

}
?>
</body>
</html>
