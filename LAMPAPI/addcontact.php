<?php
	$inData = getRequestInfo();
	
	$userID = $inData["UserID"];
	$FirstName = $inData["FirstName"];
	$LastName = $inData["LastName"];
	$Email = $inData["Email"];
	$Phone = $inData["Phone"];
	$Company = $inData["Company"];


	$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	
	if($conn->connect_error)
	{
		returnWithError( $conn->connect_error);
	}
	else
	{
		$sql = $conn->prepare("INSERT into Contacts(FirstName, LastName, Phone, Email,UserID,Company)
		VALUES(?,?,?,?,?,?)");
		$sql->bind_param("ssssss",$FirstName,$LastName,$Phone,$Email,$userID,$Company);
		$sql->execute();
		$sql->close();
		$conn->close();
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