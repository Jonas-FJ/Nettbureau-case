<?php

include_once "logger.php";
include "update\updateLead.php";

function createLead($data, $domain, $api_key, $org_id, $person_id) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v1\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v1\Api\LeadsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v2/leads",
            'debug' => false,
        ]),
        $config
    );

    $new_lead = array(
        'title' => $data["deal_type"] . 'deal for: ' . $data['name'],
        'person_id' => $person_id,
        'organization_id' => $org_id
    );

    $lead_body = new \Pipedrive\versions\v1\Model\AddLeadRequest($new_lead);

    try {
        $result = $apiInstance->addLead($lead_body);
        if ($result->getSuccess() === true) {
            $title = $result->getData()->getTitle();
            $id = $result->getData()->getId();
            logMessage("Lead created with title: $title, and id: $id");
            updateLead($data, $domain, $api_key, $id);
            $return_array = [
                'lead_id' => $id,
                'lead_title' => $title
            ];
            return $return_array;
        }else {
            logMessage("Add lead failed") ;
            return null;
        }
    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling LeadsApi->addLead: ' . $e->getMessage() . PHP_EOL;
        echo $error_string;
        error_log(
            $error_string,
            3,
            'errorLog.log'
        );
    }
}
?>
