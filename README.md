# laravel-model-filter

A Laravel Eloquent Models filter way

## Introduction
Let say we want to return a list of users filtered by multiple parameters. When we see to:

`/users?name=wmkong&age=&client=ios&roles[]=1&roles[]=4&roles[]=7`

`$request->all()` will return:

```php
[
    'name'         => 'wmkong',
    'age'          => '',
    'mobile_phone' => '12345',
    'roles'        => ['1','4','7'],
]
```

To filter by all those parameters we would need to do something like:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::when($request->has('name'), function ($query) use ($request) {
                return $query->where('name', $request->get('name'));
            })
            ->when($request->has('age'), function ($query) use ($request) {
                return $query->where('age', $request->get('age'));
            })
            ->when($request->has('mobile_phone'), function ($query) use ($request) {
                return $query->where('mobile_phone', 'like', "%{$request->get('mobile_phone')}%");
            })
            ->when($request->has('client'), function ($query) use ($request) {
                return $query->where('client', $request->get('client'));
            })
            ->when($request->has('roles'), function ($query) use ($request) {
                return $query->whereIn('roles', $request->get('roles'));
            })
            ->get();
    }
}
```

To filter that same input With Eloquent Filters:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::filter($request->all())->get();
    }
}
```

## Configuration
### Install Through Composer
```
composer require im-wmkong/laravel-model-filter
```

There are a few ways to define the filter a model will use:

- [Use ModelFilter's Default Settings](#default-settings)
- [Use A Custom Namespace For All Filters](#with-configuration-file-optional)
- [Define A Model's Default Filter](#define-the-default-model-filter-optional)

#### Default Settings
The default namespace for all filters is `App\Filters\` and each Model expects the filter classname to follow the `{$ModelName}Filter` naming convention regardless of the namespace the model is in.

##### With Configuration File (Optional)

> Registering the service provider will give you access to the `php artisan make:filter {model}` command as well as allow you to publish the configuration file.  Registering the service provider is not required and only needed if you want to change the default namespace or use the artisan command

After installing the Model Filter library, register the `ModelFilter\ModelFilterServiceProvider::class` in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...

    ModelFilter\ModelFilterServiceProvider::class,
],
```

Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --provider="ModelFilter\ModelFilterServiceProvider"
```

In the `config/eloquentfilter.php` config file.  Set the namespace your model filters will reside in:

```php
'namespace' => "App\\Filters",
```

#### Define The Default Model Filter (optional)

> The following is optional. If no `modelFilter` method is found on the model's filter class will be resolved by the [default naming conventions](#default-settings)

Create a public method `modelFilter()` that returns `Your\Model\Filter::class;` in your model.

```php
<?php

namespace App\Models;

use ModelFilter\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    public function modelFilter()
    {
        return \App\ModelFilters\CustomFilters\CustomUserFilter::class;
    }

    //User Class
}
```

### Generating The Filter
> Only available if you have registered `ModelFilter\ModelFilterServiceProvider::class` in the providers array in your `config/app.php`

You can create a model filter with the following artisan command:

```bash
php artisan make:filter User
```

Where `User` is the Eloquent Model you are creating the filter for.  This will create `app/Filters/UserFilter.php`

## Usage

### Defining The Filter Logic
Define the filter logic based on the camel cased input key passed to the `filter()` method.

- Empty strings and null values are ignore
- Input without a corresponding filter method are ignored
- The value of the key is injected into the method
- All Eloquent Builder methods are accessible in `$this` context in the model filter class.

To define methods for the following input:

```php
[
    'name'         => 'wmkong',
    'age'          => '',
    'mobile_phone' => '12345',
    'roles'        => ['1','4','7'],
]
```

You would use the following methods:

```php
<?php

namespace App\Filters;

use ModelFilter\Filter;

class UserFilter extends Filter
{
    /**
     * The attributes that equal query is used by default.
     *
     * @var array
     */
    public $filterable = [
        'name',
        'roles'
    ];
    
    public function mobilePhone($phone)
    {
        return $this->where('mobile_phone', 'like', "%{$phone}%");
    }
}
```

> **Note:** In the above example if you do not want equal query you can configure `$filterable` attributes

> **Note:**  In the above example if you do not want `mobile_phone` to be mapped to `mobilePhone()` you can set `'input_camel' => false` on `config/eloquentfilter.php`. Doing this would allow you to have a `mobile_phone()` filter method instead of `mobilePhone()`. By default, `mobilePhone()` filter method can be called thanks to one of the following input key: `mobile_phone`, `mobilePhone`

### Applying The Filter To A Model

Implement the `ModelFilter\Traits\Filterable` trait on any Eloquent model:

```php
<?php

namespace App\Models;

use ModelFilter\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    //User Class
}
```

This gives you access to the `filter()` method that accepts an array of input:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::filter($request->all())->get();
    }
}
```

# Contributing
Any contributions welcome!

# Thanks
- [Tucker-Eric/EloquentFilter](https://github.com/Tucker-Eric/EloquentFilter)
- [overtrue/laravel-skeleton](https://github.com/overtrue/laravel-skeleton)
