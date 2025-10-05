<?php
require_once 'logger.php';

function updateLead($data, $domain, $api_key, $lead_id) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v1\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v1\Api\LeadsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v1/leads",
            'debug' => false,
        ]),
        $config
    );

    $housing_type = null;
    $property_size = (int)$data['property_size'];
    $comment = null;
    $deal_type = null;

    switch ($data['housing_type']) {
        case 'Enebolig':
            $housing_type = '30';
            break;
        case 'Leilighet':
            $housing_type = '31';
            break;
        case 'Tomannsbolig':
            $housing_type = '32';
            break;
        case 'Rekkehus':
            $housing_type = '33';
            break;
        case 'Hytte':
            $housing_type = '34';
            break;
        case 'Annet':
            $housing_type = '35';
            break;
        default:
        $housing_type = '35';
    };

    switch ($data['deal_type']) {
        case 'Alle strømavtaler er aktuelle':
            $deal_type = '42';
            break;
        case 'Fastpris':
            $deal_type = '43';
            break;
        case 'Spotpris':
            $deal_type = '44';
            break;
        case 'Kraftforvaltning':
            $deal_type = '45';
            break;
        case 'Annen avtale/vet ikke':
            $deal_type = '46';
            break;
        default:
            $deal_type = '46';
    }

    $lead_array = array(
        '35c4e320a6dee7094535c0fe65fd9e748754a171' => $housing_type,
        '533158ca6c8a97cc1207b273d5802bd4a074f887' => $property_size,
        '1fe6a0769bd867d36c25892576862e9b423302f3' => $comment,
        '761dd27362225e433e1011b3bd4389a48ae4a412' => $deal_type
    );
   
    try {
        $result = $apiInstance->updateLead($lead_id, $lead_array);
        if($result->getSuccess() === true) {
            logMessage('Added custom fields');
            logMessage($result);
        }else {
            logMessage("Add custom fields failed") ;
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