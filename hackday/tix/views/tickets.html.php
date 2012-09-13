<? include('_layout/header.html.php'); ?>

<ul class="actions">
    <li><a href="/" class="back">Back to Home</a></li>
    <li><a href="/ticket_new.php" class="create">Create Ticket</a></li>
</ul>

<ul class="tickets">
<? foreach($tickets as $ticket) {
    $rev = $ticket->lastRev();
?>
    <li>
        <a href="/ticket.php?id=<?=$ticket->id?>" rel="ticket" class="summary"><?=$rev->summary?></a>
        <p class="description"><?=$rev->description?></p>
    </li>
<? } ?>
</ul>

<? include('_layout/footer.html.php'); ?>