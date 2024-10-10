<?php 
    declare(strict_types = 1);
    require_once(__DIR__ . '/../database/item.class.php');
    require_once(__DIR__ . '/../database/users.class.php'); 

?>

<?php function drawChats($pairs, $db) { ?>
    <section class="chats">
        <h2>Chats</h2>
        <?php
            foreach ($pairs as $pair) {
                echo "<article>";
                $otherUserId = (int)$pair['otherUserId'];
                $itemId = (int)$pair['idItem'];

                $otherUser = User::getUserById($db, $otherUserId);
                $item = Item::getItemById($db, $itemId);
                $otherUserImage = $otherUser->getProfileImage($db);

                $otherUserImageSrc = htmlentities((string)$otherUserImage);
                $otherUserName = htmlentities((string)$otherUser->name);
                $itemName = htmlentities((string)$item->name);

                if ($otherUserImageSrc) {
                    echo "<a href='../pages/chat_messages.php?otherUserId={$otherUserId}&itemId={$itemId}'><img src='{$otherUserImageSrc}' alt='User Image'></a>";
                }
                else {
                    echo "<a href='../pages/chat_messages.php?otherUserId={$otherUserId}&itemId={$itemId}'><img src='../docs/images/default_profile_picture.png' alt='User Image'></a>";
                }

                echo "<a class='user_select' href='../pages/chat_messages.php?otherUserId={$otherUserId}&itemId={$itemId}'>{$otherUserName} - {$itemName}</a>";
                echo "</article>";
            }
        ?>
    </section>
<?php } ?>

<?php function drawChatMessages($db, $chats, $userId, $otherUserId, $itemId) { ?>
    <section class="chat">
        <?php
            if (isset($_SESSION['message'])) {
                echo "<p>{$_SESSION['message']}</p>";
                unset($_SESSION['message']);
            }
        ?>
        <header>
            <?php
                $otherUser = User::getUserById($db, (int)$otherUserId);
                $otherUserImage = $otherUser->getProfileImage($db);
                $item = Item::getItemById($db, (int)$itemId);
                if ($otherUserImage) {
                    echo "<a href='../pages/user-profile.php?idUser={$otherUserId}'><img src='{$otherUserImage}' alt='User Image'></a>";
                } 
                else {
                    echo "<a href='../pages/user-profile.php?idUser={$otherUserId}'><img src='../docs/images/default_profile_picture.png' alt='User Image'></a>";
                }
            ?>
            <div>
                <h3>Chat with <?=User::getUserById($db,(int) $otherUserId)->name?></h3>
                <h4><?=$item->name?></h4>
            </div>
        </header>
            <div class="messages-container">
            <?php foreach ($chats as $chat) { ?>
                <div class="message_<?php echo $chat['idSender'] === $userId ? 'outgoing' : 'incoming'; ?>">
                    <h4><?= htmlentities(User::getUserById($db, $chat['idSender'])->name) ?></h4>
                    <p class="message_content"><?= htmlentities($chat['message']) ?></p>
                    <p class="hint"><?= htmlentities(date('d/m/Y H:i', strtotime($chat['timestamp']))) ?></p>
                </div>
            <?php } ?>   
           </div>
            <form action="../actions/action_send_message.php" method="post" class="message-form">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <input type="hidden" name="otherUserId" value="<?php echo $otherUserId; ?>">
                <input type="hidden" name="itemId" value="<?php echo $itemId; ?>">
                <div class="message-input">
                    <input type="text" name="message" placeholder="Type your message here..." required>
                    <button type="submit"><img src="../docs/images/icon_send.svg" alt="Send"></button>
                </div>
            </form>

    </section>
<?php } ?>
