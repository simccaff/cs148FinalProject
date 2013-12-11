<?php
/* the purpose of this page is to accept the hashed date joined and primary key  
 * as passed into this page in the GET format.
 * 
 * I retreive the date joined from the table for this person and verify that 
 * they are the same. After which i update the confirmed feild and acknowlege 
 * to the user they were successful. Then i send an email to the system admin 
 * to approve their membership 
 * 
 * Written By: Robert Erickson robert.erickson@uvm.edu
 * Last updated on: October 10, 2013
 * 
 * 
 */
require_once("connect.php");
include_once('mailMessage.php');
//#############################################################################
// 
// Initialize variables
//  
$debug = false;

$baseURL = "https://www.uvm.edu/~simccaff/";
$folderPath = "cs148/assignment4.1/";
// full URL of this form
$yourURL = $baseURL . $folderPath . "register.php";

$adminEmail = "simccaff@uvm.edu";


if ($debug) print "<p>DEBUG MODE IS ON</p>";


//##############################################################
// process request
//
if(isset($_GET["q"])){
    $key1 = htmlentities($_GET["q"], ENT_QUOTES, "UTF-8");
    $key2 = htmlentities($_GET["w"], ENT_QUOTES, "UTF-8");

    $confirmed = false;
    
    //##############################################################
    // get the membership record 
    
    $sql = "SELECT fldDateJoined, fldEmail FROM tblRegister WHERE pkRegisterId=" . $key2;
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $dateSubmitted = $result["fldRegisteredDate"];
    $email = $result["pkEmail"];
    
    $k1 =  sha1($dateSubmitted);
    
    //##############################################################
    // update confirmed
    if($key1==$k1){
        
        if ($debug) print "<h1>Confirmed</h1>";
        $sql = "UPDATE tblRegister set fldConfirmed=1 WHERE pkRegisterId=" . $key2;
        $confirmed = true;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute();
} else {
    print "Page is not avaialbe at this time, please contact us for help";
}

// start displaying output

include ("top.php");

$ext = pathinfo(basename($_SERVER['PHP_SELF']));
$file_name =  basename($_SERVER['PHP_SELF'],'.'.$ext['extension']);

print '<body id="' . $file_name . '">';

include ("header.php");
include ("nav.php");

?>

<section id="main">
<h1>Confirmed Registration</h1>

<?php
if($confirmed){
    $message =  "<p>Thank you for taking the time to confirm your registration. You are now confirmed!</p>";
}else{
    $message =  "<p>I am sorry but this project cannot be confirmed at this time. Please call (978)825-8541 for help in resolving this matter.</p>"; 
}

print $message;

//##############################################################
// prepare email to send to person

$subject="Maxine Davis Glass Art Order";
$mailed = sendMail($email, $subject, $message);
if ($debug) {
    print "<p>";
    if(!$mailed){
        print "NOT ";
    }
        print "mailed to person</p>";

}
?>
</section>


<? 
include ("footer.php"); 

//##############################################################
// prepare email to send to admin

if($confirmed){
    // send an email to the admin, the message of the email is like this message with a link to aprrove

        $message = '<h2>The following project has been confirmed:</h2>';

        $message = "<p>Click this link to approve this project: ";
        $message .= '<a href="' . $baseURL . 'approve.php?q=' . $key2 . '">Approve Registration</a></p>';
        $message .= "<p>or copy and paste this url into a web browser: ";
        $message .= $baseURL . 'approve.php?q=' . $key2 . "</p>";
        
        if ($debug) print "<p>" . $message;
        
        $subject="New Maxine Davis Glass Art Customer: Approve?";
        $mailed = sendMail($adminEmail, $subject, $message);
        if ($debug) {
            print "<p>";
            if(!$mailed){
                print "NOT ";
            }
                print "mailed to admin</p>";
            
        }
}

if ($debug) print "<p>END OF PROCESSING</p>";

?>

</body>
</html>