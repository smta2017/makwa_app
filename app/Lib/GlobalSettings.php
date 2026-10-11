<?php

namespace App\Lib;

use App\GlobalSetting;

class GlobalSettings
{
    private static function _getfromGlobalSettings($settingName)
    {
        return GlobalSetting::where('setting_name', $settingName)->first('value')["value"];
    }

    public static function getMaxOrders()
    {
        return self::_getfromGlobalSettings('max_orders');
    }

    public static function bufferTime()
    {
        return self::_getfromGlobalSettings('buffer_time');
    }

    public static function turnaroundTime()
    {
        return self::_getfromGlobalSettings('turnaround_time');
    }

    public static function cancellationBuffer()
    {
        return self::_getfromGlobalSettings('cancellation_buffer');
    }

    public static function cancellationFee()
    {
        return self::_getfromGlobalSettings('cancellation_fee');
    }

    public static function reschedulingBufferStart()
    {
        return self::_getfromGlobalSettings('rescheduling_buffer_start');
    }

    public static function reschedulingBufferEnd()
    {
        return self::_getfromGlobalSettings('rescheduling_buffer_end');
    }

    public static function reschedulingFee()
    {
        return self::_getfromGlobalSettings('rescheduling_fee');
    }
}
