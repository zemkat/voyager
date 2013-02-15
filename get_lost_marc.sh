#!/bin/bash
#
# get_lost_marc.sh
#
# Copy replaced/deleted MARC records from the Voyager server, and convert
#   them to mnemonic text format.
#
# (c) 2013 Kathryn Lybarger. CC-BY-SA
#

#---------------------------------------------------------------------------
# Change these variables to reflect your local computer                    #
#                                                                          #
#   ME_path - where MarcEdit is installed (mine is in C:\local\MarcEdit)   #
#   file_dir - where the files should be put (mine is in C:\local\Voyager) #
#---------------------------------------------------------------------------
export ME_path=/cygdrive/c/local/MarcEdit/
export file_dir=/cygdrive/c/local/Voyager/

#---------------------------------------------------------------------------
# Change these variables to reflect your Voyager server setup              #
#   host - your database server hostname                                   #
#   login - your server login                                              #
#   path_to_db - your database location on the server                      #
#---------------------------------------------------------------------------
export login=XXXX
export host=XXXX-voy.hosted.exlibrisgroup.com
export path_to_db=/m1/voyager/XXXXdb
export PATH=${PATH}:"$ME_path"



#---------------------------------------------------------------------------
#                                                                          #
# You shouldn't need to modify beyond this point.                          #
#                                                                          #
#---------------------------------------------------------------------------

if [ -d $file_dir ]; then
	pushd $file_dir
else
	echo ERROR: No such directory: $file_dir
	exit
fi

if [ ! -x $ME_path/cmarcedit.exe ]; then
	echo ERROR: cmarcedit.exe not available
	exit
fi


scp $login@$host:$path_to_db/rpt/\{deleted,replace\}.*.marc .

for j in {deleted,replace}.*.marc
do
	cmarcedit -s $j -d ${j%%.marc}.mrk -break
done

