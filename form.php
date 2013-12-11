<?php
$debug = false;
if ($debug) print "<p>DEBUG MODE IS ON</p>";

$baseURL = "http://www.uvm.edu/~simccaff/";
$folderPath = "cs148/assignment7.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "form.php";

require_once("connect.php");

//#############################################################################
// set all form variables to their default value on the form. for testing i set
// to my email address. you lose 10% on your grade if you forget to change it.

$email = "";
$firstName = "";
$lastName = "";
$gender = "";


$emailERROR = false;




//#############################################################################
//  
$mailed = false;
$messageA = "";
$messageB = "";
$messageC = "";


//-----------------------------------------------------------------------------
// 
// Checking to see if the form's been submitted. if not we just skip this whole 
// section and display the form
// 
//#############################################################################
// minor security check

if (isset($_POST["btnSubmit"])) {
    $fromPage = getenv("http_referer");

    if ($debug)
        print "<p>From: " . $fromPage . " should match ";
        print "<p>Your: " . $yourURL;

    if ($fromPage != $yourURL) {
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>");
    }


//#############################################################################
// replace any html or javascript code with html entities
//

    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
	
	$firstName = htmlentities($_POST["txtfirstname"], ENT_QUOTES, "UTF-8");
	
	$lastName = htmlentities($_POST["txtlastname"], ENT_QUOTES, "UTF-8");
	
	$gender = htmlentities($_POST["radGender"], ENT_QUOTES, "UTF-8");
	
	$pkArtID = htmlentities($_POST["ArtName"], ENT_QUOTES, "UTF-8");
	
  include ("validation_functions.php");

    $errorMsg = array();


//############################################################################
// 
// Check each of the fields for errors then adding any mistakes to the array.
//
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^       Check email address
    if (empty($email)) {
        $errorMsg[] = "Please enter your Email Address";
        $emailERROR = true;
    } else {
        $valid = verifyEmail($email); /* test for non-valid  data */
        if (!$valid) {
            $errorMsg[] = "I'm sorry, the email address you entered is not valid.";
            $emailERROR = true;
        }
    }
	

	 if (empty($firstName)) {
        $errorMsg[] = "Please enter your first name";
        $FnameERROR = true;
    } else {
        $validFirstName = verifyText ($firstName); /* test for non-valid  data */
        if (!$validFirstName) {
            $errorMsg[] = "I'm sorry, the first name you entered is not valid.";
            $FnameERROR = true;
        }
    }
	
	 if (empty($lastName)) {
        $errorMsg[] = "Please enter your last name";
        $LnameERROR = true;
    } else {
        $validLastName = verifyText ($lastName); /* test for non-valid  data */
        if (!$validLastName) {
            $errorMsg[] = "I'm sorry, the last name you entered is not valid.";
            $LnameERROR = true;
        }
    }
	


//############################################################################
// 
// Processing the Data of the form
//

    if (!$errorMsg) {
        if ($debug) print "<p>Form is valid</p>";
		
		
		
		$pkOrderID = "";
        $dataEntered = false;
        
        try {
            $db->beginTransaction();
           
            $sql = 'INSERT INTO tblCustomer SET pkEmail="' . $email . '", fldFirstName="' . $firstName . '", fldLastName="' . $lastName . '", fldGender="' . $gender . '"';
            $stmt = $db->prepare($sql);
            if ($debug) print "<p>sql ". $sql;
       
            $stmt->execute();
			
			$sql2 = 'INSERT INTO tblOrder SET fkEmail="' . $email . '"';
            $stmt2 = $db->prepare($sql2);
            if ($debug) print "<p>sql ". $sql2;
       
            $stmt2->execute();
			
			$pkOrderID = $db->lastInsertId();
			if ($debug) print "<p>pk2= " . $pkOrderID; 
			
			
			
			$sql4 = 'INSERT INTO tblArtOrder SET fkArtID="' . $pkArtID . '", fkOrderID="' . $pkOrderID . '"';
            $stmt4 = $db->prepare($sql4);
            if ($debug) print "<p>sql ". $sql4;
			
			$stmt4->execute();
			
			  $dataEntered = $db->commit();
            if ($debug) print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $db->rollback();
            if ($debug) print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }


        // If the transaction was successful, give success message[][][] CHANGE THIS BLOCKKKKKKKKKKKKKKKKKKK
        if ($dataEntered) {
            if ($debug) print "<p>data entered now prepare keys ";
            //#################################################################
            // create a key value for confirmation

            $sql = "SELECT fldRegisteredDate FROM tblCustomer WHERE pkEmail=" . $email;
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $dateSubmitted = $result["fldRegisteredDate"];

            $key1 = sha1($dateSubmitted);
            $key2 = $email;

            if ($debug) print "<p>key 1: " . $key1;
            if ($debug) print "<p>key 2: " . $key2;


            //#################################################################
            //
            //Put forms information into a variable to print on the screen
            //

            $messageA = '<h2>Thank you for registering.</h2>';

            $messageB = "<p>Click this link to confirm your registration: ";
            $messageB .= '<a href="' . $baseURL . $folderPath  . 'confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . '">Confirm Registration</a></p>';
            $messageB .= "<p>or copy and paste this url into a web browser: ";
            $messageB .= $baseURL . $folderPath  . 'confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . "</p>";

            $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>";

            //##############################################################
            //
            // email the form's information
            //
            
            $subject = "Maxine Davis Glass Art";
            include_once('mailMessage.php');
            $mailed = sendMail($email, $subject, $messageA . $messageB . $messageC);
        } //data entered   
    } // no errors 
}// ends if form was submitted. 

    include ("top.php");

    $ext = pathinfo(basename($_SERVER['PHP_SELF']));
    $file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']);

    print '<body id="' . $file_name . '">';

    include ("header.php");
    include ("nav.php");
    ?>

    <section id="main">
		<p>Here you can sign up to become an official customer, as well as tell us what type of custom glass art you would like! Select what type of art you want, as well as all of the various colors you want to be in it. This is not an actual working site but merely a representation of what an ordering system could be like. The custom pieces of art you decide on will be entered into our database and linked with your customer registration. Click submit once all of the form fields are filled out. Chose between different categories of glass art and then decide whether you want a solid color, or a mix and match style with multiple colors! So many choices try to contain yourself!</p>
		<br>
        <h1>Register </h1>

        <?
