# Laravel Filter

This package helps developers build query filters.

## Translation

[中文文档](README-cn.md)

## Installation

This package can be installed via composer:

```sh
composer require huang-yi/laravel-filter
```

## Register service provider (Optional)

You need to register the service provider manually if your Laravel version is less than 5.5:

```php
<?php // File: config/app.php

return [
    'providers' => [
        HuangYi\Filter\FilterServiceProvider::class,
    ],
];

```

## Configurations (Optional)

Run this command to publish the configuration file:

```
$ php artisan vendor:publish --provider="HuangYi\Filter\FilterServiceProvider"
```

`key`: The key to get filter rules from query string.

`log_level`: The log level.

`parser`: The filter rules parser.

## Generating filters

To create a new filter, use the make:filter Artisan command. This command will
create a new filter class in the `app/Filters` directory and name the filter in
the `app/filters.php` file.

```sh
php artisan make:filter user.gender
```

```php
<?php // File: app/Filters/User/Gender.php

namespace App\Filters\User;

use HuangYi\Filter\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class Gender implements FilterContract
{
    /**
     * Apply filter to eloquent builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return void
     */
    public function apply(Builder $query, $value)
    {
        $query->where('gender', $value);
    }
}

```

```php
<?php // File: app/filters.php

use HuangYi\Filter\Facades\Filter;

Filter::name('user.gender', App\Filters\User\Gender::class);

```

## Using filters

This package provides a trait `HuangYi\Filter\HasFilter`.

```php
<?php

namespace App\Http\Controllers;

use App\User;
use HuangYi\Filter\HasFilter;

class UserController extends Controller
{
    use HasFilter;

    /**
     * User list.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function index()
    {
        $query = User::query();

        $this->applyFilters($query);

        $users = $query->get();

        return $users;
    }
}

```

## Client query

We should Base 64 encode a JSON representation of the desired filters into the
`filters` query string variable.

```javascript
let Base64 = require('js-base64').Base64;
let axios = require('axios');

let filters = JSON.stringify([
    {
        name: "user.gender",
        value: "female"
    },
    {
        name: "user.cities",
        value: [1, 2, 3]
    }
]);

filters = Base64.encode(filters);

axios.get('/users', {
    filters: filters
}).then((response) => {
    console.log(response);
});
```

## License

Laravel Filter is licensed under [The MIT License (MIT)](http://opensource.org/licenses/MIT).
