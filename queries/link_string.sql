#
# link_sring.sql
#
# Find unsuppressed bib records in Voyager whose links contain the provided 
#   string
#
# (c) 2014 Kathryn Lybarger. CC-BY-SA
#
# (Paste the line below into an MS Access SQL window)
SELECT BIB_MASTER.BIB_ID, ELINK_INDEX.LINK FROM ELINK_INDEX INNER JOIN BIB_MASTER ON ELINK_INDEX.RECORD_ID = BIB_MASTER.BIB_ID WHERE (ELINK_INDEX.LINK like "*" & [Search links for:] & "*") AND (ELINK_INDEX.RECORD_TYPE="B") AND (BIB_MASTER.SUPPRESS_IN_OPAC = "N");
