<? include('_layout/header.html.php'); ?>

<ul class="actions">
    <li><a href="/ticket.php?id=<?=$ticket->id?>" class="back">Back to Ticket</a></li>
</ul>

<form method="POST" action="/ticket_revs.php?id=<?=$ticket->id?>">
    <input type="hidden" name="_method" value="PUT" />
    <fieldset>
        <legend>Edit Ticket #<?=$ticket->id?></legend>
        <div class="field">
            <label>Author Name</label><span><?=$ticket->author->name?></span>
        </div>
        <div class="field">
            <label>Author Email</label><span><?=$ticket->author->email?></span>
        </div>
        <div class="field">
            <label for="ticket_summary">Ticket Summary</label><br/>
            <input class="text" name="ticket[summary]" id="ticket_summary" value="<?=$rev->summary?>"/>
        </div>
        <div class="field">
            <label for="ticket_description">Ticket Description</label><br/>
            <textarea class="text" name="ticket[description]" id="ticket_description" style="width: 480px; height: 240px;"><?=$rev->description?></textarea>
        </div>
        <div class="field">
            <label for="assignee_name">Assignee Name</label>
            <input class="text" name="assignee[name]" id="assignee_name" value="<?=$rev->assignee->name?>"/>
        </div>
        <div class="field">
            <label for="assignee_email">Assignee Email</label>
            <input class="text" name="assignee[email]" id="assignee_email" value="<?=$rev->assignee->email?>"/>
        </div>
        <div class="field">
            <input type="submit" />
        </div>
    </fieldset>
</form>

<? include('_layout/footer.html.php'); ?>