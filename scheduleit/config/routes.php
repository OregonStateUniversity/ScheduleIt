<?php

$request_uri = str_replace(SITE_DIR, '', $_SERVER['REQUEST_URI']);
$request_queries = explode('?', $request_uri);
$request = $request_queries[0];
$meeting_show = preg_match('/meetings\/[0-9]+$/i', $request);
$meeting_edit = preg_match('/meetings\/[0-9]+\/edit$/i', $request);
$meeting_edit_dates = preg_match('/meetings\/[0-9]+\/dates$/i', $request);
$uri = explode('/', $request);

switch ($request) {
    case '/':
    case '':
        require_once ABSPATH . 'scheduleit/views/home/index.php';
        break;
    case '/calendar':
        require_once ABSPATH . 'scheduleit/views/calendar/index.php';
        break;
    case '/login':
        require_once ABSPATH . 'scheduleit/views/home/login.php';
        break;
    case '/logout':
        require_once ABSPATH . 'scheduleit/views/home/logout.php';
        break;
    case '/invite':
        require_once ABSPATH . 'scheduleit/views/invites/show.php';
        break;
    case '/manage':
        require_once ABSPATH . 'scheduleit/views/manage/index.php';
        break;
    case ($meeting_edit > 0):
        $meeting_id = $uri[2];
        require_once ABSPATH . 'scheduleit/views/meetings/edit.php';
        break;
    case ($meeting_edit_dates > 0):
        $meeting_id = $uri[2];
        require_once ABSPATH . 'scheduleit/views/meetings/edit_dates.php';
        break;
    case ($meeting_show > 0):
        $meeting_id = $uri[2];
        require_once ABSPATH . 'scheduleit/views/meetings/show.php';
        break;
    case '/meetings/create':
        require_once ABSPATH . 'scheduleit/views/meetings/create.php';
        break;
    case '/meetings/invite':
        require_once ABSPATH . 'scheduleit/views/meetings/invite.php';
        break;
    case '/meetings':
        require_once ABSPATH . 'scheduleit/views/meetings/index.php';
        break;
    case '/profile':
        require_once ABSPATH . 'scheduleit/views/profile/index.php';
        break;
    case '/meetings/remove_attendee':
        require_once ABSPATH . 'scheduleit/views/meetings/remove_attendee.php';
        break;
    default:
        require_once ABSPATH . 'scheduleit/views/errors/error_logged_out.php';
        break;
}
