<?php
include_once "logger.php";

function duplicateOrgCheck($data, $domain, $api_key) {
    require_once(__DIR__ . '/../vendor/autoload.php');

    $config = (new Pipedrive\versions\v2\Configuration())->setApiKey('x-api-token', $api_key);

    $apiInstance = new Pipedrive\versions\v2\Api\OrganizationsApi(
        new GuzzleHttp\Client([
            'base_uri' => "https://$domain.pipedrive.com/api/v2/organizations",
            'debug' => false,
        ]),
        $config
    );

    $search_string = urlencode($data["name"]);

    $term = $search_string; // string | The search term to look for. Minimum 2 characters (or 1 if using `exact_match`). Please note that the search term has to be URL encoded.
    $fields = 'name'; // string | A comma-separated string array. The fields to perform the search from. Defaults to all of them. Only the following custom field types are searchable: `address`, `varchar`, `text`, `varchar_auto`, `double`, `monetary` and `phone`. Read more about searching by custom fields <a href=\"https://support.pipedrive.com/en/article/search-finding-what-you-need#searching-by-custom-fields\" target=\"_blank\" rel=\"noopener noreferrer\">here</a>.
    $exact_match = false; // bool | When enabled, only full exact matches against the given term are returned. It is <b>not</b> case sensitive.
    $limit = 2; // int | For pagination, the limit of entries to be returned. If not provided, 100 items will be returned. Please note that a maximum value of 500 is allowed.
    $cursor = null; // string | For pagination, the marker (an opaque string value) representing the first item on the next page

    try {
        logMessage("Searching for org");
        $org_id = null;
        $result = $apiInstance->searchOrganization($term, $fields, $exact_match, $limit, $cursor);
        if ($result->getSuccess() === true) {
            logMessage("Org search success");
            $items = $result->getData()->getItems();

            if(count($items) === 0) {
                return null;
            }else {
                $org_id = $items[0]->getItem()->getId();
                $org_name = $items[0]->getItem()->getName();

                $return_arrya = [
                    'org_id' => $org_id,
                    'org_name' => $org_name
                ];
                return $return_arrya;
            }
        } else {
            logMessage("Org search failed") ;
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