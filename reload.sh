#!/bin/bash
echo "loading"
pid=`pidof live_master`
echo $pid
kill -SIGTERM $pid
php server.php
echo "loading success"

