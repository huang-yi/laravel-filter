# Laravel Filter

这个拓展包帮助开发者快速构建查询过滤器。

## 实例

现在有一个查询用户列表的任务，要求能够按性别、所在城市筛选。

一般情况下，我们可能会提前在Model里定义一系列的scope：

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Scope of gender. 
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $gender
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGender(Builder $query, $gender)
    {
        return $query->where('gender', $gender);
    }
    
    /**
     * Scope of cities. 
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array $cityIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCities(Builder $query, $cityIds)
    {
        return $query->whereIn('city_id', (array) $cityIds);
    }
}

```

然后在Controller里调用：

```php
<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * User list.
     * 
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($gender = $request->input('gender')) {
            $query->gender($gender);
        }

        if ($cities = explode(',', $request->input('cities'))) {
            $query->cities($cities);
        }

        $users = $query->get();

        return $users;
    }
}

```

这样的实现看着还能接受，但往往现实需求中，需要过滤的往往不止两个条件，而且除了过滤外还有其他的业务逻辑，这样很容易引发Fat Controller的问题。

如果使用了该拓展包，代码是这样的：

1、 定义filter

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
<?php // File: app/Filters/User/Cities.php

namespace App\Filters\User;

use HuangYi\Filter\Contracts\FilterContract;
use Illuminate\Database\Eloquent\Builder;

class Cities implements FilterContract
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
        $query->whereIn('cities', $value);
    }
}

```

2、命名你的filter：

```php
<?php

// File: app/filters.php

use HuangYi\Filter\Facades\Filter;

Filter::name('user.gender', App\Filters\User\Gender::class);
Filter::name('user.cities', App\Filters\User\Cities::class);

```

3、在Controller中调用：

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

可以看到，虽然我们需要创建多个filter文件，但是Controller里面只需要一行代码就搞定了，所以无论需求有多少个过滤条件，都不会引起
Fat Controller的问题，如此实现是不是更加优雅？

开发者可能会担心创建filter很麻烦，不必慌张，你只需要敲一行Artisan命令就能快速创建filter到文件夹`app/Filtres/`，并自动注册
filter名到文件`app/filters.php`文件：

```sh
php artisan make:filter user.gender
```

## 快速开始

### 安装

使用composer安装：

```sh
composer require huang-yi/laravel-filter
```

### 注册服务（可选）

如果你正在使用版本号低于5.5的Laravel，需要手动注册服务，在`config/app.php`中填加：

```php
<?php

return [
    'providers' => [
        HuangYi\Filter\FilterServiceProvider::class,
    ],
];

```

### 配置（可选）

使用`vendor:publish`命令发布配置文件：

```sh
php artisan vendor:publish --provider="HuangYi\Filter\FilterServiceProvider"
```

执行上述命令后，会生成一个`config/filter.php`文件，即为该拓展包的配置文件。

一般来说，我们不需要修改任何配置，除非你有一些特殊的需求。

`key`：前端上传过滤器规则时使用的key，默认为`filters`。

`log_level`：设置日志等级，默认为`debug`。如果你想关闭日志，可以设置为`null`。

`parser`：规律器规则解析器。如果你想修改`filters`的参数格式，可以自行实现parser，但是parser类必须实现`HuangYi\Filter\Contracts\ParserContract`。

### 创建filter

使用命令`make:filter`快速创建filter：

```sh
php artisan make:filter user.gender
```

上述命令中`user.gender`为filter的名字，它会自动创建一个filter类到`app/Filters/`文件夹下面，并在文件`app/filters.php`中命名过滤器。

如果你不喜欢使用命令，则可以手动创建：

1、 创建一个filter类，你可以将其放至任何位置，只要满足`psr4`规范，但是它必须实现`HuangYi\Filter\Contracts\FilterContract`。

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

2、 命名你的filter：可以使用`HuangYi\Filter\Facades\Filter`的`name`方法来命名，默认系统会自动加载
`app/filters.php`，所以你可以在这个文件内进行命名。但你也可以在任意文件中执行，只要这个文件被系统require。

```php
<?php // File: app/filters.php

use HuangYi\Filter\Facades\Filter;

Filter::name('user.gender', App\Filters\User\Gender::class);

```

### 使用filter

一般我们在Controller中使用filter（你也可以在任意地方使用），你只需要use一下`HuangYi\Filter\HasFilter`，然后调用trait中的`applyFilters`方法即可。

`use HasFilter`不是必须的，你也可以直接调用`app('filter')->apply()`。

```php
<?php

use App\User;

Route::get('users', function () {
    $query = User::query();
    
    app('filter')->apply($query);
    
    return $query->get();
});
```

### 前端构造filters规则

单个filter的结构：

```javascript
let filter = {
    name: "user.gender",
    value: "female"
}
```

然后将多个filter组合在一起，并生成json字符串：

```javascript
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
```

最后将json字符串做base64处理后发送给服务端：

```javascript
let Base64 = require('js-base64').Base64;
let axios = require('axios');

filters = Base64.encode(filters);

axios.get('/users', {
    filters: filters
}).then((response) => {
    console.log(response);
});
```

## License

Laravel Filter遵循[The MIT License (MIT)](http://opensource.org/licenses/MIT)开源许可。
