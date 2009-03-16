/*
 *   JavaScript Obfucator is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   JavaScript Obfucator is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

import java.util.*;
import java.io.*;

/**
 * Obfuscate the JavaScript code.
 * updated by Evgeny Gryaznov, March 2006
 * @author Shane Ng <gnenahs at poboxes dot com>
 */
public class JSO {
	
	private Random randomizer = new Random(321);
	
    public static final String[] reserved = {
        "abstract", "else", "instanceof", "switch", "boolean", "enum", "int",
        "synchronized", "break", "export", "interface", "this", "byte", "extends",
        "long", "throw", "case", "false", "native", "throws", "catch", "final",
        "new", "transient", "char", "finally", "null", "true", "class", "float",
        "package", "try", "const", "for", "private", "typeof", "continue", "function",
        "protected", "var", "debugger", "goto", "public", "void", "default", "if",
        "return", "volatile", "delete", "implements", "short", "while", "do", "import",
        "static", "with", "double", "in", "super", "undefined", "arguments"
    };

    public static final String[] builtIn = {
        "history", "packages", "pageXOffset", "pageYOffset", "isNaN", "array", "java", 
		"plugin", "clientInformation", "prototype", "layer", "layers", "crypto", "date", "secure",
        "embed", "navigator", "product", "netscape", "escape", "eval", "sun", 
        "taint", "fileUpload", "toString", "unescape", "untaint", "frameRate", 
		"valueOf", "getClass", "encodeURIComponent", "overrideMimeType",

		// Types
		"Math", "Date", "Array", "RegExp", "Image", "Function", "ActiveXObject", 
		"Number", "String", "Object", "JavaClass", "JavaObject", "JavaPackage", "JavaArray", 

		// DOM 
		"anchor", "image", "area", "checkbox", "password", "radio", "textarea", 
		"contentDocument", "contentWindow", "document", 
		"window", "element", "location", "option", "style", "body",

		// Properties
		"accessKey", "action", "activeElement", "align", "aLink", "aLinkColor", "alt", 
		"altHTML", "altKey", "appCodeName", "appMinorVersion", "appName", "appVersion",
		"autocomplete", "availHeight", "availWidth", "background", "backgroundAttachment", 
		"backgroundColor", "backgroundImage", "backgroundPosition", "backgroundPositionX", 
		"backgroundPositionY", "backgroundRepeat", "balance", "behavior", "bgColor",
		"bgProperties", "border", "borderBottom", "borderBottomColor", "borderBottomStyle",
		"borderBottomWidth", "borderCollapse", "borderColor", "borderColorDark", 
		"borderColorLight", "borderLeft", "borderLeftColor", "borderLeftStyle", 
		"borderLeftWidth", "borderRight", "borderRightColor", "borderRightStyle", 
		"borderRightWidth", "borderStyle", "borderTop", "borderTopColor", "borderTopStyle", 
		"borderTopWidth", "borderWidth", "bottom", "bottomMargin", "boundingHeight", "boundingLeft", 
		"boundingTop", "boundingWidth", "browserLanguage", "bufferDepth", "button", 
		"cancelBubble", "canHaveChildren", "caption", "cellIndex", "cellPadding", "cellSpacing", 
		"checked", "classid", "className", "clear", "clientHeight", "clientLeft", "clientTop", 
		"clientWidth", "clientX", "clientY", "clip", "clipBottom", "clipLeft", "clipRight", 
		"clipTop", "closed", "code", "codeBase", "codeType", "color", "colorDepth", "cols", 
		"colSpan", "compact", "complete", "content", "cookie", "cookieEnabled", "coords",
		"cpuClass", "cssText", "ctrlKey", "cursor", "data", "dataFld", "dataFormatAs", 
		"dataPageSize", "dataSrc", "defaultCharset", "defaultChecked", "defaultSelected", 
		"defaultStatus", "defaultValue", "defer", "designMode", "dialogArguments", "dialogHeight", 
		"dialogLeft", "dialogTop", "dialogWidth", "dir", "direction", "disabled", "display", 
		"documentElement", "domain", "dropEffect", "dynsrc", "effectAllowed", "encoding", "event", 
		"expando", "face", "fgColor", "fileCreatedDate", "fileModifiedDate", "fileSize", 
		"fileUpdatedDate", "filter", "firstChild", "font", "fontFamily", "fontSize", 
		"fontSmoothingEnabled", "fontStyle", "fontVariant", "fontWeight", "form", "frame", 
		"frameBorder", "frameSpacing", "fromElement", "hash", "height", "hidden", "host", 
		"hostname", "href", "hspace", "htmlFor", "htmlText", "httpEquiv", "id", "imeMode",
		"indeterminate", "index", "innerHTML", "innerText", "isMap", "isTextEdit", "keyCode", 
		"lang", "language", "lastChild", "lastModified", "layoutGrid", "layoutGridChar", 
		"layoutGridCharSpacing", "layoutGridLine", "layoutGridMode", "layoutGridType", "left",
		"leftMargin", "length", "letterSpacing", "lineBreak", "lineHeight", "link", "linkColor",
		"listStyle", "listStyleImage", "listStylePosition", "listStyleType", "loop", "lowsrc", 
		"margin", "marginBottom", "marginHeight", "marginLeft", "marginRight", "marginTop", 
		"marginWidth", "maxLength", "media", "menuArguments", "method", "Methods", "multiple", 
		"name", "nameProp", "nextSibling", "nodeName", "nodeType", "nodeValue", "noHref", 
		"noResize", "noShade", "noWrap", "object", "offscreenBuffering", "offsetHeight", 
		"offsetLeft", "offsetParent", "offsetTop", "offsetWidth", "offsetX", "offsetY", 
		"onLine", "opener", "outerHTML", "outerText", "overflow", "overflowX", "overflowY", 
		"owningElement", "padding", "paddingBottom", "paddingLeft", "paddingRight", "paddingTop", 
		"pageBreakAfter", "pageBreakBefore", "palette", "parent", "parentElement", "parentNode", 
		"parentStyleSheet", "parentTextEdit", "parentWindow", "pathname", "pixelBottom", 
		"pixelHeight", "pixelLeft", "pixelRight", "pixelTop", "pixelWidth", "platform", 
		"pluginspage", "port", "posBottom", "posHeight", "position", "posLeft", "posRight", 
		"posTop", "posWidth", "previousSibling", "propertyName", "protocol", "qualifier", 
		"readOnly", "reason", "recordNumber", "recordset", "referrer", "rel", 
		"repeat", "returnValue", "rev", "right", "rightMargin", "rowIndex", "rowSpan", 
		"rubyAlign", "rubyOverhang", "rubyPosition", "scopeName", "screenLeft", 
		"screenTop", "screenX", "screenY", "scrollAmount", "scrollDelay", 
		"scrollHeight", "scrolling", "scrollLeft", "scrollTop", "scrollWidth", "search", 
		"sectionRowIndex", "selected", "selectedIndex", "selectorText", "self", "shape", 
		"shiftKey", "size", "sourceIndex", "span", "specified", "src", "srcElement", "srcFilter", 
		"srcUrn", "start", "status", "styleFloat", "systemLanguage", "tabIndex", "tableLayout", 
		"tagName", "tagUrn", "target", "text", "textAlign", "textAutospace", "textDecoration", 
		"textDecorationLineThrough", "textDecorationNone", "textDecorationOverline", 
		"textDecorationUnderline", "textIndent", "textJustify", "textTransform", "tFoot", "tHead", 
		"title", "toElement", "top", "topMargin", "trueSpeed", "type", "unicodeBidi", "uniqueID", 
		"units", "updateInterval", "URL", "urn", "useMap", "userAgent", "userLanguage", "vAlign", 
		"value", "vcard_name", "verticalAlign", "visibility", "vLink", "vlinkColor", "volume", 
		"vspace", "whiteSpace", "width", "wordBreak", "wordSpacing", "wrap", "x", 
		"XMLDocument", "y", "zIndex",
		// non-IE
		"outerHeight", "innerHeight", "outerWidth", "innerWidth", 
		"which", "enabledPlugin",

		// collections
		"all", "anchors", "applets", "areas", "attributes", "behaviorUrns", 
		"bookmarks", "boundElements", "cells", "childNodes", "children", 
		"controlRange", "elements", "embeds", "filters", "forms", "frames", 
		"images", "imports", "links", "mimeTypes", "options", "plugins", "rows", 
		"rules", "scripts", "styleSheets", "tBodies", "TextRectangle",  
		
		// Methods
		"add", "addBehavior", "AddFavorite", "addImport", "addRule", "alert", 
		"appendChild", "applyElement", "assign", "attachEvent", "back", "blur", 
		"clearAttributes", "clearData", "clearInterval", "clearRequest", 
		"clearTimeout", "click", "cloneNode", "close", "collapse", "compareEndPoints", 
		"componentFromPoint", "confirm", "contains", "createCaption", "createControlRange", 
		"createElement", "createRange", "createStyleSheet", "createTextNode", "createTextRange", 
		"createTFoot", "createTHead", "deleteCaption", "deleteCell", "deleteRow", "deleteTFoot", 
		"deleteTHead", "detachEvent", "doScroll", "duplicate", "elementFromPoint", 
		"empty", "execCommand", "execScript", "expand", "findText", "firstPage", "focus", 
		"forward", "getAdjacentText", "getAttribute", "getBookmark", "getBoundingClientRect", 
		"getClientRects", "getData", "getElementById", "getElementsByName", "getElementsByTagName", 
		"getExpression", "go", "hasChildNodes", "inRange", 
		"insertAdjacentElement", "insertAdjacentHTML", "insertAdjacentText", "insertBefore", 
		"insertCell", "insertRow", "isEqual", "IsSubscribed", "item", "javaEnabled", "lastPage", 
		"mergeAttributes", "move", "moveBy", "moveEnd", "moveRow", "moveStart", "moveTo", 
		"moveToBookmark", "moveToElementText", "moveToPoint", "namedRecordset", "navigate", 
		"NavigateAndFind", "nextPage", "open", "pasteHTML", 
		"previousPage", "print", "prompt", "queryCommandEnabled", "queryCommandIndeterm", 
		"queryCommandState", "queryCommandSupported", "queryCommandValue", "recalc", "refresh", 
		"releaseCapture", "reload", "remove", "removeAttribute", "removeBehavior", 
		"removeChild", "removeExpression", "removeNode", "removeRule", "replace", 
		"replaceAdjacentText", "replaceChild", "replaceNode", "reset", "resizeBy", 
		"resizeTo", "scroll", "scrollBy", "scrollIntoView", "scrollTo", "select", 
		"setAttribute", "setCapture", "setData", "setEndPoint", "setExpression", "setInterval", 
		"setTimeout", "ShowBrowserUI", "showHelp", "showModalDialog", "showModelessDialog", 
		"splitText", "stop", "submit", "swapNode", "tags", "taintEnabled", 
		"urns", "write", "writeln",
		// builtIn
		"toUpperCase", "toLowerCase", "match", "substring", "split", "indexOf",
		"parseFloat", "parseInt", 
		"getYear", "getTime", "getMonth", "getFullYear", "getDay", "getDate",
		"exec", "join", "call", "floor", "toUTCString",

		// events
		"onabort", "onafterprint", "onafterupdate", "onbeforecopy", "onbeforecut", 
		"onbeforeeditfocus", "onbeforepaste", "onbeforeprint", "onbeforeunload", 
		"onbeforeupdate", "onblur", "onbounce", "oncellchange", "onchange", "onclick",
		"oncontextmenu", "oncopy", "oncut", "ondataavailable", "ondatasetchanged", "ondatasetcomplete",
		"ondblclick", "ondrag", "ondragend", "ondragenter", "ondragleave", "ondragover",
		"ondragstart", "ondrop", "onerror", "onerrorupdate", "onfilterchange", "onfinish",
		"onfocus", "onhelp", "onkeydown", "onkeypress", "onkeyup", "onload",
		"onlosecapture", "onmousedown", "onmousemove", "onmouseout", "onmouseover", "onmouseup",
		"onpaste", "onpropertychange", "onreset", "onresize", "onrowenter",
		"onrowexit", "onrowsdelete", "onrowsinserted", "onscroll", "onselect", "onselectstart",
		"onstart", "onstop", "onsubmit", "onunload",  

		// Ajax
		"XMLHttpRequest", "readyState", "onreadystatechange", "responseXML", 
		"responseText", "responseBody", "statusText",
		"send", "abort", "setRequestHeader", "getResponseHeader", "getAllResponseHeaders",
		"timeout"
    };

