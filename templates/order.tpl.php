<?php 
  declare(strict_types = 1); 

  require_once(__DIR__ . '/../database/order.class.php');
?>

<?php function drawOrders(array $orders, PDO $db) { ?>
    <section id="orders">
        <?php foreach ($orders as $order) {
            $items = $order->getItems($db);
            $buyer = $order->getBuyer();
            $date = htmlentities((string)$order->orderDate);
            $status = htmlentities((string)$order->status);
            ?>
            <article>
                <h2>Order #<?= $order->idOrder ?></h2>
                <p>Date: <?= $date ?></p>
                <p>Status: <span class="status <?= strtolower($status) ?>"><?= $status ?></span></p>
                <p>Total price: <?= htmlentities((string)$order->totalPrice) ?>€</p>
                <ul>
                    <?php foreach ($items as $item) { ?>
                        <?php
                        $quantity = array_count_values(array_map(function ($item) {
                            return $item->idItem;
                        }, $items));
                        ?>

                        <li> <a href="../pages/item.php?idItem=<?= $item->idItem ?>"><?= htmlentities($item->name) ?></a> x <?= $quantity[$item->idItem] ?> (<?= $item->price * $quantity[$item->idItem] ?>€)</li>
                    <?php } ?>
                </ul>
                <?php if($status === 'Pending') { ?>
                <form action="../actions/action_cancel_order.php" method="post">
                    <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                    <input type="hidden" name="idOrder" value="<?= $order->idOrder ?>">
                    <button type="submit">Cancel Order</button>
                </form>
                <?php } ?>
            </article>
        <?php } ?>
    </section>
<?php } ?>


<?php function drawOrdersToShip($db, $userId) { 
    $query =    "SELECT 
                O.idOrder,
                O.orderDate,
                CI.address,
                CI.city,
                CI.zipCode,
                OI.idItem,
                I.name AS itemName,
                I.brand,
                I.model,
                I.price,
                U.idUser AS buyerId,
                U.username AS buyerUsername,
                U.name AS buyerName,
                U.email AS buyerEmail,
                OI.sent
            FROM 
                Orders O
            JOIN 
                CheckoutInfo CI ON O.idOrder = CI.idOrder
            JOIN 
                OrderItems OI ON O.idOrder = OI.idOrder
            JOIN 
                Items I ON OI.idItem = I.idItem
            JOIN 
                Users U ON O.idBuyer = U.idUser
            WHERE 
                I.idSeller = ? AND
                O.status = 'Pending' AND
                OI.sent = 0";

    $stmt = $db->prepare($query);
    $stmt->execute(array($userId));
    $orders = $stmt->fetchAll();
    ?>
    <div id = "shipping-forms">
    <?php
    foreach ($orders as $order) {
        $orderId = $order['idOrder'];
        $itemId = $order['idItem'];
        ?>

        <article>
            <h2>Shipping Form for Order #<?= $orderId ?></h2>
            <p><strong>Item:</strong> <a href="../pages/item.php?idItem=<?= $order['idItem'] ?>"><?= htmlentities($order['itemName']) ?></a></p>
            <p><strong>Price:</strong> <?= htmlentities((string)$order['price']) ?>€</p>
            <p><strong>Buyer:</strong> <a href="../pages/user-profile.php?idUser=<?= $order['buyerId'] ?>"><?= htmlentities($order['buyerName']) ?></a></p>
            <a href="../pages/print_shipping_form.php?orderId=<?= $order['idOrder'] ?>&itemId=<?= $order['idItem'] ?>&csrf=<?=$_SESSION['csrf']?>" target="_blank">Print Shipping Form</a>
            <form method="post" action="../actions/action_item_sent.php">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <input type="hidden" name="idOrder" value="<?= $orderId ?>">
                <input type="hidden" name="idItem" value="<?= $itemId ?>">
                <button type="submit">Mark as Sent</button>
            </form>
        </article>
    <?php } ?>
    </div>
<?php } ?>