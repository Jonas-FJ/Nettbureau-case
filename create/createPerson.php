<?php
include_once "logger.php";
function createPerson($data, $domain, $api_key, $org_id) {
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
    $email = [
        [
            'value' => $data['email'],
            'primary' => true,
            'label' => null
        ]
    ];
    $phone = [
        [
            'value' => $data['phone'],
            'primary' => true,
            'label' => null
        ]
    ];

    $contact_type = null;
    switch ($data['contact_type']) {
        case 'Private':
            $contact_type = 27;
            break;
        case 'Borettslag':
            $contact_type = 28;
            break;
        case 'Bedrift':
            $contact_type = 29;
            break;
        default:
        $contact_type = 27;
    };


    $new_person = [
        'name' => $data['name'],
        'owner_id' => null,
        'org_id' => $org_id,
        'add_time' => null,
        'emails' => $email,
        'phones' => $phone,
        'visible_to' => null,
        'label_ids' => null,
        'marketing_status' => null,
        'custom_fields' => [
            'c0b071d74d13386af76f5681194fd8cd793e6020' => $contact_type
        ]
    ];

    try {
        $result = $apiInstance->addPerson($new_person);
        if ($result->getSuccess() === true) {
            logMessage("Person added to drive \n$result\n");
            $person_id = $result->getData()->getId();
            $person_name = $result->getData()->getName();
            $return_array = [
                'person_id' => $person_id,
                'person_name' => $person_name
            ];

            return $return_array;
        }else {
            logMessage("Add person failed") ;
            return null;
        }
    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling PersonsApi->addPerson: ' . $e->getMessage() . PHP_EOL;
        echo $error_string;
        error_log(
            $error_string,
            3,
            'errorLog.log'
        );
    }
}
?>