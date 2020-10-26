#!/bin/bash

updatePost=`cat $1`
postId=4

status=$(curl -s -w "%{http_code}" --request PUT 'localhost:8080/api/blog/post/'$postId --header 'Content-Type: application/json' --data-raw "$updatePost")

expectedStatus=400

if [ $status != $expectedStatus ]; then
  echo status code incorrect: $status supposed to be $expectedStatus
  exit 1
fi;

source ./system/fetch_all.sh $2