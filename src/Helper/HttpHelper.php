<?php

declare(strict_types=1);
/**
 * This file is part of endness Bailing.
 *
 * @link     https://www.endnessai.com
 * @document https://help.endnessai.com
 * @contact  www.endnessai.com 7*12 9:00-21:00
 */
namespace Endness\Helper;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpHelper
{
    /**
     * json请求接口.
     * @throws Exception
     */
    public static function jsonRequest(string $url, array $param = [], string $method = 'post', array $headers = []): string
    {
        $headers['content-type'] = 'application/json';
        $clientHttp = new Client();
        if ($method == 'post') {
            try {
                $response = $clientHttp->post($url, [
                    'json' => $param,
                    'headers' => $headers,
                ]);
            } catch (GuzzleException $e) {
                stdLog()->error('HttpHelper json request error：', [$url, $param, $headers, $e->getMessage()]);
                throw new Exception('请求接口失败：' . substr($e->getMessage(), 0, 80));
            }
        } elseif ($method == 'put') {
            try {
                $response = $clientHttp->put($url, [
                    'json' => $param,
                    'headers' => $headers,
                ]);
            } catch (GuzzleException $e) {
                stdLog()->error('HttpHelper json request error：', [$url, $param, $headers, $e->getMessage()]);
                throw new Exception('请求接口失败：' . substr($e->getMessage(), 0, 80));
            }
        } elseif ($method == 'delete') {
            try {
                $response = $clientHttp->delete($url, [
                    'json' => $param,
                    'headers' => $headers,
                ]);
            } catch (GuzzleException $e) {
                stdLog()->error('HttpHelper json request error：', [$url, $param, $headers, $e->getMessage()]);
                throw new Exception('请求接口失败：' . substr($e->getMessage(), 0, 80));
            }
        } else {
            try {
                $response = $clientHttp->get($url, [
                    'query' => $param,
                    'headers' => $headers,
                ]);
            } catch (GuzzleException $e) {
                stdLog()->error('HttpHelper json request error：', [$url, $param, $headers, $e->getMessage()]);
                throw new Exception('请求接口失败：' . substr($e->getMessage(), 0, 80));
            }
        }

        $statusCode = $response->getStatusCode();

        $body = $response->getBody(); //获取响应体，对象
        $bodyStr = (string) $body; //对象转字串

        stdLog()->debug('HttpHelper json request：', [$url, $param, $headers, $bodyStr]);

        if ($statusCode != 200) {
            throw new Exception('请求接口返回失败（' . $statusCode . '）：' . $bodyStr);
        }

        return $bodyStr;
    }

    /**
     * form请求接口.
     * @throws Exception
     */
    public static function formRequest(string $url, array $param = [], string $method = 'post', array $headers = []): string
    {
        $clientHttp = new Client();
        if ($method == 'post') {
            try {
                $response = $clientHttp->post($url, [
                    'form_params' => $param,
                    'headers' => $headers,
                ]);
            } catch (GuzzleException $e) {
                stdLog()->error('HttpHelper form request error：', [$url, $param, $headers, $e->getMessage()]);
                throw new Exception('请求接口失败：' . substr($e->getMessage(), 0, 80));
            }
        } else {
            try {
                $response = $clientHttp->get($url, [
                    'query' => $param,
                    'headers' => $headers,
                ]);
            } catch (GuzzleException $e) {
                stdLog()->error('HttpHelper form request error：', [$url, $param, $headers, $e->getMessage()]);
                throw new Exception('请求接口失败：' . substr($e->getMessage(), 0, 80));
            }
        }

        $statusCode = $response->getStatusCode();

        $body = $response->getBody(); //获取响应体，对象
        $bodyStr = (string) $body; //对象转字串

        stdLog()->debug('HttpHelper form request：', [$url, $param, $headers, $bodyStr]);

        if ($statusCode != 200) {
            throw new Exception('请求接口返回失败（' . $statusCode . '）：' . $bodyStr);
        }

        return $bodyStr;
    }
}
