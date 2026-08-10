#!/bin/bash

lftp -e 'mirror -R --verbose --parallel=10  --use-pget-n=10  --exclude .hg/  --exclude phpmyadmin/ --exclude muse/  ~/webs/katz /' -u 'baeckerei_katz','KatzDBaecker2018?!' home40997644.1and1-data.host
