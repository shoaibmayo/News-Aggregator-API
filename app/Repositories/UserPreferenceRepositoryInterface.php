<?php

namespace App\Repositories;
use Illuminate\Http\Request;

interface UserPreferenceRepositoryInterface
{
    public function show($request);
    public function update($request);
    public function personalizedFeed($request);
}
