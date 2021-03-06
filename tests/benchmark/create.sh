#!/bin/bash

expectedStatus=204
benchmarkRepititions=$1
warmupRepititions=$2
repititions=$(($benchmarkRepititions+$warmupRepititions))
container=$3

port=$(./benchmark/bring_up_container.sh $container)

timeString="$container"

newPost=`cat ./data/new_post.json`

for x in $(seq 1 $repititions);
do
  if [ $x = $warmupRepititions ]; then
    docker stats --format "\t{{.MemUsage}}" test_$container >> ./results/create_memory_$container.csv &
    pid=$!
  fi;
  result=( $(curl --silent -o /dev/null -w "%{time_total} %{http_code}" --request POST http://localhost:$port/api/blog/post --header 'Content-Type: application/json' --data-raw "$newPost") )
  time=${result[0]}
  status=${result[1]}
  if [ $status -eq $expectedStatus ]; then
    if [ $x -gt $warmupRepititions ]; then
      timeString="$timeString,$time"
    fi;
  else
    echo "wrong status; got $status, expected $expectedStatus"
    kill $pid
    docker-compose down
    exit 1
  fi;
done

kill $pid

docker-compose down

echo $timeString >> ./results/create_time.csv
