@echo off
set /P palce_name="�X���F"
set file_name=�΋�\%date:/=-%@%palce_name%.xlsm

if not exist %file_name% (
    copy Template.xlsm %file_name% /-Y
)

start %file_name%
start �_��.xlsm
