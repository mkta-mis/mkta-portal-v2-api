@echo off
for /f %%A in ('dir') do set cnt=%%A
echo File count = %cnt%
ren cache cache%cnt%
md cache