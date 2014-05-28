#
# ip_links.sql
#
# Find records in Voyager with raw IP addresses (or similarly sketchy URLs)
#   in their links
# (Not strictly an IP address pattern, but a good indicator)
#
# (c) 2014 Kathryn Lybarger. CC-BY-SA
#
# (Paste the line below into an MS Access SQL window)
SELECT RECORD_ID, URL_HOST FROM ELINK_INDEX WHERE RECORD_TYPE="B" AND URL_HOST LIKE "[0-9]*";
