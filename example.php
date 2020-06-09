<?php

require 'vendor/autoload.php';

use Onetoweb\Gls\Client;

$username = 'username';
$password = 'password';
$apiKey = 'api_key';
$testModus = true;

$client = new Client($username, $password, $apiKey, $testModus);

// validate login
$client->validateLogin();

// create label
$label = $client->createLabel([
    'labelType' => 'pdf',
    'trackingLinkType' => 'u',
    'notificationEmail' => [
        'sendMail' => true,
        'senderName' => 'sender name',
        'senderReplyAddress' => 'info@example.com',
        'senderContactName' => 'sender contact name',
        'emailSubject' => 'email subject',
        'emailAddressCC' => 'info@example.com',
    ],
    'addresses' => [
        'deliveryAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ]
    ],
    'reference' => 'test label',
    'units' => [
        [
            'unitId' => '1',
            'weight' => 10,
        ], [
            'unitId' => '2',
            'weight' => 20,
        ]
    ],
    'shiptype' => 'p',
]);

$unit = $label['units'][0];

// save label
$client->saveLabel('/path/to/file.pdf', $unit);

// confirm label
$result = $client->confirmLabel([
    'unitNo' => $unit['unitNo'],
    'shiptype' => 'p',
]);

//  delete label
$result = $client->deleteLabel([
    'unitNo' => $unit['unitNo'],
    'shiptype' => 'p'
]);

// get delivery options
$deliveryOptions = $client->getDeliveryOptions([
    'countryCode' => 'NL',
    'zipCode' => '8261CA'
]);

// get parcel shops
$parcelShops = $client->getParcelShops([
    'zipCode' => '8261CA',
    'amountOfShops' => 1
]);

// create pickup
$pickup = $client->createPickup([
    'trackingLinkType' => 'u',
    'pickupDate' => '2020-07-09',
    'addresses' => [
        'requesterAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ],
        'pickupAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ],
        'deliveryAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ]
    ],
    'reference' => 'test label',
    'units' => [
        [
            'unitId' => '1',
            'weight' => 10,
        ], [
            'unitId' => '2',
            'weight' => 20,
        ]
    ],
    'shiptype' => 'p',
]);

$pickupUnit = $pickup['units'][0];

// delete pickup
$result = $client->deletePickup([
    'unitNo' => $pickupUnit['unitNo'],
    'shiptype' => 'p'
]);

// create shop return
$shopReturn = $client->createShopReturn([
    'labelType' => 'pdf',
    'addresses' => [
        'requesterAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ],
        'pickupAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ],
        'deliveryAddress' => [
            'addresseeType' => 'b',
            'name1' => 'name',
            'street' => 'street',
            'houseNo' => '1',
            'houseNoExt' => 'A',
            'zipCode' => '1000AA',
            'city' => 'city',
            'countryCode' => 'NL',
            'contact' => 'contact',
            'phone' => '0123456789',
            'email' => 'info@example.com',
        ]
    ],
    'units' => [
        [
            'unitId' => '1',
            'weight' => 10,
        ], [
            'unitId' => '2',
            'weight' => 20,
        ]
    ],
]);

$shopReturnUnit = $shopReturn['units'][0];

// save shop return unit label
$client->saveLabel('/path/to/file.pdf', $shopReturnUnit);

// health probe
$client->healthProbe();