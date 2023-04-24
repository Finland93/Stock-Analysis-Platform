# TrendTonic - Stock analysis using Alphavantage API 
**DISCLAIMER:** This project is "work in progress" and its not 100% working platform, you need to make some editing if you want to use this platform. 

# Install.php & Installation
Download this as .zip file, extract every file and make these edits before you upload this to your server. 
- Rename Admin folder, also rename login.php file and edit index.php file redirection to login whit correct details
- Check APP folder files: Verification.php, reset-password.php, register.php, form-handler.php, delete-profile.php for email adresses and also google recaptcha keys and verification URL`s
- Check FETCH folder files for hard coded alphavantage API keys
- Make sure you have Google Recaptcha and Alphavantage premium API key, this platform wont work whitout these! You need also Mysql database to install this project
- Make your own static content for all page files, this platform includes free stock images for candlestick cheat sheet
- Make sure you have your own domain added to install.php file for sending admin user details
- **Double check every single file before you download this to public server **

# Security
**This project is unfinished for security hardening, you need to make sure this platform is secure before your launch this as public website.**

# What this platform does?


# Libraries
- Recaptcha
- Alphavantage API
- Chart.js
- Jquery
- FontAwesome
- Luxon
- Bootstrap
