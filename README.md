# WP Export/Import Users Script

A straightforward PHP script for conveniently exporting and importing users between different WordPress databases.

## Features

- Effortlessly export thousands of users from one WordPress database.
- Seamlessly import thousands of users into another WordPress database.
- Simple user interface for easy interaction.

## How to Use

1. Clone or download the WPS-EIS.php script.
2. Upload the script to your web server.
3. Open the script in your web browser.
4. Fill in the database details and desired table prefix.
5. Click "Export Users" to export users to a JSON file.
6. Click "Import Users" to import users into another database.
7. Remove the json file when done!

## Requirements

- PHP 7.4 or higher.
- MySQL/MariaDB database access.

## Usage Notes

- Ensure proper backup of databases before performing imports.
- User passwords are exported and imported as-is.
- Imported users are skipped if they already exist in the target database.
- Mind that only the basic WordPress user records are created in table wp_users and no user orders, pages, posts or other data is being imported.  

## License

This project is licensed under the [GPL 3.0](LICENSE). 
Use it as you see fit. 
