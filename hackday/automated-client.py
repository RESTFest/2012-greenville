#!/usr/bin/python
import copy
import itertools
import sys
import unittest
import urlparse
import httplib2
# httplib2.debuglevel=1
import warnings
import urllib
from bs4 import BeautifulSoup

class Choice(set):

    """A selection in an HTML form."""

    def __init__(self, l, default):
        super(Choice, self).__init__(l)
        self.default = default

    def __str__(self):
        return self.default


class HTMLForm(object):

    """An HTML form."""

    def __init__(self, soup):
        self.fields = {}
        self.action = soup['action']
        self.method = soup.get('method', 'GET').upper()

        for field in soup("input"):
            self.fields[field['name']] = field.get('value', '')
        for field in soup("select"):
            default = None
            l = []
            for choice in field("option"):
                l.append(choice['value'])
                if choice.get('selected') == 'selected':
                    default = choice['value']
            self.fields[field['name']] = Choice(l, default)

    def submit(self, http):
        headers = {"Content-Type": "application/x-www-form-urlencoded"}
        return http.request(self.action, self.method, headers=headers,
                            body=urllib.urlencode(self.fields))


class ClientTestCase(unittest.TestCase):

    UID_COUNTER = 0

    @classmethod
    def global_setup(cls):
        cls.http = httplib2.Http()

        # Retrieve a representation of the home page. Subclasses will
        # use this to navigate to the resource they want to test.
        response, data = cls.http.request(cls.ROOT)
        cls.homepage = cls.parse(data)

        users_rel = "http://helpdesk.hackday.2012.restfest.org/rels/users"
        tickets_rel = "http://helpdesk.hackday.2012.restfest.org/rels/tickets"
        changes_rel = "http://helpdesk.hackday.2012.restfest.org/rels/changes"

        # Isolate the HTML forms
        cls._ticket_list_form = cls.homepage.find("form", rel=tickets_rel)
        cls._user_list_form = cls.homepage.find("form", rel=users_rel)
        cls._change_feed_form = cls.homepage.find("form", rel=changes_rel)

        # Get the URLs to other resources
        cls.ticket_list_url = None
        link = cls.homepage.find("link", rel=tickets_rel)
        if link is not None:
            cls.ticket_list_url = link['href']

        cls.user_list_url = None
        link = cls.homepage.find("link", rel=users_rel)
        if link is not None:
            cls.user_list_url = link['href']

        cls.change_feed_url = None
        link = cls.homepage.find("link", rel=changes_rel)
        if link is not None:
            cls.change_feed_url = link['href']

    @classmethod
    def request(cls, url, *args, **kwargs):
        url = urlparse.urljoin(cls.ROOT, url)
        return cls.http.request(url, *args, **kwargs)

    @property
    def ticket_list_form(self):
        return HTMLForm(self._ticket_list_form)

    @classmethod
    def post_new_ticket(cls, fetch_representation=True, **kwargs):
        body = """<ticket
        xmlns="urn:org.restfest.2012.hackday.helpdesk.ticket"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:comments="urn:org.restfest.2012.hackday.helpdesk.comments">

<summary>%(summary)s</summary>
<description>%(description)s</description>
</ticket>
"""
        for i in ("summary", "description"):
            if i not in kwargs:
                kwargs[i] = cls.uid()
        return cls.post_ticket(body % kwargs, fetch_representation)

    @classmethod
    def post_ticket(cls, body, fetch_representation=True):
        headers = {"Content-Type": "application/vnd.org.restfest.2012.hackday+xml"}
        response, data = cls.request(
            cls.ticket_list_url, "POST", headers=headers, body=body)

        if fetch_representation:
            if response['status'] != '201' or 'location' not in response:
                return response, data, None
            location = response['location']
            response2, representation = cls.request(location, "GET")
            return response, data, representation
        else:
            return response, data

    @classmethod
    def put_ticket(cls, soup):
        """PUT a (presumably modified) ticket back to its address."""
        url = soup.find("link", rel="self")['href']
        body = soup.encode()
        headers = {"Content-Type": "application/vnd.org.restfest.2012.hackday+xml"}
        response, content = cls.request(
            url, "PUT", headers=headers, body=body)

        if int(response['status']) / 100 == 2:
            new_response, new_content = cls.request(url, "GET")
            return response, content, new_content
        else:
            return response, content, None

    @classmethod
    def parse(self, data):
        return BeautifulSoup(data, "xml")

    @classmethod
    def sample_file(cls, name):
        return "media_type/examples/%s" % (
                name)

    @classmethod
    def sample_ticket(cls):
        return cls.parse(open(cls.sample_file("ticket.xml")))

    @classmethod
    def uid(self):
        self.UID_COUNTER += 1
        return str(self.UID_COUNTER)

    def assertTag(self, soup, *args, **kwargs):
        self.assertNotEquals(None, soup.find(*args, **kwargs))

    def assertValidTicket(self, soup):
        for tag in ['created_at', 'updated_at', 'summary', 'description', 'state']:
            self.assertTag(soup, tag)

        self.assertTag(soup, 'link', rel='self')

        self.assertTrue(soup.state.string in ['open', 'closed'])

