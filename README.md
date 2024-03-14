## Setting Up JWT(JSON Web Tokens) authentication to use in Laravel project

I believe JWT(JSON Web Tokens) authentication is better especially when developing an API using laravel than the default Sanctum which comes with laravel.

Some of the benefits of JWT Web Tokens over Sanctum: 

1. Stateless Authentication: 
    JWT tokens are stateless, meaning they don't rely on server-side storage and can be easily scaled across multiple servers. This can be advantageous for microservices architectures or when building APIs consumed by various clients.
2. Token-Based Authentication: 
    JWT tokens are self-contained and contain all necessary information about the user and their permissions. This makes them ideal for token-based authentication in APIs.
3. Flexibility: 
    JWT tokens can carry custom claims and metadata, providing flexibility in encoding user information. They are also widely supported by various programming languages and frameworks.
4. Performance: 
    Since JWT tokens are stateless, they can improve performance by reducing the need for server-side storage and database lookups for session data.

To use JWT in Laravel, you can use the `tymon/jwt-auth` package. Here's how you can install and set it up:

1. Install the package via composer:

```bash
composer require tymon/jwt-auth
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

3. Generate a secret key for JWT:

```bash


php artisan jwt:secret
```

4. In your `User` model, implement the `Tymon\JWTAuth\Contracts\JWTSubject` interface and add the required methods:

```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // ...

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

5. In your `config/auth.php` file, set the driver of the `api` guard to `jwt`:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

6. Then in routes/api.php file wrap your protected routes with 'jwt.auth' as the middleware

```php
// Example of adding middleware to a specific route
Route::get('/example', [ExampleController::class, 'exampleMethod'])->middleware('jwt.auth');

// Example of adding middleware to a route group
Route::group(['middleware' => ['jwt.auth']], function () {
    // Routes that require the middleware
    Route::get('/route1', [Controller1::class, 'method1']);
    Route::post('/route2', [Controller2::class, 'method2']);
});
```

7. In auth controller where you would like to generate token include  'use Tymon\JWTAuth\Facades\JWTAuth';

```php

//jwt
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // your code here
    // Login with email and password
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Attempt to authenticate the user
            $credentials = $request->only('email', 'password');

            if (! $token = JWTAuth::attempt($credentials)) {

                // Authentication failed
                // return response()->json(['error' => 'Unauthorized'], 401);
                //throw an exception with message 'unauthorized'
                throw new \Exception('Invalid Credentials');

            }

            // Authentication successful, return token
            return response()->json(['message' => 'Login successful', 'token' => $token], 200);

        } catch (\Exception $th) {

            // JWT Exception
            return response()->json(['error_message' => 'Could not create token', 'error' => $th->getMessage()], 500);

        }
    }
}
```

The token returned you can use it when making request to protected endpoints.

Now, you can use JWT for authentication in your Laravel application, and Enjoy secure JWT tokens without worry. 
