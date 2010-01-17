#!/bin/sh

rm -rf *.dmg
hdiutil create mibew-notifier-temp.dmg -volname "Mibew Notifier 1.0" -fs HFS+ -srcfolder "Mibew Notifier.app"
hdiutil convert "mibew-notifier-temp.dmg" -format UDZO -imagekey zlib-level=9 -o "mibew-1.0.0.dmg"
rm mibew-notifier-temp.dmg
