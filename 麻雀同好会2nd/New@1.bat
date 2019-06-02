@echo off
set file_name=‘Î‹Ç\%date:/=-%@1.xlsm
copy Template.xlsm %file_name% /-Y
start %file_name%
