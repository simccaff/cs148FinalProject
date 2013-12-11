

<?php
require_once("connect.php");

$db->beginTransaction();

$sql = "SELECT fldCategory FROM tblArt";
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;
       
            $stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

?>



 <form action="<? print $_SERVER['PHP_SELF']; ?>"
                  method="post"
                  id="frmRegister">
				  
				<fieldset class="lists">	
					<legend>What category of art do you want?</legend>
					<?php
						$array_length = count($result);
						echo $array_length;
						?>
					<select id="lstCategory" name="lstCategory" tabindex="60" size="1">
						<?php
						for ($i=1;$i<=$array_length;$i++){
						?>
						<option value="<?=$result[$i];?>"><?=$result[$i];?></option>
						 <?php
						 }
						 ?>
					</select>
				</fieldset>
				<?php
				echo $result[1];
				?>
</form>