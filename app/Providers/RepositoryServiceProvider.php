<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\ClientRepository;
use App\Repositories\Interfaces\ClientInterface;
use App\Repositories\PhoneRepository;
use App\Repositories\Interfaces\PhoneInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bind the interface to an implementation repository class
     */
    public function register()
    {
        $this->app->bind(
            UserInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            ClientInterface::class,
            ClientRepository::class
        );
        $this->app->bind(
            PhoneInterface::class,
            PhoneRepository::class
        );
    }
}