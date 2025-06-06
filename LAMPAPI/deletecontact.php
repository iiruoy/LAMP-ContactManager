<?php
	$inData = getRequestInfo();
	
	$userID = $inData["UserID"];
	$contactID = $inData["ID"];


	$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	
	
	if($conn->connect_error)
	{
		returnWithError( $conn->connect_error);
	}
	else
	{
		$sql = $conn->prepare("DELETE FROM Contacts WHERE UserID = ? and ID = ?");
		$sql->bind_param("ss",$userID,$contactID);
		$sql->execute();
		$sql->close();
		$conn->close();
		returnWithError("");
	}
	
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'),true);
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"'. $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
?>