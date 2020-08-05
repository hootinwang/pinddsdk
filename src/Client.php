<?php
/**
 * Created by PhpStorm.
 * User: HootinWang
 * Date: 2020/8/5
 * Time: 15:26
 */
namespace Pinduoduo;

use Pinduoduo\Exceptions\PinduoduoException;

class Client
{
    //  生产环境url
    private $api_url = 'https://gw-api.pinduoduo.com/api/router';

    //  client_secret
    private $client_secret = null;

    //  返回数据类型 JSON|XML 默认: JSON
    public $data_type = 'JSON';

    //  版本
    public $version = 'V1';

    //  当前环境 test测试环境;pro生产环境
    public $env = 'test';

    private $requestParameters = [];

    /**
     * 设置 clientId
     * @param $clientId
     * @throws PinduoduoException
     */
    public function setClientId($clientId)
    {
        if(empty($clientId)){
            throw new PinduoduoException('ClientId cannot be empty');
        }

        $this->requestParameters['client_id'] = $clientId;
    }

    /**
     * 设置返回数据类型
     * @param string $dataType
     * @throws PinduoduoException
     */
    public function setDataType($dataType = 'JSON')
    {
        if(!in_array($dataType,['JSON','XML'],true)){
            throw new PinduoduoException('The dataType must be JSON or XML');
        }

        $this->data_type = $dataType;
    }

    /**
     * 设置 accessToken
     * @param $accessToken
     * @throws PinduoduoException
     */
    public function setAccessToken($accessToken)
    {
        if(empty($accessToken)){
            throw new PinduoduoException('accessToken cannot be empty');
        }

        $this->requestParameters['access_token'] = $accessToken;
    }

    /**
     * 设置 client_secret
     * @param $client_secret
     * @throws PinduoduoException
     */
    public function setClientSecret($client_secret)
    {
        if(empty($client_secret)){
            throw new PinduoduoException('client_secret cannot be empty');
        }
        $this->client_secret = $client_secret;
    }

    /**
     * 发起请求
     * @param $api
     * @param $parameter
     * @param bool $isAccessToken
     */
    public function getResponse($request)
    {

        if(!isset($request->api)){
            throw new PinduoduoException('api undefined');
        }

        if(empty($request->api)){
            throw new PinduoduoException('api cannot be empty');

        }

        if(!isset($request->parameter)){
            throw new PinduoduoException('parameter undefined');
        }
        if(!is_array($request->parameter)){
            throw new PinduoduoException('parameter must be an array');
        }

        if($request->parameter){
            foreach($request->parameter as $k=>$v){
                $this->requestParameters[$k] = $v;
            }
        }
        $this->requestParameters['data_type'] = $this->data_type;
        $this->requestParameters['type'] = $request->api;
        $this->requestParameters['timestamp'] = strval(time());
        $this->requestParameters['data_type'] = $this->data_type;
        $this->requestParameters['version'] = $this->version;

        //  生成签名
        $this->requestParameters['sign'] = $this->getSign($this->requestParameters);
        
        //  发送请求
        $reqParam = '';
        foreach($this->requestParameters as $k=>$v){
            $reqParam .= $k . '=' . $v . '&';
        }

        $reqParam = '?' . substr($reqParam,0,-1);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',$this->api_url . $reqParam);

        if($response->getStatusCode() <> 200){
            throw new PinduoduoException('api: ' . $request->api . ', responseHttpCode: ' . $response->getStatusCode() . ', body: ' . $response->getBody()->getContents());
        }

        $resData = json_decode($response->getBody()->getContents(),true);
        if(empty($resData)){
            throw new PinduoduoException('返回数据解析失败');

        }

        if(isset($resData['error_response']['error_code'])){
            throw new PinduoduoException('api返回错误, json:' . json_encode($resData,JSON_UNESCAPED_UNICODE));
        }

        return $resData;
    }

    /**
     * 生成请求签名
     * @param $param
     * @return string
     * @throws PinduoduoException
     */
    private function getSign($param)
    {
        if(empty($param) || !is_array($param)){
            throw new PinduoduoException('cannot be empty. It must be an array');
        }
        if(empty($this->client_secret)){
            throw new PinduoduoException('client_secret cannot be empty');
        }

        ksort($param);
        $sign = '';

        foreach($param as $k=>$v){
            $sign .= $k . $v;
        }

        $sign = $this->client_secret . $sign . $this->client_secret;

        $sign = md5($sign);

        return $sign;
    }
}