    public static final char[] DELIMITER = {'?', ':', '!', '=', '(', ')', '[', ']',
        '{', '}', '\r', '\n', '\t', ' ', '\"', '\'', '<', '>', ',', '.', '/',
        '\\', '+', '-', '*', '&', '|', '^', '%', ';'
    };


    public static final String[] alpha = {
    	"m", "n", "q", "r", "s", "t", "u", 
        "h", "i", "j", "k", "l", "o", "p", 
        "d", "e", "f", "g", "a", "b", "c",  
        "v", "w", "x", "y", "z", "$", "_"
    };

    public static final HashSet exclusionTokenSet = new HashSet();
    public static final HashSet forceReplace = new HashSet();
    public static final HashSet forceReplaceInStrings = new HashSet();
    
    public static final HashMap forceTextualReplace = new HashMap();
    
    public static int ref = alpha.length;
    public static HashMap map = new HashMap();

    public static final String ARG_EXCLUDE_TOKENS = "e=";
    public static final String ARG_DESTINATION_DIR = "d=";
    public static final String ARG_OBFUSCATE_STRING = "o=";
    public static final String ARG_DEBUG = "debug";
    public static final String ARG_DEBUGEXCLUDE = "debugnames";
    public static final String ARG_REPLACE = "textrepl=";

