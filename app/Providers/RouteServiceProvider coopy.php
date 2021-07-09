<?php  
  
namespace App\Providers;  
  
use Illuminate\Routing\Router;  
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;  
  
class RouteServiceProvider extends ServiceProvider  
{  
    /** 
     * This namespace is applied to the controller routes in your routes file. 
     * 
     * In addition, it is set as the URL generator's root namespace. 
     * 
     * @var string 
     */  
    protected $namespace = 'App\Http\Controllers';  
    protected $backendNamespace;  
    protected $frontendNamespace;  
    protected $apiNamespace;  
    protected $currentDomain;  
  
    /** 
     * Define your route model bindings, pattern filters, etc. 
     * 
     * @param  \Illuminate\Routing\Router $router 
     * @return void 
     */  
    public function boot(Router $router)  
    {  
        //  
        $this->backendNamespace = 'App\Http\Controllers\Backend';  
        $this->frontendNamespace = 'App\Http\Controllers\Frontend';  
        $this->apiNamespace = 'App\Http\Controllers\API';  
        // $this->currentDomain = $this->app->request->server->get('HTTP_HOST');  
        $this->currentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "";  
  
        parent::boot($router);  
    }  
  
    /** 
     * Define the routes for the application. 
     * 
     * @param  \Illuminate\Routing\Router $router 
     * @return void 
     */  
    public function map(Router $router)  
    {  
//        $router->group(['namespace' => $this->namespace], function ($router) {  
//            require app_path('Http/routes.php');  
//        });  
  
        $backendUrl = config('route.backend_url');  
        $frontendUrl = config('route.frontend_url');  
        $apiUrl = config('route.api_url');  
  
        switch ($this->currentDomain) {  
            case $apiUrl:  
                // API路由  
                $router->group([  
                    'domain' => $apiUrl,  
                    'namespace' => $this->apiNamespace],  
                    function ($router) {  
                        require app_path('Http/routes-api.php');  
                    }  
                );  
  
                break;  
            case $backendUrl:  
                // 后端路由  
                $router->group([  
                    'domain' => $backendUrl,  
                    'namespace' => $this->backendNamespace],  
                    function ($router) {  
                        require app_path('Http/routes-backend.php');  
                    }  
                );  
                break;  
            default:  
                // 前端路由  
                $router->group([  
                    'domain' => $frontendUrl,  
                    'namespace' => $this->frontendNamespace],  
                    function ($router) {  
                        require app_path('Http/routes-frontend.php');  
                    }  
                );  
  
                break;  
        }  
  
    }  
}  
