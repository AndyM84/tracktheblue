@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../stoic/web/stoic-migrate
php "%BIN_TARGET%" %*
