<?php

namespace App\Repositories\TimeSlot;

interface ITimeSlot
{

    public function store($request);
    public function all();
    public function show($id);
    public function update($request);
    public function delete($id);
}
