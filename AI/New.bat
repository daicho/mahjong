@echo off
set file_name=�΋�\%date:/=-%.xlsm

if not exist %file_name% (
    copy Template.xlsm %file_name% /-Y
)

start %file_name%
