# Views

Views files are divided into PHP files for logic and [Twig](https://twig.symfony.com/) templates for rendering HTML. PHP and Twig pairs are named with the same filename. In general, files are named to indicate the type of action that takes place in the file. Twig partials that are used on multiple pages are prefixed with an underscore `_`.

## URL Routing

Routes are defined in `config/routes.php`. These are the URLs that are accessible in a browser.
