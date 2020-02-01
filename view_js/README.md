# Front End Javascript Files

This folder contains all the files responsible for the front end functionality (javascript code) of MyEventBoard. Each javascript files are seperated by which page they are responsible for. However, there are a few exceptions to this. This exception was made to minimize the size of files sizes and reuse code whenever it is possible. The Javascript files uses a mixture of pure javascript and JQuery. It would probably be better to be consistent and use one or the other. This problems is more prevalent during the earlier stages (create page, register page) of development in which JQuery wasn't being used yet. This problem should be fixed via refactoring.

## Main Page:
- main.js 

## Create Page: 
- create.js

## Manage Page:
- events.js (This is for the landing page for the managing events)
- manage.js (This is for managing an individual event)

## Reservations Page:
- reservations.js (this is for the landing page of the reservations page)
- reservations_details.js (this is for viewing an individual reservation)

## Register Page:
- register.js

## Edit Page:
- edit.js (this is for the second page (time fields section) of the edit page. This should be renamed to "editTimeSection.js" ideally)
- editFormSection.js (this is for the first page (form fields section) of the edit page
- editGeneralFunctions.js (general functions that are used in both edit pages (form and time)

## UI:
- ui.js (this is used for the UI throughout the web application

## Browse:
- browse.js (this file has been deprecated and is unneeded but left in the folder in case the browse feature is to be reimplmented)
