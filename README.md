# SimplePHPRouter

SimplePHPRouter is a lightweight, high-performance routing library for PHP applications. It's designed to be fast, easy to use, and suitable for production-grade applications.

## Features

- Fast and efficient routing
- Support for GET, POST, and other HTTP methods
- Dynamic route parameters
- Route groups with shared attributes
- Named routes for easy URL generation
- Support for closure and controller@method handlers
- No external dependencies

## Installation

You can install SimplePHPRouter using Composer:

```bash
composer require riyad/simplephprouter
```

Or include the `Router.php` file directly in your project.

## Basic Usage

Here's a quick example of how to use SimplePHPRouter:

```php
<?php

require 'vendor/autoload.php';

use SimplePHPRouter\Router;

$router = new Router();

// Define routes
$router->get('/', function() {
    return "Welcome to the homepage!";
});

$router->get('/users/{id}', function($id) {
    return "User profile for user: " . $id;
});

// Dispatch the route
try {
    $response = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    echo $response;
} catch (\Exception $e) {
    http_response_code($e->getCode());
    echo $e->getMessage();
}
```

## Defining Routes

### Basic Routes

```php
$router->get('/hello', function() {
    return "Hello, World!";
});

$router->post('/submit', function() {
    return "Form submitted!";
});
```

### Routes with Parameters

```php
$router->get('/users/{id}', function($id) {
    return "User ID: " . $id;
});
```

### Named Routes

```php
$router->get('/posts/{slug}', function($slug) {
    return "Post: " . $slug;
}, 'blog.post');

// Generate URL
echo $router->url('blog.post', ['slug' => 'hello-world']); // Outputs: /posts/hello-world
```

## Advanced Usage

### Route Groups

Group routes with shared attributes:

```php
$router->group(['prefix' => 'admin'], function($router) {
    $router->get('/dashboard', function() {
        return "Admin Dashboard";
    });
    
    $router->get('/users', function() {
        return "Admin Users List";
    });
});
```

### Using Controller Methods

You can use controller methods instead of closures:

```php
$router->get('/users', 'UserController@index');
$router->post('/users', 'UserController@store');
```

Ensure your controller file is autoloaded or included.

## Handling 404 Errors

When a route is not found, the router throws an exception. You can catch this to handle 404 errors:

```php
try {
    $response = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    echo $response;
} catch (\Exception $e) {
    if ($e->getCode() === 404) {
        echo "404 - Page not found";
    } else {
        // Handle other exceptions
        echo "An error occurred: " . $e->getMessage();
    }
}
```

## Performance Tips

- For large applications, consider caching the routes array.
- Use route groups to organize and optimize your routes.
- Minimize the use of wildcard routes to improve matching speed.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).