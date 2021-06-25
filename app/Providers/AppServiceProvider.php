<?php

namespace App\Providers;

use Inertia\Inertia;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGlide();
        $this->registerLengthAwarePaginator();
        $this->registerInertia();
    }

    public function registerInertia()
    {
        Inertia::share('app.name', config('app.name'));

        Inertia::share('errors', function() {
           return session()->get('errors') ? session()->get('errors')->getBag('default')->getMessages() : (object) [];
        });

        Inertia::share('flash', function () {
            return [
                'success' => Session::get('success'),
            ];
        });

        Inertia::share([
            'auth' => function () {
              return [
                'user' => auth()->user() ? [
                  'loggedIn' => auth()->check(),
                  'id' => auth()->user()->id,
                  'name' => auth()->user()->name,
                  'email' => auth()->user()->email,
                  'first_name' => auth()->user()->first_name,
                  'last_name' => auth()->user()->last_name,
                  'can' => [
                    'purchase' => [
                        'viewAny' => auth()->user()->can('view any purchase'),
                        'view' => auth()->user()->can('view purchase'),
                        'create' => auth()->user()->can('create purchase'),
                        'update' => auth()->user()->can('update purchase'),
                        'delete' => auth()->user()->can('delete purchase'),
                        'print' => auth()->user()->can('print purchase'),
                    ],
                    'batangasTransaction' => [
                        'viewAny' => auth()->user()->can('view any batangas transaction'),
                        'view' => auth()->user()->can('view batangas transaction'),
                        'create' => auth()->user()->can('create batangas transaction'),
                        'update' => auth()->user()->can('update batangas transaction'),
                        'delete' => auth()->user()->can('delete batangas transaction'),
                        'print' => auth()->user()->can('print batangas transaction'),
                    ],
                    'mindoroTransaction' => [
                        'viewAny' => auth()->user()->can('view any mindoro transaction'),
                        'view' => auth()->user()->can('view mindoro transaction'),
                        'create' => auth()->user()->can('create mindoro transaction'),
                        'update' => auth()->user()->can('update mindoro transaction'),
                        'delete' => auth()->user()->can('delete mindoro transaction'),
                        'print' => auth()->user()->can('print mindoro transaction'),
                    ],
                    'supplier' => [
                        'viewAny' => auth()->user()->can('view any supplier'),
                        'view' => auth()->user()->can('view supplier'),
                        'create' => auth()->user()->can('create supplier'),
                        'update' => auth()->user()->can('update supplier'),
                        'delete' => auth()->user()->can('delete supplier'),
                        'restore' => auth()->user()->can('restore supplier'),
                    ],
                    'client' => [
                        'viewAny' => auth()->user()->can('view any client'),
                        'view' => auth()->user()->can('view client'),
                        'create' => auth()->user()->can('create client'),
                        'update' => auth()->user()->can('update client'),
                        'delete' => auth()->user()->can('delete client'),
                        'restore' => auth()->user()->can('restore client'),
                        // Payment
                        'viewPayment' => auth()->user()->can('view client payment'),
                        'updatePayment' => auth()->user()->can('update client payment'),
                        'verifyPayment' => auth()->user()->can('verify client payment'),
                    ],
                    'clientPayment' => [
                        'viewAny' => auth()->user()->can('view any client payment'),
                        'view' => auth()->user()->can('view client payment'),
                        'create' => auth()->user()->can('create client payment'),
                        'update' => auth()->user()->can('update client payment'),
                        'delete' => auth()->user()->can('delete client payment'),
                        'restore' => auth()->user()->can('restore client payment'),
                    ],
                    'driver' => [
                        'viewAny' => auth()->user()->can('view any driver'),
                        'view' => auth()->user()->can('view driver'),
                        'create' => auth()->user()->can('create driver'),
                        'update' => auth()->user()->can('update driver'),
                        'delete' => auth()->user()->can('delete driver'),
                        'restore' => auth()->user()->can('restore driver'),
                    ],
                    'helper' => [
                        'viewAny' => auth()->user()->can('view any helper'),
                        'view' => auth()->user()->can('view helper'),
                        'create' => auth()->user()->can('create helper'),
                        'update' => auth()->user()->can('update helper'),
                        'delete' => auth()->user()->can('delete helper'),
                        'restore' => auth()->user()->can('restore helper'),
                    ],
                    'tanker' => [
                        'viewAny' => auth()->user()->can('view any tanker'),
                        'view' => auth()->user()->can('view tanker'),
                        'create' => auth()->user()->can('create tanker'),
                        'update' => auth()->user()->can('update tanker'),
                        'delete' => auth()->user()->can('delete tanker'),
                        'restore' => auth()->user()->can('restore tanker'),
                    ],
                    'product' => [
                        'viewAny' => auth()->user()->can('view any product'),
                        'view' => auth()->user()->can('view product'),
                        'create' => auth()->user()->can('create product'),
                        'update' => auth()->user()->can('update product'),
                        'delete' => auth()->user()->can('delete product'),
                        'restore' => auth()->user()->can('restore product'),
                    ],
                    'user' => [
                        'viewAny' => auth()->user()->can('view any user'),
                        'view' => auth()->user()->can('view user'),
                        'create' => auth()->user()->can('create user'),
                        'update' => auth()->user()->can('update user'),
                        'delete' => auth()->user()->can('delete user'),
                        'restore' => auth()->user()->can('restore user'),
                    ],
                    'role' => [
                        'viewAny' => auth()->user()->can('view any role'),
                        'view' => auth()->user()->can('view role'),
                        'create' => auth()->user()->can('create role'),
                        'update' => auth()->user()->can('update role'),
                        'delete' => auth()->user()->can('delete role'),
                        'restore' => auth()->user()->can('restore role'),
                    ],
                    'permission' => [
                        'viewAny' => auth()->user()->can('view any permission'),
                        'view' => auth()->user()->can('view permission'),
                        'create' => auth()->user()->can('create permission'),
                        'update' => auth()->user()->can('update permission'),
                        'delete' => auth()->user()->can('delete permission'),
                        'restore' => auth()->user()->can('restore permission'),
                    ],
                  ],
                ] : null,
              ];
            },
          ]);
    }

    protected function registerGlide()
    {
        $this->app->bind(Server::class, function ($app) {
            return Server::create([
                'source' => Storage::getDriver(),
                'cache' => Storage::getDriver(),
                'cache_folder' => '.glide-cache',
                'base_url' => 'img',
            ]);
        });
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    return [
                        'data' => $this->items->toArray(),
                        'links' => $this->links(),
                    ];
                }

                public function links($view = null, $data = [])
                {
                    $this->appends(request()->all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);

                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url' => $url,
                                    'label' => $page,
                                    'active' => $this->currentPage() === $page,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url' => null,
                                    'label' => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    })->prepend([
                        'url' => $this->previousPageUrl(),
                        'label' => 'Previous',
                        'active' => false,
                        'current_page' => $this->currentPage(),
                    ])->push([
                        'url' => $this->nextPageUrl(),
                        'label' => 'Next',
                        'active' => false,
                    ]);
                }
            };
        });
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
