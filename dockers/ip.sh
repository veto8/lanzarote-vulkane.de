#!/bin/bash
docker run -i --rm --net=host salamander1/mysql --verbose -h 127.0.0.1 -u vulkansql1 -ppasspass vulkansql1 <init/vulkansql1.sql
