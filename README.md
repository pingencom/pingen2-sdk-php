# Requirements

You need to have an account in pingen v2 and obtain oauth credentials for your desired grant type (usually client_credentials).

How to obtain these are described here: https://api.pingen.com/documentation#section/Authentication/How-to-obtain-a-Client-ID

# Installation

Require the package via composer (Get composer here: https://getcomposer.org/download/)

`composer require pingencom/pingen2-sdk-php`

# Environments

We have two Environments available: Production and Staging (see https://api.pingen.com/documentation#section/Basics/Environments)

This SDK supports staging as well. **When initiating the provider** (see Usage), the optional 'staging' attribute should be set, **as well as when creating an endpoint object**.

# Usage

The simplest way to integrate is using the client credentials grant (see https://api.pingen.com/documentation#section/Authentication/Which-grant-type-should-i-use)

```php
require __DIR__ . '/vendor/autoload.php';

$provider = new \Pingen\Provider\Pingen(
    array(
        'clientId' => 'YOUR_OAUTH2_CLIENT_ID',
        'clientSecret' => 'YOUR_OAUTH2_CLIENT_SECRET',
        'staging' => true,
    )
);

$access_token = $provider->getAccessToken('client_credentials');

$lettersEndpoint = (new \Pingen\Endpoints\LettersEndpoint($access_token))
    ->setOrganisationId('INSERT_YOUR_ORGANISATION_UUID_HERE')
    ->useStaging();

$lettersEndpoint->uploadAndCreate(
    (new \Pingen\Endpoints\DataTransferObjects\Letter\LetterCreateAttributes())
        ->setFileOriginalName('your_original_pdf_name.pdf')
        ->setAddressPosition('left')
        ->setAutoSend(false),
    fopen('path_to_your_original_pdf_name.pdf', 'r')
);
```

# Batches

A batch is created for a channel (`post`, `ebill` or `email`) and sending it takes the attributes of that channel:

```php
use Pingen\Endpoints\DataTransferObjects\Batch\BatchCreateAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEbillSendAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchEmailSendAttributes;
use Pingen\Endpoints\DataTransferObjects\Batch\BatchPostSendAttributes;

$batchesEndpoint = (new \Pingen\Endpoints\BatchesEndpoint($access_token))
    ->setOrganisationId('INSERT_YOUR_ORGANISATION_UUID_HERE')
    ->useStaging();

$batch = $batchesEndpoint->uploadAndCreate(
    (new BatchCreateAttributes())
        ->setName('My campaign')                         // 5 - 100 characters
        ->setIcon('rocket')
        ->setChannelType('post')
        ->setFileOriginalName('your_original_pdf_name.pdf')
        ->setAddressPosition('left')
        ->setGroupingType('merge'),
    fopen('path_to_your_original_pdf_name.pdf', 'r')
);

// post
$batchesEndpoint->send($batch->data->id, (new BatchPostSendAttributes())
    ->setDeliveryProduct('cheap')
    ->setPrintMode('simplex')
    ->setPrintSpectrum('color')
);

// email / ebill, the delivery product is fixed
$batchesEndpoint->send($batch->data->id, new BatchEmailSendAttributes());
$batchesEndpoint->send($batch->data->id, new BatchEbillSendAttributes());
```

The allowed values are constants on the attribute objects themselves: `BatchCreateAttributes::ICONS`, `BatchCreateAttributes::CHANNEL_TYPES`, `BatchPostSendAttributes::DELIVERY_PRODUCTS`, `::PRINT_MODES` and `::PRINT_SPECTRUMS`. They are checked in `validate()` before the request goes out.

# Examples & Docs

Our API Docs are here: https://api.pingen.com/documentation

On the right-hand side of every endpoint you can see request samples for PHP and other languages, which you can copy and paste into your application.

# Bugreport & Contribution

If you find a bug, please either create a ticket in github, or initiate a pull request.

# Versioning

We adhere to semantic (major.minor.patch) versioning (https://semver.org/). This means that:
* Patch (x.x.patch) versions fix bugs
* Minor (x.minor.x) versions introduce new, backwards compatible features or improve existing code.
* Major (major.x.x) versions introduce radical changes which are not backwards compatible.

In your automation or procedure you can always safely update patch & minor versions without the risk of your application failing.

# Testing

PHPUnit: `vendor/bin/phpunit`

Integration tests run against the real staging api and are therefore kept in their own suite: `vendor/bin/phpunit --testsuite integration`

They need staging credentials - copy `.env.example` to `.env` and fill it in (or export the same variables, they take precedence). Without credentials the whole suite is skipped.

ECS: `vendor/bin/ecs check src`

PHPStan: `vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=512M`

Lint: `vendor/bin/parallel-lint --exclude vendor .`