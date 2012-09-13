<? include('_layout/header.html.php'); ?>

<ul class="actions">
    <li><a href="/tickets.php" class="back">Back to All</a></li>
    <li><a href="/ticket_edit.php?id=<?=$ticket->id?>" class="edit">Edit Ticket</a></li>
</ul>

<h2 class="summary"><?=$rev->summary?></h2>
<p class="description"><?=$rev->description?></p>
<p><span class="created_at"><?=$ticket->created_date->format(\DateTime::ISO8601)?></span></p>
<p><span class="updated_at"><?=$rev->created_date->format(\DateTime::ISO8601)?></span></p>

<? include('_layout/footer.html.php'); ?>