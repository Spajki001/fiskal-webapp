# Fiskal Webapp
A set of simple web apps made for easier inventure check in stores and checking the revenue for Fiskal d.o.o. company I work at.

## fispro_knjigovodstvo
A webapp that has lots of functionality for bookkeeping.<br>Some of them are:
- Table list of all partner's info, location and referent they are assigned to
- Creation of new `firma` or `referent` role accounts and editing them after
- Adding new partners and editing their info.
- Removing everything above
- Exporting the whole table of data to Microsoft Excel file (.xlsx)
- Filtering data for better views of some data
- Searching through the same data either by using the name of the partner or their OIB

## fispro_analytics
A webapp that has lots of functionality for businesses.<br>Some of them are:
- Chart for top 10 sold articles per month
- Chart for revenue per month
- A neat table view of revenue every time the store closed (end of day)
- Neat table view for active tables, their current total value and detailed view of all the articles on that tab
- More functionality and possibly new UI coming in the future

## fispro_inventura
A webapp that looks similar to the first two, but has different functionalities.<br>Some of them are:
- QR code scanner for simple article scans
- List of scanned data and ability to enter the amount of scanned article
- Ability to choose if you are 1st or 2nd entry and based on that, writes to corresponding columns in a MySQL DB
- Connects to MySQL DB and works with it for MultiPOS and Z3 compacto programs
- More functionality (if needed) and possibly new UI coming in the future

## Creating htdocs symbolic link
    ln -s /opt/lampp/htdocs /home/mateo/fiskal-webapp/htdocs

## Opening Xampp control panel on Linux
    sudo /opt/lampp/manager-linux-x64.run

## How to start and use the project
1. To test and check out the project, unzip it and place `fispro_knjigovodstvo`, `fispro_analytics` and `fispro_inventura` folders/directories in `htdocs` folder/directory.
2. Open the Xampp control panel and start `Apache Web Server`.
3. Open your browser and go to:<br>
a.) `localhost/fispro_analytics`<br>
or to<br>
b.) `localhost/fispro_inventura`,<br>
depending on which you want to use.
