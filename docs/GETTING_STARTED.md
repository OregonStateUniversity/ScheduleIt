# Getting Started

## Software

This application was written in PHP and also uses the following libraries:

- [Bootstrap](https://getbootstrap.com/) - a CSS framework
- [Composer](https://getcomposer.org/) - a dependency manager for PHP
- [FontAwesome](https://fontawesome.com/) - an icon library
- [FullCalendar](https://fullcalendar.io/) - a JavaScript event calendar
- [jQuery](https://jquery.com/) - a JavaScript library
- [phpCAS](https://github.com/apereo/phpCAS) - a PHP library to communicate with OSU’s central authentication service
- [Twig](https://twig.symfony.com/) - a PHP templating engine

## App Versions

Major and minor versions of this application are available for [download on GitHub](https://github.com/OregonStateUniversity/MyEventBoard/releases). The latest code for the application is available in the [master branch](https://github.com/OregonStateUniversity/MyEventBoard/tree/master).

## Project History

Schedule-It (MyEventBoard) was a Capstone project requested by [Donald Heer](mailto:heer@eecs.oregonstate.edu). For project context, files are available in the `docs/project` folder.

## To-Do List For Future Development

### High Priority

- Support for "find best common time" use case (this requires the ability to reserve multiple timeslots of an meeting)
- Walk-through for website usage
- User experience testing
- Ability to view other users in registered slots (meeting must not be set to anonymous)
- Textbox for submitting text information at registration
- Ability to specify an alternate name (such a group name) for a reservation
- Reset and undo feature for meeting editing
- Editing the meeting capacity

### Low Priority

- Time conflict checks (for meeting registration. Maybe for meeting creation)
- Feedback for while waiting on a running task in the UI (e.g. the loading spinner before confirmation to show that the action is being worked on)
- Ability to export schedule (reservations) to different calendar application (google calendar)
- Reminder feature for Reservations (e.g through email)
