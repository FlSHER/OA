<?php

namespace App\Services;

use Mockery\Exception;

class ApiResponseService
{

    /**
     * 生成基础错误响应
     * @return array
     */
    public function makeErrorResponse($message = '', $errorCode = 0, $stausCode = 500, $outerResponse = [])
    {
        $response = [
            'status' => -1,
            'message' => $message,
            'error_code' => $errorCode,
            'timestamp' => time()
        ];
        $response = array_collapse([$response, $outerResponse]);
        return response($response, $stausCode);
    }

    /**
     * 生成基础成功响应
     * @return array
     */
    public function makeSuccessResponse($message = '', $stausCode = 200, $outerResponse = [])
    {
        $response = [
            'status' => 1,
            'message' => $message,
            'timestamp' => time()
        ];
        $response = array_collapse([$response, $outerResponse]);
        return response($response, $stausCode);
    }

    /* 生成天气预报图片 start */

    public function getWeatherImage($cityCode)
    {
        if ($cityCode = '000000') {
            $image = imagecreate(310, 45);
            imagecolorallocate($image, 255, 255, 255);
            imagepng($image);
        }
        $baseUrl = 'http://restapi.amap.com/v3/weather/weatherInfo?key=46649b37382db424a33e34f487c5f3b7&extensions=all&city=';
        $weatherData = app('Curl')->setUrl($baseUrl . $cityCode)->get();
        try {
            $this->makeWeatherImage($weatherData);
        } catch (\Exception $e) {
            $cityCode = substr($cityCode, 0, 4) . '00';
            $weatherData = app('Curl')->setUrl($baseUrl . $cityCode)->get();
            try {
                $this->makeWeatherImage($weatherData);
            } catch (\Exception $e) {
                $cityCode = substr($cityCode, 0, 2) . '0000';
                $weatherData = app('Curl')->setUrl($baseUrl . $cityCode)->get();
                $this->makeWeatherImage($weatherData);
            }
        }
    }

    protected function makeWeatherImage($weatherData)
    {
        $province = $weatherData['forecasts'][0]['province'];
        $city = $weatherData['forecasts'][0]['city'];
        $weather1 = $weatherData['forecasts'][0]['casts'][0];
        $weather2 = $weatherData['forecasts'][0]['casts'][1];
        $weather3 = $weatherData['forecasts'][0]['casts'][2];

        $image = imagecreate(310, 45);
        imagecolorallocate($image, 255, 255, 255);
        $grey = imagecolorallocate($image, 200, 200, 200);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        imageline($image, 70, 0, 70, 45, $grey);
        imageline($image, 150, 0, 150, 45, $grey);
        imageline($image, 230, 0, 230, 45, $grey);
        /*第一栏*/
        imagettftext($image, 9, 0, 5, 17, $black, '/fonts/microsoft_yahei.ttf', mb_substr($province, 0, 2, 'utf8'));
        imagettftext($image, 9, 0, 5, 38, $black, '/fonts/microsoft_yahei.ttf', mb_substr($city, 0, 5, 'utf8'));
        /*第二栏*/
        imagettftext($image, 9, 0, 77, 12, $black, '/fonts/microsoft_yahei.ttf', $weather1['date']);
        imagettftext($image, 9, 0, 77, 27, $black, '/fonts/microsoft_yahei.ttf', $this->getTemperature($weather1));
        imagettftext($image, 9, 0, 77, 42, $black, '/fonts/microsoft_yahei.ttf', $this->getWeather($weather1));
        /*第二栏*/
        imagettftext($image, 9, 0, 157, 12, $black, '/fonts/microsoft_yahei.ttf', $weather2['date']);
        imagettftext($image, 9, 0, 157, 27, $black, '/fonts/microsoft_yahei.ttf', $this->getTemperature($weather2));
        imagettftext($image, 9, 0, 157, 42, $black, '/fonts/microsoft_yahei.ttf', $this->getWeather($weather2));
        /*第二栏*/
        imagettftext($image, 9, 0, 237, 12, $black, '/fonts/microsoft_yahei.ttf', $weather3['date']);
        imagettftext($image, 9, 0, 237, 27, $black, '/fonts/microsoft_yahei.ttf', $this->getTemperature($weather3));
        imagettftext($image, 9, 0, 237, 42, $black, '/fonts/microsoft_yahei.ttf', $this->getWeather($weather3));
        imagepng($image);
    }

    protected function getTemperature($weatherData)
    {
        return $weatherData['nighttemp'] . '~' . $weatherData['daytemp'] . '℃';
    }

    protected function getWeather($weatherData)
    {
        $dayWeather = preg_replace('/^(.+-)?(.+)$/', '$2', $weatherData['dayweather']);
        $nightWeather = preg_replace('/^(.+-)?(.+)$/', '$2', $weatherData['nightweather']);
        if ($dayWeather != $nightWeather) {
            return $dayWeather . '转' . $nightWeather;
        } else {
            return $dayWeather;
        }
    }

    /* 生成天气预报图片 end */

}
