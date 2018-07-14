# Webhooks-cli

[![Build Status](https://travis-ci.com/Alymosul/webhooks-cli.svg?branch=master)](https://travis-ci.com/Alymosul/webhooks-cli)

## Installation
- rename `env.example` to `.env` and fill it with your config details.
- create a database with the name just provided in the `.env`
- run `composer install`
- run `php webhooks-cli install`

## Cron jobs

To process failed jobs automatically, make sure you run the scheduler by adding the following Cron entry to your server:

`* * * * * php (path-to-project-root-dir)/webhooks-cli schedule:run >> /dev/null 2>&1`
