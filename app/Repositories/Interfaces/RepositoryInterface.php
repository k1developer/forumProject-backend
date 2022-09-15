<?php

namespace App\Repositories\Interfaces;


use Illuminate\Http\Request;

interface RepositoryInterface {

    public function all();

    public function update($model, Request $request);

    public function destroy($model);
}
