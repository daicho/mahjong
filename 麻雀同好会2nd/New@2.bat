@echo off
set file_name=‘Î‹Ç\%date:/=-%@2.xlsm
copy Template.xlsm %file_name% /-Y
start %file_name%
