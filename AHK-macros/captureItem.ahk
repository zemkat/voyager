;
; captureItem.ahk - Press Window-I to copy the Item number of the open
;     item record
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

$#i::
IfWinActive, Voyager Cataloging
{
	WinGetTitle, Window_Title, A
	If (RegExMatch(Window_Title, "Voyager Cataloging - \[Item (\d+)[ \]]", SubPat)) {
		clipboard = %SubPat1%
		MsgBox, 0, Success, Copied Item #: %SubPat1%, 1
    	Sleep 500
	} else {  
		MsgBox, 0, Fail, Item not specified, 1
	}
} else {
	Send #i
}
Return

