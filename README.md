# TrendTonic - Stock analysis using Alpha Vantage API
**DISCLAIMER:** This project is "work in progress" and its not 100% working platform, you need to make some editing if you want to use this platform. 

This project also included: FAQ with schemas on frontpage, image swap from PNG/JPG files to webP files

# Install.php & Installation

Download this as a .zip file, extract every file and make these edits before you upload this to your server. This platform will make config files outside of the public directory, you need to install this using install.php file this will make all necessary tables to the database.
- Rename Admin folder, also rename login.php file and edit index.php file redirection to login with correct details
- Check APP folder files: Verification.php, reset-password.php, register.php, form-handler.php, delete-profile.php for email addresses and also google recaptcha keys and verification URL`s
- Check FETCH folder files for hard coded alpha vantage API keys
- Make sure you have Google Recaptcha and Alpha Vantage premium API key, this platform wont work without these! You need also Mysql database to install this project
- Make your own content for all page files, this platform includes free stock images for candlestick cheat sheet
- Make sure you have your own noreply email added to install.php file for sending admin user details
- Write your disclaimers for every page, write terms & conditions and also privacy policy
- Edit your own Google Analytics for every page
- Edit blog and add some posts if you like to have blog whit this platform
- **Double check every single file before you download this to public server**

# Security
**This project is unfinished for security hardening, you need to make sure this platform is secure before you launch this as a public website.**

# What does this platform do?
This platform will fetch NYSE and NASDAQ stocks from Alphavantage, it will also add these symbols inside 2 separated databases. When you have imported every symbol to these two databases you can start fetching: Sectors, Market Capitalization, P/E ratios, P/B ratios, RSI and Earnings per share for every stock symbol you have on your tables.
This platform also has USA economics GDP & Inflation data from alpha vantage, i would recommend you also to add CPI, Federal Funds rate and unemployment rate to give more accurate data for the economic state of USA. Here is URLs:
- https://www.alphavantage.co/documentation/#interest-rate
- https://www.alphavantage.co/documentation/#unemployment
- https://www.alphavantage.co/documentation/#cpi

When this platform creates tables to a public website it will also include 2 buttons on tables: News for latest 6 news of current stock and chart for last 100 data points, this will also make Fibonacci lines to charts.

You can also calculate sector averages for each exchange and sector for: P/E, P/B and RSI and when users will click "display" button this platform will look sector averages and swap numbers to images: SELL | BUY | HOLD based on sector average values.

# PRO User improvements
For PRO users you should edit this platform to display everything, and disable some features from free users
Alpha Vantage offers Search feature, this would be good to add for PRO users to fetch different symbols with same data to separated table when they have PRO role here is reference for this: https://www.alphavantage.co/documentation/#symbolsearch

# How to improve this?
- Blog post should be done with tinyMCE and database from admin panel
- User management on the admin side
- Payment gateway from stripe
- Cron Jobs to automate data fetching when exchanges closes, now this is done manually

# Libraries
- Alpha Vantage API
- FontAwesome
- Bootstrap
- Recaptcha
- Chart.js
- Jquery
- Luxon

# Known issues
- Chart.js for stocks will not have the correct amount of data points for fibonacci or Candle sticks; it will display first and last on charts.
- Economics of the USA will display only GDP data, this issue is related to chart.js and you need to look why it wont make these charts as separated.

**Here on read me file is also some improvements & ideas what to do next for this platform, if you like this code and want to make this better please feel free to contact me here on github**