    private double[] stringObfuscationParameter = {0, 0, 0.5};
    private String[] file = null;
    public static boolean isDebug = false; 
    public static boolean isExcludeAll = false; 
    private String destinationDir = null;
    private JSOState state = new JSOState();
    private String delimiter = new String(DELIMITER);
    private HashSet encounteredInStrings = new HashSet();
    private HashMap encounteredInStringsFiles = new HashMap();

    public static void main(String[] args) throws Exception {
        ArrayList fileList = new ArrayList(args.length);
        String[] file = null;
        String destinationDir = null;
        double[] stringObfuscationParameter = {1, .59, 0.5};

        if (args.length == 0) {
            printUsage();
            return;
        } else if (args.length > 1) {
            for (int i = 0; i < args.length; i++) {
            	if( args[i].equals(ARG_DEBUG)) {
            		isDebug = true;
            	} else if( args[i].equals(ARG_DEBUGEXCLUDE) ) {
            		isExcludeAll = true;
            	} else if (args[i].startsWith(ARG_EXCLUDE_TOKENS)) {
                    readexclusionTokenSet(args[i].substring(ARG_EXCLUDE_TOKENS.length()));
                } else if (args[i].startsWith(ARG_DESTINATION_DIR) && destinationDir == null) {
                    File dir = new File(args[i].substring(ARG_DESTINATION_DIR.length()));
                    if (!dir.exists() && !dir.mkdirs()) {
                        System.err.println("Cannot create the output directory \"" + dir.getName() + "\"");
                        return;
                    } else if (dir.exists() && dir.isFile()) {
                        System.err.println("The output parameter \"" + args[i] + "\" is not a directory");
                        return;
                    }
                    destinationDir = dir.getAbsolutePath();
                } else if (args[i].startsWith(ARG_OBFUSCATE_STRING)) {
                    String[] param = args[i].substring(ARG_OBFUSCATE_STRING.length()).split(",", 3);
                    if (param.length >= 2) {
                        try {
                            stringObfuscationParameter[0] = Double.parseDouble(param[0]);
                            stringObfuscationParameter[1] = Double.parseDouble(param[1]);
                            if (param.length == 3) {
                                stringObfuscationParameter[2] = Double.parseDouble(param[2]);
                            }
                        } catch (NumberFormatException e) {
                            System.err.println("The obfuscation parameters are not numbers.");
                            return;
                        }
                    } else {
                        System.err.println("At least 2 obfuscation parameters are required, e.g. o=0.4,0.7.");
                        return;
                    }
                } else if( args[i].startsWith(ARG_REPLACE) ) {
                    String[] param = args[i].substring(ARG_REPLACE.length()).split(",", 3);
                    if (param.length == 2) {
                    	forceTextualReplace.put(param[0], param[1]);
                    } else {
                        System.err.println("2 parameters are required, e.g. textrepl=a,b");
                        return;
                    }
                } else {
                    fileList.add(args[i]);
                }
            }
            file = new String[fileList.size()];
            fileList.toArray(file);
        } else {
            file = new String[]{args[0]};
        }
        addexclusionTokenSet(reserved);
        addexclusionTokenSet(builtIn);

        JSO obfuscator = new JSO(file, destinationDir, stringObfuscationParameter);
        obfuscator.run();
    }

