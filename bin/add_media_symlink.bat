@ECHO OFF

SET medialib=%~dp0..\public\media_lib\
ECHO This script will create a windows symlink for a targeted media directory, inside media_lib folder

FOR %%i IN ("%medialib%") DO (
    SET mediadrive=%%~di
)
CD \
%mediadrive%
CD %medialib%

SET /p mediapath="Enter path to media directory: "
IF "%mediapath%"=="" EXIT
IF NOT EXIST "%mediapath%" (
    ECHO Cannot found media path
    PAUSE
    EXIT
)

FOR %%i IN ("%mediapath%") DO (
    SET defaultname=%%~ni
)

SET /p linkname="Enter name for link [%defaultname%]: "
IF "%linkname%"=="" (
    SET linkname=%defaultname%
)

ECHO Media path found, creating symlink...

MKLINK /D "%linkname%" "%mediapath%"

PAUSE
EXIT
