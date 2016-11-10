/**
 * Testing script for development
 * 
 * usage: phantomjs detect-overrides.js 'http://www.unl.edu/'
 * 
 */

var args = require('system').args;
var fs = require('fs');
var page = require('webpage').create();

page.open(args[1], function (status) {
	// Check for page load success
	if (status !== 'success') {
		console.log('Unable to access network: ' + status);
		phantom.exit();
		return;
	}

	page.onConsoleMessage = function(msg, lineNum, sourceId) {
		console.log('CONSOLE: ' + msg);
	};
	
	var violations = page.evaluate(function() {
		var violations = [];
		
		for (var sheetIndex = 0; sheetIndex < document.styleSheets.length; ++sheetIndex) {
			var sheet = document.styleSheets[sheetIndex];
			
			//Skip the WDN distributed sheets
			if (sheet.href && sheet.href.match(/\/wdn\/templates_\d+\.\d+\//)) {
				//console.log('this is a wdn sheet');
				continue;
			}
			
			//Skip sheets with no rules
			if (null == sheet.rules) {
				continue;
			}
			
			for (var ruleIndex = 0; ruleIndex < sheet.rules.length; ++ruleIndex) {
				var rule = sheet.rules[ruleIndex];
				
				if (!rule.selectorText) {
					continue;
				}
				
				var violation = {};
				violation.sheet = sheet.href;
				violation.selector = rule.selectorText;
				violation.cssText = rule.cssText;
				
				//#breadcrumbs should NEVER be modified with CSS
				if (rule.selectorText.match(/#breadcrumbs/)) {
					violations.push(violation);
				}
			}
		}
		
		return violations;
	});
	
	console.log(JSON.stringify(violations));
	
	phantom.exit();
});