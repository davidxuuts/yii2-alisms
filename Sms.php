<?php
/**
 * Created by PhpStorm.
 * User: davidxu
 * Date: 19/07/2017
 * Time: 5:31 PM
 */
namespace davidxu\alisms;

use davidxu\alisms\profile\DefaultProfile;
use davidxu\alisms\regions\Endpoint;
use davidxu\alisms\regions\EndpointProvider;
use davidxu\alisms\regions\ProductDomain;
use davidxu\alisms\request\QuerySendDetailsRequest;
use davidxu\alisms\request\SendSmsRequest;

class Sms
{
    public function __construct()
    {
        $endpoint_filename = dirname(__FILE__) . DIRECTORY_SEPARATOR
            . 'regions' . DIRECTORY_SEPARATOR . 'endpoints.xml';
        $xml = simplexml_load_string(file_get_contents($endpoint_filename));
        $json = json_encode($xml);
        $jsonArray = json_decode($json, TRUE);
        $endpoints = array();

        foreach ($jsonArray["Endpoint"] as $json_endpoint) {
            # pre-process RegionId & Product
            if (!array_key_exists("RegionId", $json_endpoint["RegionIds"])) {
                $region_ids = array();
            } else {
                $json_region_ids = $json_endpoint['RegionIds']['RegionId'];
                if (!is_array($json_region_ids)) {
                    $region_ids = array($json_region_ids);
                } else {
                    $region_ids = $json_region_ids;
                }
            }

            if (!array_key_exists("Product", $json_endpoint["Products"])) {
                $products = array();
            } else {
                $json_products = $json_endpoint["Products"]["Product"];

                if (array() === $json_products or !is_array($json_products)) {
                    $products = array();
                } else if (array_keys($json_products) !== range(0, count($json_products) - 1)) {
                    # array is not sequential
                    $products = array($json_products);
                } else {
                    $products = $json_products;
                }
            }

            $product_domains = array();
            foreach ($products as $product) {
                $product_domain = new ProductDomain($product['ProductName'], $product['DomainName']);
                array_push($product_domains, $product_domain);
            }

            $endpoint = new Endpoint($region_ids[0], $region_ids, $product_domains);
            array_push($endpoints, $endpoint);
        }

        EndpointProvider::setEndpoints($endpoints);
    }

    public $accessKeyId;
    public $accessKeySecret;
    public $signName;
    public $product = 'Dysmsapi';
    public $domain = 'dysmsapi.aliyuncs.com';
    public $region = 'cn-hangzhou';

    /**
     * @param string $templateCode
     * @param array $phoneNumbers
     * @param array $templateParams
     * @param string $outId
     * @return mixed|\SimpleXMLElement
     */
    public function sendSms($templateCode, $phoneNumbers, $templateParams = [], $outId = '') {
        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);

        $acsClient= new DefaultAcsClient($profile);

        $request = new SendSmsRequest();

        //必填-短信接收号码
        $request->setPhoneNumbers(implode(',', $phoneNumbers));

        //必填-短信签名
        $request->setSignName($this->signName ? : '宠吧');

        //必填-短信模板Code
        $request->setTemplateCode($templateCode);

        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        if (is_array($templateParams) && $templateParams) {
            $request->setTemplateParam(json_encode($templateParams));
        }

        if ($outId) {
            //选填-发送短信流水号
            $request->setOutId($outId);
        }

        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);

        return $acsResponse;
    }

    /**
     * @param string $date
     * @param string $phoneNumber
     * @param string $bizId
     * @param integer $pageSize
     * @param integer $page
     * @return mixed|\SimpleXMLElement
     */
    public function querySendDetails($phoneNumber, $date, $bizId = '', $pageSize = 10, $page = 1) {

        //初始化访问的acsClient
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);

        $acsClient = new DefaultAcsClient($profile);
        $request = new QuerySendDetailsRequest();

        //必填-短信接收号码
        $request->setPhoneNumber($phoneNumber);

        //选填-短信发送流水号
        if ($bizId) {
            $request->setBizId($bizId);
        }

        //必填-短信发送日期，支持近30天记录查询，格式yyyyMMdd

        $request->setSendDate($date);
        $request->setPageSize($pageSize);
        //必填-当前页码
        $request->setCurrentPage($page);
//        $request->setContent($page);
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);

        return $acsResponse;
    }
}