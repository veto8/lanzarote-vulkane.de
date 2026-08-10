#!/bin/bash
lftp -e 'set ftp:ssl-allow no; mirror --exclude .hg/ --verbose --parallel=10  --use-pget-n=10   / /var/customers/webs/vulkan' -u 'vulkan','interRos'  nephilla.com
