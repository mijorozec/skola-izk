$(function () {
	$("a.ajax").live("click", function (event) {
		event.preventDefault();
		$.get(this.href);
	});
});