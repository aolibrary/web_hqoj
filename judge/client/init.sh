#!/bin/bash

useradd -m -u 1536 judge

mkdir -p /home/judge/etc
mkdir -p /home/judge/data
mkdir -p /home/judge/log
mkdir -p /home/judge/run0
mkdir -p /home/judge/run1
mkdir -p /home/judge/run2
mkdir -p /home/judge/run3

chown judge -R /home/judge