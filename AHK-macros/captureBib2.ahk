;
; captureBib2.ahk - Press Window-B to copy the Bib number of the bib record
;     in your hierarchy, using Window title if possible
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

AutoTrim, on

$#b::
IfWinActive, Voyager Cataloging
{
  WinGetText, Window_Title, A
  If (RegExMatch(Window_Title, "^(Voyager Cataloging - \[)?(Hldg \d+ for )?[Bb]ib (\d+) ", SubPat)) {
    clipboard = %SubPat3%
    MsgBox, 0, Success, Copied Bib #: %SubPat3%, 1
    Sleep 500
  } else {    
    If (RegExMatch(Window_Title, "^(Voyager Cataloging - \[)?Item")) {
	Send !fl{enter}
	Sleep, 500
	Send {tab}{tab}{tab}{Down}{Down}{Right}{Right}{Right}{Right}{Right}{Right}{Right}{Right}
	Sleep, 300
	Send {Shift Down}{End}{Shift Up}
	Sleep, 300
	Send, ^c{esc}
	Sleep, 500
	bak = %clipboard%
	clipboard = %bak%
        MsgBox, 0, Success, Copied Bib #: %clipboard%, 1
    } else {
      MsgBox, 0, Fail, Record not specified, 1
    }
  }
} else {
	Send, #b
}
Return
