# Short Description Of Securelooks

Laravel Project management system.

## Installation

You can install the package via composer:

```bash
composer require themelooks/securelooks
```

## Usage
Add this line under providers array in config/app.php
``` php
ThemeLooks\SecureLooks\SecureLooksServiceProvider::class
```
Add this line under Aliases array in config/app.php
``` php
'AppLoader' => ThemeLooks\SecureLooks\SecureLooksFacade::class
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


### Security

If you discover any security related issues, please email kousar.themelooks@gmail.com instead of using the issue tracker.

## Credits

- [Kousar Rahman](https://github.com/kousar2334)
- [Themelooks](http://themelooks.com/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
