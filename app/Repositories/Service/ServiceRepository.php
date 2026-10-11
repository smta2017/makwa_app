<?php

namespace App\Repositories\Service;

use App\Location;
use App\ServiceCategory;

class ServiceRepository
{
    public function getServicesInLocation($location_id)
    {
        $serviceCategory = ServiceCategory::whereHas('Services.VendorServices.Vendor.vendorLocations.Location', function ($q) use ($location_id) {
            $q->where('id', $location_id);
        })->get();

        return $serviceCategory;
    }
}
