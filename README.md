About
=====

AmazonSES implementation of the PEAR Mail:: interface

Installation
============

Install the AWS SDK for PHP from http://aws.amazon.com/sdkforphp/

The easiest way is to use their PEAR channel
http://pear.amazonwebservices.com/

`# pear channel-discover pear.amazonwebservices.com`

`# pear install aws/sdk`

Usage
=====

    <?php
    require_once('Mail.php');
    require_once('Mail/AmazonSES.php');

    $recipients = 'joe@example.com';

    $headers = array(
      'From'    => 'richard@example.com',
      'Subject' => 'Test message',
    );

    $body = 'Test message';

    $params = array(
      'sdk_path' => 'path/to/amazon/sdk/',
      'key'      => 'amazon_key_id',
      'secret'   => 'amazon_key_secret'
    );

    // Create the mail object using the Mail::factory method
    $mail_object =& Mail::factory('AmazonSES', $params);

    $mail_object->send($recipients, $headers, $body);
    ?>

Compatibility
=============

Compatible with [AWS SDK for PHP 1.5 "Allegro"] [0] and newer.

License
=======

Copyright (c) 2012 The PHP Group
Author: Gregorio Hernandez Caso

This source file is subject to version 2.02 of the PHP license,
that is bundled with this package in the file LICENSE, and is
available at through the world-wide-web at
<http://www.php.net/license/2_02.txt>
If you did not receive a copy of the PHP license and are unable to
obtain it through the world-wide-web, please send a note to
license@php.net so we can mail you a copy immediately.

[0]: http://aws.amazon.com/releasenotes/PHP/3719565440874916 "Release Notes: AWS SDK for PHP 1.5 'Allegro'"