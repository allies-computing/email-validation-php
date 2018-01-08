<?php

    /*

    Email validation with PHP
    Simple demo which passes email address to the API on form submit and shows a message based on response.

    Including showing alternative suggestions for common typos such as gamil.com instead of gmail.com

    Full email validation API documentation:-
    https://developers.alliescomputing.com/postcoder-web-api/email-validation
    
    */

    if (array_key_exists("email", $_GET)) {

        var_dump(validate_email_address($_GET['email']));
        
    } else {
        
        echo "<p>Pass an email address using <code>?email=you@domain.com</code></p>";
        
    }

    function validate_email_address($email = "") {
        
        // Replace with your API key, test key will always return true regardless of email address
        $api_key = "PCW45-12345-12345-1234X";
        
        // Grab the input text and trim any whitespace
        $email = trim($email);
        
        // Create an empty output object
        $output = new StdClass();
        
        if ($email == "") {
            
            // Respond without calling API if no email address supplied
            $output->valid = false;
            $output->score = 0;
            $output->message = "No email supplied";
            
        } else {
            
            // Create the URL to API including API key and encoded email address
            $email_url = "https://ws.postcoder.com/pcw/" . $api_key . "/emailaddress/" . urlencode($email); 
            
            // use cURL to send the request and get the output
            $session = curl_init($email_url); 
            // Tell cURL to return the request data
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true); 
            // use application/json to specify json return values, the default is XML.
            $headers = array('Content-Type: application/json');
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);

            // Execute cURL on the session handle
            $response = curl_exec($session);
            
            $http_status_code = curl_getinfo($session, CURLINFO_HTTP_CODE);

            // Close the cURL session
            curl_close($session);
            
            if ($http_status_code != 200) {
                
                // Triggered if API does not return 200 HTTP code
                // More info - https://developers.alliescomputing.com/postcoder-web-api/error-handling
                
                // Here we will output a basic message with HTTP code
                $output->message = "An error occurred - " . $http_status_code;
                
            } else {
                
                // Convert JSON into an object
                $result = json_decode($response);

                // Check for alternative email address suggestion
                if(isset($result->alternative)) {

                    $output->valid = $result->valid;
                    $output->score = (int) $result->score;
                    $output->message = "Did you mean: " . $result->alternative;

                } else {

                    // Basic is valid check
                    if($result->valid === true) {

                        // Do something if valid, here we will output the response

                        $output->valid = $result->valid;
                        $output->score = (int) $result->score;
                        $output->message = $result->state;

                    } else {

                        // Do something if invalid, here we will output the response

                        $output->valid = $result->valid;
                        $output->score = (int) $result->score;
                        $output->message = $result->state;

                    }

                    // Note: If "score" is less 100, it may be a generic sales@ mailbox, disposable email address or a catch all server
                    // More info - https://www.alliescomputing.com/support/validating-email-addresses

                    // Full list of "state" responses - https://developers.alliescomputing.com/postcoder-web-api/email-validation
                }
                
            }
            
        }
            
        return $output;
        
    }

?>
