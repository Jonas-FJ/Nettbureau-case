<?php
include_once "logger.php";

function updatePerson($domain, $api_key, $person_id, $org_id) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v2\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v2\Api\PersonsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v2/persons",
            'debug' => false,
        ]),
        $config
    );

    $person_array = array(
        'org_id' => $org_id
    );

    $person_body = new \Pipedrive\versions\v2\Model\PersonRequestBody($person_array);

    try {
        $result = $apiInstance->updatePerson($person_id, $person_body);
        logMessage("New org added to person: $result");
    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling PersonsApi->updatePerson: ' . $e->getMessage() . PHP_EOL;
        echo $error_string;
        error_log(
            $error_string,
            3,
            'errorLog.log'
        );
    }
}

?>