    private static void printUsage() {
        System.err.println("Usage: java JSO <list of javascript file> [options]");
        System.err.println("");
        System.err.println("where the options are:");
        System.err.println("\te=<exception list file>");
        System.err.println("\t\t- filename of the exception list");
        System.err.println("\t\t- exception tokens are delimited by tab, space, dot, comma, ");
        System.err.println("\t\t  single quote and double quote");
        System.err.println("\td=<destination directory>");
        System.err.println("\t\t- the output directory");
        System.err.println("\t\t- print to the STDOUT if not specified");
        System.err.println("\to=<obfuscation parameters of string literals>");
        System.err.println("\t\t- If it is specified, the characters in string literals will be ");
        System.err.println("\t\t  encoded to either \\uXXXX (hexidemcial) or \\XXX (octal) format");
        System.err.println("\t\t- The parameters are a 2 or 3 floating point values delimited ");
        System.err.println("                  by commas. e.g. 0.5,0.3 or 0.5,0.3,0.9");
        System.err.println("\t\t- The values are ");
        System.err.println("\t\t  * probability to encode a string");
        System.err.println("\t\t  * probability to encode a character in a candidate string");
        System.err.println("\t\t  * probability to encode a character into \\uXXXX format");
        System.err.println("\t\t- The last parameter is set to 0.5 if not specified");
        System.err.println("");
        System.err.println("Press Enter to read the examples...");
        try{
            System.in.read();
        } catch (Exception e){}
        System.err.println("Examples:");
        System.err.println("");
        System.err.println(" Obfuscate all scripts in the current directory and output to ./out directory:");
        System.err.println("\tjava JSO *.js d=out");
        System.err.println("");
        System.err.println(" Pipe the STDOUT output to x.o.js:");
        System.err.println("\tjava JSO x.js > x.o.js ");
        System.err.println("");
        System.err.println(" Merge a.js and b.js and pipe the merged output to script.js. Tokens in ");
        System.err.println("  exception list, noReplace.txt will not be replaced:");
        System.err.println("\tjava JSO a.js b.js e=noReplace.txt > script.js");
        System.err.println("");
        System.err.println(" Obfuscate the 100% of string literals, 68% of the characters will be encoded. ");
        System.err.println("  50% of the characters will be encoded as \\uXXXX format (default):");
        System.err.println("\tjava JSO x.js o=1,0.68");
    }

