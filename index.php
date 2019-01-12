<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	$text = $json->queryResult->parameters->salonlar;
  
	switch ($text) {
		case 'viaport':
			$speech = "viaport'taki sinemada bugün oynayan filmler þunlar:";
			break;

		case 'bye':
			$speech = "Bye, good night";
			break;

		case 'anything':
			$speech = "Yes, you can type anything here.";
			break;
		
		default:
			$speech = "Sorry, I didnt get that. Please ask me something else.";
			break;
	}

	$response = new \stdClass();
	$response->fulfillmentText = $speech;
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}







?>