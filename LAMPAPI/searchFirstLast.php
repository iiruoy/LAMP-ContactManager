<?php

	$inData = getRequestInfo();

	$searchResults = "";
	$searchCount = 0;
	
	$userID = $inData["UserID"];
	$searchTerm = "%" .$inData["search"] . "%";

	$conn = mysqli_connect("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sql = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email, Company FROM Contacts WHERE (FirstName LIKE ? OR LastName LIKE ?) AND UserID = ?");
		$sql->bind_param("ssi", $searchTerm, $searchTerm, $userID);
		$sql->execute();
		$result = $sql->get_result();
		
		while($row = $result->fetch_assoc())
		{
			if( $searchCount > 0 )
			{
				$searchResults .= ",";
			}
			$searchCount++;
			$searchResults .= '{"ID" : "' . $row["ID"] . '", "FirstName" : "' . $row["FirstName"] . '", "LastName" : "' . $row["LastName"] . '", "Email" : "' . $row["Email"] . '", "Phone" : "' . $row["Phone"] . '", "Company" : "' . $row["Company"] . '"}';
		}
		
		if( $searchCount == 0 )
		{
			returnWithError( "No Records Found" );
		}
		else
		{
			returnWithInfo( $searchResults );
		}
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
	
		function returnWithInfo( $searchResults )
	{
		$retValue = '{"results":[' . $searchResults . '],"error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>