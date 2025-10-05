<?php
require_once 'logger.php';

function updateLeadPerson($domain, $api_key, $lead_id, $person_id) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v1\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v1\Api\LeadsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v1/leads",
            'debug' => false,
        ]),
        $config
    );

    $lead_array = array(
        'person_id' => $person_id
    );
   
    try {
        $result = $apiInstance->updateLead($lead_id, $lead_array);
        if($result->getSuccess() === true) {
            logMessage('Added person to lead');
            logMessage($result);
        }else {
            logMessage("Add person failed") ;
        }
    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling LeadsApi->updateLead: ' . $e->getMessage() . PHP_EOL;
        echo $error_string;
        error_log(
            $error_string,
            3,
            'errorLog.log'
        );
    }
}

?>