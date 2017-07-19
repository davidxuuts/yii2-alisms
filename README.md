An Aliyun Short Message Library Package
=======================================
An Aliyun Short Message Library Package

Description
----------

This is for aliyun Short Message send and query purpose.

Based on Alipay SMS API 20170525.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist davidxu/yii2-alisms "*"
```

or add

```
"davidxu/yii2-alisms": "*"
```

to the require section of your `composer.json` file, and run

```
"php composer.phar update"
```


Usage
-----
To send sms, follow the following example

```
use davidxu\alisms\Sms;

class AliSms extends Controller
{
    public function actionSend()
    {

        $alisms = new Sms();
        $alisms->accessKeyId = 'your-alismsAccessKeyId';
        $alisms->accessKeySecret = 'your-alismsAccessKeySecret';
        $alisms->signName = 'your-smsSignName';

        $mobiles = [
            '18800000000',
            '18800000002',
        ];
        $response = $alisms->sendSms('your-template-code', $mobiles);
        return $response;
    }
    
    public function actionQuery()
    {
        $alisms = new Sms();
        $alisms->accessKeyId = Yii::$app->params['alismsAccessKeyId'];
        $alisms->accessKeySecret = Yii::$app->params['alismsAccessKeySecret'];
        $alisms->signName = Yii::$app->params['smsSignName'];
        
        $mobile = '18800000001';
        $queryDate = '20170720';
        $response = $alisms->querySendDetails($mobile, $queryDate);
        return $response;
    }
}
```

Contact
-----
If anything please contact at david.xu.uts@163.com

Have fun.