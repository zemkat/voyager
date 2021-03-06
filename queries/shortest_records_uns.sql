#
# shortest_records.sql
#
# Find the shortest unsuppressed records in Voyager, by byte length
#
# (c) 2014 Kathryn Lybarger. CC-BY-SA
#
# (Paste the line below into an MS Access SQL window)
SELECT BIB_DATA.BIB_ID FROM BIB_DATA INNER JOIN BIB_MASTER ON BIB_DATA.BIB_ID = BIB_MASTER.BIB_ID WHERE BIB_DATA.SEQNUM='1' AND BIB_MASTER.SUPPRESS_IN_OPAC='N' AND MID(BIB_DATA.RECORD_SEGMENT,1,5) = (SELECT MIN(MID(BIB_DATA.RECORD_SEGMENT,1,5)) FROM BIB_DATA INNER JOIN BIB_MASTER ON BIB_DATA.BIB_ID = BIB_MASTER.BIB_ID WHERE BIB_DATA.SEQNUM='1' AND BIB_MASTER.SUPPRESS_IN_OPAC='N');
