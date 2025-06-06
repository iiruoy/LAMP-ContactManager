<?php

	$inData = getRequestInfo();
	
	$firstName = $inData["FirstName"];
	$lastName = $inData["LastName"];
	$Email = $inData["Email"];
	$Phone = $inData["Phone"];
	$Company = $inData["Company"];
	$login = $inData["login"];
	$password = $inData["password"];
	
	
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	
	else
	{
		$stmt = $conn->prepare("INSERT into Users (FirstName, LastName, Login, Password,Email,Phone,Company) VALUES (?,?,?,?,?,?,?)");
		$stmt->bind_param("sssssss",$firstName,$lastName,$login,$password,$Email,$Phone,$Company);
		$stmt->execute();
	
		$stmt->close();
		$conn->close();
		returnWithInfo('"registered"');
	
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"'. $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'),true);
	}
	
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithInfo($message)
	{
		$retValue = '{"message": ' . $message . ',"error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>