<?php

namespace App\Repositories;
use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function register($request);
    public function login($request);
    public function logout($request);
}
