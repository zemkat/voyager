;
; captureBib.ahk - Press Window-B to copy the Bib number of the open
;     Voyager record, using Print Label 
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

#b::
IfWinActive, Voyager Cataloging
{
	Send !fl{enter}
	Sleep, 300
	Send {tab}{tab}{tab}{Down}{Down}{Right}{Right}

{Right}{Right}{Right}{Right}{Right}{Right}
	Sleep, 300
	Send {Shift Down}{End}{Shift Up}
	Sleep, 300
	Send, ^c{esc}
	Sleep, 500
	bak = %clipboard%
	clipboard = %bak%
}
return
