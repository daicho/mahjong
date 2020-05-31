@echo off
set file_name=‘Î‹Ç\%date:/=-%.xlsm

if not exist %file_name% (
    copy Template.xlsm %file_name% /-Y
)

start %file_name%
timeout /t 1 /nobreak > nul
start “_”.xlsm
