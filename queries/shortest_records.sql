#
# shortest_records.sql
#
# Find the shortest records in Voyager, by byte length
#
# (c) 2014 Kathryn Lybarger. CC-BY-SA
#
# (Paste the line below into an MS Access SQL window)
SELECT BIB_ID FROM BIB_DATA WHERE MID(RECORD_SEGMENT,1,5) = (SELECT MIN(MID(RECORD_SEGMENT,1,5)) FROM BIB_DATA WHERE SEQNUM='1');
