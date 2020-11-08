<html>
<head>
	<title>Add Shipment Form</title>
	<!--Form used to add a shipment to the database-->
<head>
<body>
	<?php require_once('Functions/SQLFunc.php'); ?><!--include PHP file to query DB-->
	<form name='newship' method='post' action='NewShipment.php'>
		<table id='newship'>
			<tr>
				<th colspan=2>New Shipment</th>
			</tr>
			<tr>
				<td class="a">Client <input type="text" list='client' name='client'>
					<datalist id='client'>
					<?php clientDropdown(); ?>
					</datalist>
				</td>
				<td class="b">Carrier <input type='text' list='carrier' name='carrier'>
					<datalist id='carrier'>
						<?php carrierDropdown(); ?><!--displays current list of carriers from database-->
					</datalist>
				</td>
			</tr>
			<tr>
				<td class='a'>Ship Date <input name='shipdate' type='text'></td>
				<td class='b'>Est Delivery Date <input name='deliverydate' type='text'></td>
			</tr>
			<tr>
				<td class='a'>Items Shipped: <input type="textbox" name="items"></td>
				<td class='b'>Notes: <input type="textbox" name="notes"></td>
			</tr>
			<tr>
				<td class='a'>Tracking Number <input name='tracknum' type='text'></td>
				<td class='b'>Shipment Status <select name='status'>
					<option value='InTransit'>In Transit</option>
					<option value='Delivered'>Delivered</option>
					<option value='OnHold'>On Hold</option>
				</select></td>
			</tr>
			<tr>
				<td id="mybuttona"><input type='submit' value='Submit' name='Submit'></td>
				<td id="mybuttonb"><input type='reset' value='Reset Form' name='Reset'></td>
			</tr>
		</table>
	</form>
	<p></p>
	<p id="note">NOTE: All fields must be filled</p>
</body>
</html>
