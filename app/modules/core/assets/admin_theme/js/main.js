(function($, window) {
    var cms;
    window.CMS = cms = {
        store: {},
        popup: {},
        modal: function(title, url, skip, htmlClass) {
            
            var loader = $('<div id="main-loader">loading ...</div>'),
                popup  = this.popup,
                load   = function(content) {
                    loader.fadeOut(function() {
                        popup.content.html(content).show();
                    });
                };
                         
            popup.content.html(loader);
            popup.title.html(title);
            popup.el.find('.modal-dialog').attr('class', popup.class + ' ' + htmlClass);
            if (!popup.el.is(':visible')) {
                popup.el.modal('show');
            }
            this.loadContent(url, skip, function(content) {
                load(content);
            });
            return false;
        },
        loadContent: function(url, skip, cb) {
            this.store.popup = this.store.popup || {};
            var self = this,
                popup = this.popup;
        
            if (!skip && self.store.popup[url]) {
                cb(self.store.popup[url]);
            } else {
                $.get(url, null, function(rsp) { 
                    self.store.popup[url] = rsp;
                    cb(rsp);
                });
            }
        },
        loadRelated: function($el) {
            var $container = $el.closest('tr'),
                $related = $('#related' + $el.data('id'));
            if ($container.hasClass('selected')) {
                $related.fadeOut();
                return $container.removeClass('selected');
            }
            
            if ($related.length) {
                $container.addClass('selected');
                return $related.fadeIn();
            }
            
            var td = $('<td />').attr({'colspan': $container.find('td').length}).html('loading ...');
            $related = $('<tr/>').prop({'id': 'related' + $el.data('id')}).append(td);
            $container.after($related).addClass('selected');
            
            this.loadContent($el.data('url'), $el.data('skip'), function(content) {
                td.html(content);
            });
        },
        submitForm: function(form, cb) {
            form = $(form);
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                dataType: form.data('dataType') || 'json',
                type: form.attr('method'),
                success: function(data) {
                    cb(null, data);
                },
                error: function(xhr, textStatus, errorThrown) {
                    cb(errorThrown, null);
                }
            });
        },
        processMethod: function(link) {
            var href = link.attr('href'),
                method = link.data('method'),
                target = link.attr('target'),
                csrfToken = $('meta[name=csrf-token]').attr('content'),
                csrfParam = $('meta[name=csrf-param]').attr('content'),
                form = $('<form method="post" action="' + href + '"></form>'),
                metadataInput = '<input name="_method" value="' + method + '" type="hidden" />';

            if (csrfParam !== undefined && csrfToken !== undefined) {
                metadataInput += '<input name="' + csrfParam + '" value="' + csrfToken + '" type="hidden" />';
            }

            if (target) { 
                form.attr('target', target); 
            }
            form.hide().append(metadataInput).appendTo('body');
            form.submit();
        },
        init: function() {
            var self = this;
            self.$body = $('body');
            this.popup = {
                el: $('#popup-modal'),
                title: $('#popup-title'),
                content: $('#popup-content'),
                class: 'modal-dialog'
            };
            
            self.$body.on('click.api.process-method', '[data-method="post"]', function(e) {
                e.preventDefault();
                self.processMethod($(this));
            }).on('click.api.open-modal', '[data-op="modal"]', function(e) {
                e.preventDefault();
                var $this = $(this);
                if ($this.attr('disabled') || $this.data('disabled')) {
                    return;
                }
                self.modal($this.data('title'), $this.attr('href'), $this.data('skip'), $this.data('class'));
            }).on('click.api.ajax-operation', '[data-op="ajax"]', function(e) {
                e.preventDefault();
                var $el = $(this),
                    confirmText = $el.data('confirming');
                if (!confirmText || confirm(confirmText)) {
                    $.ajax({
                        url: $el.attr('href'),
                        dataType: 'json',
                        type: $el.data('method') || 'post',
                        success: function(data) {
                            if (data.success) {
                                if (data.target && data.html !== undefined) {
                                    $(data.target).each(function() {
                                        $(this).replaceWith(data.html);
                                    });
                                }
                            }
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });                    
                }
            }).on('click.related', '.related-link', function(e) {
                e.preventDefault();
                self.loadRelated($(this));
            });
        }
    }
    
    $.fn.fixedBar = function(options) {
        var options = $.extend({
            barClass: "bar",
            barFixedClass: "bar-fixed",
        }, options);

        return this.each(function() {
            var $el = $(this),
                buttonSetPosition = $el.offset().top + $el.height();
            $(window)
                .scroll(function() {
                    if ($(this).scrollTop() + $(this).height() < buttonSetPosition) {
                        $el.addClass(options.barFixedClass);
                    } else {
                        $el.removeClass(options.barFixedClass);
                    }
                });
//                .keydown(function(event) {
//                    if (event.keyCode == "13" && event.ctrlKey == true && event.shiftKey == true) {
//                        $("#acceptButton").click();
//                    } else if (event.keyCode == "13" && event.ctrlKey == true) {
//                        $("#saveAndCloseButton").click();
//                    } else if (event.keyCode == "27" && event.ctrlKey == true) {
//                        $("#cancelButton").click();
//                    }
//                });
            $(window).scroll();
        });
    }
    
    $(function() {
        cms.init();
        
        var $formActions = $('.form-actions');
        if ($formActions.length > 0) {
            $formActions.fixedBar();
        }
    });
    
})(jQuery, window);



