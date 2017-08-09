<?php
	// Imports
	require_once("pure360/PaintSystemException.php");
	require_once("pure360/PaintSecurityException.php");
	require_once("pure360/PaintValidationException.php");
	require_once("pure360/PaintMethods.php");

	// Receive data posted from the form
	$processInd				= (!empty($_REQUEST["processInd"])? $_REQUEST["processInd"]: "N");
	$messageName			= (!empty($_REQUEST["messageName"])? $_REQUEST["messageName"]: null);		
	$listName				= (!empty($_REQUEST["listName"])? $_REQUEST["listName"]: null);		
	$deliveryStartFromDate	= (!empty($_REQUEST["deliveryStartFromDate"])? $_REQUEST["deliveryStartFromDate"]: null);		
	$deliveryStatuses		= (!empty($_REQUEST["deliveryStatuses"])? $_REQUEST["deliveryStatuses"]: null);		

	$output			= "";
	$searchOutput	= "";
	$eventData		= "";
	
	// Send the request to process
	if($processInd=="Y")
	{		
        try
        {
	      	$output			= "No deliveries found<BR/><BR/>";
        	
            // ***** Log in and create a context *****
            $paint = new PaintMethods();
            $paint->login();

            // ***** Retrieve the event data *****
            $searchResult = $paint->searchDeliveries($messageName, $listName, $deliveryStartFromDate, $deliveryStatuses);

			// Output the meta data as a readable string
			foreach($searchResult as $searchResultItem)
			{
				$searchOutput.= print_r($searchResultItem,true)."\n\n";

	            // Output to help the user see what's going on.
	            $output = "Matching delivery(s) found (see below)<BR/><BR/>";     
 			}
						
           
        }
        catch (PaintValidationException $pve)
        {
            $output = "Validation Error<BR/><BR/>".
                                    $paint->convertResultToDebugString($pve->getErrors())."<BR/><BR/>";
        }
        catch (PaintSecurityException $psece)
        {
            $output = "Security Exception<BR/><BR/>".$psece->getMessage()."<BR/><BR/>";
        }
        catch (PaintSystemException $pse)
        {
            $output = "System Exception<BR/><BR/>".$pse->getMessage()."<BR/><BR/>";
        }
        catch (Exception $exp)
        {
            $output = "Unhandled Exception<BR/><BR/>".$exp->getMessage()."<BR/><BR/>";
        }

        // Log out of the session.  This should be placed so that
        // it will always occur even if there is an exception
        try
        {
            $paint->logout();
        }
        catch (Exception $exp)
        {
        	// Ignore
        }		
		
	} else
	{
       	// Ignore
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>))) Pure: PAINT Example Implementation</title>    
    <link rel="stylesheet" type="text/css" href="paint.css" />    
</head>
<body>
    <form action="" method="post">
	    <input type="hidden" name="processInd" value="Y" />
	    <div>
	        <a href="index.htm"><b>home</b></a><br />
	        <br />
			Search deliveries with a message name parameter (optional)
	        <br />
	        <font color="red"><?php echo $output; ?></font>
			Message name (optional):<br />
	        <input name="messageName" value="<?php echo $messageName; ?>" size="50"/><br />
	        <br />
	        <br />

	Search deliveries with a list name parameter (optional)
    <br />
    <font color="red"><?php echo $output; ?></font>
	List name (optional):<br />
    <input name="listName" value="<?php echo $listName; ?>" size="50"/><br />
    <br />
    <br />
	Search deliveries with a start date parameter (optional)
    <br />
    <font color="red"><?php echo $output; ?></font>
	Start date (dd/mm/yyyy) (optional):<br />
    <input name="deliveryStartFromDate" value="<?php echo $deliveryStartFromDate; ?>" size="50"/><br />
    <br />
    <br />
	Search deliveries with a status parameter (optional, e.g. COMPLETED)
    <br />
    <font color="red"><?php echo $output; ?></font>
	Status (optional):<br />
    <input name="deliveryStatuses" value="<?php echo $deliveryStatuses; ?>" size="50"/><br />
    <br />
    <br />

	        Result:<br />
	        <em>(the returned deliveries will be displayed below)</em><br />
	        <br/>
	        <b>Deliveries returned:</b>
	        <br/>
	        <br/>
	        <?php echo $searchOutput;?>
			<br/>
			<br/>
	        <input type="submit" value="Search deliveries" /></div>
	    </div>
    </form>
</body>
</html>