    public JSO(String[] file, String destinationDir, double[] stringObfuscationParameter){
        this.file = file;
        this.destinationDir = destinationDir;
        if (stringObfuscationParameter != null && stringObfuscationParameter.length >= 2) {
            this.stringObfuscationParameter = stringObfuscationParameter;
        }
    }

    public void run() throws IOException {
        for (int i = 0; i < file.length; i++) {
            BufferedReader in = new BufferedReader(new FileReader(file[i]));
            PrintWriter out = null;
            File f = new File(file[i]);

            if (destinationDir == null) {
                out = new PrintWriter(System.out, true);
            } else {
                out = new PrintWriter(new FileWriter(new File(destinationDir + File.separator + f.getName())));
            }

            this.obfuscate(in, out, f.getName());
            System.out.println("obfuscated " + f.getName());

            in.close();
            out.flush();
            out.close();
        }

        
        System.err.println( "Obfuscated" );
        System.err.println(map.toString().replace(',', '\n'));

        encounteredInStrings.retainAll(map.keySet());
        System.err.println( "Not Obfuscated in strings:" );
        for( Iterator it = encounteredInStrings.iterator(); it.hasNext(); ) {
        	String s = (String)it.next();
        	System.err.println( "\t"+s+": " + (String)encounteredInStringsFiles.get(s) );
        }
    }

