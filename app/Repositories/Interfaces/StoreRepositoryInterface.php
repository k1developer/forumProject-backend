<?php

namespace App\Repositories\Interfaces;


use Illuminate\Http\Request;

interface StoreRepositoryInterface {

    public function store(Request $request);

}
