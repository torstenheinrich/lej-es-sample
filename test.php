<?php

declare(strict_types=1);

$loader = require __DIR__ . '/vendor/autoload.php';

use LejSample\Tests\Subscription\Domain\Model\AccountIntegrationTest;
use MongoDB\Client;

$client = new Client();
$database = 'lej-es-sample';

$test = new AccountIntegrationTest($client, $database);
$test->execute();
