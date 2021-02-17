<?php


namespace Milebits\SocietyTests;


use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|Application|HttpKernelInterface
     */
    public function createApplication()
    {
        $this->app = new Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));
        $this->app->singleton(HttpKernel::class, App\HttpKernel::class);
        $this->app->singleton(ConsoleKernel::class, App\ConsoleKernel::class);
        $this->app->singleton(ExceptionHandler::class, App\ExceptionHandler::class);
        return $this->app;
    }
}