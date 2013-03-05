;
; PasteForOverlay.ahk - Press Window-O to paste buffer contents 
;     (hopefully bib ID) into a new 946 field, to force overlay
;
; (c) 2013 Kathryn Lybarger. CC-BY-SA
;

#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#Warn  ; Recommended for catching common errors.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

#SingleInstance
SetTitleMatchMode, 2

$#o::
IfWinActive, Voyager Cataloging
{
	Send !M{tab}{tab}{tab}{tab}{tab}{tab}{tab}^{Down}

{Up}{Up}{Insert}946{tab}{tab}{tab}^v
} else {
	Send #o
}
