@echo off
start "Server" cmd /c php App/server.php
start "Scripts" cmd /c php App/scripts.php
start "Test" cmd /c php App/test.php