<?php
	$inData = getRequestInfo();
	
	$userID = $inData["UserID"];
	$FirstName = $inData["FirstName"];
	$LastName = $inData["LastName"];
	$Email = $inData["Email"];
	$Phone = $inData["Phone"];
	$contactID = $inData["ID"];

	$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	
	
	if($conn->connect_error)
	{
		returnWithError( $conn->connect_error);
	}
	else
	{
		
		$sql1 = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Phone = ?, Email = ? WHERE UserID = ? and ID = ?");
		$sql1->bind_param("ssssss",$FirstName,$LastName,$Phone,$Email,$userID,$contactID);
		$sql1->execute();

		returnWithError("");
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'),true);
	}
	
	function sendResultInfoAsJson($obj)
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"'. $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>