class TestHomePage(ClientTestCase):

    def test_homepage_contains_self_link(self):
        self.assertNotEquals(
            None,
            self.homepage.link(rel="self"))

    def test_homepage_contains_tickets_form(self):
        self.assertTag(
            self.homepage, "form",
            rel="http://helpdesk.hackday.2012.restfest.org/rels/tickets")

    def test_homepage_contains_user_form(self):
        self.assertTag(
            self.homepage, "form",
            rel="http://helpdesk.hackday.2012.restfest.org/rels/users")

class TestTicketList(ClientTestCase):

    @classmethod
    def setUpClass(cls):
        # Before getting the list, post a number of new tickets to
        # increase the chances that there will be 'next' and 'last'
        # links in the list.
        for i in range(0,6):
            cls.post_new_ticket(False)

        # Get the ticket list.
        response, data = cls.request(cls.ticket_list_url)
        cls.tickets = cls.parse(data)

    def assertValidTicketList(self, soup):
        self.assertTag(soup, "tickets")
        for ticket in self.tickets:
            self.assertValidTicket(ticket)

    def test_follow_next_links(self):
        next_link = self.tickets.find("link", rel="next", recursive=False)
        if next_link is None:
            warnings.warn("No next link in initial ticket list, can't test following.")
        while next_link is not None:
            response, next_page = self.request(next_link['href'])
            self.assertEqual(200, response['status'])
            soup = self.parse(next_page)
            self.assertValidTicketList(soup)
            next_link = soup.find("link", rel="next")

    def test_follow_last_link(self):
        last_link = self.tickets.find("link", rel="last", recursive=False)
        if last_link is None:
            warnings.warn("No last link in initial ticket list, can't test following.")
        else:
            response, last_page = self.request(last_link['href'])
            self.assertEqual(200, response['status'])
            soup = self.parse(last_page)
            self.assertValidTicketList(soup)

    def test_follow_self_link(self):
        last_link = self.tickets.find("link", rel="self", recursive=False)
        if last_link is None:
            warnings.warn("No self link in initial ticket list, can't test following.")
        else:
            response, same_page = self.request(last_link['href'])
            self.assertEqual(200, response['status'])
            soup = self.parse(same_page)
            self.assertValidTicketList(soup)

    def test_ticket_list_form_submission(self):
        form = self.ticket_list_form
        # Test every value for sort_field, with every value for sort_order
        field_choice = form.fields['sort_field']
        order_choice = form.fields['sort_order']
        for field in field_choice:
            for order in order_choice:
                form.fields['sort_field'] = field
                form.fields['sort_order'] = field
                response, data = form.submit(self.http)
                self.assertValidTicketList(self.parse(data))

    def test_bad_result_page(self):
        form = self.ticket_list_form
        for value in ("0", "-1", "notanumber"):
            form.fields['result_page'] = value
            response, data = form.submit(self.http)
            self.assertEqual(400, response['status'])

    def test_bad_items_per_page(self):
        form = self.ticket_list_form
        for value in ("0", "-1", "notanumber"):
            form.fields['items_per_page'] = value
            response, data = form.submit(self.http)
            self.assertEqual(400, response['status'])