//############################################################################
//
//  In this block  display the information that was submitted and do not 
//  display the form.
//
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
            print "<h2>Your Request has ";

            if (!$mailed) {
                echo "not ";
            }

            echo "been processed</h2>";

            print "<p>A copy of this message has ";
            if (!$mailed) {
                echo "not ";
            }
            print "been sent to: " . $email . "</p>";

            echo $messageA . $messageC;
        } else {


//#############################################################################
//
// Here we display any errors that were on the form
//

            print '<div id="errors">';

            if ($errorMsg) {
                echo "<ol>\n";
                foreach ($errorMsg as $err) {
                    echo "<li>" . $err . "</li>\n";
                }
                echo "</ol>\n";
            }

            print '</div>';
            ?>
			
	 <form action="<? print $_SERVER['PHP_SELF']; ?>"
                  enctype="multipart/form-data"
                  method="post"
                  id="frmRegister">
                <fieldset class="contact">
                    <legend>Contact Information</legend>

                    <label class="required" for="txtEmail">Email </label>

                    <input id ="txtEmail" name="txtEmail" class="element text medium<?php if ($emailERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $email; ?>" placeholder="enter your preferred email address" onfocus="this.select()"  tabindex="30"/>
				
					<label class="required" for="txtfirstname">First Name </label>
					
                    <input id ="txtfirstname" name="txtfirstname" class="element text medium<?php if ($FnameERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $firstName; ?>" placeholder="enter your first name" onfocus="this.select()"  tabindex="35"/>
					
					<label class="required" for="txtlastname">Last Name </label>

                    <input id ="txtlastname" name="txtlastname" class="element text medium<?php if ($LnameERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $lastName; ?>" placeholder="enter your last name" onfocus="this.select()"  tabindex="40"/>
					
					
                </fieldset> 
				<br>
				<fieldset class="radio">
					<legend>What is your gender?</legend>
					
					<label><input type="radio" id="radGenderMale" name="radGender" value="Male" tabindex="50" 
						checked="checked" >Male</label>
            
					<label><input type="radio" id="radGenderFemale" name="radGender" value="Female" tabindex="55" 
						>Female</label>
						
				</fieldset>
			
				<br>
				
				<h1>Choose Item Here</h1>
				
				  
				<fieldset class="lists">	
					<legend>What piece of Art do you want?</legend>
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
				</fieldset>
				
				 <fieldset class="buttons">
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" tabindex="991" class="button">
                </fieldset>    
				
			</form>
		<?php
        } // end body submit
        if ($debug)
            print "<p>END OF PROCESSING</p>";
        ?>
    </section>


    <?
    include ("footer.php");
    ?>

</body>
</html>