Welcome to the Lego Retirement Script.

This project set out to find some sort of reliable way of determining when Lego retires their sets.

Before you get started, you can get a CSV of all current lego sets at http://www.brickset.com/export/all/. In my case, I traversed the CSV and wrote a script to add them to a database. I added a retired field to deterine and keep track of whether the set is retired or available.

The basic functionality of this script is to query your local database (containing all of the lego sets). Initially, all of the sets are marked as not retired. 

The script grabs all of the lego sets that are not retired. It then tries to do a request to a url in the format "http://shop.lego.com/en-US/Diagon-Alley-$lego_id?p=$lego_id";

Initially, I planned on using the lego set name to add to the URL, but I discovered that if you simply change the IDs after Diagon-Alley (or any other real set), it will automatically redirect you to the appropriate URL.

ie, shop.lego.com/en-US/Diagon-Alley-10197?p=10197 eventually redirects you to http://shop.lego.com/en-US/Fire-Brigade-10197?p=10197

In a situation where something is no longer available, it returns a 404 error. There are two possibilities here:
- It sends you to the actual page and gives you a 404 error.
- It sends you to the mainpage of Lego Shop at Home and you can still find the 404 in the headers. 

So in my script, if a 404 is returned, it updates your local database to mark that set retired and then sends an email and text blast to what you have defined in your config.php file. That way, whenever a set is found to be retired, you are notified immediately.

Ideally, this script is intended to be run as a cronjob as often as possible. I run it about every 20 minutes, and it takes awhile to run as it is making a lot of requests.

SUGGESTION: When you first create your database, you will probably set all of the sets to be not retired. This means that the first time you run the script, it'll update all of the currently retired sets. On this first run, you should comment out the mail() function call so that you do not receive approximately 5000+ texts/emails saying stuff is retired. Once you run the initial script, it should only mail when it finds a new retirement. 


