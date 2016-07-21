<?php

/**
 * @copyright (c) 2916, Ryan [CHAOMA.ME]
 */

namespace jobs;

use core\Log;
use core\Core;
use TimeCheer\Util\Curl;

abstract class OAOJobBase extends JobBase
{

    /**
     * 成功
     */
    const CODE_SUCC = '1';

    /**
     * 失败
     */
    const CODE_FAIL = '0';

    /**
     * 内部授权吗
     * @var string
     */
    protected $accessToken;

    /**
     * url
     * @var string
     */
    protected $host;

    public function __construct()
    {
        $config = Core::c('CMB');
        $this->accessToken = $config['access_token'];
        $this->host = $config['host'];
    }

    /**
     * 请求接口 cli样式
     * @param string $api
     * @param array $params
     * @return type
     */
    public function request($api, array $params)
    {
        return $this->requestInCli($api, $params);
    }

    /**
     * 以Cli模式本地调用接口,只能支持GET参数
     * 通过php exec 函数执行本地代码
     * 如php index.php Api/Addon/DiySpecial/Speciaxxxx/xxx/id/111
     * @param string $api
     * @param array $params
     * @return mixed
     */
    public function requestInCli($api, array $p_params)
    {
        $oao_env = Core::C('OAOENV');
        $cmd = "cd {$oao_env['root_path']}; ";
        $cmd .= "php index.php api/" . trim($api, '/') . '/';
        $params = array_merge($p_params, array('access_token' => $this->accessToken));
        foreach ($params as $key => $value) {
            $cmd .= "{$key}/{$value}/";
        }
        Log::record('requestInCli', $cmd);
        $response = null;
        $status = null;
        exec($cmd, $response, $status);
        $response = implode("\r\n", $response);
        Log::record('requestInCli', $status . var_export($response, true));
        $response = json_decode($response, true);
        if (!isset($response['ret']) || ($response['ret'] != self::CODE_SUCC)) {
            $this->errId = -1000;
            if (isset($response['error'])) {
                $this->errId = $response['error'];
                $this->errMsg = $response['error_description'];
            }

            return false;
        }

        return $response;
    }

    /**
     * 请求数据
     * @param string $api api
     * @param array $params 键值对数组
     * @return boolean|array
     */
    public function requestHttp($api, array $params)
    {
        $curl = new Curl();
        $curl->post($this->host . '/' . $api, array_merge($params, array('access_token' => $this->accessToken)));
        Log::record('curl_post', [$this->host . '/' . $api, $curl->response]);
        if ($curl->error_code) {
            $this->errId = $curl->error_code;
            $this->errMSg = $curl->error_message;
            return false;
        }
        $response = $curl->response;
        $data = json_decode($response, true);
        if (!isset($data['ret']) || ($data['ret'] != self::CODE_SUCC)) {
            $this->errId = $data['error'];
            $this->errMSg = $data['error_description'];
            return false;
        }
        return $data['data'];
    }

}
