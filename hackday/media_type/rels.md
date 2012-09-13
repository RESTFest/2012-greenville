# Link relations in the 2012 REST Fest Help Desk Media Type

## Common

See http://www.iana.org/assignments/link-relations/link-relations.xml

## Links

### http://helpdesk.hackday.2012.restfest.org/rels/ticket
An individual ticket

### http://helpdesk.hackday.2012.restfest.org/rels/comment
An individual comment

### http://helpdesk.hackday.2012.restfest.org/rels/user
An individual user

## Forms

### http://helpdesk.hackday.2012.restfest.org/rels/tickets
A collection of tickets.

Fields:

* `sort_field`: either of
    1. `created_at` for ordering by ticket creation datetime
    1. `updated_at` for ordering by ticket update datetime
* `sort_order`: either of
    1. `asc` for ascending order
    1. `desc` for descending order

### http://helpdesk.hackday.2012.restfest.org/rels/users
A collection of users.

Fields:

* `user_name`: for searching by user name
* `user_email`: for searching by user email
* `sort_field`: either of
    1. `created_at` for ordering by ticket creation datetime
    1. `updated_at` for ordering by ticket update datetime
* `sort_order`: either of
    1. `asc` for ascending order
    1. `desc` for descending order
