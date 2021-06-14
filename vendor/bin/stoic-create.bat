@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../stoic/web/stoic-create
php "%BIN_TARGET%" %*
