@echo off
start "Server" cmd /c php App/server.php
start "Scripts" cmd /c php App/scripts.php 