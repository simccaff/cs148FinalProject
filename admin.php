<?php
/* Edited by Shane McCafferty 12/3/13
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 * Last updated on: November 5, 2013
 */


//-----------------------------------------------------------------------------
// 
// Initialize variables
//  


$debug = false;
if (isset($_GET["debug"])) {
    $debug = true;
}

include("connect.php");

$baseURL = "http://www.uvm.edu/~simccaff/";
$folderPath = "cs148/assignment7.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "admin.php";

$fromPage = getenv("http_referer");

if ($debug) {
    print "<p>From: " . $fromPage . " should match ";
    print "<p>Your: " . $yourURL;
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// initialize my form variables either to what is in table or the default 
// values.
// display record to update
if (isset($_POST["lstArt"])) {
    

    // you may want to add another security check to make sure the person
    // is allowed to delete records.
    
    $id = htmlentities($_POST["lstArt"], ENT_QUOTES);

    $sql = "SELECT fldTitle, fldCategory, fldRetailPrice ";
    $sql .= "FROM tblArt ";
    $sql .= "WHERE pkArtId=" . $id;

    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $Art = $stmt->fetchAll();
    if ($debug) {
        print "<pre>";
        print_r($Art);
        print "</pre>";
    }

    foreach ($Art as $art) {
        $title = $art["fldTitle"];
        $category = $art["fldCategory"];
        $retail = $art["fldRetailPrice"];
    }
} else { 

    $id = "";
    $title = "";
    $category = "";
    $retail = "";


} 


//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// if form has been submitted, validate the information both add and update
if (isset($_POST["btnSubmitted"])) {
    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }
    
    // initialize my variables to the forms posting	
    $id = htmlentities($_POST["id"], ENT_QUOTES);
    $title = htmlentities($_POST["txttitle"], ENT_QUOTES);
    $category = htmlentities($_POST["lstCategory"], ENT_QUOTES);
    $retail = htmlentities($_POST["txtretail"], ENT_QUOTES);
	$pkArtID = htmlentities($_POST["ArtName"], ENT_QUOTES);

    
    // Error checking forms input
    include ("validation_functions.php");

    $errorMsg = array();

    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
    // begin testing each form element 
    if ($title == "") {
        $errorMsg[] = "Please enter the Item Name";
    } else {
        $valid = verifyAlphaNum($title); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "Item Name must be letters and numbers, spaces, dashes and ' only.";
        }
    }

   //********List box needs no verification as the choices are hard-coded in************

    if ($retail == "") {
        $errorMsg[] = "Please enter the retail price";
    } else {
        $valid = verifyNum($retail); /* test for non-valid  data */
        if (!$valid) {
            $error_msg[] = "Retail price must be numbers, spaces, and . only.";
        }
    }
    //- end testing ---------------------------------------------------
    
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%


    if (!$errorMsg) {
        
        if ($debug)
            echo "<p>Form is valid</p>";

        if (isset($_POST["id"])) { // update record
            $sql = "UPDATE ";
            $sql .= "tblArt SET ";
            $sql .= "fldTitle='$title', ";
            $sql .= "fldCategory='$category', ";
            $sql .= "fldRetailPrice='$retail' ";
            $sql .= "WHERE pkArtId=" . $id;
        } else { // insert record
            $sql = "INSERT INTO ";
            $sql .= "tblArt SET ";
            $sql .= "fldTitle='$title', ";
            $sql .= "fldCategory='$category', ";
            $sql .= "fldRetailPrice='$retail'";
        }
      

        if ($debug)
            echo "<p>SQL: " . $sql . "</p>";
			
        $stmt = $db->prepare($sql);

        $enterData = $stmt->execute();

        // Processing for other tables falls into place here. I like to use
        // the same variable $sql so i would repeat above code as needed.
        if ($debug){
            print "<p>Record has been updated";
        }
        
        // update or insert complete
       
        
    }// end no errors	
} // end isset cmdSubmitted
if (isset($_POST["cmdDelete"])) {
    
    
    $delId = htmlentities($_POST["ArtName"], ENT_QUOTES);
	

    // I may need to do a select to see if there are any related records.
    // and determine my processing steps before i try to code.

	$sql = "DELETE ";
    $sql .= "FROM tblArtOrder ";
    $sql .= "WHERE fkArtId=" . $delId;

    if ($debug)
        print "<p>sql " . $sql;

    $stmt = $db->prepare($sql);

   $stmt->execute();
   
   
	
    $sql2 = "DELETE ";
    $sql2 .= "FROM tblArt ";
    $sql2 .= "WHERE pkArtId=" . $delId;

    if ($debug)
        print "<p>sql " . $sql2;

    $stmt2 = $db->prepare($sql2);

    $DeleteData = $stmt2->execute();
	
	 if ($debug){
            print "<p>Record has been updated";
        }

    // at this point you may or may not want to redisplay the form
   
}
 
include("top.php");
include("header.php");
include("nav.php");


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// display any errors at top of form page
if ($errorMsg) {
    echo "<ul>\n";
    foreach ($errorMsg as $err) {
        echo "<li style='color: #ff6666'>" . $err . "</li>\n";
    }
    echo "</ul>\n";
 //- end of displaying errors -----------------------------------
    //%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^% 
} else {
	print "<p>This Admin page is a place where records can be added to the database. Simply fill in the first form and click submit and the record will be added. Use the drop down menu to select a category for the art and then enter in some brief details. Records can also be deleted using the delete form. Select the item you wish to delete from the drop down list and then click the Delete button instead of submit. This should cause this record to be deleted permanently, so use with care!</p>";
    print "<h1>Add Art Information</h1>";
}
?>
<form action="<? print $_SERVER['PHP_SELF']; ?>" method="post">
	<fieldset class="lists">	
			<legend>Art Category</legend>
			<select id="lstCategory" name="lstCategory" tabindex="60" size="1">
				<option value="Bowl">Bowl</option>
				<option value="Plate" >Plate</option>
				<option value="Jewellery" >Jewellery</option>
				<option value="Figurine" >Figurine</option>
			</select>
	</fieldset>
	
    <fieldset>
        <label for="txttitle"	>Item Name*</label><br>
        <input name="txttitle" type="text" size="20" id="txttitle" <? print "value='$title'"; ?>/><br>


        <label for="txtretail">Retail Price* </label><br>
        <input name="txtretail" type="text" size="20" id="txtretail" <? print "value='$retail'"; ?> /><br>

        
        <input type="submit" name="btnSubmitted" value="Submit" />
    </fieldset>	
</form>


<form action="<? print $_SERVER['PHP_SELF']; ?>" method="post">
	<fieldset class="lists">	
					<legend>Select Art Entry to Delete</legend>
					<?php
						$conn = mysql_connect("webdb.uvm.edu","simccaff_admin","herbish12");
						mysql_select_db("SIMCCAFF_MaxineDavisArt",$conn); 
						$result = mysql_query("SELECT pkArtID,fldTitle FROM tblArt");
						?>
						<select name="ArtName">
						<?php
						$i=0;
						while($row = mysql_fetch_array($result)) {
						?>
						<option value="<?=$row["pkArtID"];?>"><?=$row["fldTitle"];?></option>
						<?php
						$i++;
						}
						?>
						</select>
						<?php
						mysql_close($conn);
						?>
				
						<input type="submit" name="cmdDelete" value="Delete" />
						
				</fieldset>	
</form>
<?php

include ("footer.php");
?>
</body>
</html>