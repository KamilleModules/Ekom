<?php


$sum = $v['orderDetails'];

?>
<div class="my-account">
    <div class="page-title">
        <h1>My Orders</h1>
    </div>


    <p>You have placed no orders.</p>

    <table style="width: 100%">
        <tr>
            <th>Date</th>
            <th>Order ref</th>
            <th>Summary</th>
            <th>Pdf</th>
        </tr>
        <?php foreach ($sum as $item): ?>
            <tr>
                <td><?php echo $item['date']; ?></td>
                <td><?php echo $item['ref']; ?></td>
                <td>
                    <table style="width: 100%">
                        <?php foreach ($item['products'] as $p): ?>
                            <tr>
                                <td><img width="40" src="<?php echo $p['image']; ?>"></td>
                                <td><a href="<?php echo $p['uri']; ?>"><?php echo $p['label']; ?> (réf: <?php echo $p['ref']; ?>)</a></td>
                                <td><?php echo $p['quantity']; ?> exemplaire(s)</td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
                <td><a href="#">Voir le pdf</a></td>
            </tr>
        <?php endforeach; ?>
    </table>


    <div class="buttons-set">
        <p class="back-link"><a href="http://ultimo.infortis-themes.com/demo/default/customer/account/">
                <small>&laquo;</small>
                Back</a></p>
    </div>
</div>