    private void obfuscate(BufferedReader in, PrintWriter out, String fname) throws IOException {
        state.reset();

        int line_counter = 0;
        for (String line = in.readLine(); line != null; line = in.readLine()) {
        	line_counter++;
        	if( !isDebug )
        		line = line.trim();
            if (line.length() == 0) {
            	if( isDebug )
            		out.println();
                continue;
            }
            if( line.startsWith( "//-" ) ) {
            	String[] toAdd = line.substring(3).split(",");
            	for( int i = 0; i < toAdd.length; i++ )
            		exclusionTokenSet.add(toAdd[i].trim());
             	continue;
            } else if( line.startsWith("//+")) {
            	String[] toAdd = line.substring(3).split(",");
            	for( int i = 0; i < toAdd.length; i++ )
            		forceReplace.add(toAdd[i].trim());
            	continue;
            } else if( line.startsWith("//'")) {
            	String[] toAdd = line.substring(3).split(",");
            	for( int i = 0; i < toAdd.length; i++ ) {
            		forceReplace.add(toAdd[i].trim());
            		forceReplaceInStrings.add(toAdd[i].trim());
            	}
            }
            
            for( Iterator it = forceTextualReplace.keySet().iterator(); it.hasNext(); ) {
            	String key = (String)it.next(); 
            	line = line.replaceAll(key, (String)forceTextualReplace.get(key));
            }

            StringTokenizer st = new StringTokenizer(line, delimiter, true);

            if (st.hasMoreTokens()) {
                state.setToken(st.nextToken());
            }

            for (; state.token != null; state.skipToken()) {
                if (st.hasMoreTokens()) {
                    state.setNextToken(st.nextToken());
                } else {
                    state.noToken();
                }

                boolean doubleSlashed = state.flipFlags();
                if (doubleSlashed) {
                    break;
                }

                handleToken(out, fname, line_counter);
            }

            if( isDebug )
            	out.println();
            else if (!state.delimiter && !state.commented )
                out.print(" ");
        }
    }

