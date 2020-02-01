# MyEventBoard

MyEventBoard is a scheduling web application developed by Tommy Liao, Simon Louie and Blaise Takushi,  
as part of the Schedule It! project.

## Accessing the Application

While MyEventBoard is currently in development, it can be accessed at:  
https://eecs.oregonstate.edu/education/myeventboard/

## Setting Up the Repository for Development

For the project to be functional, it should be cloned into the 'public_html' folder in your engineering filespace.  
MyEventBoard in its current state is supposed to run only on the engineering servers, as requested by the client.  

There are a few things to install. Run the Bash shell commands below to install Composer and some packages.  
MyEventBoard uses Twig, Mimey, and phpCAS. Install Composer first, and then install the packages.

`bash install_composer.sh`  
`bash install_packages.sh`

Remember to also set permissions for everything inside the repository.  
Run the script named 'set_permissions.sh'.

## Application Features Overview

Video: https://oregonstate.app.box.com/file/683373432528

PDF: https://github.com/takushib/MyEventBoard/blob/master/doc/MyEventBoard%20Application%20Overview.pdf


## Resources for Development

Composer is a dependency manager for PHP. Visit https://getcomposer.org/ for more information.

Twig is a templating engine for PHP. Documentation is available at https://twig.symfony.com/doc/1.x/  

phpCAS is an official client for CAS, the single sign-on system used by Oregon State University.  
Go to https://github.com/apereo/phpCAS/tree/master/docs/examples for example code.  
For documentation on the source code, it can be found at https://apereo.github.io/phpCAS/api/

Mimey is a package that MyEventBoard uses for its file upload functionality.  
Read more about it at https://github.com/ralouphie/mimey

## To-Do List For Future Development

### High Priority
- Refactoring for front end code (special attention needed for HTML and JS)
- Data binding for event editing or a better method for updating the UI after data changes
- Improvement of styling for mobile
- Support for "find best common time" use case  
    (this requires the ability to reserve multiple time slots of an event)
- Walk-through for website usage
- User experience testing
- Invitations for event registration via e-mail
- Ability to view other users in registered slots (event must not be set to anonymous)
- Textbox for submitting text information at registration
- Ability to specify an alternate name (such a group name) for a reservation
- File upload at registration (not only after registration)
- Reset and undo feature for event editing
- Editing the event capacity

### Low Priority
- Time conflict checks (for event registration. Maybe for event creation)
- Log out button (ONID)
- Feedback for while waiting on a running task in the UI 
     (e.g. the loading spinner before confirmation to show that the action is being worked on)
- Homepage calendar view for schedule (possibly?)
- Ability to export schedule (reservations) to different calendar application (google calendar)
- Removing a registered user from your event (event manage page)
- Reminder feature for Reservations (e.g through email)

## Contact Information

Tommy Liao: liaoto@oregonstate.edu or tommycs404@gmail.com

Simon Louie: louisi@oregonstate.edu or simon-louie@outlook.com

Blaise Takushi: takushib@oregonstate.edu or Btsg808@gmail.com 

## License & Copyright

Copyright (c) 2020 Tommy Liao, Blaise Takushi, Simon Louie

Licensed under the [MIT License](LICENSE).

