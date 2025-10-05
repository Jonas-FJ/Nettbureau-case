<?php
include_once "logger.php";

function duplicateLeadCheck($title, $domain, $api_key) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v2\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v2\Api\LeadsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v2/organizations",
            'debug' => false,
            'verify' => false,
        ]),
        $config
    );

    $search_string = urlencode($title);

    $term = $search_string; // string | The search term to look for. Minimum 2 characters (or 1 if using `exact_match`). Please note that the search term has to be URL encoded.
    $fields = 'title'; // string | A comma-separated string array. The fields to perform the search from. Defaults to all of them.
    $exact_match = false; // bool | When enabled, only full exact matches against the given term are returned. It is <b>not</b> case sensitive.
    $person_id = null; // int | Will filter leads by the provided person ID. The upper limit of found leads associated with the person is 2000.
    $organization_id = null; // int | Will filter leads by the provided organization ID. The upper limit of found leads associated with the organization is 2000.
    $include_fields = null; // string | Supports including optional fields in the results which are not provided by default
    $limit = 100; // int | For pagination, the limit of entries to be returned. If not provided, 100 items will be returned. Please note that a maximum value of 500 is allowed.
    $cursor = null; // string | For pagination, the marker (an opaque string value) representing the first item on the next page

    try {
        logMessage("Searching for leads");
        $result = $apiInstance->searchLeads($term, $fields, $exact_match, $person_id, $organization_id, $include_fields, $limit, $cursor);
        if ($result->getSuccess() === true) {
            logMessage("Leads search success");
            $items = $result->getData()->getItems();

            if(count($items) === 0) {
                return null;
            }else {
                $lead_id = $items[0]->getItem()->getId();
                $lead_title = $items[0]->getItem()->getTitle();
                $lead = [
                    'lead_id' => $lead_id,
                    'lead_title' => $lead_title
                ];
                
                return $lead;
            }
        } else {
            logMessage("Leads search failed") ;
            return null;
        }
    } catch (Exception $e) {
        $error_string = date("d.m.Y H:i:s : ") . 'Exception when calling OrganizationsApi->searchOrganization: ' . $e->getMessage() . PHP_EOL . "\n";
        echo $error_string;
        error_log(
            $error_string,
            3,
            "errorLog.log",
        );
    }
}

?>