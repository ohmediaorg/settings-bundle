Overview
========

This bundle offers functionality to store name/value settings in the DB.

Installation
------------

Enable the bundle in `config/bundles.php`:

```php
return [
    // ...
    JstnThms\SettingsBundle\JstnThmsSettingsBundle::class => ['all' => true],
];
```

Make and run the migration:

```bash
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```

How-To
------

Custom settings are created through the service:

```php
use JstnThms\SettingsBundle\Settings\Settings;

public function myAction(Settings $settings)
{
    $settings->set('app_my_new_setting', 'my value');
}
```

Once the setting is saved the value will be accessible in Twig:

```twig
{{ jstnthms_settings('app_my_new_setting') }}
```

or from the service itself:

```php
use JstnThms\SettingsBundle\Settings\Settings;

public function myAction(Settings $settings)
{
    $value = $settings->get('app_my_new_setting');
}
```

It is recommended to prefix your setting with your bundle name
to significantly reduce the chance of ID collision.

More Complex Data
-----------------

If your settings value is more complex than a string,
then you need to be able to convert it to and from a string.

First, create a service tagged with `jstnthms_settings.transformer`:

```yaml
services:
    mybundle.settings:
        class: App\Settings\Transformer
        tags:
            - { name: jstnthms_settings.transformer }
```

Your service should implement `JstnThms\SettingsBundle\Settings\SettingsTransformerInterface`,
which requires three functions. One function that gives the ID of the setting,
and two functions to transform that setting's value.

```php
<?php

namespace App\Settings;

use App\Entity\User;
use App\Repository\UserRepository;
use JstnThms\SettingsBundle\Settings\SettingsTransformerInterface;

class Transformer implements SettingsTransformerInterface
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
