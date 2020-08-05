<?php
namespace Pinduoduo\Exceptions;
/**
 * Created by PhpStorm.
 * User: wuyan
 * Date: 2020/8/5
 * Time: 15:10
 */
class PinduoduoException extends \Exception
{
    function __construct($message) {
        parent::__construct('拼多多SDK- ' . $message);
    }
}