<? include('_layout/header.html.php'); ?>

<ul class="actions">
    <li><a href="/tickets.php" class="back">Back to All</a></li>
</ul>

<form method="POST" action="/tickets.php">
    <fieldset>
        <legend>Create Ticket</legend>
        <div class="field">
            <label for="author_name">Author Name</label>
            <input class="text" name="author[name]" id="author_name" value="<?=$name?>"/>
        </div>
        <div class="field">
            <label for="author_email">Author Email</label>
            <input class="text" name="author[email]" id="author_email" value="<?=$email?>"/>
        </div>
        <div class="field">
            <label for="ticket_summary">Ticket Summary</label><br/>
            <input class="text" name="ticket[summary]" id="ticket_summary"/>
        </div>
        <div class="field">
            <label for="ticket_description">Ticket Description</label><br/>
            <textarea class="text" name="ticket[description]" id="ticket_description" style="width: 480px; height: 240px;"></textarea>
        </div>
        <div class="field">
            <label for="assignee_name">Assignee Name</label>
            <input class="text" name="assignee[name]" id="assignee_name" value=""/>
        </div>
        <div class="field">
            <label for="assignee_email">Assignee Email</label>
            <input class="text" name="assignee[email]" id="assignee_email" value=""/>
        </div>
        <div class="field">
            <input type="submit" />
        </div>
    </fieldset>
</form>

<? include('_layout/footer.html.php'); ?>