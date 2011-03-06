#!/bin/sh
# Runs the bibtexbrowser test suite
# Note that the files must be executed in different processes (i.e. without PHP include)

for i in *php
do
  php $i
done


