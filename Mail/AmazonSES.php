<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2012 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Gregorio Hernandez Caso <gregoriohc@gmail.com>               |
// +----------------------------------------------------------------------+

/**
 * AmazonSES implementation of the PEAR Mail:: interface.
 * @access public
 * @package Mail
 * @version $Revision: 1.00 $
 */
class Mail_AmazonSES extends Mail {

    /**
     * Amazon SDK path.
     * @var array
     */
    var $amazon_sdk_path = '';

    /**
     * AmazonSES options.
     * @var array
     */
    var $ses_options = array();

    /**
     * Constructor.
     *
     * Instantiates a new Mail_AmazonSES:: object based on the parameters
     * passed in. 
     *
     * @param array $params Hash containing AmazonSES options.
     * @access public
     */
    function Mail_AmazonSES($params = array())
    {
        // Set Amazon AWS SDK path
        if (isset($params['sdk_path'])) {
            $this->amazon_sdk_path = $params['sdk_path'];
            unset($params['sdk_path']);
        }
        // Include required Amazon SDK files
        require_once($this->amazon_sdk_path.'sdk.class.php');
        require_once($this->amazon_sdk_path.'services/ses.class.php');

        // Set Amazon SES service params
        $this->ses_options = $params;
    }

    /**
     * Implements Mail::send() function using AmazonSES api.
     *
     * @param mixed $recipients Either a comma-seperated list of recipients
     *              (RFC822 compliant), or an array of recipients,
     *              each RFC822 valid. This may contain recipients not
     *              specified in the headers, for Bcc:, resending
     *              messages, etc.
     *
     * @param array $headers The array of headers to send with the mail, in an
     *              associative array, where the array key is the
     *              header name (ie, 'Subject'), and the array value
     *              is the header value (ie, 'test'). The header
     *              produced from those values would be 'Subject:
     *              test'.
     *
     * @param string $body The full text of the message body, including any
     *               Mime parts, etc.
     *
     * @return mixed Returns true on success, or a PEAR_Error
     *               containing a descriptive error message on
     *               failure.
     * @access public
     */
    function send($recipients, $headers, $body)
    {
        // Create Amazon SES service
        $ses = new AmazonSES($this->ses_options);

        // Check recipients
        $recipients = $this->parseRecipients($recipients);
        if (PEAR::isError($recipients)) {
            return $recipients;
        }

        // Send an email for each recipient
        foreach ($recipients as $recipient) {
            $headersRecipient = $headers;
            // Set email 'To' header
            if (!isset($headersRecipient['To'])) $headersRecipient['To'] = $recipient;

            // Prepare headers
            $headerElements = $this->prepareHeaders($headersRecipient);
            if (PEAR::isError($headerElements)) {
                return $headerElements;
            }
            // Get 'from' and headers in text mode
            list($from, $textHeaders) = $headerElements;

            // Send email
            $response = $ses->send_raw_email(array(
                'Data' => base64_encode($textHeaders . "\n" . $body)
            ), array(
                'Source' => $from,
                'Destinations' => $recipient
            ));

            // Check response
            if(!$response->isOK()) {
                return PEAR::raiseError('Error Sending via Amazon SES');
            }
        }

        return true;
    }

}
