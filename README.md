# Mibew Messenger

Mibew Messenger is an open-source live support application written
in PHP and MySQL. It enables one-on-one chat assistance in real-time
directly from your website.

## About this repository

This repository contains the core of Mibew Messenger application.

## Server requirements

1. A webserver or web hosting account running on any major Operating System
2. PHP (5.3.3 and above) with PDO, pdo_mysql, cURL, mbstring and gd extensions
3. MySQL 5.0 and above

## Build from sources

There are several actions one should do before use the latest version of Mibew from the repository:

1. Obtain a copy of the repository using `git clone`, download button, or another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Navigate to `src/` directory of the local copy of the repository.
5. Install npm dependencies using `npm install`.
6. Run Gulp to build Mibew using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use Mibew will be available in `src/release` directory.

## Terms of Use

Mibew Messenger is licensed under the terms of [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).

## Other repositories of the Mibew project

### Actual
1. [Mibew Messenger i18n repository](https://github.com/Mibew/i18n)
2. [Mibew Messenger design repository](https://github.com/Mibew/design)
3. [Mibew documentation repository](https://github.com/Mibew/docs.mibew.org)

### Obsolete
1. [Mibew Java applications repository](https://github.com/Mibew/java)
2. [Mibew Tray repository](https://github.com/Mibew/tray)

### Plugins

1. [Mibew Boilerplate plugin](https://github.com/Mibew/boilerplate-plugin) - a template for a real plugin

#### Ready for production use
1. [Mibew Emoji plugin](https://github.com/Mibew/emoji-plugin)
2. [Mibew Geo IP plugin](https://github.com/Mibew/geo-ip-plugin)
3. [Mibew Google Maps plugin](https://github.com/Mibew/google-maps-plugin)
4. [Mibew Purge History plugin](https://github.com/Mibew/purge-history-plugin)
5. [Mibew Real Ban plugin](https://github.com/Mibew/real-ban-plugin)
6. [Mibew Slack plugin](https://github.com/Mibew/mibew_slack)
7. [Mibew Title Notification plugin](https://github.com/Mibew/title-notification-plugin)

#### Not ready for production use (not stable, broken, obsolete, etc.)
1. [Mibew Button Refresh plugin](https://github.com/Mibew/button-refresh-plugin)
2. [Mibew External API plugin](https://github.com/Mibew/external-api-plugin)
3. [Mibew First Message plugin](https://github.com/Mibew/first-message-plugin)
