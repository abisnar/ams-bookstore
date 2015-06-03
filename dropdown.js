function runDropdown() {
  'use strict'; // jshint ;_;

  $.fn.dropSelect = function(option) {
    return this.each(function() {

      var $this = $(this);
      var display = $this.find('.dropdown-display');        // display span
      var field = $this.find('input.dropdown-field');       // hidden input
      var options = $this.find('ul.dropdown-menu > li > a');// select options

      // when the hidden field is updated, update dropdown-toggle
      var onFieldChange = function(event) {
        var val = $(this).val();
        var displayText = options.filter("[data-value='" + val + "']").html();
        display.html(displayText);
      };

      // when an option is clicked, update the hidden field
      var onOptionClick = function(event) {
        // stop click from causing page refresh
        event.preventDefault();
        field.val($(this).attr('data-value'));
        field.change();
      };

      field.change(onFieldChange);
      options.click(onOptionClick);

    });
  };

  // invoke on every div element with 'data-select=true'
  $(function() {
    $('div[data-select=true]').dropSelect();
  });

};

function printDropdown(value, alert) {
	var node = "";
	if(alert == 1) {
		node += '<div class="alert alert-info" style="width:530px;">\
		<button type="button" class="close" data-dismiss="alert" style="top: 8px; right:10px">x</button>';
	}
	node += '<div class="input-group" style="width:500px;">\
	<span class="input-group-btn">\
	<div class="btn-group dropdown" data-select="true">\
	  <a class="btn btn-ams dropdown-toggle dropdown-toggle-left-side" data-toggle="dropdown"  style="width:150px;">\
		 <span class="dropdown-display pull-left">Title</span>\
		 <span class="caret caret-black pull-right"></span>\
	  </a>\
	  <input type="hidden" value="title" name="search'+value+'" id="search'+value+'" class="dropdown-field" />\
	  <ul class="dropdown-menu">\
		<li><a href="#" data-value="title">Title</a></li>\
		<li><a href="#" data-value="price">Max price</a></li>\
		<li><a href="#" data-value="type">Type (CD/DVD)</a></li>\
		<li><a href="#" data-value="category">Category</a></li>\
		<li><a href="#" data-value="leadsinger">Lead Singer</a></li>\
		<li><a href="#" data-value="upc">UPC</a></li>\
	  </ul>\
	</div>\
	</span>\
	<input type="text" class="form-control" name="input'+value+'" id="input'+value+'" />\
	</div>';
	if(alert == 1) {
		node += '</div>';
	}
	return node;
}