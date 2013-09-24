(function($){
	$.fn.center = function () {
	    this.css("position","absolute");
	    var reference = $(window);
	    var tagName = $($(this).parent()[0]).prop("tagName");
	    if(tagName && tagName != "BODY"){
	    	reference = $(this.parent());
	    }
	    this.css("top", ( reference.height() - this.height() ) / 2+reference.scrollTop() + "px");
	    this.css("left", (reference.width() - this.width() ) / 2+reference.scrollLeft() + "px");
	    return this;
	}
	window.Message = function Message(a) {
		this.reference = a.reference != undefined ? a.reference : this;
		this.element = a.element != undefined ? $(a.element) : undefined;
		this.css = a.css != undefined ? a.css : undefined;
		this.message = a.message || "";
		this.beforeShow = a.beforeShow != undefined ? a.beforeShow : function() {
		};
		this.onBlock = a.onBlock != undefined ? a.onBlock : undefined;
		this.onUnblock = a.onUnblock != undefined ? a.onUnblock : function() {
		};
		this.alternateMessage = false;
		this.buttons = a.buttons || {
			Ok : function() {
				this.close()
			}
		};
		this.showMessage = function(b) {
			$(".confirm_wrapper").html(b);
			$('.blockUI.blockMsg').center();
		};
		this.showErrorMessage = function(c) {
			var b = '<div class="message_error">';
			b += c;
			b += "</div>";
			this.showMessage(b);
			return b
		};
		this.showSuccessMessage = function(c) {
			var b = '<div class="message_success">';
			b += c;
			b += "</div>";
			this.showMessage(b);
			return b
		};
		this.showLoadingMessage = function(b) {
			var c = '<div class="message_loading">';
			c += b;
			c += "</div>";
			this.showMessage(c);
			return c
		};
		this.close = function(b) {
			if (this.element != undefined) {
				this.element.unblock({
					onUnblock : this.onUnblock
				})
			} else {
				$.unblockUI()
			}
		};
		this.createMessage = function() {
			var b = '<div><div class="confirm_wrapper">';
			if (typeof (this.alternateMessage) == "string") {
				b += this.alternateMessage;
				b += "</div></div>"
			} else {
				b += this.message;
				b += "<br />";
				b += '<div class="confirm">';
				b += "</div>";
				b += "</div></div>"
			}
			b = $(b);
			$.each(this.buttons, function(c, e) {
				var d = $("<button></button>", {
					"class" : "button black",
					value : c
				});
				d.html(c);
				$(b).find(".confirm").append(d)
			});
			return b.html()
		};
		this.bindEvents = function() {
			var b = this;
			$.each(this.buttons, function(c, d) {
				$(document).off("click", "button[value=" + c + "]");
				$(document).on("click", "button[value=" + c + "]", function() {
					d.apply(b, [ b.reference ])
				})
			})
		};
		this.init = function(b) {
			$(this).queue(function(e) {
				b.beforeShow.call(b);
				var f = b.createMessage();
				var d = e;
				if (a.onBlock != undefined) {
					d = function() {
						a.onBlock();
						e()
					}
				} else {
					d = e
				}
				var c = {
					message : f,
					onBlock : d,
					css : b.css
				};
				if (b.element != undefined) {
					b.element.block(c)
				} else {
					$.blockUI(c)
				}
				$('.blockUI.blockMsg').center();
			}).queue(function(c) {
				b.bindEvents();
				c()
			})
		}(this)
	};
})(jQuery);
