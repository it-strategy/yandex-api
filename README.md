yandex-api
==========

PHP library for Yandex API

Documentation
-------------

Full Yandex API documentation available at http://api.yandex.ru/

Direct API used in example below available at http://api.yandex.ru/direct/doc/concepts/About.xml
Authentication instructions available at http://api.yandex.ru/direct/doc/concepts/auth-token.xml

Usage
-----

```php
<?php

use Yandex\Direct;

// create api
$api = new Direct('https://api.direct.yandex.ru/live/v4/json/', '443', $appId, $appToken);

// get client info for your login
$infos = $api->GetClientInfo();

// get client info for login 'bill'
$infos = $api->GetClientInfo('bill');

// get client info for logins 'bill' and 'joe'
$infos = $api->GetClientInfo(array('bill', 'joe'));

// and so on.. read full API documentation
```
