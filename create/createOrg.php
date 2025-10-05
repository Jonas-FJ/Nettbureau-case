<?php
include_once "logger.php";

function createOrg($data, $domain, $api_key) {
    require_once(__DIR__ . '/../vendor/autoload.php');
    
    $config = (new Pipedrive\versions\v2\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v2\Api\OrganizationsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v2/organizations",
            'debug' => false,
            'verify' => false,
        ]),
        $config
    );

    $org_data = [
        "name" => $data['name'],
        "owner_id" => null,
        "add_time" => null,
        "visible_to" => null,
        "label_ids" => null,
        "address" => null,
        "custom_fields" => null
    ];

    try {
        $result = $apiInstance->addOrganization($org_data);
        if($result->getSuccess() === true) {
            logMessage("Org added to drive \n$result \n");
            $org_id = $result->getData()->getId();
            $org_name = $result->getData()->getName();
            $return_array = [
                'org_id' => $org_id,
                'org_name' => $org_name
            ];
            return $return_array;
        } else {
            logMessage("Org add to drive failed \n");
            return null;
        }
    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling OrganizationsApi->addOrganization: ' . $e->getMessage() . PHP_EOL . "\n";
        echo $error_string;
        error_log(
            $error_string,
            3,
            'errorLog.log'
        );
    }
}

?>