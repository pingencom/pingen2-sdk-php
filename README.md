# Important information

This SDK uses the new API V2 of pingen v2. Pingen v2 does not share any data with v1 and therefore separate accounts
are required.

If you are looking for the SDK for pingen v1, please visit this repository: https://github.com/pingencom/pingen

# Requirements

You need to have an account in pingen v2 and obtain oauth credentials for your desired grant type (usually client_credentials).

How to obtain these are described here: https://api.v2.pingen.com/documentation#section/Authentication/How-to-obtain-a-Client-ID

# Installation

Require the package via composer (Get composer here: https://getcomposer.org/download/)

`composer require pingencom/pingen2-sdk-php`

# Environments

We have two Environments available: Production and Staging (see https://api.v2.pingen.com/documentation#section/Basics/Environments)

This SDK supports staging as well. **When initiating the provider** (see Usage), the optional 'staging' attribute should be set, **as well as when creating an endpoint object**.

# Usage

The simplest way to integrate is using the client credentials grant (see https://api.v2.pingen.com/documentation#section/Authentication/Which-grant-type-should-i-use)

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

Our API Docs are here: https://api.v2.pingen.com/documentation

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

ECS: `vendor/bin/ecs check src`

PHPStan: `vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=512M`

Lint: `vendor/bin/parallel-lint --exclude vendor .`