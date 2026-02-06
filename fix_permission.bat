@echo off
echo Setting permissions for Savori...
echo.

REM Buka Command Prompt sebagai Administrator
REM atau jalankan file ini sebagai Administrator

REM Set permissions untuk semua file dan folder
icacls "C:\xampp\htdocs\Savori" /grant "Users:(OI)(CI)RX"
icacls "C:\xampp\htdocs\Savori" /grant "IIS_IUSRS:(OI)(CI)RX"
icacls "C:\xampp\htdocs\Savori" /grant "Everyone:(OI)(CI)RX"

REM Berikan write permission untuk folder tertentu
icacls "C:\xampp\htdocs\Savori\assets\images" /grant "Users:(OI)(CI)W"
icacls "C:\xampp\htdocs\Savori\assets\images" /grant "IIS_IUSRS:(OI)(CI)W"

REM Tampilkan hasil
echo.
echo Permissions telah diupdate!
echo.
pause