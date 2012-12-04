var couchapp = require('couchapp')
  , path = require('path')
  ;

ddoc = 
  { _id:'_design/helpdesk'
  , rewrites : 
    [ {from:"/", to:'home.json'}
    , {from:"/api", to:'../../'}
    , {from:"/api/*", to:'../../*'}
    , {from:"/tickets", to:'_list/tickets/tickets',
    	query: {reduce: "false", include_docs: "true"}}
    , {from:"/tickets/*", to:'_show/ticket/*', method:'GET'}
    , {from:"/tickets/", to:'../../', method:'POST'}
    , {from:"/*", to:'*'}
    ]
  }
  ;

ddoc.views = {
		tickets: {
			map: function (doc) {
				if (doc.ticket) {
					emit(doc.ticket.updated_at, 1);
				}
			},
			reduce: "_count"
		}
};

ddoc.lists = {
		tickets: function(head, req) {
			if (!req.query.include_docs) {
			    return {code:418, body: "I'm a teapot"};
			}
		  var row,
		  	  output = 
		      { "tickets:tickets": {
		        "@xmlns": "urn:org.restfest.2012.hackday.helpdesk.ticket",
		        "@xmlns:tickets": "urn:org.restfest.2012.hackday.helpdesk.tickets",
		        "@xmlns:atom": "http://www.w3.org/2005/Atom",
		        "@xmlns:comments": "urn:org.restfest.2012.hackday.helpdesk.comments",
		        "@xmlns:user": "urn:org.restfest.2012.hackday.helpdesk.user",
		        "atom:link": [
		            {
	                "@rel": "self",
	                "@href": "http://bigbluehat.ic.tl/restfest_hackday_2012/_design/helpdesk/_rewrite/tickets?sort=time&order=desc&page=1",
	                "@type": "application/vnd.org.restfest.2012.hackday+xml"
		            },
		            {
	                "@rel": "next",
	                "@href": "http://bigbluehat.ic.tl/restfest_hackday_2012/_design/helpdesk/_rewrite/tickets?sort=time&order=desc&page=1",
	                "@type": "application/vnd.org.restfest.2012.hackday+xml"
		            },
		            {
	                "@rel": "last",
	                "@href": "http://bigbluehat.ic.tl/restfest_hackday_2012/_design/helpdesk/_rewrite/tickets?sort=time&order=desc&page=7",
	                "@type": "application/vnd.org.restfest.2012.hackday+xml"
		            }
		        ],
		        "ticket": []}
		       };
		  start({
		    "headers": {
		      "Content-Type": "application/json"
		     }
		  });
		  while(row = getRow()) {
			  output["tickets:tickets"].ticket.push(row.doc.ticket);
		  }
		  send(JSON.stringify(output));
		}
};

ddoc.shows = {
		ticket: function (doc) {
			var output = {"ticket": doc.ticket};
			return {
				   "headers" : {"Content-Type" : "application/json"},
				   "body" : JSON.stringify(output)
				};
		}
};

ddoc.validate_doc_update = function (newDoc, oldDoc, userCtx) {   
  if (newDoc._deleted === true &&
		  (userCtx.roles.indexOf('_admin') === -1
		  && userCtx.roles.indexOf('helpdesk_admin') === -1)) {
    throw "Only admin can delete documents on this database.";
  } 
};

couchapp.loadAttachments(ddoc, path.join(__dirname, 'attachments'));

module.exports = ddoc;