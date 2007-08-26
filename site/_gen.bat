@echo off
xsltproc --xinclude --stringparam lang eng default.xslt index.xml > public_html\index.html
xsltproc --xinclude --stringparam lang eng default.xslt download.xml > public_html\download.html
