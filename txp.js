/*
* txp.js
* Textpattern syntax file for EditArea <http://www.cdolivet.com/index.php?page=editArea>
* Adapted by Jeff Soo from EditArea's html syntax file
* last update: 2010-12-20
*/

editAreaLoader.load_syntax["txp"] = {
	'DISPLAY_NAME' : 'TXP'
	,'COMMENT_SINGLE' : {}
	,'COMMENT_MULTI' : {'<!--' : '-->', '<txp:hide>' : '</txp:hide>'}
	,'QUOTEMARKS' : {1: "'", 2: '"'}
	,'KEYWORD_CASE_SENSITIVE' : false
	,'KEYWORDS' : {
	}
	,'OPERATORS' :[
	]
	,'DELIMITERS' :[
	]
	,'REGEXPS' : {
		'doctype' : {
			'search' : '()(<!DOCTYPE[^>]*>)()'
			,'class' : 'doctype'
			,'modifiers' : ''
			,'execute' : 'before' // before or after
		}
		,'txp' : {
			'search' : '(<)(/?txp:(?!hide)[^ \r\n\t>]*)([^>]*>)'
			,'class' : 'txp'
			,'modifiers' : 'gi'
			,'execute' : 'before' // before or after
		}
		,'tags' : {
			'search' : '(<)(/?(?!txp:)[a-z][^ \r\n\t>]*)([^>]*>)'
			,'class' : 'tags'
			,'modifiers' : 'gi'
			,'execute' : 'before' // before or after
		}
		,'attributes' : {
			'search' : '( |\n|\r|\t)([^ \r\n\t=]+)(=)'
			,'class' : 'attributes'
			,'modifiers' : 'g'
			,'execute' : 'before' // before or after
		}
	}
	,'STYLES' : {
		'COMMENTS': 'color: #AAAAAA;'
		,'QUOTESMARKS': 'color: #6381F8;'
		,'KEYWORDS' : {
			}
		,'OPERATORS' : 'color: #E775F0;'
		,'DELIMITERS' : ''
		,'REGEXPS' : {
			'attributes': 'color: #B1AC41;'
			,'tags': 'color: #E62253;'
			,'doctype': 'color: #8DCFB5;'
			,'txp': 'color: #EE9216;'	// orange
//			,'txp': 'color: #44AA22;'	// green
		}	
	}		
};
