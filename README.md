# Overview

This bundle offers functionality to store name/value settings in the DB.

## Installation

Enable the bundle in `config/bundles.php`:

```php
return [
    // ...
    OHMedia\SettingsBundle\OHMediaSettingsBundle::class => ['all' => true],
];
```

Make and run the migration:

```bash
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```

## How-To 

Custom settings are created through the service:

```php
use OHMedia\SettingsBundle\Service\Settings;

public function myAction(Settings $settings)
{
    $settings->set('app_my_new_setting', 'my value');
}
```

Once the setting is saved the value will be accessible in Twig:

```twig
{{ setting('app_my_new_setting') }}
```

or from the service itself:

```php
use OHMedia\SettingsBundle\Service\Settings;

public function myAction(Settings $settings)
{
    $value = $settings->get('app_my_new_setting');
}
```

It is recommended to prefix your setting with your bundle name
to significantly reduce the chance of ID collision.

## Entities

If you have an entity and save it in a settings, it will be automatically handled
as long as the identifier is not composite. There is no need for a custom transformer.

## More Complex Data

If your settings value is more complex than a string or a basic Entity,
then you need to be able to convert it to and from a string.

First, create a service that implements `OHMedia\SettingsBundle\Interfaces\TransformerInterface`,
which requires three functions. One function that gives the ID of the setting,
and two functions to transform that setting's value.

```php
<?php

namespace App\Settings;

use App\Entity\User;
use App\Repository\UserRepository;
use OHMedia\SettingsBundle\Interfaces\TransformerInterface;

class Transformer implements TransformerInterface
{
    private $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function getId(): string
    {
        return 'my_special_user';
    }
    
    public function transform($value): ?string
    {
        return (string) $value->getId();
    }
    
    public function reverseTransform(?string $value)
    {
        return $userRepository->find($value);
    }
}
```

The example transformer above will be the only transformer
to handle settings with ID 'my_special_user'.

You will need to create a transformer for every unique
setting ID you wish to transform.
