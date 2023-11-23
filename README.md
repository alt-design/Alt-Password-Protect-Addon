# Alt Password Protect

> Secure your Statamic entries with user friendly, individualized password protection

## Features

- Choose which entries to password protect
- Choose individual password for each entry
- Extends, but doesnt affect, existing Statamic password protection

## How to Install

You can search for this addon in the `Tools > Addons` section of the Statamic control panel and click **install**, or run the following command from your project root:

``` bash
composer require alt-design/alt-password-protect
```

## How to Use

- In config/statamic/protect.php, to the schemes array add:
``` bash
        'alt_password_protect_custom' => [
            'driver' => 'alt_password_protect_custom',
        ],

        'none' => [
            'driver' => 'none',
        ],
```

In entries, on the Alt Password Protect tab select Custom Password and enter your chosen password for the entry.

To hide password protected entries when displaying collections use 
``` bash
{{ collection:example protect:isnt="alt_password_protect_custom"}}
```
