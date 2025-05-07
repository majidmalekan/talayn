<?php

use App\Repositories\Commission\CommissionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Morilog\Jalali\CalendarUtils;

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     *
     * @return string
     */
    function config_path(string $path = ''): string
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('generate_otp')) {
    /**
     * Generate random OTP
     *
     * @param int $length
     *
     * @return string
     */
    function generate_otp(int $length = 6): string
    {
        $pool = '0123456789';

        do {
            $code = substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
        } while (strlen((string)((int)$code)) < $length);

        return $code;
    }
}

if (!function_exists('success')) {
    /**
     * Return successful response from the application.
     *
     * @param string $message
     * @param null $object
     * @param int $status
     *
     * @return JsonResponse
     */
    function success(string $message = '', $object = null, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $object], $status);
    }
}

if (!function_exists('failed')) {
    /**
     * Return failed response from the application.
     *
     * @param string $message
     * @param int $status
     *
     * @return JsonResponse
     */
    function failed(string $message = '', int $status = 500): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'statusCode' => $status], $status);
    }
}


if (!function_exists('convertPersianDateToLatin')) {
    /**
     * @param string $date
     * @return string
     */
    function convertPersianDateToLatin(string $date): string
    {
        return CalendarUtils::createCarbonFromFormat('Y-m-d', $date)
            ->format('Y-m-d');
    }
}

if (!function_exists('convertLatinDateToPersian')) {
    /**
     * @param string $date
     * @return string
     */
    function convertLatinDateToPersian(string $date): string
    {
        return CalendarUtils::strftime('Y-m-d', strtotime($date));
    }
}
if (!function_exists('ipInRange')) {
    /**
     * @param string $ip
     * @param string $range
     * @return bool
     */
    function ipInRange(string $ip, string $range): bool
    {
        if (str_contains($range, '/')) {
            list($subnet, $bits) = explode('/', $range);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            $subnet &= $mask;
            $ip = ip2long($ip);
            return ($ip & $mask) === $subnet;
        } elseif (str_contains($range, '-')) {
            list($start, $end) = explode('-', $range);
            $start = ip2long($start);
            $end = ip2long($end);
            $ip = ip2long($ip);
            return ($ip >= $start && $ip <= $end);
        }
        return false;
    }
}


if (!function_exists('arabicToPersian')) {
    /**
     * @param $string
     * @return array|string
     */

    //Convert arabic tp persian word
    function arabicToPersian($string): array|string
    {
        $characters = [
            'ك' => 'ک',
            'دِ' => 'د',
            'بِ' => 'ب',
            'زِ' => 'ز',
            'ذِ' => 'ذ',
            'شِ' => 'ش',
            'سِ' => 'س',
            'ى' => 'ی',
            'ي' => 'ی',
            '١' => '۱',
            '٢' => '۲',
            '٣' => '۳',
            '٤' => '۴',
            '٥' => '۵',
            '٦' => '۶',
            '٧' => '۷',
            '٨' => '۸',
            '٩' => '۹',
            '٠' => '۰',
        ];
        return str_replace(array_keys($characters), array_values($characters), $string);
    }
}

if (!function_exists('detectDeviceByMac')) {
    function detectDeviceByMac(string $mac): array
    {
        $prefix = strtoupper(str_replace([':', '-'], '', substr($mac, 0, 8)));
        $file = './mac-devices.json';

        if (!file_exists($file)) {
            return ['vendor' => 'Unknown', 'device' => 'Unknown'];
        }

        $data = json_decode(file_get_contents($file), true);

        return $data[$prefix] ?? ['vendor' => 'Unknown', 'device' => 'Unknown'];
    }
}

if (!function_exists('calculateDynamicCommission')) {
    /**
     * @param $amountGram
     * @param $totalPrice
     * @return int
     * @throws BindingResolutionException
     */
    function calculateDynamicCommission($amountGram, $totalPrice): int
    {
        $commissionRule = app()->make(CommissionRepositoryInterface::class)->firstByRule();
        $percent = $commissionRule ? $commissionRule->percent : 0;
        $commission = $totalPrice * ($percent / 100);
        $min = app()->make(CommissionRepositoryInterface::class)->firstByKey('min_commission') ?? 50000;
        $max = app()->make(CommissionRepositoryInterface::class)->firstByKey('max_commission') ?? 5000000;

        return (int)round(max($min, min($commission, $max)));
    }
}
