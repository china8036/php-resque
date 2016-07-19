<?php

/**
 * Oauth2 请求端封装
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 * 
 */
namespace TimeCheer\OAuth2Client;

use TimeCheer\Util\Curl;

class Client
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

    /**
     * 错误码
     * @var type 
     */
    protected $errId = null;

    /**
     * 错误信息
     * @var string 
     */
    protected $errMSg = '';

    /**
     * 构造方法
     * @param type $host
     * @param type $access_token
     */
    public function __construct($host, $access_token)
    {
        $this->host = $host;
        $this->accessToken = $access_token;
    }

    /**
     * 请求数据
     * @param string $api api
     * @param array $params 键值对数组
     * @return boolean|array
     */
    public function request($api, array $params)
    {
        $curl = new Curl();
        $curl->post($this->host . '/' . $api, array_merge($params, array('access_token' => $this->accessToken)));
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

    /**
     * 得到错误id
     * @return int 
     */
    public function getErrorId()
    {
        return $this->errId;
    }

    /**
     * 得到错误信息
     * @return sting
     */
    public function getErrorMsg()
    {
        return $this->errMSg;
    }

}
