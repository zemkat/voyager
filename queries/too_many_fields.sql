#
# too_few_fields.sql
#
# Find the unsuppressed records in Voyager with number of fields
#   fewer than a specified number
#
# (c) 2014 Kathryn Lybarger. CC-BY-SA
#
# (Paste the line below into an MS Access SQL window)
SELECT BIB_DATA.BIB_ID FROM BIB_DATA INNER JOIN BIB_MASTER ON BIB_DATA.BIB_ID = BIB_MASTER.BIB_ID WHERE (MID(BIB_DATA.RECORD_SEGMENT,13,5) > format(61 + 12*[How many fields min?],"00000")) AND BIB_MASTER.SUPPRESS_IN_OPAC='N' AND BIB_DATA.SEQNUM='1';
