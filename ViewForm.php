<?php require_once('Functions/SQLFunc.php'); ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>View Form</title>
</head>
<body>
	<form name="ViewForm" method="post" action="IndexShipment.php">
		<table id='searchform'>
			<th colspan=2>Search Shipments</th>
			<tr>
				<td class="a">Client <input type="text" list='client' name='client'>
					<datalist id='client'>
					<?php clientDropdown(); ?>
					</datalist>
				</td>
				<td class="b">Carrier <input type='text' list='carrier' name='carrier'>
					<datalist id='carrier'>
					<?php carrierDropdown(); ?>
					</datalist>
				</td>
			</tr>
			<tr>
				<td class="a">Ship Date >= <input name='startdate' type='text'></td>
				<td class="b">Ship Date <= <input name='enddate' type='text'></td>
			</tr>
			<tr>
				<td class="a">Status <input type='text' list='status' name='status'>
					<datalist id='status'>
					<option value='InTransit'>In Transit</option>
					<option value='Delivered'>Delivered</option>
					<option value='OnHold'>On Hold</option>
				</datalist></td>
				<td class="b">Tracking Number <input name='tracknum' type='text'></td>
			</tr>
			<tr>
				<td class="a"> Limit <select name='limit'>
					<option value='25'>25</option>
					<option value='50'>50</option>
					<option value='75'>75</option>
					<option value='100'>100</option>
				</select></td>
				<td class="b"> Order <select name='order'>
					<option value='DESC'> Descending</option>
					<option value='ASC'>Ascending</option>
				</select></td>
			</tr>
			<tr>
				<td id="mybuttona"><input type='submit' value='Search' name='Submit'></td>
				<td id="mybuttonb"><input type='reset' value='Reset Form' name='Reset'></td>
			</tr>
		</table>
	</form>
	<p id="note">NOTE: All dates must be formated YYYY-MM-DD</p>
</body>
</html>