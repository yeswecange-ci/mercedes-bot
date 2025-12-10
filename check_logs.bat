@echo off
cd "C:\YESWECANGE\Mercedes-bot App"
echo ========================================
echo DERNIERS LOGS API
echo ========================================
echo.
tail -n 100 storage\logs\laravel.log | findstr /C:"API Response to Twilio" /A
echo.
pause
