**FirstKB** is a **lightweight** and fast **PHP framework** that simplifies web application development and speeds up their loading.

Features
--------

* Easy to learn and use.
* Built-in routing system.
* Powerful middleware support.
* Template engine agnostic.
* Lightweight and fast.

Requirements
------------

* PHP 7.1 or higher
* Composer

Installation
-----

* Clone the repository
```
git clone https://github.com/firstkb/first-project.git
```

* Install dependencies
```
composer install
```
* For server. Configure your web server to point to the `public` directory.
* For local. To run the application, execute the following command:
```
php -S localhost:8000 -t public
```
* Open your browser and navigate to http://localhost:8000

Usage
-----

###Config

The config.php file is a global configuration file for the FirstKB framework, which is used for defining various settings related to the application. It includes the following settings:

* route_not_found: defines the controller and method to be executed when a route is not found.
* default_locale: defines the default locale for the application.
* locale: defines the available locales for the application along with their corresponding URL paths.
* timezone_set: defines the timezone for the application.
* environment: defines the current environment for the application, such as "dev" or "prod".

To customize the configuration for local use, you can create a separate config.local.php file and override the necessary values. This file should be located in the same directory as config.php.

Example
```
<?php

return [
    'route_not_found' => 'App\\Controllers\\Home::pageNotFound',
    'default_locale' => 'en',
    'locale' => [
        'en' => '',
        'es' => '/es'
    ],
    'timezone_set' => 'America/New_York',
    'environment' => 'dev',
];
```

###Router
The FirstKB router supports two ways of defining routes - through annotations in controllers and through an array in the /config/routes.php file. In both cases, routes are described using a path, HTTP methods, route name, and the name of the controller that will handle the request.

The router supports various types of variables that can be passed in the route, such as integer, string, and others. For example, to limit a variable to only alphabetical characters, the name:string type can be used.

To use annotations, the path to the controller, HTTP method, and route name must be specified. For example:

```
/**
 * @Route(path="/user/{_locale}/{id:int}/{name:string}/", methods={"GET", "POST"}, name="user_profile")
 */
```

The second way of defining routes is to use an array of routes in the configuration file routes.php. Each route contains a path, HTTP methods, route name, and controller name. For example:

```
return [
    [
        'path' => '/user/{_locale}/{id:int}/{name:string}/',
        'methods' => ['GET', 'POST'],
        'name' => 'user_profile',
        'controller' => 'App\\Controllers\\UserController::profile'
    ]
];
```

The router can use the following types of variables:

* _locale - a string variable representing the language code, for example, en or fr (set in file /config/config.php).
* id:int - an integer variable limited to numeric values only.
* name:string - a string variable limited to alphanumeric characters only.
* You can use these variable types to define constraints on input parameters when processing requests.

###Request

The Request class also provides access to the $_GET, $_POST, $_COOKIE, $_FILES, and $_SERVER global variables through the query, request, cookies, files, and server properties, respectively. You can use these properties to get or set values for these global variables:

```
$request = new Request(Config $config, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
$name = $request->query->get('name); // get $_GET['name']
$name = $request->request->get('name); // get $_POST['name']
$currentUrl = $request->server->get('REQUEST_URI'); // get $_SERVER['REQUEST_URI']
$server = $request->server->all(); // get $_SERVER return array
```

An example of connecting a class done in AbstractController.

###Responce

The Response class is used to represent an HTTP response. It has several properties and methods that allow you to set the response status, headers, and content.

Example use
```
// send content
$response = new Response($content, $status, $header);
$response->send();

// redirect
$response = new Response('', 302);
$response->setRedirect($url);
$response->send();

// set cookie
$response = new Response($content);
$response->setCookie(string $name, string $value, $expires = null, string $path = '/', $domain = null, bool $secure = false, bool $httpOnly = true);
$response->send();

```


License
-------

FirstKB is open-sourced software licensed under the MIT license.