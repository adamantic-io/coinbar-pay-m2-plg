# Coinbar Pay - Magento 2 Plugin

## Pre-requisites
 * A working Magento 2.4.x installation - better have officially supported (i.e., not *end-of-life*) versions
 * Your keys for the Magento composer repository - get them by logging in with your account on https://commercemarketplace.adobe.com/.
 * Your Coinbar Pay credentials (Service Client ID, API Key, Secret Key)

 # Installation
 ## Add the repositories to the `composer.json` file
 Add the following three repositories to the `composer.json` file of your Magento 2 installation (it is in the installation root directory).
 They must be placed in the `repositories` section:
 ```json
...
"repositories": [
    ... other repositories here, leave them and append a comma (,)
    ... then add the following:

        { "type": "git", "url": "https://github.com/adamantic-io/coinbar-pay-php-sdk" },
        { "type": "git", "url": "https://github.com/adamantic-io/crypto-payments-php" },
        { "type": "git", "url": "https://github.com/adamantic-io/coinbar-pay-m2-plg.git" }

]
```

## Install the plug-in via Composer
Open a terminal in the Magento 2 installation directory, and issue the following commands:
 1. `composer require coinbar/coinbar-pay-m2-plg/coinbar-pay`
 2. `php bin/magento setup:upgrade`
 3. `php bin/magento setup:di:compile`

## Configure the plug-in
Open the Magento 2 admin section, and head to
`Stores -> Configuration -> Sales -> Payment Methods`.
You will find a new payment method (usually at the bottom) called `Coinbar Pay`; select it and insert the required data:
 * Service Client ID
 * Coinbar Pay API Key
 * Coinbar Pay Secret Key

Also, make sure that the payment method is enabled (option `Enabled = Yes`).
Then save the config and clear the caches.

That's it - you should now be able to use Coinbar Pay at the Magento 2 checkout.

# License
This plug-in is released under a LGPL license - version 3. Please refer to the [LICENSE](./LICENSE) file for further detail.