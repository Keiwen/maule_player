@ECHO OFF

SET medialib=%~dp0..\public\media_lib\
ECHO This script will create a windows symlink for a targeted media directory, inside media_lib folder

SET /p mediapath="Enter path to media directory: "
IF "%mediapath%"=="" EXIT
IF NOT EXIST "%mediapath%" (
    ECHO Cannot found media path
    PAUSE
)

FOR %%i IN ("%mediapath%") DO (
    SET defaultname=%%~ni
)

SET /p linkname="Enter name for link [%defaultname%]: "
IF "%linkname%"=="" (
    SET linkname=%defaultname%
)

ECHO Media path found, creating symlink...

CD %medialib%
MKLINK /D "%linkname%" "%mediapath%"

PAUSE
EXIT
