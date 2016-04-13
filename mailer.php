<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    	$name = strip_tags(trim($_POST["name"]));
		$name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["message"]);
        $captcha = trim($_POST["captcha"]);

        // Check that data was sent to the mailer.
        if ( empty($name) OR empty($message) OR empty($captcha) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Oops! There was a problem with your submission. Please complete the form and try again.";
            exit;
        }

		$referer = $_SERVER['HTTP_REFERER'];
		$clientip = $_SERVER['REMOTE_ADDR'];
		$useragent = $_SERVER['HTTP_USER_AGENT'];

		// Check recaptcha
		$opts = array(
   			'http' => array(
        			'method' => "POST",
        			'header' => 'Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n',
					'content' => http_build_query(array(
            			"secret" => "6Lc6yQMTAAAAAGgyA2un8bgee4_fRb7HnMRlee55",
            			"response" => $captcha,
						"remoteip" => $clientip
       			 	))
    			 )
		);
		$fp = fopen('https://www.google.com/recaptcha/api/siteverify', 'r', false, stream_context_create($opts));
		fpassthru($fp);
		fclose($fp);
stream_set_blocking($fp, true);
		$captchareply=unserialize(stream_get_contents($fp));

echo $captchareply["success"];

        // Check that data was sent to the mailer.
        if ( !$fp.success) {
            http_response_code(401);
            echo "Oops! There was a problem with your ReCAPTCHA validation. Please complete the form and try again.";
            exit;
        }

        // Set the recipient email address.
        $recipient = "pedro.boado@trenddevs.co.uk";

        // Set the email subject.
        $subject = "New contact from $name";

        // Build the email content.
        $email_content = "From: $name <$email>\n";
        $email_content .= "--------------------------------------------------------------\n\n";
        $email_content .= "Message:\n$message\n\n\n\n";
        $email_content .= "--------------------------------------------------------------\n\n";
        $email_content .= "Referer: $referer\n";
        $email_content .= "UserAgent: $useragent\n\n";
        $email_content .= var_export(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientip)),  true);

        // Build the email headers.
        $email_headers = "From: Contact Form  <admin@trenddevs.co.uk>";

        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong and we couldn't send your message.";
        }

    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

?>
