#!/usr/bin/php

<?php

/************************************************************************************
*
* Copyright (c) 2012, Alexander Todorov <atodorov()otb.bg>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
************************************************************************************/

$NAME = "monupco-dotcloud-php";
$VERSION = "0.1";

/****
 Dependencies:

 HTTP_Request2
 PEAR
 pecl/json

****/

function startsWith($haystack,$needle) {
   return strpos($haystack, $needle, 0) === 0;
}


$senv = file_get_contents('/home/dotcloud/environment.json');
$env = json_decode($senv);

$data = array(
    'user_id'    => intval($env->MONUPCO_USER_ID),
    'app_name'   => $env->DOTCLOUD_PROJECT.'.'.$env->DOTCLOUD_SERVICE_NAME,
    'app_uuid'   => $env->DOTCLOUD_WWW_HTTP_HOST,
    'app_type'   => 'PHP',
    'app_url'    => $env->DOTCLOUD_WWW_HTTP_URL,
    'app_vendor' => 1,   // dotCloud
    'pkg_type'   => 500, // PHP PEAR
    'installed'  => array(),
);


set_include_path(get_include_path() . PATH_SEPARATOR . "/home/dotcloud/php-env/share/php");

require_once 'PEAR/Registry.php';
require_once 'HTTP/Request2.php';


$registry = new PEAR_Registry("/home/dotcloud/php-env/share/php");
foreach ($registry->packageInfo(null, null) as $package) {
    $data['installed'][] = array('n' => $package['name'], 'v' => $package['version']['release']);
}

// Add self as installed so that user is able to see when new version is available
// this is of type 2000 - package released on GitHub which has tags
$data['installed'][] = array('n' => 'monupco/'.$NAME, 'v' => $VERSION, 't' => 2000);

$json_data = json_encode($data);

$request = new HTTP_Request2('https://monupco-otb.rhcloud.com/application/register/');
$request->setMethod(HTTP_Request2::METHOD_POST);
$request->setHeader('User-agent', sprintf('%s/%s', $NAME, $VERSION));
$request->addPostParameter('json_data', $json_data);
// $request->setConfig('ssl_verify_peer', false);  // another bug in OpenShift

$response = $request->send();

if (($response->getStatus() != 200) || (! startsWith($response->getHeader('Content-type'), 'application/json'))) {
    throw new Exception(sprintf('Communication failed - %s', $response->getBody()));
}

$result = json_decode($response->getBody());
printf("%s\n", $result->message);
exit($result->exit_code);

?>
