;
; Delimiter.ahk - Override Ctrl-D in Voyager to type subfield delimiter
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

$^d::
IfWinActive, Voyager Cataloging
{
    Send {F9}
} else {
    Send ^d
}
Return
