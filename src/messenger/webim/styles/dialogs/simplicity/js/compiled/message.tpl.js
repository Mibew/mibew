(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['message'] = template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, functionType="function", escapeExpression=this.escapeExpression, helperMissing=helpers.helperMissing, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, foundHelper;
  buffer += "<span class='n";
  foundHelper = helpers.kindName;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.kindName; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "'>";
  foundHelper = helpers.name;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.name; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</span>: ";
  return buffer;}

function program3(depth0,data) {
  
  var stack1, foundHelper;
  stack1 = depth0.message;
  foundHelper = helpers.apply;
  stack1 = foundHelper ? foundHelper.call(depth0, stack1, "urlReplace, nl2br, allowTags", {hash:{}}) : helperMissing.call(depth0, "apply", stack1, "urlReplace, nl2br, allowTags", {hash:{}});
  return escapeExpression(stack1);}

function program5(depth0,data) {
  
  var stack1, foundHelper;
  stack1 = depth0.message;
  foundHelper = helpers.apply;
  stack1 = foundHelper ? foundHelper.call(depth0, stack1, "urlReplace, nl2br", {hash:{}}) : helperMissing.call(depth0, "apply", stack1, "urlReplace, nl2br", {hash:{}});
  return escapeExpression(stack1);}

  buffer += "<span>";
  stack1 = depth0.created;
  foundHelper = helpers.formatTime;
  stack1 = foundHelper ? foundHelper.call(depth0, stack1, {hash:{}}) : helperMissing.call(depth0, "formatTime", stack1, {hash:{}});
  buffer += escapeExpression(stack1) + "</span> \r\n";
  stack1 = depth0.name;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.noop,fn:self.program(1, program1, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\r\n<span class='m";
  foundHelper = helpers.kindName;
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0.kindName; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "'>";
  stack1 = depth0.allowFormating;
  stack1 = helpers['if'].call(depth0, stack1, {hash:{},inverse:self.program(5, program5, data),fn:self.program(3, program3, data)});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</span><br/>";
  return buffer;});
})();