    private void handleToken(PrintWriter out, String fname, int line ) {
        if (state.token.length() > 0) {
            if (state.delimiter) {
                if (state.inString() && !state.backslashed && state.c != '\\' &&
                    state.c != '\"' && state.c != '\'') {
                    state.token = obfuscateQuotedString(state.token);
                }
            } else {
                if (state.inString()) {
                	if( forceReplaceInStrings.contains(state.token) && canReplace(state.token) ) {
                		state.token = generateToken(state.token);
                	} else if( Character.isJavaIdentifierStart(state.token.charAt(0))) {
                		encounteredInStrings.add(state.token);
                		String s = (String)encounteredInStringsFiles.get(state.token);
               			s = ( s == null ) ? "" : s + ", ";
                		s += fname +"[" + line + "]";
                		encounteredInStringsFiles.put(state.token, s);                		
                	}
                	if (!state.backslashed) {
                        state.token = obfuscateQuotedString(state.token);
                    }
                } else if( !state.commented ) { 
                	if(canReplace(state.token)){
                		state.token = generateToken(state.token);
                	} else if( state.token.length() > 0 && Character.isDigit( state.token.charAt(0) ) ) {
                		try {
                			int i = Integer.parseInt( state.token );
                			double res = randomizer.nextDouble();
                			int e = (int)(i * res);
                			
                			if( i > 3 ) {
	                			if( res < 0.2 && i >= 16 )
	                				state.token = "0x"+Integer.toHexString(i);
	                			else if( res < 0.6 && i >= 8 )
	                				state.token = "0" + Integer.toOctalString(i);
	                			else
	                				state.token = "(" + e + "+" + (i-e) + ")";
                			}

                		} catch( NumberFormatException ex ) {
                		}
                	}
                }
            }
        }

        if (!state.commented && (state.printToken || state.inString())) {
            out.print(state.token);
        }

        if (state.c == '}' && !state.commented && !isDebug) {
            out.print(" ");
        }
    }

    private static void readexclusionTokenSet(String file) throws IOException {
        BufferedReader in = null;
        try {
            in = new BufferedReader(new FileReader(file));
            for (String line = in.readLine(); line != null; line = in.readLine()) {
                StringTokenizer st = new StringTokenizer(line, "\t ,.\"\'");
                for (; st.hasMoreTokens();) {
                    exclusionTokenSet.add(st.nextToken());
                }
            }
        } finally {
            if (in != null) {
                in.close();
            }
        }
    }

    private String obfuscateQuotedString(String token) {
        if (randomizer.nextDouble() < stringObfuscationParameter[0]) {
            StringBuffer buffer = new StringBuffer(token.length());
            int n = token.length();
            int pos = 0;
            for (int i = 0; i < n; i++) {
                if (randomizer.nextDouble() < stringObfuscationParameter[1]) {
                    buffer.append(token.substring(pos, i));
                    encode(token.charAt(i), buffer);
                    pos = i + 1;
                }
            }
            if (pos < n) {
                buffer.append(token.substring(pos));
            }
            return buffer.toString();
        } else {
            return token;
        }
    }

    private void encode(char c, StringBuffer buffer) {
        if (randomizer.nextDouble() < stringObfuscationParameter[2] || c > 0777) {
            buffer.append("\\u");
            encode(c, 16, 4, buffer);
        } else {
            buffer.append("\\");
            encode(c, 8, 3, buffer);
        }
    }

    private void encode(char c, int radix, int length, StringBuffer buffer) {
        String value = Integer.toString(c, radix);
        int n = length - value.length();

        if (n > 0) {
            for (int i = 0; i < n; i++) {
                buffer.append('0');
            }
            buffer.append(value);
        } else {
            buffer.append(value.substring(-n));
        }
    }

    private static String generateToken(String token) {
        if (map.containsKey(token)) {
            return (String) map.get(token);
        } else {
            String result = null;
            do {
                StringBuffer buffer = new StringBuffer(token.length());
                for (int i = ref; i > 0; i = i / alpha.length) {
                    buffer.append(alpha[i % alpha.length]);
                }

                ref++;
                result = buffer.toString();
            } while (exclusionTokenSet.contains(result) || map.containsValue(result));

            map.put(token, result);
            return result;
        }
    }

