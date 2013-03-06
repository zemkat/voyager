;
; captureMfhd.ahk - Press Window-H to copy the MFHD number of the MFHD record
;     in your hierarchy, using Window title
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

$#h::
IfWinActive, Voyager Cataloging
{
  WinGetText, Window_Title, A
	If (RegExMatch(Window_Title, "^(Voyager Cataloging - \[)?(Item \d+ for )?[Hh]o?ldi?n?g (\d+)", SubPat)) {
		clipboard = %SubPat3%
		MsgBox, 0, Success, Copied MFHD #: %SubPat3%, 1
    	Sleep 500
	} else {  
		MsgBox, 0, Fail, MFHD not specified, 1
	}
} else {
	Send, #h
}
Return
