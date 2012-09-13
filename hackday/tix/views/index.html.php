<? include('_layout/header.html.php'); ?>

<p><a href="/tickets.php" rel="tickets">All Tickets</a></p>
<form method="GET" action="/tickets.php">
    <fieldset>
        <legend>Search Tickets</legend>
        <div class="field">
            <label for="sort_field">Sort Field</label>
            <select name="sort_field" id="sort_field">
                <option>created_date</option>
                <option>updated_date</option>
            </select>
        </div>
        <div class="field">
            <label for="sort_order">Sort Order</label>
            <select name="sort_order" id="sort_order">
                <option>asc</option>
                <option>desc</option>
            </select>
        </div>
        <div class="field">
            <label for="result_size">Result Size</label>
            <input name="result_size" id="result_size" value="10"/>
        </div>
        <div class="field">
            <label for="result_size">Result Page</label>
            <input name="result_page" id="result_page" value="1"/>
        </div>
        <div class="field">
            <input type="submit" />
        </div>
    </fieldset>
</form>

<p><a href="/users.php" rel="users">All Users</a></p>
<form method="GET" action="/users.php">
    <fieldset>
        <legend>Search Users</legend>
        <div class="field">
            <label for="user_name">User Name</label>
            <input name="user_name" id="user_name" value="10"/>
        </div>
        <div class="field">
            <label for="user_email">User Email</label>
            <input name="user_email" id="user_email" value="10"/>
        </div>
        <div class="field">
            <label for="sort_field">Sort Field</label>
            <select name="sort_field" id="sort_field">
                <option>name</option>
                <option>email</option>
            </select>
        </div>
        <div class="field">
            <label for="sort_order">Sort Order</label>
            <select name="sort_order" id="sort_order">
                <option>asc</option>
                <option>desc</option>
            </select>
        </div>
        <div class="field">
            <label for="result_size">Result Size</label>
            <input name="result_size" id="result_size" value="10"/>
        </div>
        <div class="field">
            <label for="result_size">Result Page</label>
            <input name="result_page" id="result_page" value="1"/>
        </div>
        <div class="field">
            <input type="submit" />
        </div>
    </fieldset>
</form>

<p><a href="/changes.php" rel="changes">All Changes</a></p>
<form method="GET" action="/changes.php">
    <fieldset>
        <legend>Search Changes</legend>
        <div class="field">
            <label for="from">From</label>
            <input name="from" id="from" value="2012-09-13T00:00Z" />
        </div>
        <div class="field">
            <label for="to">To</label>
            <input name="to" id="to" value="2012-09-13T23:59Z" />
        </div>
        <div class="field">
            <input type="submit" />
        </div>
    </fieldset>
</form>

<? include('_layout/footer.html.php'); ?>