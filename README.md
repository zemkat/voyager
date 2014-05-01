Voyager
=======

Tools for the Ex Libris Voyager integrated library system.

* `get_items.php` -- List the item records for a given MFHD
* `get_lost_marc.sh` -- Pull replaced/deleted records from Voyager server
* `quick_query.php` -- Run Oracle SQL queries on Voyager database
* `tag_report.php` -- Generate a report of all tag usage in Voyager
* `triple_nickel.php` -- Generate a report of all instances of one tag, raw and grouped
* `AHK-macros` -- AutoHotkey macros for Voyager
* `queries` -- Voyager queries, including fast BLOB queries

PHP tools use oracle (11g) for php (5.4). The connection information to the voyager database is stored in a file called `passwd.php` and a sample file is provided in `passwd.php.txt` 

Tools that generate spreadsheets use my [Spreadsheet library](https://github.com/zemkat/Spreadsheet)

These are copyright Kathryn Lybarger and distributed under CC-BY-SA.
