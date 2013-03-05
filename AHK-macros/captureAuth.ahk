;
; captureAuth.ahk - Press Window-A to copy the Auth ID of the open
;     authority record
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

$#a::
IfWinActive, Voyager Cataloging
{
	WinGetTitle, Window_Title, A
	If (RegExMatch(Window_Title, "Voyager Cataloging - \[Auth (\d+) ", SubPat)) {
		clipboard = %SubPat1%
		MsgBox, 0, Success, Copied Auth #: %SubPat1%, 1
    	Sleep 500
	} else {  
		MsgBox, 0, Fail, Not an authority record, 1
	}
} else {
	Send, #a
}
Return

