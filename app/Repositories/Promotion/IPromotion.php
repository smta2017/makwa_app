<?php

namespace App\Repositories\Promotion;

interface IPromotion
{

    public function store($request);
    public function all();
    public function show($id);
    public function update($request);
    public function delete($id);
}
