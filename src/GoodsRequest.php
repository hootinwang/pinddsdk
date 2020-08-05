<?php
/**
 * 商品类
 * User: wuyan
 * Date: 2020/8/5
 * Time: 15:58
 */

namespace Pinduoduo;

use Pinduoduo\Exceptions\PinduoduoException;

class GoodsRequest
{
    public $api;
    public $parameter;

    /**
     * 获取商品基本信息
     * @param $goods_id
     * @throws PinduoduoException
     */
    public function getGoodsBasicInfo($goods_id)
    {
        if(empty($goods_id)){
            throw new PinduoduoException('goods_id cannot be empty');
        }

        if(is_array($goods_id)){
            $this->parameter['goods_id'] = json_encode($goods_id);
        }elseif(is_string($goods_id) || is_int($goods_id)){
            $this->parameter['goods_id'] = json_encode([$goods_id]);
        }else{
            throw new PinduoduoException('goods_id type error. Must be an array or string');
        }
        $this->api = 'pdd.ddk.goods.basic.info.get';
    }
}