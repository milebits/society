# Society - Social media package for Laravel 6-7-8+

A Laravel library that allows your application to interact like a social network, in one of a heck of a simple way.

# How to install

## Requirements

- PHP: 8 or higher
- ext-json: *
- Laravel Framework: ^6|^7|^8
- Milebits Eloquent Filters: 2 or higher
- Milebits Laravel Stream: 1 or higher
- Milebits Helpers: 1 or higher

## Installation

It is quite simple to install this package and all its features, all you have to do is:

````
composer require milebits/society
````

After the composer installation is complete, you need to add the `Sociable` trait to the model you want to be social, in
our case it will be the `App\Models\User` class that will be our sociable model.

```php
    use Illuminate\Database\Eloquent\Model;
    use Milebits\Society\Concerns\Sociable;
    class User extends Model
    {
        use Sociable;
    }
```

And there you are, you have just installed the Sociable package to your model!

# How to use

## The society repository

```php
public function getSocietyRepository(Illuminate\Http\Request $request)
{
    return $request->user()->society;
}
```

## FriendRequests

### Sending a friend request

```php
public function store(Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Model $friend)
{
    return $request->user()->society()->friends()->add($friend);
}
```

The rest of the documentation is coming later...

# Contributions

If in any case while using this package, and you which to request a new functionality to it, please contact us at
suggestions@os.milebits.com and mention the package you are willing to contribute or suggest a new functionality.

# Vulnerabilities

If in any case while using this package, you encounter security issues or security vulnerabilities, please do report
them as soon as possible by issuing an issue here in GitHub or by sending an email to security@os.milebits.com with the
mention **Vulnerability Report milebits/society** as your subject.