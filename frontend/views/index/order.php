<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile("@web/style/fillin.css");
?>
<div class="fillin w990 bc mt15">
    <form method="post">
    <div class="fillin_hd">
        <h2>填写并核对订单信息</h2>
    </div>

    <div class="fillin_bd">
        <!-- 收货人信息  start-->
        <div class="address">
            <h3>收货人信息 </h3>
            <div class="address_info">
                <?php foreach(Yii::$app->user->identity->addresses as $address):?>
                <p><input type="radio" value="<?=$address->id?>" name="address_id"><?=$address->name.' '.$address->tel.' '.$address->province.' '.$address->city.' '.$address->area.' '.$address->detail?></p>
                <?php endforeach;?>
                <p>
            </div>
        </div>
        <!-- 收货人信息  end-->

        <!-- 配送方式 start -->
        <div class="delivery">
            <h3>送货方式 </h3>
            <div class="delivery_select none" style="display: block;">
                <table>
                    <thead>
                    <tr>
                        <th class="col1">送货方式</th>
                        <th class="col2">运费</th>
                        <th class="col3">运费标准</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach(\frontend\models\Order::$deliveries as $id=>$item):?>
                    <tr class="">
                        <td>
                            <input type="radio" name="Order[delivery_id]" value="<?=$id?>" ><?=$item[0]?>
                        </td>
                        <td>￥<?=$item[1]?></td>
                        <td><?=$item[2]?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- 配送方式 end -->

        <!-- 支付方式  start-->
        <div class="pay">
            <h3>支付方式 </h3>


            <div class="pay_select none" style="display: block;">
                <table>
                    <tbody>
                    <?php foreach(\frontend\models\Order::$payments as $id=>$payment):?>
                    <tr class="">
                        <td class="col1"><input type="radio" value="<?=$id?>" name="Order[payment_id]"><?=$payment[0]?></td>
                        <td class="col2"><?=$payment[1]?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody></table>
            </div>
        </div>
        <!-- 支付方式  end-->



        <!-- 商品清单 start -->
        <div class="goods">
            <h3>商品清单</h3>
            <table>
                <thead>
                <tr>
                    <th class="col1">商品</th>
                    <th class="col3">价格</th>
                    <th class="col4">数量</th>
                    <th class="col5">小计</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($models as $model):?>
                <tr>
                    <td class="col1"><a href=""><?=\yii\helpers\Html::img(Yii::$app->params['backPicUrl'].$model['logo'])?></a>  <strong><a href=""><?=$model['name']?></a></strong></td>
                    <td class="col3">￥<?=$model['shop_price']?></td>
                    <td class="col4"> <?=$model['num']?></td>
                    <td class="col5"><span>￥<?=$model['shop_price']*$model['num']?></span></td>
                </tr>
                <?php endforeach;?>

                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">
                        <ul>
                            <li>
                                <span>4 件商品，总商品金额：</span>
                                <em>￥5316.00</em>
                            </li>
                            <li>
                                <span>返现：</span>
                                <em>-￥240.00</em>
                            </li>
                            <li>
                                <span>运费：</span>
                                <em>￥10.00</em>
                            </li>
                            <li>
                                <span>应付总额：</span>
                                <em>￥5076.00</em>
                            </li>
                        </ul>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <!-- 商品清单 end -->

    </div>

    <div class="fillin_ft">

        <input type="submit" value=""class="submit">
        <p>应付总额：<strong>￥5076.00元</strong></p>

    </div>
        </form>
</div>