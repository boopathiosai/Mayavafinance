<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
</HEAD>

<BODY>
Table
<table width="98%" border="1" align="center" cellpadding="0" cellspacing="0" class="list-tbl" style="border:1px solid red;">
<?php 
$k=1;
$data=5;
$count_data = $data/3;
echo "<table>";
echo "<tr>";
echo "<td>";
for($i=1;$i<=3;$i++)
{
	echo "<td>";
	echo "<table border=1>";
	echo "<tr><th>Lon Date</th>
	                  <th>Loan ID</th>
					  <th>Loan Amount</th></tr>";	

	for($j=0;$j<=$count_data;$j++) {
        echo "<tr>";
		echo "<td>";
		echo "test". $k;
		echo "</td>";
        		
		echo "<td>";
		echo "sample". $k;
		echo "</td>";

		echo "<td>";
		echo "hello". $k;
		echo "</td>";
		echo "</tr>";

		$k++;
	}		

echo "</table>";
echo "</td>";
}
echo "</td>";
echo "</tr>";
echo "</table>";
?>
</table>

</BODY>
</HTML>
