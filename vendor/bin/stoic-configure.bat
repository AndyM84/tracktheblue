@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../stoic/web/stoic-configure
php "%BIN_TARGET%" %*
