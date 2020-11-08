<html>
<head>
	<link rel="stylesheet" type="text/css" href="Design.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
	<?php require_once('Functions/SQLFunc.php'); ?><!--include PHP file to query DB-->
	<form name='updateship' method='post' action='ChangeShip.php'>
		<table id='changeship'>
			<tr>
				<th colspan=2>Modify Shipment</th>
			</tr>
			<tr>
				<td class="a">Tracking Number <input name='tracknum' type='text'></td>
				<td></td>
			</tr>
			<tr>
				<td class="a">Please fill in any fields needing to be changed below</td>
				<td></td>
			</tr>
			<tr>
				<td class="a">Client <input list='client'>
					<datalist id='client'>";
					<?php clientDropdown();?><!--displays current list of clients from db-->
					</datalist>
				</td>
				<td class="b">Carrier <input list='carrier'>
					<datalist id='carrier'>";
						<?php carrierDropdown();?><!--displays current list of carriers from db-->
					</datalist>
				</td>
			</tr>
			<tr>
				<td class="a">Estimated Delivery <input name='estdel' type='text'></td>
				<td class="b">Shipment Status <input list='status'>
					<datalist id='list'>
						<option value='InTransit'>In Transit</option>
						<option value='Delivered'>Delivered</option>
						<option value='OnHold'>On Hold</option>
				</datalist></td>
			</tr>
			<tr>
				<td id="mybuttona"><input type='submit' value='Search' name='Submit'></td>
				<td id="mybuttonb"><input type='reset' value='Reset Form' name='Reset'></td>
				
		</table>
	</form>
</body>
</html>
