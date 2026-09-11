# Requirements

You need to have an account in Pingen and obtain OAuth credentials for your desired grant type (usually client_credentials).

How to obtain these, are described here: https://api.pingen.com/documentation#section/Authentication/How-to-obtain-a-Client-ID

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

# Examples & Docs

Our API Docs are here: https://api.pingen.com/documentation

On the right-hand side of every endpoint you can see request samples for PHP and other languages, which you can copy and paste into your application.

# Bugreport & Contribution

If you find a bug, please either create a ticket in GitHub, or initiate a pull request.

# Versioning

We adhere to semantic (major.minor.patch) versioning (https://semver.org/). This means that:
* Patch (x.x.patch) versions fix bugs
* Minor (x.minor.x) versions introduce new, backwards compatible features or improve existing code.
* Major (major.x.x) versions introduce radical changes which are not backwards compatible.

In your automation or procedure you can always safely update patch & minor versions without the risk of your application failing.

# Testing

There are two test suites in `phpunit.xml`:

* **default** – Fast, Offline unit tests (HTTP is mocked). This is what CI runs on every change and it does not require credentials.
* **integration** – talks to the **real Pingen Staging API**. Is excluded from running in CI and every test is tagged `#[Group('integration')]`.

Prepare the docker image and composer:

```
docker-compose build
docker-compose run --rm php composer install
```

Running the unit tests (Http Mocked)

```
docker-compose run --rm php vendor/bin/phpunit
```

Running the integration tests (Live calls against Pingen Staging API, Credentials required in .env)

```
# Run whole integration suite
docker-compose run --rm php vendor/bin/phpunit --testsuite integration

# Run a single integration test file
docker-compose run --rm php vendor/bin/phpunit tests/Integration/LettersIntegrationTest.php

# Run a single test method
docker-compose run --rm php vendor/bin/phpunit --filter testCreateLetter
```

## Integration test Credentials

Credentials come from a `.env` file in the repository root (copy `.env.example` and replace with your values)

Without `PINGEN2_CLIENT_ID` / `PINGEN2_CLIENT_SECRET` the whole integration suite is **skipped** (not failed).

## Static analysis and style

```
docker-compose run --rm php vendor/bin/parallel-lint --exclude vendor .
docker-compose run --rm php vendor/bin/ecs check src
docker-compose run --rm php vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=512M
```

## Testing without Docker

We highly recommend to use our prepared docker image for running tests to ensure all necessary packages and versions are available. However if you prefer you can always install anything necessary locally and run all commands without the `docker-compose run --rm php` prefix
