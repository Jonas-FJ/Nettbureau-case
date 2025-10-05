<?php
include_once "logger.php";

function duplicatPersonCheck($data, $domain, $api_key) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v2\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v2\Api\PersonsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v2/persons",
            'debug' => false,
            'verify' => false,
        ]),
        $config
    );

    $search_string = urlencode($data["name"]);

    $term = $search_string; // string
    $fields = 'name'; // string
    $exact_match = false; // bool
    $organization_id = null; // int
    $include_fields = null; // string
    $limit = 1; // int
    $cursor = null; // string

    try {
        logMessage("Searching for person ") ;
        $result = $apiInstance->searchPersons($term, $fields, $exact_match, $organization_id, $include_fields, $limit, $cursor);
        if ($result->getSuccess() === true) {
            logMessage( "Person search Success");
            $items = $result->getData()->getItems();

            if(count($items) === 0) {
                return null;
            }else {
                $person_id = $items[0]->getItem()->getId();
                $person_name = $items[0]->getItem()->getName();
                $return_array = [
                    'person_id' => $person_id,
                    'person_name' => $person_name
                ];

                return $return_array;
            }
        } else {
            logMessage( "search for person not success");
            return null;
        }

    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling PersonsApi->searchPersons: ' . $e->getMessage() . PHP_EOL . "\n";
        echo $error_string;
        error_log(
            $error_string,
            3,
            "errorLog.log",
        );
    }

}

?>