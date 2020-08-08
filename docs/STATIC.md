# Static

The `static` folder contains CSS, images, and JavaScript files for this application.

## css

All CSS files in this folder are combined by the `static/styles.php` file. New CSS files are automatically added. The `styles.php` file is included in `views/layouts/_header.twig` and doesn't need to be updated.

## js

This folder is intended for custom JS files. New JS files need to be added to `views/layouts/_footer.twig`.

## lib

Third-party CSS and JS libraries should be included in this application from a CDN when possible, such as [cdnjs](https://cdnjs.com/). If a library isn't available from a CDN, save them in this folder. Add CSS files to `views/layouts/_head.twig` and JS files to `views/layouts/_footer.twig`.
