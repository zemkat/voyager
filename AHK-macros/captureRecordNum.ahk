;
; captureRecordNum.ahk - Press Window-N to copy the Record number of the open
;     open record, regardless of form
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

$#n::
IfWinActive, Voyager Cataloging
{
  WinGetText, Window_Title, A
	If (RegExMatch(Window_Title, "^(Voyager Cataloging - \[)?(Bib|Hldg|Item|Auth) (\d+)", SubPat)) {
		clipboard = %SubPat3%
		MsgBox, 0, Success, Copied %SubPat2% Record #: %SubPat3%, 1
		Sleep 500
	} else {
		MsgBox, 0, Fail, No record open!, 1
	}
} else {
	Send, #n
}
Return
