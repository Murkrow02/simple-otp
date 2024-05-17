# Simple OTP

This package provides a simple way to manage One-Time Passwords (OTPs) for your application users. This package includes functionality for generating, validating, and managing OTPs associated with your user models in a Laravel application.

## Installation

To install the Simple OTP package, run the following command:

```bash
composer require murkrow/simple-otp
```

## Usage

### Adding the Trait to Your User Model

To enable OTP functionality for your user model, add the `HasOtps` trait to the model. Typically, this will be your `User` model.

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Murkrow\Otp\Traits\HasOtps;

class User extends Authenticatable
{
    use HasOtps;

    // Your user model code here
}
```

### Generating an OTP

To generate a new OTP for a user, use the `generateOtp` method. This method accepts the following optional parameters:

- `$minExp` (int): The OTP expiration time in minutes (default: 30 minutes).
- `$codeLength` (int): The length of the OTP code (default: 6 characters).
- `$alphaNumeric` (bool): Whether to generate an alphanumeric OTP (default: false, numeric only).

```php
$user = User::find(1);
$otp = $user->generateOtp(30, 6, true);
```

### Validating an OTP

To validate an OTP without removing it, use the `validateOtp` method. This method returns `true` if the OTP is valid and `false` otherwise.

```php
$isValid = $user->validateOtp('123456');
```

### Validating and Removing an OTP

To validate an OTP and remove it from the valid OTPs, use the `validateAndRemoveOtp` method. This method returns `true` if the OTP is valid and `false` otherwise.

```php
$isValid = $user->validateAndRemoveOtp('123456');
```

### Configuration

#### Maximum Number of OTPs Per User

By default, a user can have up to 5 OTPs at a time. If a user exceeds this limit, the oldest OTP will be deleted. You can change this limit using the `setMaxOtpsPerUser` method.

```php
User::setMaxOtpsPerUser(10);
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

For more information and detailed documentation, please refer to the official documentation.



