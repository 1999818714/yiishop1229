<table border="1">
    <tr>
        <th>ID</th>
        <th>收货人</th>
        <th>价格</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <tr>
        <td><?=$order->id?></td>
        <td><?=$order->name?></td>
        <td><?=$order->price?></td>
        <td><?=$order->status?></td>
        <td><?=\yii\helpers\Html::a('微信支付',['index/wechat-pay','id'=>$order->id])?></td>
    </tr>
</table>