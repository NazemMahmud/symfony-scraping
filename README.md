# Implementation
1. Project Init: created docker environment for Nginx, PHP 8.2, MySQL 8


# TODO:
- check with sending extra header to find out for the POSt request
- CRUD
1. Create DB and connect with PHPMYADMIN
   1. Dockerfile, env & compose file updated for migration; initial migration done
2. Error Log / Handler: Custom exception classes and Logger are used for error handling and to store error details
3. Redis: for index API
4. Turnover Database


# Next Todo:
- Store API: check unique value for regi code [DONE]
  - Indexing for regi code [LATER]
- Index, Delete, Update API
- Check with original scraping script (from file and from site)
- Create turnover crapping
- Create & Get API for turnover
- Add pagination and filtering
- Front end

# Additional:
- Add index for column registration code
- Use exception handler and Logger for error handling
- Store the logs in log file (each day new log file will generate) inside the project directory instead of var/log/dev.log

# NOTE:
- remove scripts/test directory after done everything


mysql -u root -p
