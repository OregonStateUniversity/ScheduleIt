# Code Style

## Commitlint

We use [Conventional Commits](https://www.conventionalcommits.org) for consistency in our Git commit messages. Commit messages are linted when pull requests are opened.

## PHP Linting

Our PHP files follow the [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/). You can lint PHP files by running this script in the root directory of this application:

```bash
bash run_linter_php.sh
```

If you want to automate running the linter as part of the Git commit process, you can add a `pre-commmit` hook to your environment:

```bash
cp bin/pre-commmit .git/hooks/pre-commmit
chmod 700 .git/hooks/pre-commmit
```

## Environment Variables

Passwords and environment-specific values shouldn't be checked in to the repository. If you're adding a new environment variable, add the key to `.env.example` without specifyinf a value.

## Global Constants

PHP constants are defined in `constants.inc.php`. These values are safe to check into the repository. However, if any of these values need to be changed frequently, it may be better to change them to read from a `.env` file.

Twig constants are defined in `config/twig.php`. Twig constants that rely on sessions are defined in `config/session.php`.
