@echo off
:repeat
echo.
echo Back up database...
cd D:\UwAmp\bin\database\mysql-5.7.11\bin
D:\UwAmp\bin\database\mysql-5.7.11\bin\mysqldump -h localhost -u root -psly pgludtr > D:\pgludtr.sql
if errorlevel 2 goto repeat
if errorlevel 0 goto end
:end
D:
cd \
rem ren pgludtr.sql boutique_%date:~10,4%%date:~7,2%%date:~4,2%.sql
ren pgludtr.sql %date:~10,4%%date:~4,2%%date:~7,2%_pgludtr.sql
echo Database succcessfully back up...
pause