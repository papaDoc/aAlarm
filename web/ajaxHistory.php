<?php
	require "db.php";
?>
<?php
	$statusDateTSStart = -1;
	$statusDateTSEnd = -1;
	$factor = 1;
	if(isset($_POST["statusDateTSStart"]))
	{
		$statusDateTSStart = $_POST["statusDateTSStart"];
	}
	if(isset($_POST["statusDateTSEnd"]))
	{
		$statusDateTSEnd = $_POST["statusDateTSEnd"];
	}
	else
	{
		$statusDateTSEnd = time();
	}
	if(isset($_POST["ms"]))
	{
		$factor = 1000;
	}
	
	
	$reqStatus = "
	SELECT e.date as eDate, e.sensorId as sensorId, e.state as state, rg.state as rgState, rs.state as rsState
	FROM Event e, RefGlobalState rg, RefSensorState rs
	WHERE e.state = rg.id
	AND e.state = rs.id
	";
	
	if ($statusDateTSStart != -1)
	{
		print "from ".date("Y-m-d H:i:s", $statusDateTSStart / $factor);
		print " to ".date("Y-m-d H:i:s", $statusDateTSEnd / $factor);
		$statusDateSQLStart = date("Y-m-d H:i:s", $statusDateTSStart / $factor);
		$statusDateSQLEnd = date("Y-m-d H:i:s", $statusDateTSEnd / $factor);
		$reqStatus .= "
		AND e.date BETWEEN '$statusDateSQLStart' AND '$statusDateSQLEnd'
		ORDER BY e.id DESC
		";
	}
	else
	{
		$reqStatus .= "
		ORDER BY e.id DESC
		limit 0,10";
	}
	$results = $db->selectLinesObjects($reqStatus);

	$strTableLines = "";
	if($db->getNbRows())
	{
		foreach($results as $item)
		{
			$strTableLines .= "<tr><td>";
			$strTableLines .= $item->eDate;
			if($item->sensorId == 0)
			{
				$strTableLines .= "</td><td class=\"color_globalState".$item->state."\">";
				$strTableLines .= $item->rgState;
			}
			else
			{
				
				$strTableLines .= "</td><td class=\"color_sensorState".$item->state."\">";
				$strTableLines .= "sensor".$item->sensorId." ";
				$strTableLines .= $item->rsState;
			}
			$strTableLines .= "</td></tr>";
		}

?>
<table class="historyTable">
	<thead>
		<tr>
			<td>Date</td>
			<td>Status</td>
			<td>Sensor</td>
		</tr>
	</thead>
	<tbody>
		<?php print $strTableLines;?>
	</tbody>
</table>
<?
	}
	else
	{
		print "no data !!";
	}
?>
