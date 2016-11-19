# JWT
Simple JSON Web Token Toolkit for Lumen.

# Install
add
```
    "qufo/jwt":"dev-master"
```
to your composer.json, and composer update.

# Configure
add the below to  your .env file like
```
[JWT]
JWT_SECRET=YOURSECRETWITH32CHARSET
JWT_ALGO=SHA256
JWT_ENABLE=true
JWT_TTL=60
JWT_REFRESH_TTL=60
```

add
```
$app->register(Qufo\JWT\Provider\LumenServiceProvider::class);
```
to your bootstrap/app.php.

# How to use
1. Build a Token first. at any place of your code, like
```
    $jwt_token = \Qufo\JWT\JWT::encode($payload);
```
and bring it up to your client, so the client can take and remember it .

2. Build a Middleware at app/Http/Middleware names JWTAuth.php
```
<?php
/**
 * JWT
 */

namespace App\Http\Middleware;
use Closure;

class JWTAuth
{

    /**
     *  JWT Middleware
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function handle($request, Closure $next)
    {
        // if JWT ENABLE ?
        if (!env('JWT_ENABLE',false)) {
            return $next($request);
        }

        // Check Token
        $bearerToken = $request->bearerToken();
        $bearerToken = $bearerToken?:$request->input('access_token',false);
        if ($bearerToken) {
            try {
                $token = $this->decodeToken($bearerToken);
                $request->merge(['jwt'=>$token]);
                return $next($request);
            } catch (\Exception $e) {
                return response(['message'=>$e->getMessage(),'status_code'=>$e->getCode()],403);
            }
        } else {
            return response(['message'=>'Unauthorized,Bearer token seemed not exists.','status_code'=>401],401);
        }
    }

    /**
     * Decode Token
     * @param $bearerToken
     * @return mixed
     * @throws \Exception
     */
    private function decodeToken($bearerToken){
        $token_s = \Qufo\JWT\JWT::Base64UrlDecode($bearerToken);
        try {
            $token = \Qufo\JWT\JWT::decode($token_s);
            return $token;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(),403);
        }
    }
}
```

3. add
```
$app->routeMiddleware([
    'jwt.auth'  => 'App\Http\Middleware\JWTAuth',
]);
```

to your bootstrap/app.php.

4. Modify you routes.php like
```
$app->group(['middleware'=>'jwt.auth'],function() use ($app){
   //Your Routes Protected by JWT Auth.
});
```

# Some others
It is a "SIMPLE" JWT , so there is only one core file at src/JWT.php .
And, It only support SHA256 SHA512 SHA384,change it to your like at your .env file.