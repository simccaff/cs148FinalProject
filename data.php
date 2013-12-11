<?php include ("top.php"); ?>	

<body id="display">

<?php include ("header.php"); ?>		

<?php include ("nav.php"); ?>
		
<br>
<article id="main">

<h2>Here is all the data that you have submitted to us via our form:</h2>


<?php

require_once("connect.php");

	$db->beginTransaction();

	$sql = "SELECT fldFirstName, fldLastName, pkEmail FROM tblCustomer WHERE pkEmail!=''";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $CustomerResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
          
			
		echo "<table border='1'>
		<tr>
		<th>Firstname</th>
		<th>Lastname</th>
		<th>Email</th>
		</tr>";	
		
	
	for($i = 0, $size = count($CustomerResult[pkEmail]); $i < $size; $i++)
		{
		echo "<tr>";
		echo "<td>" . $CustomerResult[fldFirstName] . "</td>";
		echo "<td>" . $CustomerResult[fldLastName] . "</td>";
		echo "<td>" . $CustomerResult[pkEmail] . "</td>";
		echo "</tr>";
		}
		echo "</table>";
		
		
		
		$sql2 = "SELECT pkOrderID, fkEmail, fldOrderDate FROM tblOrder";
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute();

            $OrderResult = $stmt2->fetch(PDO::FETCH_ASSOC);
            
          
			
		echo "<table border='1'>
		<tr>
		<th>OrderID</th>
		<th>Email</th>
		<th>OrderDate</th>
		</tr>";	
		
	
	for($i = 0, $size = count($OrderResult[fkEmail]); $i < $size; $i++)
		{
		
		echo "<tr>";
		echo "<td>" . $OrderResult[pkOrderID] . "</td>";
		echo "<td>" . $OrderResult[fkEmail] . "</td>";
		echo "<td>" . $OrderResult[fldOrderDate] . "</td>";
		echo "</tr>";
		}
		echo "</table>";


?>


</article>
<?php include ("footer.php"); ?>		

</body>
</html>
