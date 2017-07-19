<?php
/**
 * Created by PhpStorm.
 * User: davidxu
 * Date: 19/07/2017
 * Time: 5:31 PM
 */
namespace davidxu\alisms;

use davidxu\alisms\profile\DefaultProfile;
use davidxu\alisms\request\QuerySendDetailsRequest;
use davidxu\alisms\request\SendSmsRequest;

class Sms
{
    public $accessKeyId;
    public $accessKeySecret;
    public $product = 'Dysmsapi';
    public $domain = 'dysmsapi.aliyuncs.com';
    public $region = 'cn-hangzhou';
    public $signName;

    /**
     * @param string $templateCode
     * @param array $phoneNumbers
     * @param array $templateParams
     * @param string $outId
     * @return mixed|\SimpleXMLElement
     */
    public function sendSms($templateCode, $phoneNumbers, $templateParams, $outId) {
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
        if (is_array($templateParams)) {
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

        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);
        $acsClient= new DefaultAcsClient($profile);

        $request = new QuerySendDetailsRequest();

        //必填-短信接收号码
        $request->setPhoneNumber($phoneNumber);

        //选填-短信发送流水号
        $request->setBizId($bizId);
        //必填-短信发送日期，支持近30天记录查询，格式yyyyMMdd

        $request->setSendDate($date);
        $request->setPageSize($pageSize);
        //必填-当前页码
        $request->setContent($page);

        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);

        return $acsResponse;
    }
}