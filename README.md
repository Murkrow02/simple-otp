# Simple OTP

This package provides a simple way to manage One-Time Passwords (OTPs) for your application users. This package includes functionality for generating, validating, and managing OTPs associated with your user models in a Laravel application.

## Installation

To install the Simple OTP package, run the following command:

```bash
composer require murkrow/simple-otp
```
### Running migrations
Remember to run the migrations to create the `otps` table.

```bash
php artisan migrate
```

### (Optional) Publishing vendor files
You can publish the config file and the migration using
```bash
php artisan vendor:publish 
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

To generate an OTP, use the `OtpBuilder` class. You can customize the OTP length, whether it is alphanumeric, its expiration time, and associate it with a user.

```php
use Murkrow\Otp\Builders\OtpBuilder;

$otp = (new OtpBuilder())
    ->forUser($user) // User object
    ->length(6) // Length of the OTP
    ->alphaNumeric(true) // Alphanumeric or numeric
    ->tag('login') // Optional tag
    ->expiresInMinutes(30) // Expiration time in minutes
    ->create();
```

### Validating an OTP

To validate an OTP, use the `validateOtp` method provided by the `HasOtps` trait:

```php
$user = User::find(1); // User object

$isValid = $user->validateOtp($otp, 'login'); // OTP and optional tag

if ($isValid) {
    // OTP is valid
} else {
    // OTP is invalid
}
```

### Validating and Removing an OTP

To validate and remove an OTP after successful validation, use the `validateAndRemoveOtp` method:

```php
$isValid = $user->validateAndRemoveOtp($otp, 'login'); // OTP and optional tag

if ($isValid) {
    // OTP is valid and removed
} else {
    // OTP is invalid
}
```

## Example

Here is an example of how to generate an OTP for a user, validate it, and then remove it after successful validation:

```php
use App\Models\User;
use Murkrow\Otp\Builders\OtpBuilder;

// Assume $user is an instance of the User model
$user = User::find(1);

// Generate an OTP
$otpBuilder = new OtpBuilder();
$otp = $otpBuilder
    ->forUser($user)
    ->length(6)
    ->alphaNumeric(true)
    ->expiresInMinutes(30)
    ->create();

// Validate the OTP
$isValid = $user->validateOtp($otp->code, 'login');

if ($isValid) {
    echo "OTP is valid!";
} else {
    echo "Invalid OTP!";
}

// Validate and remove the OTP
$isValidAndRemoved = $user->validateAndRemoveOtp($otp->code, 'login');

if ($isValidAndRemoved) {
    echo "OTP is valid and has been removed!";
} else {
    echo "Invalid OTP!";
}
```


### Configuration

#### Maximum Number of OTPs Per User

By default, a user can have up to 5 OTPs at a time. If a user exceeds this limit, the oldest OTP will be deleted. 
You can change this behavior by setting the `max_otps_per_user` configuration value in the `otp` configuration file.

```php
// config/otp.php
max_otps_per_user' => 5,
```

## License

This package is open-sourced software licensed under the [GNU General Public License v3.0](LICENSE).