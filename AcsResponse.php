<?php
/**
 * Created by PhpStorm.
 * User: davidxu
 * Date: 19/07/2017
 * Time: 5:54 PM
 */

namespace davidxu\alisms;


class AcsResponse
{
    private $code;
    private $message;

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}