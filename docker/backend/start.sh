#!/bin/sh

crond

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
