<ticket
	xmlns="urn:org.restfest.2012.hackday.helpdesk.ticket"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:comments="urn:org.restfest.2012.hackday.helpdesk.comments"
	xmlns:user="urn:org.restfest.2012.hackday.helpdesk.user"
>
    <atom:link rel="self" href="/ticket.php?id=<?=$ticket->id?>" type="application/vnd.org.restfest.2012.hackday+xml" />

    <created_at><?=$ticket->created_date->format(\DateTime::ISO8601)?></created_at>
    <updated_at><?=$rev->created_date->format(\DateTime::ISO8601)?></updated_at>

    <summary><?=$rev->summary?></summary>

    <description><?=$rev->description?></description>

    <author>
        <user:user>
            <name><?=$ticket->author->name?></name>
            <email><?=$ticket->author->email?></email>
        </user:user>
    </author>
    <? if($rev->assignee) { ?>
    <assignee>
        <user:user>
            <atom:link rel="http://helpdesk.hackday.2012.restfest.org/rels/user" href="/user.php?id=<?=$rev->assignee->id?>" type="application/vnd.org.restfest.2012.hackday+xml" />
            <name><?=$rev->assignee->email?></name>
        </user:user>
    </assignee>
    <? } ?>
<? /*
    <tag>restfest</tag>
    <tag>breakfast</tag>
    <tag>coffee</tag>
    <tag>catering</tag>
*/ ?> 
    <state>open</state>
<? /*
    <atom:link rel="http://helpdesk.hackday.2012.restfest.org/rels/close" href="http://.../tickets/9172361" type="application/vnd.org.restfest.2012.hackday.helpdesk+xml" /> 
*/ ?>
<? /*
    <comments:comments count="2">
        <atom:link rel="http://helpdesk.hackday.2012.restfest.org/rels/comments" href="http://.../tickets/9172361/comments" type="application/vnd.org.restfest.2012.hackday.helpdesk+xml" />
    </comments:comments>
*/ ?>
</ticket>