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

The suite is split in two in `phpunit.xml`:

* **default** – fast, offline unit tests (http is mocked). This is what `vendor/bin/phpunit` and CI run, and it needs no credentials.
* **integration** – talks to the **real Pingen staging api**. Lives in `tests/Integration`, is excluded from the default suite and every test is tagged `#[Group('integration')]`.

## Unit tests (default suite)

```
vendor/bin/phpunit
```

## Integration tests

These create, read and cancel **real** resources on staging (letters, batches, emails, ebills, webhooks) and include short waits while the api settles, so they are noticeably slower than the unit suite.

Credentials come from a `.env` file in the repository root (copy `.env.example` and fill it in) or from real environment variables, which take precedence so CI can inject secrets without writing a file:

| Variable | Required | Purpose |
| --- | --- | --- |
| `PINGEN2_CLIENT_ID` | yes | OAuth client id (`client_credentials` grant) |
| `PINGEN2_CLIENT_SECRET` | yes | OAuth client secret |
| `PINGEN2_ORGANIZATION_ID` | no | Organisation to use; when empty the account's first organisation is picked |
| `PINGEN2_ORGANIZATION_NAME` | no | When set, the organisation test asserts on this name |
| `PINGEN2_USE_STAGING` | no | Defaults to `true`; integration tests must never run against production |

Without `PINGEN2_CLIENT_ID` / `PINGEN2_CLIENT_SECRET` the whole integration suite is **skipped** (not failed).

```
# whole integration suite
vendor/bin/phpunit --testsuite integration

# a single integration test file
vendor/bin/phpunit tests/Integration/LettersIntegrationTest.php

# a single test method
vendor/bin/phpunit --filter testCreateLetter
```

Coverage is collected on every run and needs a driver (`pcov`, baked into the Docker image; CI uses it too). Without one PHPUnit aborts with *"No tests executed!"* — add `--no-coverage` to run without it, which also speeds integration runs up.

## Static analysis & style

```
vendor/bin/parallel-lint --exclude vendor .                      # lint
vendor/bin/ecs check src                                         # coding standard (add --fix to apply)
vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=512M   # static analysis
```

## In Docker

Composer and the PHP runtime are baked into the image (see `Dockerfile`), so no local PHP install is needed:

```
docker-compose build
docker-compose run --rm php8 composer install
docker-compose run --rm php8 vendor/bin/phpunit
docker-compose run --rm php8 vendor/bin/phpunit --testsuite integration   # needs .env with staging credentials
```