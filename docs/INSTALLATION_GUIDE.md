# Installation Guide

## Requirements

- CentOS Linux 7.7.1908
- Apache 2.4.6
- PHP 7.2.28
- MySQL 15.1
- MariaDB 10.4.11
- Wget

## Installing on OSU server

Download the latest [ZIP archive](https://github.com/OregonStateUniversity/MyEventBoard/archive/master.zip). Unzip the files into a public directory on your server (this might be your `public_html` directory). If you are unzipping the archive on a local machine and then transferring the files to a server, be sure to copy all invisible files (those starting with a `.`).

In the root directory of this application, create a `.env` file by copying `.env.example` and `.htaccess` by copying `.htaccess.example`. Set the values needed in those files:

```bash
cp .env.example .env
cp .htaccess.example .htaccess
```

In the root directory of this application, run:

```bash
bash setup.sh
```

Run this script again if you add any Composer packages or database migrations.

## Installing on a local machine

See the guide for your operating system:

- [Local Setup Mac](LOCAL_SETUP_MAC.md)
- [Local Setup Windows](LOCAL_SETUP_WINDOWS.md)