    private static boolean canReplace(String token) {
		if (token.length() <= 1 || token.charAt(0) == '$' )
			return false;
		if (map.containsKey(token))
			return true;
		if( isExcludeAll )
			return false;
		if( forceReplace.contains(token) )
			return true;		
		if (exclusionTokenSet.contains(token) )
			return false;
		if (Character.isDigit(token.charAt(0)))
			return false;
		if( token.charAt(0) == '_' )
			return true;
		return true;
	}

    private static void addexclusionTokenSet(String[] array) {
        if (array != null) {
            for (int i = 0; i < array.length; i++) {
				if( exclusionTokenSet.contains(array[i]) )
					System.err.println( "warn, already excluded: " + array[i] );
                exclusionTokenSet.add(array[i]);
            }
        }
    }

    public static boolean isDelimiter(String token) {
        if (token != null && token.length() > 0) {
            for (int i = 0; i < DELIMITER.length; i++) {
                if (token.charAt(0) == DELIMITER[i]) {
                    return true;
                }
            }
        }
        return false;
    }
}

class JSOState {
    boolean dotted = false;
    boolean doubleQuoted = false;
    boolean singleQuoted = false;
    boolean backslashed = false;
    boolean commented = false;
    boolean printToken = true;
    boolean delimiter = false;

    String token;
    String lastToken;
    String nextToken;

    char c0 = 0;
    char c = 0;
    char c2 = 0;

    void reset() {
        dotted = false;
        doubleQuoted = false;
        singleQuoted = false;
        backslashed = false;
        commented = false;
        printToken = true;
        delimiter = false;
        
        token = null;
        lastToken = null;
        nextToken = null;

        c0 = 0;
        c = 0;
        c2 = 0;
    }

    boolean printable() {
        return !commented && (printToken || inString());
    }

    boolean inString() {
        return doubleQuoted || singleQuoted;
    }

    boolean delimiterSurrounded() {
        return !JSO.isDelimiter(nextToken) && !JSO.isDelimiter(lastToken);
    }

    boolean isWhitespace(){
        return Character.isWhitespace(c);
    }

    String setToken(String value) {
        String oldToken = lastToken;
        lastToken = token;
        token = value;
        nextToken = null;

        if (value != null) {
            c0 = c;
            c = token == null ? 0 : token.charAt(0);
            c2 = 0;

            backslashed = c0 == '\\';
            dotted = c0 == '.';
            delimiter = JSO.isDelimiter(token);
            printToken = true;
        }

        return oldToken;
    }

    String tokenBackslashed() {
        String result = null;
        int index = 0;
        if (c == 'u') {
            index = 4;
        } else if (Character.isDigit(c)) {
            index = 3;
        } else {
            throw new IllegalStateException("Token not backslashed or invalid JavaScript syntax.");
        }
        result = token.substring(0, index);
        token = token.substring(index);

        return result;
    }

    void setNextToken(String value) {
        nextToken = value;
        c2 = value.charAt(0);
    }

    void skipToken() {
        this.setToken(nextToken);
    }

    void noToken() {
        nextToken = null;
        c2 = 0;
    }

    boolean flipFlags() {
        if (isWhitespace()) {
            printToken = delimiterSurrounded() || JSO.isDebug;
        } else if (c == '/') {
            if (!commented && c2 == '/') {
                return true;
            } else if (!commented && c2 == '*' &&
                   !inString()) {
                commented = true;
            } else if (commented && c0 == '*') {
                commented = false;
                printToken = false;
            }
        } else if (c == '\"' && !singleQuoted && !backslashed && !commented) {
            doubleQuoted = !doubleQuoted;
        } else if (c == '\'' && !doubleQuoted && !backslashed && !commented) {
            singleQuoted = !singleQuoted;
        }
        return false;
    }
}
