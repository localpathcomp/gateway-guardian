# GatewayGuardian
PHP Class For CSRF Protection In Web Apps

### How To Use

1. Create a unique CSRF Token tied to your users session
```php
<?php

session_start();
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

?>```
2. Embed the CSRF Token in the document `<meta>` tag, JavaScript `const`, or as an HTML input field `<input type="hidden">`