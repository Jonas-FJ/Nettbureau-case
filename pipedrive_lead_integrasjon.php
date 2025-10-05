<?php
include "duplicateCheck\duplicatePersonCheck.php";
include "duplicateCheck\duplicateOrgCheck.php";
include "duplicateCheck\duplicateLeadCheck.php";
include "create\createOrg.php";
include "create\createPerson.php";
include "create\createLead.php";
include "update\updatePerson.php";
include "update\updateLeadPerson.php";
include "update\updateLeadOrg.php";
include_once "logger.php";

//checks arguments from command prompt
$lead_data = null;
$all_new_test = false;
$with_org_test = false;
$with_person_test = false;
$all_pre_test = false;

if ($argc === 3) {
    switch ($argv[1]) {
        case 'a':
            $all_new_test = true;
            break;
        case 'b':
            $with_org_test = true;
            break;
        case 'c':
            $with_person_test = true;
            break;
        case 'd':
            $all_pre_test = true;
            break;
        default:
            echo "Failed to detect test type.\n" . 
            "a: for running code that test with all new data. \n" . 
            "b: for running code that test with existing org but not person or lead\n" . 
            "c: for running code that test with existing person but not org or lead\n" . 
            "d: for running code that test when all date is preexisting\n";
            exit;
    }


    $file_path = $argv[2];
    if(file_exists($file_path)) {
        $lead_data = include $file_path;
    } else {
        echo 'no file detected from provided file path';
        exit;
    }

} else {
    echo "Failed to detect all the arguments when running code. First argument needs to be:\n" .
    "a: for running code that test with all new data. \n" . 
    "b: for running code that test with existing org but not person or lead\n" . 
    "c: for running code that test with existing person but not org or lead\n" . 
    "d: for running code that test when all date is preexisting\n" . 
    "The last argument need to be path to the test data file\n";
    exit;
}

//Integration code starts here:
$domain = 'nettbureaucase';
$api_key = '24eaceaa89c83e18fd4aadd3dbab7a3b01ddffc8';

function intigrateLead($data, $domain, $api_key) {
    logMessage("\nIntigrating new lead... \n");
    $org = duplicateOrgCheck($data, $domain, $api_key);
    $person = duplicatPersonCheck($data, $domain, $api_key);
    $no_pre_org = false;
    $no_pre_person = false;

    //Checks if an preexisting org was found. If no org was found creats a new org
    if($org === null) {
        logMessage("No preexisting organization found based on name. Creating new org");
        $org = createOrg($data, $domain, $api_key);
        $no_pre_org = true;
        if($org != null) {
            logMessage('New org created wtih ID: ' . $org['org_id'] . ' and name: ' . $org['org_name']);
        }else {
            logMessage("Error org not ceated");
        }
    } else {
        logMessage( "Org aleardy exist id: " . $org['org_id'] . ' and name: ' . $org['org_name']);
        $no_pre_org = false;
    }

    //Checks if an preexisting person was found or not. Creats new person if not and attaches org to it. 
    //If preexisting person was found cheks if an new org was created and attaches the new org to the person.
    if($person === null) {
        logMessage("No preexisting person found. Creating new person");
        $person = createPerson($data, $domain, $api_key, $org['org_id']);
        if($person != null) {
            logMessage('New person created with id: ' . $person['person_id'] . ' and name: ' . $person['person_name']);
            $no_pre_person = true;
        } else {
            logMessage("Error person not ceated");
        }
    } else {
        logMessage( "Person already exists with id: " . $person['person_id'] . ' and name: ' . $person['person_name']);
        $no_pre_person = false;
        if($no_pre_org === true) {
            updatePerson($domain, $api_key, $person['person_id'], $org['org_id']);
        }
    }

    $lead = duplicateLeadCheck($data['name'], $domain, $api_key);

    //checks if lead exists by checking lead title with the org and persons name
    //if a lead already exists check if a person and/or org was created and attaches it to the lead
    if($lead === null) {
        logMessage("No preexisting lead found. Creating new lead");
        $lead = createLead($data, $domain, $api_key, $org['org_id'], $person['person_id']);
        if ($lead === null) {
            logMessage("Creating new lead failed");
        } else {
            logMessage("New lead created with id: " . $lead['lead_id'] . "and title: " . $lead['lead_title'] );
        }
    }else {
        logMessage('Lead with same name already exists, no new lead created');
        if($no_pre_org === true) {
            updateLeadOrg($domain, $api_key, $lead['lead_id'], $org['org_id']);
        }
        if($no_pre_person === true) {
            updateLeadPerson($domain, $api_key, $lead['lead_id'], $person['person_id']);
        }
    }
}

if($all_new_test === true) {
    logMessage("Running all new data test:");
    intigrateLead($lead_data, $domain, $api_key);
    exit;
}
if($with_org_test === true) {
    logMessage("Runnin pre existing org test:");
    createOrg($lead_data, $domain, $api_key);
    logMessage("Updating database...");
    sleep(1);

    intigrateLead($lead_data, $domain, $api_key);
    exit;
}
if($with_person_test === true) {
    logMessage("Running pre existing person test:");
    createPerson($lead_data, $domain, $api_key, 105);
    logMessage("Updating database...");
    sleep(1);
    intigrateLead($lead_data, $domain, $api_key);
    exit;
}
if($all_pre_test === true) {
    logMessage("Running all date preexists test:");
    intigrateLead($lead_data, $domain, $api_key);
    exit;
}

?>