# GatewayGuardian
PHP Class For CSRF Protection In Web Apps

## How To Use

### ***Client-side***

Create a unique CSRF Token tied to your users session
```php
<?php
session_start();
$token = $_SESSION['token'] = $_SESSION['token'] ?? bin2hex(random_bytes(32));
```
Embed the CSRF Token in the document `<meta>` tag, JavaScript `const`, or as an HTML input field `<input type="hidden">`
```php
<meta name="X-CSRF-TOKEN" content="<?php echo htmlentities($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
```
```php
const csrfToken = '<?php echo htmlentities($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>'
```
```php
<input type="hidden" name="X-CSRF-TOKEN" value="<?php echo htmlentities($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
```
Send your token to the server with every request for validation...
```javascript
fetch('/api/my/endpoint', {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="X-CSRF-TOKEN"]').content
    }
})
```

### ***Server-side***

Prefer using the [PSR-4 Autoloader](https://www.php-fig.org/psr/psr-4/) in conjunction with [Composer](https://getcomposer.org/) to access your class files but you may load the class any way you choose.

For the rest of this documentation we will assume you're using Composer with the PSR-4 Autoloader & a similar file structure.

Create a folder named `src` outside of the public directory of your site. Assuming your DNS record points at the `public` folder on your server. Your project structure can look something like this:
```
my-project/
-public/
--api/
---endpoint.php
--index.php
-src/
--GatewayGuardian.php
-vendor/
--composer/
--autoload.php
-composer.json
-php.ini
-php-error.log
```
Create a composer.json file like this:
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```
Using a shell update your Composer class dependencies or install for the first time like this:
```sh
$ /usr/bin/php7.3-cli composer.phar dump-autoload -o -d my-project/
```
**Composer will install its dependencies & update its class definitions for optimization.**

*requires Composer to be installed on your server*

Next we can our endpoint like this:

```php
<?php

use App\GatewayGuardian;

session_start();
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$gatewayGuardian = new GatewayGuardian('X_CSRF_TOKEN', 'token', array('POST'), true);
```
**You may need to adjust how you require your autoloader file in relation to your endpoint!**

When you service a request the instantiated `$gatewayGuardian` will provide the security check for you.

*make sure you provide the HTTP header name you are using on the client as well as the `$_SESSION['']` name of your token*

**Use underscores `_` in place of hyphens when passing the name of your HTTP header token name**

If your HTTP header CSRF Token name is `MY-COOL-CSRF-TOKEN` then you'll pass its name as `MY_COOL_CSRF_TOKEN` for example

You may provide the HTTP verb that is allowed also or decline to have that check by setting the `$checkRequest` parameter to false