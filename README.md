# TableCraft - Automated Multi Category Data Formatting Web Application

A complete PHP and MySQL project for college presentation.

## Main Features
- User registration and login
- Dynamic dashboard with animated cards and canvas background
- Popular categories:
  - Student Record
  - Shop Order
  - Employee Info
  - Library Book List
  - Product Stock
  - Commercial Category
  - Custom Category Editor
- Table generation from typed data
- Edit, update and delete saved tables
- Download table in PDF, PNG and DOC format
- Bright and dark theme switch
- Google font: Figtree
- Bootstrap-based responsive UI

## Technology Stack
- Front end: HTML5, CSS, Bootstrap, JavaScript
- Back end: PHP
- Database: MySQL
- Local server: WAMP

## Setup
1. Copy the `TableCraft_Project` folder into your WAMP `www` directory.
2. Start Apache and MySQL in WAMP.
3. Import `database/tablecraft.sql` into MySQL.
4. Update database credentials in `config/db.php` if needed.
5. Open `http://localhost/TableCraft_Project/`.

## Demo Login
- Email: demo@tablecraft.local
- Password: demo12345

## Notes
- PDF and PNG export use `html2canvas` and `jsPDF` from CDN.
- The DOC download is Word-compatible HTML saved with `.doc` extension.
- For best presentation, keep internet connected during export.
