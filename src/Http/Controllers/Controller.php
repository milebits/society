<?php


namespace Milebits\Society\Http\Controllers;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Milebits\Society\Concerns\Sociable;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return array|Application|Request|string|null
     */
    public function request()
    {
        return request();
    }

    /**
     * @param Request|null $request
     * @return Model|Sociable
     */
    public function user(Request $request = null)
    {
        return (is_null($request) ? $this->request() : $request)->user();
    }
}