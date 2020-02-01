# Front End User Interface Files

This folder contains all the files responsible for the front end user interface. The user interface uses the Twig template engine to help modularize the HTML code of the application by a far amount. This allows certain sections of HTML to be reused without the need of rewriting the HTML. The Layout and Partials folders are always included in each page as these sections of elements should persist through the web application. Files inside the views folder pertains to each specific page of the web application.

## Twig Template Engine
For more information on the the Twig Template Engine visit https://twig.symfony.com/

## Main Page:
- main.twig

## Create Page: 
- create.twig

## Manage Page:
  These files should be renamed upon refactoring ideally
- events.twig (This is for the landing page for the managing events).
- manage.twig (This is for managing an individual event)

## Reservations Page:
- reservations.twig (this is for the landing page of the reservations page)
- reservationsEventView.twig (this is for viewing an individual reservation)

## Register Page:
- register.twig

## Edit Page:
- edit.twig (this is for the second page (time fields section) of the edit page. This should be renamed to "editTimeSection.js" ideally)

## Error Page:
- error.twig (error page)

## Browse:
- browse.twig (this file has been deprecated and is unneeded but left in the folder in case the browse feature is to be reimplmented)
