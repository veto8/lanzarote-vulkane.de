#!/bin/bash
docker run -i --rm --net=host salamander1/mysqldump --verbose -h 127.0.0.1 -u vulkansql1 -ppasspass vulkansql1 >init/vulkansql1.sql

#mysqldump --verbose -h 127.0.0.1 -u katzsql1 -pAntonias  katzsql1  > init/katzsql1.sql
