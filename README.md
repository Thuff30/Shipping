# Shipping Application
This is a simple WAMP application meant to track shipments to clients for a small business. A file with the source code to create 
the database the application is created for is included in the MySQL folder. A file with hardcoded database parameters is included as well,
however this is purely for demonstrative purposes and is not best practice to use if/when the application is put in place.

# Security
Security is not tightented as it is not meant to hold any sensitive information and, as such is meant to
be open for all users to access. While the principle of least privelage is not actively utitlized, it is configured 
to allow for it to be implemented in the future if need be. Stored procedures are included in the source code for 
the database, however this was done to increase performance, not as a security measure at this time.