class TestTicket(ClientTestCase):

    def test_post_garbage_xml(self):
        response, body = self.post_ticket("<this is garbage", False)
        self.assertEqual("400", response['status'])

    def test_post_empty_ticket(self):
        response, body = self.post_ticket("<ticket></ticket>", False)
        self.assertEqual("400", response['status'])

    def test_post_ticket_with_no_summary(self):
        response, body = self.post_ticket(
            "<ticket><description>foo</description></ticket>", False)
        self.assertEqual("400", response['status'])

    def test_post_ticket_with_summary_only(self):
        response, body = self.post_ticket(
            "<ticket><summary>foo</summary></ticket>", False)
        self.assertEqual("200", response['status'])

    def test_post_ticket_gives_location_of_ticket(self):
        response, body = self.post_new_ticket(False)
        self.assertEqual('201', response['status'])
        self.assertTrue("location" in response)
        location = response['location']

        # Get the location and make sure it's a ticket.
        response, data = self.request(location)
        self.assertEqual('200', response['status'])
        self.assertEqual(
            "application/vnd.org.restfest.2012.hackday+xml",
            response['content-type'])
        ticket = self.parse(data)
        self.assertValidTicket(ticket)

        # Ensure that certain fields were created
        self.assertTag(ticket, "created_at")
        self.assertTag(ticket, "updated_at")
        self.assertTag(ticket, "state")

    def test_delete_ticket(self):
        response, body = self.post_new_ticket(False)
        import pdb; pdb.set_trace()
        location = response['location']
        response, data = self.request(location, 'DELETE')
        self.assertEqual('200', response['status'])
        response, data = self.request(location, 'GET')
        self.assertEqual('404', response['status'])

    def test_modify_state(self):
        response, body, representation = self.post_new_ticket(True)
        ticket = self.parse(representation)

        # The sample ticket starts out open. Close it.
        ticket.state.string = "closed"
        response, content, new_representation = self.put_ticket(ticket)
        soup = self.parse(new_representation)
        self.assertEqual("closed", soup.state.string)

        # Reopen it.
        ticket.state.string = "open"
        response, content, new_representation = self.put_ticket(ticket)
        soup = self.parse(new_representation)
        self.assertEqual("open", soup.state.string)

    def test_set_bad_state(self):
        response, body, representation = self.post_new_ticket(True)
        ticket = self.parse(representation)
        ticket.state.string = "nosuchstate"
        response, content, ignore = self.put_ticket(ticket)
        self.assertEquals('400', response['status'])

    def test_remove_all_tags(self):
        ticket = self.sample_ticket()
        response, content, new_ticket = self.post_ticket(ticket.encode())
        soup = self.parse(new_ticket)
        for tag in soup.find_all("tag"):
            tag.extract()
        response, content, new_representation = self.put_ticket(soup)
        self.assertEquals([], soup.find_all("tag"))

    def test_set_duplicate_tag(self):
        response, content, representation = self.post_new_ticket()
        soup = self.parse(representation)
        for i in range(0,3):
            tag = soup.new_tag("tag")
            tag.string = "foo"
            soup.ticket.append(tag)
        response, content, new_representation = self.put_ticket(soup)
        self.assertEqual(1, len(soup.find_all("tag")))

class TestChangeList(ClientTestCase):

    @classmethod
    def setUpClass(cls):
        # Get the change feed.
        response, data = cls.request(cls.change_feed_url)
        cls.changes = cls.parse(data)

    def assertValidEvent(self, soup):
        for key in ['timestamp', 'type']:
            self.assertNotEqual(None, soup.get(attr, None))

    def assertValidChangeList(self, soup):
        self.assertEqual("changes", soup.find(True).name)
        for tag in ['from', 'to']:
            self.assertTag(soup, tag)
        self.assertTag(soup, 'link', rel='self')

        for event in soup.find_all("event"):
            self.assertValidEvent(event)

    def test_change_list_is_valid(self):
        self.assertValidChangeList(self.changes)

    def test_follow_next_links(self):
        next_link = self.changes.find("link", rel="next")
        if next_link is None:
            warnings.warn("No next link in initial change list, can't test following.")
        while next_link is not None:
            response, next_page = self.request(next_link['href'])
            self.assertEqual(200, response['status'])
            soup = self.parse(next_page)
            self.assertValidChangeList(soup)
            next_link = soup.find("link", rel="next")

if __name__ == '__main__':

    if len(sys.argv) != 2:
        print "Usage: %s [service root URL]" % (sys.argv[0])
        sys.exit()

    ClientTestCase.ROOT = sys.argv[1]
    ClientTestCase.global_setup()

    loader = unittest.TestLoader()
    suite = unittest.TestSuite()
    for c in TestHomePage, TestTicketList, TestTicket, TestChangeList:
        tests = loader.loadTestsFromTestCase(c)
        suite.addTests(tests)

    unittest.TextTestRunner(verbosity=2).run(suite)
